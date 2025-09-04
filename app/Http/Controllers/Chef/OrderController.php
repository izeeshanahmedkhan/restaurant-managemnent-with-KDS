<?php

namespace App\Http\Controllers\Chef;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\CustomerAddress;
use App\Model\DeliveryHistory;
use App\Model\Order;
use App\Models\DeliveryChargeByArea;
use App\Models\OfflinePayment;
use App\Models\OrderArea;
use App\Models\OrderPartialPayment;
use App\Models\ReferralCustomer;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

use function App\CentralLogics\translate;

class OrderController extends Controller
{
    public function __construct(
        private Order           $order,
        private User            $user,
        private BusinessSetting $business_setting,
        private CustomerAddress $customer_addresses,
        private OrderArea $orderArea,
    ){}


    private function getChefBranchId()
    {
        $chefBranch = \App\Model\ChefBranch::where('user_id', auth('chef')->user()->id)->first();
        if (!$chefBranch) {
            \Log::error('Chef branch not found for user: ' . auth('chef')->user()->id);
            return null;
        }
        return $chefBranch->branch_id;
    }


    public function list($status, Request $request): Renderable
    {
        $queryParam = [];
        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];

        $branchId = $this->getChefBranchId();
        \Log::info('Order list - Branch ID: ' . $branchId . ', Status: ' . $status);
        
        $query = $this->order->where('branch_id', $branchId);

        if ($status !== 'all') {
            $query = $query->where('order_status', $status);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $request['search']];
        }

        if ($request->has('from') && $request->has('to')) {
            $query = $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            $queryParam = ['from' => $from, 'to' => $to];
        }

        $orders = $query->with(['customer', 'delivery_man'])->orderBy('id', 'DESC')->paginate(Helpers::getPagination())->appends($queryParam);

        // Build counts for All Orders cards (filtered only by branch)
        $orderCount = [];
        if ($status === 'all') {
            $base = $this->order->where('branch_id', $branchId);
            $orderCount = [
                'pending' => (clone $base)->where('order_status', 'pending')->count(),
                'confirmed' => (clone $base)->where('order_status', 'confirmed')->count(),
                'processing' => (clone $base)->where('order_status', 'processing')->count(),
                'out_for_delivery' => (clone $base)->where('order_status', 'out_for_delivery')->count(),
                'delivered' => (clone $base)->where('order_status', 'delivered')->count(),
                'canceled' => (clone $base)->where('order_status', 'canceled')->count(),
                'returned' => (clone $base)->where('order_status', 'returned')->count(),
                'failed' => (clone $base)->where('order_status', 'failed')->count(),
            ];
        }

        return view('chef-views.order.list', compact('orders', 'status', 'search', 'from', 'to', 'orderCount'));
    }

    /**
     * Kitchen Display System (KDS) view
     */
    public function kds(): Renderable
    {
        $branchId = $this->getChefBranchId();
        
        // Debug: Log the branch ID
        \Log::info('KDS - Branch ID: ' . $branchId);
        
        // Get all orders for this branch (not just specific statuses)
        $allOrders = $this->order->where('branch_id', $branchId)
            ->with(['details.product', 'customer'])
            ->orderBy('created_at', 'DESC')
            ->get();
            
        \Log::info('KDS - Total orders found: ' . $allOrders->count());
        
        // Get orders for KDS - include more statuses
        $kdsOrders = $allOrders->whereIn('order_status', ['pending', 'confirmed', 'processing', 'out_for_delivery']);
        
        \Log::info('KDS - KDS orders found: ' . $kdsOrders->count());

        // Group orders by status
        $ordersByStatus = [
            'confirmed' => $kdsOrders->where('order_status', 'confirmed'),
            'processing' => $kdsOrders->where('order_status', 'processing'),
            'out_for_delivery' => $kdsOrders->where('order_status', 'out_for_delivery'),
        ];

        // Get all items with total quantities from all KDS orders (confirmed, processing, out_for_delivery)
        $allItems = [];
        $allKdsOrders = $kdsOrders; // Use all KDS orders instead of just confirmed
        
        \Log::info('KDS - All KDS orders count: ' . $allKdsOrders->count());
        
        foreach ($allKdsOrders as $order) {
            \Log::info('KDS - Processing order: ' . $order->id . ' with ' . $order->details->count() . ' details');
            foreach ($order->details as $detail) {
                $productName = $detail->product->name ?? 'Unknown Product';
                $quantity = $detail->quantity;
                
                \Log::info('KDS - Product: ' . $productName . ', Quantity: ' . $quantity);
                
                if (isset($allItems[$productName])) {
                    $allItems[$productName]['total_quantity'] += $quantity;
                    \Log::info('KDS - Updated existing item: ' . $productName . ' to total: ' . $allItems[$productName]['total_quantity']);
                } else {
                    $allItems[$productName] = [
                        'name' => $productName,
                        'total_quantity' => $quantity,
                        'variations' => $detail->variation ? json_decode($detail->variation, true) : null
                    ];
                    \Log::info('KDS - Added new item: ' . $productName . ' with quantity: ' . $quantity);
                }
            }
        }
        
        // Convert array back to collection
        $allItems = collect(array_values($allItems));

        // Debug data
        \Log::info('KDS - Items found: ' . $allItems->count());
        foreach ($allItems as $item) {
            \Log::info('KDS - Final item: ' . $item['name'] . ' = ' . $item['total_quantity']);
        }
        \Log::info('KDS - Orders by status: ', [
            'confirmed' => $ordersByStatus['confirmed']->count(),
            'processing' => $ordersByStatus['processing']->count(),
            'out_for_delivery' => $ordersByStatus['out_for_delivery']->count(),
        ]);

        return view('chef-views.kds.standalone', compact('ordersByStatus', 'allItems', 'allOrders'));
    }

    public function updateOrderStatus(Request $request): JsonResponse
    {
        try {
            $orderId = $request->input('order_id');
            $newStatus = $request->input('status');
            
            $order = Order::where('id', $orderId)
                ->where('branch_id', $this->getChefBranchId())
                ->first();
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }
            
            $order->order_status = $newStatus;
            $order->save();
            
            \Log::info('KDS - Order status updated: Order ID ' . $orderId . ' to ' . $newStatus);
            
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order_id' => $orderId,
                'new_status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            \Log::error('KDS - Error updating order status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating order status'
            ], 500);
        }
    }

    public function details($id): Renderable
    {
        $order = $this->order->with(['customer', 'delivery_man', 'details'])->where(['id' => $id, 'branch_id' => $this->getChefBranchId()])->first();

        if (isset($order)) {
            return view('chef-views.order.order-view', compact('order'));
        } else {
            Toastr::info(translate('No more orders!'));
            return back();
        }
    }


    public function preparationTime(Request $request, $id): JsonResponse
    {
        $order = $this->order->find($id);
        $order->preparation_time = $request->preparation_time;
        $order->save();

        return response()->json(['message' => translate('Preparation time updated')]);
    }


    public function status(Request $request): JsonResponse
    {
        $order = $this->order->find($request->id);
        $order->order_status = $request->order_status;
        $order->save();

        return response()->json(['message' => translate('Order status updated')]);
    }


    public function addDeliveryman(Request $request): JsonResponse
    {
        $order = $this->order->find($request->order_id);
        $order->delivery_man_id = $request->delivery_man_id;
        $order->save();

        return response()->json(['message' => translate('Delivery man assigned')]);
    }


    public function paymentStatus(Request $request): JsonResponse
    {
        $order = $this->order->find($request->id);
        $order->payment_status = $request->payment_status;
        $order->save();

        return response()->json(['message' => translate('Payment status updated')]);
    }




    public function addPaymentReferenceCode(Request $request, $id): JsonResponse
    {
        $order = $this->order->find($id);
        $order->payment_reference = $request->payment_reference;
        $order->save();

        return response()->json(['message' => translate('Payment reference added')]);
    }



    /**
     * @param Request $request
     * @param $order_id
     * @return JsonResponse
     */
    public function changeDeliveryTimeDate(Request $request, $order_id): JsonResponse
    {
        $order = $this->order->find($order_id);
        $order->delivery_date = $request->delivery_date;
        $order->delivery_time = $request->delivery_time;
        $order->save();

        return response()->json(['message' => translate('Delivery time updated')]);
    }


    public function verifyOfflinePayment($order_id, $status): RedirectResponse
    {
        $order = $this->order->find($order_id);
        $order->payment_status = $status;
        $order->save();

        Toastr::success(translate('Payment status updated'));
        return back();
    }

    /**
     * @param Request $request
     * @param $order_id
     * @return JsonResponse
     */
    public function updateOrderDeliveryArea(Request $request, $order_id): JsonResponse
    {
        $order = $this->order->find($order_id);
        $order->delivery_area_id = $request->delivery_area_id;
        $order->save();

        return response()->json(['message' => translate('Delivery area updated')]);
    }
}
