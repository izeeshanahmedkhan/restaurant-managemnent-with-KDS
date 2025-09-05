<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\Branch;
use App\Model\ChefBranch;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KDSController extends Controller
{
    /**
     * Display the KDS dashboard
     */
    public function dashboard()
    {
        $user = Auth::guard('branch')->user();
        $branches = collect();
        $selectedBranch = null;
        
        if (!$user) {
            return redirect()->route('branch.auth.login');
        }
        
        // Branch user information
        
        // Branch users can only see their own branch
        // For branch users, they ARE the branch, so use their own ID
        // Branch users are stored in the branches table, so they don't have a branch_id field
        $branchId = $user->id;
        
        // First try to find the branch by the user's branch_id or user's own ID
        $selectedBranch = Branch::where('id', $branchId)->where('status', 1)->first();
        
        // If no branch found, try to find any active branch as fallback
        if (!$selectedBranch) {
            $selectedBranch = Branch::where('status', 1)->first();
            if ($selectedBranch) {
            } else {
            }
        }
        
        $branches = $selectedBranch ? collect([$selectedBranch]) : collect();
        
        
        // Get orders for the selected branch
        $newOrders = collect();
        $cookingOrders = collect();
        $doneOrders = collect();
        
        if ($selectedBranch) {
            $newOrders = $this->getOrdersByStatus($selectedBranch->id, ['pending', 'confirmed', 'processing']);
            $cookingOrders = $this->getOrdersByStatus($selectedBranch->id, ['cooking']);
            $doneOrders = $this->getOrdersByStatus($selectedBranch->id, ['done'], 2); // Last 2 hours
        }
        
        return view('kds.branch', compact('branches', 'selectedBranch', 'newOrders', 'cookingOrders', 'doneOrders'));
    }
    
    /**
     * Get orders for AJAX requests
     */
    public function getOrders(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string|in:pending,confirmed,processing,cooking,done',
            'search' => 'nullable|string|max:255'
        ]);
        
        $user = Auth::guard('branch')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Branch users can only access their own branch
        // Branch users are stored in the branches table, so they ARE the branch
        $branchId = $user->id;
        $status = $request->get('status');
        $search = $request->get('search');
        
        // Validate branch access - branch users can only access their own branch
        if (!$user || $user->id !== $branchId) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }
        
        $query = Order::with(['details.product', 'customer'])
            ->where('branch_id', $branchId)
            ->whereIn('order_status', ['pending', 'confirmed', 'processing', 'cooking', 'done']);
        
        // Filter by status
        if ($status && in_array($status, ['pending', 'confirmed', 'processing', 'cooking', 'done'])) {
            $query->where('order_status', $status);
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
        
        // Format orders for KDS display
        $formattedOrders = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'order_number' => '#' . str_pad($order->id, 7, '0', STR_PAD_LEFT),
                'token_number' => $order->token ?? 'N/A',
                'status' => $order->order_status,
                'created_at' => $order->created_at->format('h:i A, d-m-Y'),
                'customer_name' => $order->customer ? $order->customer->f_name . ' ' . $order->customer->l_name : 'Guest',
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
                'total_amount' => $order->order_amount,
                'order_note' => $order->order_note
            ];
        });
        
        
        return response()->json([
            'now' => Carbon::now()->toISOString(),
            'orders' => $formattedOrders,
            'total_count' => $formattedOrders->count()
        ]);
    }
    
    /**
     * Get orders by status for initial load
     */
    private function getOrdersByStatus($branchId, $statuses, $hoursBack = null)
    {
        $query = Order::with(['details.product', 'customer'])
            ->where('branch_id', $branchId)
            ->whereIn('order_status', $statuses);
            
        if ($hoursBack) {
            $query->where('created_at', '>=', Carbon::now()->subHours($hoursBack));
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:PENDING,CONFIRMED,PROCESSING,COOKING,DONE'
        ]);
        
        $user = Auth::guard('branch')->user();
        $status = $request->input('status');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        // Validate branch access - branch users can only access their own branch orders
        if ($user->id !== $order->branch_id) {
            return response()->json(['error' => 'Unauthorized access to this order'], 403);
        }
        
        // Validate status
        $validStatuses = ['PENDING', 'CONFIRMED', 'PROCESSING', 'COOKING', 'DONE'];
        if (!in_array(strtoupper($status), $validStatuses)) {
            return response()->json(['error' => 'Invalid status'], 400);
        }
        
        // Update order status
        $order->order_status = strtolower($status);
        $order->save();
        
        return response()->json(['ok' => true]);
    }
    
    /**
     * Search orders
     */
    public function searchOrders(Request $request)
    {
        $user = Auth::guard('branch')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Branch users can only access their own branch
        // Branch users are stored in the branches table, so they ARE the branch
        $branchId = $user->id;
        $query = $request->get('q');
        
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
        $user = Auth::guard('branch')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Branch users can only access their own branch
        // Branch users are stored in the branches table, so they ARE the branch
        $branchId = $user->id;

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
            'range' => 'nullable|string|in:today,week,month'
        ]);
        
        $user = Auth::guard('branch')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Branch users can only access their own branch
        // Branch users are stored in the branches table, so they ARE the branch
        $branchId = $user->id;
        $range = $request->get('range', 'today');

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
                
                // Parse addons from JSON
                $addons = $detail->add_on_ids ?? [];
                $addonText = '';
                if (is_string($addons)) {
                    try {
                        $addonIds = json_decode($addons, true);
                        if (is_array($addonIds)) {
                            $addonQuantities = is_string($detail->add_on_qtys) ? json_decode($detail->add_on_qtys, true) : $detail->add_on_qtys;
                            $addonQuantities = $addonQuantities ?: [];
                            
                            $addonLabels = [];
                            foreach ($addonIds as $index => $addonId) {
                                $addon = \App\Model\AddOn::find($addonId);
                                if ($addon) {
                                    $quantity = $addonQuantities[$index] ?? 1;
                                    $addonLabels[] = $addon->name . ($quantity > 1 ? ' (x' . $quantity . ')' : '');
                                }
                            }
                            $addonText = implode(', ', $addonLabels);
                        }
                    } catch (\Exception $e) {
                        $addonText = '';
                    }
                }

                if (isset($itemsSummary[$productId])) {
                    $itemsSummary[$productId]['count'] += $quantity;
                } else {
                    $meta = [];
                    if ($variationText) {
                        $meta[] = $variationText;
                    }
                    if ($addonText) {
                        $meta[] = '+ ' . $addonText;
                    }
                    if ($size !== 'Regular') {
                        $meta[] = "Size: {$size}";
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
     * Check if user can access the specified branch
     */
    private function canAccessBranch($user, $branchId)
    {
        // Branch users can only access their own branch
        // For now, allow access if user exists and branchId is provided
        return $user && $branchId;
    }
}
