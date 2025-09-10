<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\CentralLogics\CustomerLogic;
use App\Http\Controllers\Controller;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\CustomerAddress;
use App\Model\Order;
use App\Models\DeliveryChargeByArea;
use App\Models\GuestUser;
// OfflinePayment model removed
use App\Models\OrderArea;
use App\Models\OrderPartialPayment;
use App\Models\ReferralCustomer;
use App\User;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Brian2694\Toastr\Facades\Toastr;
use DateTime;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CentralLogics\translate;
use function Symfony\Component\String\u;


class OrderController extends Controller
{
    public function __construct(
        private Order           $order,
        private CustomerAddress $customer_address,
        private OrderLogic      $order_logic,
        private User            $user,
        private BusinessSetting $business_setting,
        private OrderArea     $orderArea
    )
    {}

    /**
     * @param Request $request
     * @param $status
     * @return Renderable
     */
    public function list(Request $request, $status): Renderable
    {
        Helpers::update_daily_product_stock();
        $this->order->where(['checked' => 0])->update(['checked' => 1]);

        $queryParam = [];
        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];
        $branchId = $request['branch_id'];

        $query = $this->order->newQuery();

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $queryParam['search'] = $search;
        }

        if ($branchId && $branchId != 0) {
            $query->where('branch_id', $branchId);
            $queryParam['branch_id'] = $branchId;
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            $queryParam['from'] = $from;
            $queryParam['to'] = $to;
        }

        if ($status == 'schedule') {
            $query->with(['customer', 'branch'])->schedule();
        } elseif ($status != 'all') {
            $query->with(['customer', 'branch'])->where('order_status', $status)->notSchedule();
        } else {
            $query->with(['customer', 'branch']);
        }

        $key = explode(' ', $request['search']);

        $orderCount = [
            'pending' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'pending'])
                ->when($branchId && $branchId != 0, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })
                ->when($request->has('search'), function ($query) use ($key) {
                    $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_status', 'like', "%{$value}%")
                                ->orWhere('transaction_reference', 'like', "%{$value}%");
                        }
                    });
                })
                ->count(),

            'confirmed' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'confirmed'])
                ->when($branchId && $branchId != 0, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })
                ->when($request->has('search'), function ($query) use ($key) {
                    $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_status', 'like', "%{$value}%")
                                ->orWhere('transaction_reference', 'like', "%{$value}%");
                        }
                    });
                })
                ->count(),

            'processing' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'processing'])
                ->when($branchId && $branchId != 0, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })
                ->when($request->has('search'), function ($query) use ($key) {
                    $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_status', 'like', "%{$value}%")
                                ->orWhere('transaction_reference', 'like', "%{$value}%");
                        }
                    });
                })
                ->count(),

            'out_for_delivery' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'out_for_delivery'])
                ->when($branchId && $branchId != 0, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })
                ->when($request->has('search'), function ($query) use ($key) {
                    $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_status', 'like', "%{$value}%")
                                ->orWhere('transaction_reference', 'like', "%{$value}%");
                        }
                    });
                })
                ->count(),

            'delivered' => $this->order
                ->notPos()
                ->notDineIn()
                ->where(['order_status' => 'delivered'])
                ->when($branchId && $branchId != 0, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })
                ->when($request->has('search'), function ($query) use ($key) {
                    $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_status', 'like', "%{$value}%")
                                ->orWhere('transaction_reference', 'like', "%{$value}%");
                        }
                    });
                })
                ->count(),

            'canceled' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'canceled'])
                ->when($branchId && $branchId != 0, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })
                ->when($request->has('search'), function ($query) use ($key) {
                    $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_status', 'like', "%{$value}%")
                                ->orWhere('transaction_reference', 'like', "%{$value}%");
                        }
                    });
                })
                ->count(),

            'returned' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'returned'])
                ->when($branchId && $branchId != 0, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })
                ->when($request->has('search'), function ($query) use ($key) {
                    $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_status', 'like', "%{$value}%")
                                ->orWhere('transaction_reference', 'like', "%{$value}%");
                        }
                    });
                })
                ->count(),

            'failed' => $this->order
                ->notPos()
                ->notDineIn()
                ->notSchedule()
                ->where(['order_status' => 'failed'])
                ->when($branchId && $branchId != 0, function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->when(!is_null($from) && !is_null($to), function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
                })->count(),
        ];

        $orders = $query->notPos()->notDineIn()->latest()->paginate(Helpers::getPagination())->appends($queryParam);
        return view('admin-views.order.list', compact('orders', 'status', 'search', 'from', 'to', 'orderCount', 'branchId'));
    }


    public function details($id)
    {
        $order = $this->order->with(['details', 'customer', 'branch', 'order_partial_payments'])
            ->where(['id' => $id])
            ->first();

        if (!isset($order)) {
            Toastr::info('No order found!');
            return back();
        }

        $address = $order->delivery_address ?? CustomerAddress::find($order->delivery_address_id);
        $order->address = $address;

        // Delivery man functionality removed

        $deliveryDateTime = $order['delivery_date'] . ' ' . $order['delivery_time'];
        $orderedTime = Carbon::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s", strtotime($deliveryDateTime)));
        $remainingTime = $orderedTime->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');
        $order['remaining_time'] = $remainingTime;


        return view('admin-views.order.order-view', compact('order'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $order = $this->order->find($request->id);

        if (in_array($order->order_status, ['delivered', 'failed'])) {
            Toastr::warning('You can not change the status of '. $order->order_status .' order');
            return back();
        }

        if ($request->order_status == 'delivered' && $order['transaction_reference'] == null && !in_array($order['payment_method'], ['cash_on_delivery', 'wallet_payment', 'offline_payment'])) {
            Toastr::warning('Add your payment reference first');
            return back();
        }

        // Delivery man validation removed
        if ($request->order_status == 'completed' && $order->payment_status != 'paid') {
            Toastr::warning('Please update payment status first!');
            return back();
        }

        if ($request->order_status == 'delivered') {
            if ($order->is_guest == 0){
                // Loyalty point functionality removed

                if ($order->transaction == null) {
                    $ol = $this->order_logic->create_transaction($order, 'admin');
                }

                $user = $this->user->find($order->user_id);

                if (isset($user)){
                    $referralData = $user?->referral_customer_details;

                    if ($referralData && $referralData->is_used_by_refer == 0) {
                        $referralEarningAmount = $referralData->ref_by_earning_amount ?? 0;
                        $referredByUser = $this->user->find($user->refer_by);

                        // Wallet functionality removed

                       // ReferralCustomer::where('user_id', $order->user_id)->update(['is_used_by_refer' => 1]);
                    }
                }
            }

            if ($order['payment_method'] == 'cash_on_delivery'){
                $partialData = OrderPartialPayment::where(['order_id' => $order->id])->first();
                if ($partialData){
                    $partial = new OrderPartialPayment;
                    $partial->order_id = $order['id'];
                    $partial->paid_with = 'cash_on_delivery';
                    $partial->paid_amount = $partialData->due_amount;
                    $partial->due_amount = 0;
                    $partial->save();
                }
            }
        }

        $order->order_status = $request->order_status;
        if ($request->order_status == 'delivered') {
            $order->payment_status = 'paid';
        }
        $order->save();

        // Delivery man functionality removed

        $message = Helpers::order_status_update_message($request->order_status);

        $restaurantName = Helpers::get_business_settings('restaurant_name');
        $deliverymanName = ''; // Delivery man functionality removed
        $customerName = $order->is_guest == 0 ? ($order->customer ? $order->customer->f_name. ' '. $order->customer->l_name : '') : 'Guest User';
        $local = $order->is_guest == 0 ? ($order->customer ? $order->customer->language_code : 'en') : 'en';

        // Translation functionality removed - always use English

        $value = Helpers::text_variable_data_format(value:$message, user_name: $customerName, restaurant_name: $restaurantName, order_id: $order->id);

        $customerFcmToken = null;
        if($order->is_guest == 0){
            $customerFcmToken = $order->customer ? $order->customer->cm_firebase_token : null;
        }elseif($order->is_guest == 1){
            $customerFcmToken = $order->guest ? $order->guest->fcm_token : null;
        }

        try {
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order_status',
                ];
                if (isset($customerFcmToken)) {
                    Helpers::send_push_notif_to_device($customerFcmToken, $data);
                }

            }
        } catch (\Exception $e) {
            // Notification functionality removed
        }

        // Delivery man functionality removed
        // Notification functionality removed

        Toastr::success('Order status updated!');
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function preparationTime(Request $request, $id): RedirectResponse
    {
        $order = $this->order->with(['customer'])->find($id);
        $deliveryDateTime = $order['delivery_date'] . ' ' . $order['delivery_time'];

        $orderedTime = Carbon::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s", strtotime($deliveryDateTime)));
        $remainingTime = $orderedTime->add($order['preparation_time'], 'minute')->format('Y-m-d H:i:s');

        //if delivery time is not over
        if (strtotime(date('Y-m-d H:i:s')) < strtotime($remainingTime)) {
            $delivery_time = new DateTime($remainingTime); //time when preparation will be over
            $current_time = new DateTime(); // time now
            $interval = $delivery_time->diff($current_time);
            $remainingMinutes = $interval->i;
            $remainingMinutes += $interval->days * 24 * 60;
            $remainingMinutes += $interval->h * 60;
            $order->preparation_time = 0;
        } else {
            //if delivery time is over
            $delivery_time = new DateTime($remainingTime);
            $current_time = new DateTime();
            $interval = $delivery_time->diff($current_time);
            $diffInMinutes = $interval->i;
            $diffInMinutes += $interval->days * 24 * 60;
            $diffInMinutes += $interval->h * 60;
            $order->preparation_time = 0;
        }

        $newDeliveryDateTime = Carbon::now()->addMinutes((int) $request->extra_minute);
        $order->delivery_date = $newDeliveryDateTime->format('Y-m-d');
        $order->delivery_time = $newDeliveryDateTime->format('H:i:s');

        $order->save();

        if ($order->is_guest == 0){
            $customer = $order->customer;

            $message = Helpers::order_status_update_message('customer_notify_message_for_time_change');
            $local = $order->customer ? $order->customer->language_code : 'en';

            // Translation functionality removed - always use English
            $restaurantName = Helpers::get_business_settings('restaurant_name');
            $deliverymanName = ''; // Delivery man functionality removed
            $customerName = $order->customer ? $order->customer->f_name. ' '. $order->customer->l_name : '';

            $value = Helpers::text_variable_data_format(value:$message, user_name: $customerName, restaurant_name: $restaurantName, order_id: $order->id);

            try {
                if ($value) {
                    $customerFcmToken = null;
                    $customerFcmToken = $customer?->cm_firebase_token;

                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order_status',
                    ];
                    Helpers::send_push_notif_to_device($customerFcmToken, $data);
                } else {
                    throw new \Exception('Failed');
                }

            } catch (\Exception $e) {
                // Notification functionality removed
            }
        }

        Toastr::success('Order preparation time updated');
        return back();
    }


    /**
     * @param $order_id
     * @param $delivery_man_id
     * @return JsonResponse
     */
    // Delivery man functionality removed

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function paymentStatus(Request $request)
    {
        $order = $this->order->find($request->order_id ?? $request->id);
        
        if (!$order) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Order not found'], 404);
            }
            Toastr::error('Order not found!');
            return back();
        }
        
        // Handle POST request from modal
        if ($request->isMethod('post')) {
            $paymentAction = $request->payment_action;
            
            if ($paymentAction === 'paid') {
                // Mark as paid
                $order->payment_status = 'paid';
                
                // Update payment method if provided
                if ($request->payment_method) {
                    $order->payment_method = $request->payment_method;
                }
                
                // Update transaction reference if provided
                if ($request->reference_code) {
                    $order->transaction_reference = $request->reference_code;
                }
                
            } elseif ($paymentAction === 'refund') {
                // Mark as refunded
                $order->payment_status = 'refunded';
                
                // Store refund reason if provided
                if ($request->refund_reason) {
                    $order->refund_reason = $request->refund_reason;
                }
            }
            
            $order->save();
            
            if ($request->ajax()) {
                return response()->json(['message' => 'Payment status updated successfully']);
            }
            
            Toastr::success('Payment status updated!');
            return back();
        }
        
        // Handle GET request (legacy support)
        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null &&  $order['order_type'] != 'dine_in' && !in_array($order['payment_method'], ['cash_on_delivery', 'wallet_payment', 'offline_payment', 'cash'])) {
            Toastr::warning('Add your payment reference code first!');
            return back();
        }
        $order->payment_status = $request->payment_status;
        $order->save();

        Toastr::success('Payment status updated!');
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateShipping(Request $request): RedirectResponse
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required|min:5|max:20',
            'address' => 'required'
        ]);

        $address = [
            'id' => null,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'road' => $request->road,
            'house' => $request->house,
            'floor' => $request->floor,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ];

        $this->order->where('id', $request->input('order_id'))->update(['delivery_address' => json_encode($address)]);

        return back();
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function generateInvoice($id): Renderable
    {
        $order = $this->order->with(['order_partial_payments'])->where('id', $id)->first();
        $address = $order->delivery_address ?? CustomerAddress::find($order->delivery_address_id);
        $order->address = $address;
        return view('admin-views.order.invoice', compact('order'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function addPaymentReferenceCode(Request $request, $id): RedirectResponse
    {
        $this->order->where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success('Payment reference code is added!');
        return back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function branchFilter($id): RedirectResponse
    {
        session()->put('branch_filter', $id);
        return back();
    }


    /**
     * @return string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportData(): StreamedResponse|string
    {
        $orders = $this->order->all();
        return (new FastExcel($orders))->download('orders.xlsx');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportExcel(Request $request): StreamedResponse|string|RedirectResponse
    {
        $status = $request->status;
        $queryParam = [];
        $search = $request['search'];
        $from = $request['from'];
        $to = $request['to'];
        $branchId = $request['branch_id'];


        $query = $this->order->newQuery();

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_reference', 'like', "%{$value}%");
                }
            });
            $queryParam['search'] = $search;
        }

        if ($branchId && $branchId != 0) {
            $query->where('branch_id', $branchId);
            $queryParam['branch_id'] = $branchId;
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            $queryParam['from'] = $from;
            $queryParam['to'] = $to;
        }

        if ($status == 'schedule') {
            $query->with(['customer', 'branch'])->schedule();
        } elseif ($status != 'all') {
            $query->with(['customer', 'branch'])->where('order_status', $status)->notSchedule();
        } else {
            $query->with(['customer', 'branch']);
        }

        $orders = $query->notPos()->notDineIn()->latest()->get();
        if ($orders->count() < 1) {
            Toastr::warning('No Data Available');
            return back();
        }

        $data = array();
        foreach ($orders as $key => $order) {
            $data[] = array(
                'SL' => ++$key,
                'Order ID' => $order->id,
                'Order Date' => date('d M Y h:m A', strtotime($order['created_at'])),
                'Customer Info' => $order['user_id'] == null ? 'Walk in Customer' : ($order->customer == null ? 'Customer Unavailable' : $order->customer['f_name'] . ' ' . $order->customer['l_name']),
                'Branch' => $order->branch ? $order->branch->name : 'Branch Deleted',
                'Total Amount' => Helpers::set_symbol($order['order_amount']),
                'Payment Status' => $order->payment_status == 'paid' ? 'Paid' : 'Unpaid',
                'Order Status' => $order['order_status'] == 'pending' ? 'Pending' : ($order['order_status'] == 'confirmed' ? 'Confirmed' : ($order['order_status'] == 'processing' ? 'Processing' : ($order['order_status'] == 'delivered' ? 'Delivered' : ($order['order_status'] == 'picked_up' ? 'Out For Delivery' : str_replace('_', ' ', $order['order_status']))))),
            );
        }

        return (new FastExcel($data))->download('Order_List.xlsx');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxChangeDeliveryTimeAndDate(Request $request): JsonResponse
    {
        $order = $this->order->where('id', $request->order_id)->first();
        if (!$order) {
            return response()->json(['status' => false]);
        }
        $order->delivery_date = $request->input('delivery_date') ?? $order->delivery_date;
        $order->delivery_time = $request->input('delivery_time') ?? $order->delivery_time;
        $order->save();

        return response()->json(['status' => true]);
    }

    /**
     * @param $order_id
     * @param $status
     * @return JsonResponse
     */
    // Offline payment functionality removed

    public function updateOrderDeliveryArea(Request $request, $order_id)
    {
        $request->validate([
            'selected_area_id' => 'required'
        ]);

        $order = $this->order->find($order_id);
        if (!$order){
            Toastr::warning('Order not found');
            return back();
        }

        if ($order->order_status == 'delivered') {
            Toastr::warning('You can not change the area once the order status is delivered');
            return back();
        }

        $branch = Branch::with(['delivery_charge_setup', 'delivery_charge_by_area'])
            ->where(['id' => $order['branch_id']])
            ->first(['id', 'name', 'status']);

        if ($branch->delivery_charge_setup->delivery_charge_type != 'area') {
            Toastr::warning('This branch selected delivery type is not area');
            return back();
        }

        $area = DeliveryChargeByArea::where(['id' => $request['selected_area_id'], 'branch_id' => $order->branch_id])->first();
        if (!$area){
            Toastr::warning('Area not found');
            return back();
        }

        $order->delivery_charge = $area->delivery_charge;
        $order->save();

        $orderArea = $this->orderArea->firstOrNew(['order_id' => $order_id]);
        $orderArea->area_id = $request->selected_area_id;
        $orderArea->save();

        $customerFcmToken = null;
        if($order->is_guest == 0){
            $customerFcmToken = $order->customer ? $order->customer->cm_firebase_token : null;
        }elseif($order->is_guest == 1){
            $customerFcmToken = $order->guest ? $order->guest->fcm_token : null;
        }

        try {
            if ($customerFcmToken != null) {
                $data = [
                    'title' => 'Order',
                    'description' => 'Order delivery area updated',
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order_status',
                ];
                Helpers::send_push_notif_to_device($customerFcmToken, $data);
            }
        } catch (\Exception $e) {
            //
        }

        Toastr::success('Order delivery area updated successfully.');
        return back();
    }

}
