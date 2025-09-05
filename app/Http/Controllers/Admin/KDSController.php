<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\Branch;
use App\Model\ChefBranch;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KDSController extends Controller
{
    /**
     * Display the KDS dashboard
     */
    public function dashboard()
    {
        $user = Auth::guard('admin')->user();
        $branches = collect();
        $selectedBranch = null;
        
        
        // Get branches based on user role
        if ($user && $user->role_id == 1) { // Admin
            $branches = Branch::where('status', 1)->get();
        } elseif ($user) { // Kitchen staff
            $chefBranches = ChefBranch::where('user_id', $user->id)->pluck('branch_id');
            $branches = Branch::whereIn('id', $chefBranches)->where('status', 1)->get();
        } else {
            // Fallback - redirect to login if no user
            Log::warning('KDS Dashboard accessed without authentication');
            return redirect()->route('admin.auth.login');
        }
        
        // Get selected branch or first available
        $selectedBranchId = request('branch_id', $branches->first()?->id);
        if ($selectedBranchId) {
            $selectedBranch = $branches->firstWhere('id', $selectedBranchId);
        }
        
        // Get orders for the selected branch
        $newOrders = collect();
        $cookingOrders = collect();
        $doneOrders = collect();
        
        if ($selectedBranch) {
            $newOrders = $this->getOrdersByStatus($selectedBranch->id, ['pending', 'confirmed', 'processing']);
            $cookingOrders = $this->getOrdersByStatus($selectedBranch->id, ['cooking']);
            $doneOrders = $this->getOrdersByStatus($selectedBranch->id, ['done'], 2); // Last 2 hours
        }
        
        
        return view('kds.index', compact('branches', 'selectedBranch', 'newOrders', 'cookingOrders', 'doneOrders'));
    }
    
    /**
     * Get orders for AJAX requests (new API format)
     */
    public function getOrders(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'since' => 'nullable|date',
            'search' => 'nullable|string|max:255'
        ]);
        
        $user = Auth::guard('admin')->user();
        $branchId = $request->get('branch_id');
        $since = $request->get('since');
        $search = $request->get('search');
        
        
        // Validate branch access
        if (!$user || !$this->canAccessBranch($user, $branchId)) {
            Log::error('KDS unauthorized access', [
                'user_id' => $user ? $user->id : 'null',
                'branch_id' => $branchId
            ]);
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        
        $query = Order::with(['details.product', 'customer'])
            ->where('branch_id', $branchId)
            ->whereIn('order_status', ['pending', 'confirmed', 'processing', 'cooking', 'done']);
        
        // Filter by since timestamp for incremental updates
        if ($since) {
            try {
                $sinceDate = Carbon::parse($since);
                $query->where('updated_at', '>', $sinceDate);
            } catch (\Exception $e) {
                Log::warning('KDS invalid since timestamp', ['since' => $since, 'error' => $e->getMessage()]);
            }
        }
        
        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('order_note', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('f_name', 'like', "%{$search}%")
                                   ->orWhere('l_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('details.product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Auto-hide done orders older than 2 hours (from when they were marked done)
        $query->where(function($q) {
            $q->where('order_status', '!=', 'done')
              ->orWhere('updated_at', '>', Carbon::now()->subHours(2));
        });
        
        $orders = $query->orderBy('created_at', 'desc')->get();
        
        // Format orders for new API format
        $formattedOrders = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'number' => str_pad($order->id, 7, '0', STR_PAD_LEFT),
                'status' => strtoupper($order->order_status),
                'placed_at' => $order->created_at->toISOString(),
                'items' => $order->details->map(function($detail) {
                    $variations = $detail->variation ?? [];
                    $addons = $detail->add_on_ids ?? [];
                    
                    // Parse variations from JSON
                    if (is_string($variations)) {
                        try {
                            $variations = json_decode($variations, true) ?: [];
                        } catch (\Exception $e) {
                            $variations = $variations ? [$variations] : [];
                        }
                    } elseif (!is_array($variations)) {
                        $variations = $variations ? [$variations] : [];
                    }
                    
                    // Parse addons from JSON
                    if (is_string($addons)) {
                        try {
                            $addons = json_decode($addons, true) ?: [];
                        } catch (\Exception $e) {
                            $addons = $addons ? [$addons] : [];
                        }
                    } elseif (!is_array($addons)) {
                        $addons = $addons ? [$addons] : [];
                    }
                    
                    // Get addon details
                    $addonDetails = [];
                    if (!empty($addons)) {
                        $addonQuantities = is_string($detail->add_on_qtys) ? json_decode($detail->add_on_qtys, true) : $detail->add_on_qtys;
                        $addonQuantities = $addonQuantities ?: [];
                        
                        foreach ($addons as $index => $addonId) {
                            $addon = \App\Model\AddOn::find($addonId);
                            if ($addon) {
                                $addonDetails[] = [
                                    'name' => $addon->name,
                                    'quantity' => $addonQuantities[$index] ?? 1,
                                    'price' => $addon->price
                                ];
                            }
                        }
                    }
                    
                    // Format variations for display
                    $variationText = '';
                    if (!empty($variations)) {
                        $variationLabels = [];
                        foreach ($variations as $variation) {
                            if (isset($variation['values']) && is_array($variation['values'])) {
                                foreach ($variation['values'] as $value) {
                                    if (isset($value['label'])) {
                                        $variationLabels[] = $value['label'];
                                    }
                                }
                            }
                        }
                        $variationText = implode(', ', $variationLabels);
                    }
                    
                    return [
                        'name' => $detail->product->name ?? 'Unknown Item',
                        'quantity' => $detail->quantity,
                        'variations' => $variations,
                        'variation_text' => $variationText,
                        'addons' => $addonDetails,
                        'addon_text' => !empty($addonDetails) ? implode(', ', array_map(function($addon) {
                            return $addon['name'] . ($addon['quantity'] > 1 ? ' (x' . $addon['quantity'] . ')' : '');
                        }, $addonDetails)) : ''
                    ];
                }),
                'token' => $order->token,
                'customer_name' => $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : null,
                'total_amount' => $order->order_amount,
                'order_note' => $order->order_note
            ];
        });
        
        
        return response()->json([
            'now' => Carbon::now()->toISOString(),
            'orders' => $formattedOrders
        ]);
    }
    
    /**
     * Update order status (new API format)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:PENDING,CONFIRMED,PROCESSING,COOKING,DONE'
        ]);
        
        $user = Auth::guard('admin')->user();
        $newStatus = $request->input('status');
        
        
        // Validate status
        $validStatuses = ['PENDING', 'CONFIRMED', 'PROCESSING', 'COOKING', 'DONE'];
        if (!in_array(strtoupper($newStatus), $validStatuses)) {
            Log::error('Admin KDS invalid status', [
                'new_status' => $newStatus,
                'allowed_statuses' => $validStatuses
            ]);
            return response()->json(['error' => 'Invalid status'], 400);
        }
        
        $order = Order::find($id);
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        // Check branch access
        if (!$this->canAccessBranch($user, $order->branch_id)) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        
        // Update order status
        $order->order_status = strtolower($newStatus);
        $order->save();
        
        
        return response()->json(['ok' => true]);
    }
    
    /**
     * Search orders
     */
    public function searchOrders(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $branchId = $request->get('branch_id');
        $query = $request->get('q');
        
        if (!$user || !$this->canAccessBranch($user, $branchId)) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        
        $orders = Order::with(['details.product', 'customer'])
            ->where('branch_id', $branchId)
            ->where(function($q) use ($query) {
                $q->where('id', 'like', "%{$query}%")
                  ->orWhere('order_note', 'like', "%{$query}%")
                  ->orWhereHas('customer', function($customerQuery) use ($query) {
                      $customerQuery->where('f_name', 'like', "%{$query}%")
                                   ->orWhere('l_name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('details.product', function($productQuery) use ($query) {
                      $productQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->whereIn('order_status', ['pending', 'confirmed', 'processing', 'cooking', 'done'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        $formattedOrders = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'order_number' => '#' . str_pad($order->id, 7, '0', STR_PAD_LEFT),
                'status' => $order->order_status,
                'customer_name' => $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'Guest',
                'created_at' => $order->created_at->format('h:i A, d-m-Y')
            ];
        });
        
        return response()->json([
            'orders' => $formattedOrders,
            'query' => $query
        ]);
    }
    
    /**
     * Get items summary for sidebar
     */
    public function getItemsSummary(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $branchId = $request->get('branch_id');

        if (!$user || !$this->canAccessBranch($user, $branchId)) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Get active orders for the branch
        $orders = Order::with(['details.product'])
            ->where('branch_id', $branchId)
            ->whereIn('order_status', ['pending', 'confirmed', 'processing', 'cooking'])
            ->get();

        $itemsSummary = [];

        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                $productId = $detail->product_id;
                $productName = $detail->product->name ?? 'Unknown Item';
                $quantity = $detail->quantity;

                if (isset($itemsSummary[$productId])) {
                    $itemsSummary[$productId]['quantity'] += $quantity;
                } else {
                    $itemsSummary[$productId] = [
                        'id' => $productId,
                        'name' => $productName,
                        'quantity' => $quantity
                    ];
                }
            }
        }

        // Convert to array and sort by quantity descending
        $items = array_values($itemsSummary);
        usort($items, function($a, $b) {
            return $b['quantity'] - $a['quantity'];
        });

        return response()->json([
            'items' => $items
        ]);
    }

    /**
     * Get items board data for today's orders
     */
    public function getItemsBoard(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'range' => 'nullable|string|in:today,week,month'
        ]);
        
        $user = Auth::guard('admin')->user();
        $branchId = $request->get('branch_id');
        $range = $request->get('range', 'today');

        if (!$user || !$this->canAccessBranch($user, $branchId)) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Get orders for the specified range
        $query = Order::with(['details.product'])
            ->where('branch_id', $branchId)
            ->whereIn('order_status', ['pending', 'confirmed', 'processing', 'cooking', 'done']);

        if ($range === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }

        $orders = $query->get();

        $itemsSummary = [];

        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                $productId = $detail->product_id;
                $productName = $detail->product->name ?? 'Unknown Item';
                $quantity = $detail->quantity;
                $size = 'Regular'; // Default size since Product model doesn't have size field
                $variations = $detail->variation ?? [];

                // Parse variations from JSON
                $variationText = '';
                if (is_string($variations)) {
                    try {
                        $variationData = json_decode($variations, true);
                        if (is_array($variationData)) {
                            $variationLabels = [];
                            foreach ($variationData as $variation) {
                                if (isset($variation['values']) && is_array($variation['values'])) {
                                    foreach ($variation['values'] as $value) {
                                        if (isset($value['label'])) {
                                            $variationLabels[] = $value['label'];
                                        }
                                    }
                                }
                            }
                            $variationText = implode(', ', $variationLabels);
                        }
                    } catch (\Exception $e) {
                        $variationText = '';
                    }
                }

                if (isset($itemsSummary[$productId])) {
                    $itemsSummary[$productId]['count'] += $quantity;
                } else {
                    $meta = [];
                    if ($size !== 'Regular') {
                        $meta[] = "Size: {$size}";
                    }
                    if (!empty($variationText)) {
                        $meta[] = "Variations: {$variationText}";
                    }

                    $itemsSummary[$productId] = [
                        'id' => $productId,
                        'name' => $productName,
                        'count' => $quantity,
                        'meta' => $meta
                    ];
                }
            }
        }

        // Convert to array and sort by count descending
        $items = array_values($itemsSummary);
        usort($items, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        return response()->json($items);
    }
    
    /**
     * Get orders by status for initial load
     */
    private function getOrdersByStatus($branchId, $statuses, $hoursBack = null)
    {
        $query = Order::with(['details.product', 'customer'])
            ->where('branch_id', $branchId)
            ->whereIn('order_status', $statuses);
        
        // For done orders, limit to last N hours
        if ($hoursBack && in_array('done', $statuses)) {
            $query->where('created_at', '>', Carbon::now()->subHours($hoursBack));
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    /**
     * Check if user can access the specified branch
     */
    private function canAccessBranch($user, $branchId)
    {
        // Admin users can access all branches
        if ($user && $user->role_id == 1) {
            return true;
        }
        
        // For other users, check if they have access to the specific branch
        return $user && $branchId;
    }
}
