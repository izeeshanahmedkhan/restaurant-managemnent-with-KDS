@extends('layouts.branch.app')

@section('title', translate('Order Details'))

@section('content')
    <div class="content container-fluid">
        <!-- Modern Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                            <i class="tio-shopping-cart"></i>
        </div>
                                <div>
                            <h1 class="h3 mb-1 text-dark">{{translate('Order_Details')}}</h1>
                            <p class="text-muted mb-0">Order #{{$order['id']}} • {{$order->details->count()}} items</p>
                                            </div>
                                                </div>
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-primary" href={{route('branch.orders.generate-invoice',[$order['id']])}}>
                            <i class="tio-print"></i> {{translate('Print_Invoice')}}
                        </a>
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="tio-download"></i> Print
                        </button>
                                    </div>
                                    </div>
                                </div>
                                </div>

        <div class="row" id="printableArea">
            <!-- Main Content Area -->
            <div class="col-lg-8 mb-4">
                <!-- Order Status Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                    <i class="tio-info-circle text-primary"></i>
                                    Order Information
                                </h5>
                                                </div>
                            <div class="col-md-6 text-md-end">
                                        @if($order['order_status']=='pending')
                                    <span class="badge bg-warning text-dark px-3 py-2">{{translate('pending')}}</span>
                                        @elseif($order['order_status']=='confirmed')
                                    <span class="badge bg-info text-white px-3 py-2">{{translate('confirmed')}}</span>
                                        @elseif($order['order_status']=='processing')
                                    <span class="badge bg-warning text-dark px-3 py-2">{{translate('processing')}}</span>
                                        @elseif($order['order_status']=='out_for_delivery')
                                    <span class="badge bg-warning text-dark px-3 py-2">{{translate('out_for_delivery')}}</span>
                                        @elseif($order['order_status']=='delivered')
                                    <span class="badge bg-success text-white px-3 py-2">{{translate('delivered')}}</span>
                                        @elseif($order['order_status']=='failed')
                                    <span class="badge bg-danger text-white px-3 py-2">{{translate('failed_to_deliver')}}</span>
                                        @else
                                    <span class="badge bg-secondary text-white px-3 py-2">{{str_replace('_',' ',$order['order_status'])}}</span>
                                        @endif
                                    </div>
                                    </div>
                                            </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Order ID</h6>
                                    <p class="mb-0 fw-bold">#{{$order['id']}}</p>
                                            </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Branch</h6>
                                    <span class="badge bg-light text-dark px-3 py-2">
                                        <i class="tio-shop me-1"></i>
                                        {{$order->branch?$order->branch->name:'Branch deleted!'}}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Order Type</h6>
                                    <span class="badge bg-info text-white px-3 py-2 text-capitalize">
                                        {{str_replace('_',' ',$order['order_type'])}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Order Date & Time</h6>
                                    <p class="mb-0">
                                        <i class="tio-date-range me-1"></i>
                                        {{date('d M Y',strtotime($order['created_at']))}} at {{ date(config('time_format'), strtotime($order['created_at'])) }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Payment Method</h6>
                                    <p class="mb-0 text-capitalize">{{str_replace('_',' ',$order['payment_method'])}}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Payment Status</h6>
                                        @if($order['payment_status']=='paid')
                                        <span class="badge bg-success text-white px-3 py-2">{{translate('paid')}}</span>
                                        @elseif($order['payment_status']=='partial_paid')
                                        <span class="badge bg-warning text-dark px-3 py-2">{{translate('partial_paid')}}</span>
                                        @else
                                        <span class="badge bg-danger text-white px-3 py-2">{{translate('unpaid')}}</span>
                                        @endif
                                </div>
                            </div>
                                    </div>

                        @if($order['order_type'] == 'dine_in')
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="tio-table"></i>
                                            <span><strong>Table:</strong> {{$order->table?$order->table->number:'Table deleted!'}}</span>
                                            @if($order['number_of_people'] != null)
                                                <span class="ms-3"><strong>People:</strong> {{$order->number_of_people}}</span>
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($order['order_note'])
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-light">
                                        <h6 class="text-muted mb-1">Order Note</h6>
                                        <p class="mb-0">{{$order['order_note']}}</p>
                    </div>
                                </div>
                            </div>
                        @endif

                        @if($order['bring_change_amount'])
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <h6 class="text-muted mb-1">Change Required</h6>
                                        <p class="mb-0">{{translate('Please ensure you have '). \App\CentralLogics\Helpers::set_symbol($order['bring_change_amount']) . translate(' in change ready for the customer')}}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(!in_array($order['payment_method'], ['cash_on_delivery']))
                            @if($order['transaction_reference']==null && $order['order_type']!='pos' && $order['order_type'] != 'dine_in')
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted">Reference Code:</span>
                                            <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target=".bd-example-modal-sm">
                                                {{translate('add')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @elseif($order['order_type']!='pos' && $order['order_type'] != 'dine_in')
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted">Reference Code:</span>
                                            <span class="fw-bold">{{$order['transaction_reference']}}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Order Items Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="tio-shopping-basket text-primary"></i>
                            Order Items ({{$order->details->count()}})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                            @php($sub_total=0)
                            @php($total_tax=0)
                            @php($total_dis_on_pro=0)
                            @php($add_ons_cost=0)
                            @php($add_on_tax=0)
                            @php($add_ons_tax_cost=0)
                            @foreach($order->details as $detail)
                                @php($product_details = json_decode($detail['product_details'], true))
                                @php($add_on_qtys=json_decode($detail['add_on_qtys'],true))
                                @php($add_on_prices=json_decode($detail['add_on_prices'],true))
                                @php($add_on_taxes=json_decode($detail['add_on_taxes'],true))

                            <div class="border-bottom p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-1">
                                        <span class="badge bg-light text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                            {{ $loop->iteration }}
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;"
                                                 src="{{ $detail->product?->imageFullPath ?? asset('assets/admin/img/160x160/img2.jpg') }}"
                                                 alt="Product Image">
                                            <div>
                                                <h6 class="mb-1 text-capitalize">{{$product_details['name']}}</h6>
                                                <small class="text-muted">Qty: {{$detail['quantity']}}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <h6 class="mb-0">Price</h6>
                                            <span class="text-muted">{{ \App\CentralLogics\Helpers::set_symbol($detail['price']) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <h6 class="mb-0">Discount</h6>
                                            <span class="text-success">{{ \App\CentralLogics\Helpers::set_symbol($detail['discount_on_product']) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <h6 class="mb-0">Tax</h6>
                                            <span class="text-info">{{ \App\CentralLogics\Helpers::set_symbol($detail['tax_amount']) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="text-end">
                                            <h6 class="mb-0 fw-bold">{{ \App\CentralLogics\Helpers::set_symbol($detail['price'] * $detail['quantity']) }}</h6>
                                        </div>
                                    </div>
                                </div>
                                
                                                    @if (isset($detail['variation']))
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach(json_decode($detail['variation'],true) as $variation)
                                                    @if (isset($variation['name']) && isset($variation['values']))
                                                                @foreach ($variation['values'] as $value)
                                                            <span class="badge bg-light text-dark">
                                                                <strong>{{ $variation['name'] }}:</strong> {{ $value['label'] }} (+{{\App\CentralLogics\Helpers::set_symbol($value['optionPrice'])}})
                                                                </span>
                                                                @endforeach
                                                            @else
                                                                @if (isset(json_decode($detail['variation'],true)[0]))
                                                                    @foreach(json_decode($detail['variation'],true)[0] as $key1 =>$variation)
                                                                <span class="badge bg-light text-dark">
                                                                    <strong>{{$key1}}:</strong> {{$variation}}
                                                                </span>
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                        </div>
                                                    </div>
                                    </div>
                                @endif

                                                    @php($addon_ids = json_decode($detail['add_on_ids'],true))
                                                    @if ($addon_ids)
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="text-muted fw-bold">{{translate('Add-ons')}}:</span>
                                                        @foreach($addon_ids as $key2 =>$id)
                                                                @php($addon=\App\Model\AddOn::find($id))
                                                                @php($add_on_qtys==null? $add_on_qty=1 : $add_on_qty=$add_on_qtys[$key2])
                                                    <span class="badge bg-info text-white">
                                                        {{$addon ? $addon['name'] : translate('addon deleted')}} ({{$add_on_qty}}x {{ \App\CentralLogics\Helpers::set_symbol($add_on_prices[$key2]) }})
                                                                    </span>
                                                                @php($add_ons_cost+=$add_on_prices[$key2] * $add_on_qty)
                                                                @php($add_ons_tax_cost +=  $add_on_taxes[$key2] * $add_on_qty)
                                                            @endforeach
                                                </div>
                                            </div>
                                        </div>
                                @endif
                            </div>
                            
                                        @php($amount=$detail['price']*$detail['quantity'])
                                        @php($tot_discount = $detail['discount_on_product']*$detail['quantity'])
                                        @php($product_tax = $detail['tax_amount']*$detail['quantity'])
                                @php($total_dis_on_pro += $tot_discount)
                                @php($sub_total += $amount)
                                @php($total_tax += $product_tax)
                            @endforeach
                    </div>
                    </div>


                <!-- Order Summary Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                            <i class="tio-receipt text-primary"></i>
                            Order Summary
                        </h5>
                                        </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">{{translate('items')}} {{translate('price')}}:</span>
                                    <span class="fw-bold">{{ \App\CentralLogics\Helpers::set_symbol($sub_total) }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">{{translate('item')}} {{translate('discount')}}:</span>
                                    <span class="text-success">- {{ \App\CentralLogics\Helpers::set_symbol($total_dis_on_pro) }}</span>
                                        </div>
                                
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">{{translate('addon')}} {{translate('cost')}}:</span>
                                    <span class="fw-bold">{{ \App\CentralLogics\Helpers::set_symbol($add_ons_cost) }}</span>
                                        </div>
                                
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">{{translate('extra discount')}}:</span>
                                    <span class="text-success">- {{ \App\CentralLogics\Helpers::set_symbol($order['extra_discount']) }}</span>
                                        </div>
                                
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">{{translate('referral discount')}}:</span>
                                    <span class="text-success">- {{ \App\CentralLogics\Helpers::set_symbol($order['referral_discount']) }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">{{translate('tax')}} / {{translate('vat')}}:</span>
                                    <span class="fw-bold">{{ \App\CentralLogics\Helpers::set_symbol($total_tax+$add_ons_tax_cost) }}</span>
                                        </div>
                                
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">{{translate('subtotal')}}:</span>
                                    <span class="fw-bold">{{ \App\CentralLogics\Helpers::set_symbol($sub_total =$sub_total+$total_tax+$add_ons_cost-$total_dis_on_pro+$add_ons_tax_cost- $order['extra_discount'] - $order['referral_discount']) }}</span>
                                        </div>
                                
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted">{{translate('delivery')}} {{translate('fee')}}:</span>
                                    <span class="fw-bold">
                                        @if($order['order_type']=='take_away')
                                            @php($del_c=0)
                                        @else
                                            @php($del_c=$order['delivery_charge'])
                                        @endif
                                        {{ \App\CentralLogics\Helpers::set_symbol($del_c) }}
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center py-3 border-top border-2">
                                    <span class="h5 mb-0 text-dark">{{translate('total')}}:</span>
                                    <span class="h5 mb-0 text-primary fw-bold">{{ \App\CentralLogics\Helpers::set_symbol($sub_total + $del_c) }}</span>
                                </div>

                                @if ($order->order_partial_payments->isNotEmpty())
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <h6 class="text-muted mb-2">Partial Payments</h6>
                                        @foreach($order->order_partial_payments as $partial)
                                            <div class="d-flex justify-content-between align-items-center py-1">
                                                <span class="text-muted">{{translate('Paid By')}} ({{str_replace('_', ' ',$partial->paid_with)}}):</span>
                                                <span class="fw-bold">{{ \App\CentralLogics\Helpers::set_symbol($partial->paid_amount) }}</span>
                                            </div>
                                        @endforeach
                                        @php($due_amount = $order->order_partial_payments->first()?->due_amount)
                                        <div class="d-flex justify-content-between align-items-center py-1 border-top mt-2 pt-2">
                                            <span class="text-muted">{{translate('Due Amount')}}:</span>
                                            <span class="fw-bold text-danger">{{ \App\CentralLogics\Helpers::set_symbol($due_amount) }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($order->order_change_amount()->exists())
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <h6 class="text-muted mb-2">Payment Details</h6>
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-muted">{{ translate('paid_amount') }}:</span>
                                            <span class="fw-bold">{{ Helpers::set_symbol($order->order_change_amount?->paid_amount) }}</span>
                                        </div>
                                        @php($changeOrDueAmount = $order->order_change_amount?->paid_amount - $order->order_change_amount?->order_amount)
                                        <div class="d-flex justify-content-between align-items-center py-1">
                                            <span class="text-muted">{{$changeOrDueAmount < 0 ? translate('due_amount') : translate('change_amount') }}:</span>
                                            <span class="fw-bold {{$changeOrDueAmount < 0 ? 'text-danger' : 'text-success'}}">{{ Helpers::set_symbol($changeOrDueAmount) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @if($order['order_type'] != 'pos')
                    <div class="card mb-3">
                        <div class="card-body text-capitalize d-flex flex-column gap-4">
                            <h4 class="mb-0 text-center">{{translate('Order_Setup')}}</h4>
                            <!-- Offline payment functionality removed -->

                            @if($order['order_type'] != 'pos')

                                <div class="hs-unfold w-100">
                                    <label class="font-weight-bold text-dark fz-14">{{translate('Change_Order_Status')}}</label>
                                    <div class="dropdown">
                                        <button class="form-control h--45px dropdown-toggle d-flex justify-content-between align-items-center w-100" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            {{ translate($order['order_status'])}}
                                        </button>
                                        <div class="dropdown-menu text-capitalize" aria-labelledby="dropdownMenuButton">
                                            <!-- Offline payment functionality removed -->

                                                <a class="dropdown-item route-alert" href="javascript:">{{translate('confirmed')}}</a>

                                                @if($order['order_type'] != 'dine_in')
                                                    <a class="dropdown-item route-alert" href="javascript:">{{translate('processing')}}</a>
                                                    <a class="dropdown-item route-alert" href="javascript:">{{translate('out_for_delivery')}}</a>
                                                    <a class="dropdown-item route-alert" href="javascript:">{{translate('delivered')}}</a>
                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'returned'])}}"
                                                       data-message="{{ translate("Change status to returned ?") }}"
                                                       href="javascript:">{{translate('returned')}}</a>
                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'failed'])}}"
                                                       data-message="{{ translate("Change status to failed ?") }}"
                                                       href="javascript:">{{translate('failed')}}</a>
                                                @endif

                                                @if($order['order_type'] == 'dine_in')
                                                    <a class="dropdown-item route-alert" href="javascript:">{{translate('cooking')}}</a>
                                                    <a class="dropdown-item route-alert" href="javascript:">{{translate('completed')}}</a>
                                                @endif
                                                <a class="dropdown-item route-alert"
                                                   data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'canceled'])}}"
                                                   data-message="{{ translate("Change status to canceled ?") }}"
                                                   href="javascript:">{{translate('canceled')}}</a>
                                            @else

                                                @if($order['order_type'] != 'dine_in')
                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'pending'])}}"
                                                       data-message="{{ translate("Change status to pending ?") }}"
                                                       href="javascript:">{{translate('pending')}}</a>
                                                @endif

                                                <a class="dropdown-item route-alert"
                                                   data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'confirmed'])}}"
                                                   data-message="{{ translate("Change status to confirmed ?") }}"
                                                   href="javascript:">{{translate('confirmed')}}</a>

                                                @if($order['order_type'] != 'dine_in')
                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'processing'])}}"
                                                       data-message="{{ translate("Change status to processing ?") }}"
                                                       href="javascript:">{{translate('processing')}}</a>
                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'out_for_delivery'])}}"
                                                       data-message="{{ translate("Change status to out for delivery ?") }}"
                                                       href="javascript:">{{translate('out_for_delivery')}}</a>
                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'delivered'])}}"
                                                       data-message="{{ translate("Change status to delivered ?") }}"
                                                       href="javascript:">{{translate('delivered')}}</a>
                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'returned'])}}"
                                                       data-message="{{ translate("Change status to returned ?") }}"
                                                       href="javascript:">{{translate('returned')}}</a>
                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'failed'])}}"
                                                       data-message="{{ translate("Change status to failed ?") }}"
                                                       href="javascript:">{{translate('failed')}}</a>
                                                @endif
                                                @if($order['order_type'] == 'dine_in')
                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'cooking'])}}"
                                                       data-message="{{ translate("Change status to cooking ?") }}"
                                                       href="javascript:">{{translate('cooking')}}</a>

                                                    <a class="dropdown-item route-alert"
                                                       data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'completed'])}}"
                                                       data-message="{{ translate("Change status to completed ?") }}"
                                                       href="javascript:">{{translate('completed')}}</a>
                                                @endif

                                                <a class="dropdown-item route-alert"
                                                   data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'canceled'])}}"
                                                   data-message="{{ translate("Change status to canceled ?") }}"
                                                   href="javascript:">{{translate('canceled')}}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="d-flex justify-content-between align-items-center gap-10 form-control">
                                        <span class="title-color">{{ translate('Payment Status') }}</span>
                                        <!-- Offline payment functionality removed -->
                                            <label class="switcher payment-status-text">
                                                <input class="switcher_input change-payment-status" type="checkbox" name="payment_status" value="1"
                                                       data-id="{{ $order['id'] }}"
                                                       data-status="{{ $order->payment_status == 'paid' ?'unpaid':'paid' }}"
                                                    {{$order->payment_status == 'paid' ?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                    </div>

                                </div>
                            @endif
                            @if($order->customer || $order->is_guest == 1)
                                <div>
                                    <label class="font-weight-bold text-dark fz-14">{{translate('Delivery_Date_&_Time')}} {{$order['delivery_date'] > \Carbon\Carbon::now()->format('Y-m-d')? translate('(Scheduled)') : ''}}</label>
                                    <div class="d-flex gap-2 flex-wrap flex-xxl-nowrap">
                                        <input onchange="changeDeliveryTimeDate(this)" name="delivery_date" type="date" class="form-control" value="{{$order['delivery_date'] ?? ''}}">
                                        <input onchange="changeDeliveryTimeDate(this)" name="delivery_time" type="time" class="form-control" value="{{$order['delivery_time'] ?? ''}}">
                                    </div>
                                </div>
                                {{-- Delivery man functionality removed --}}
                            @endif
                            <div>
                                @if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && !in_array($order['order_status'], ['delivered', 'returned', 'canceled', 'failed', 'completed']))
                                    <label class="font-weight-bold text-dark fz-14">{{translate('Food_Preparation_Time')}}</label>
                                    <div class="form-control justify-content-between">
                                        <span class="ml-2 ml-sm-3 ">
                                        <i class="tio-timer d-none" id="timer-icon"></i>
                                        <span id="counter" class="text-info"></span>
                                        <i class="tio-edit p-2 d-none cursor-pointer" id="edit-icon" data-toggle="modal" data-target="#counter-change" data-whatever="@mdo"></i>
                                        </span>
                                    </div>
                                @endif
                            </div>
                            {{-- Delivery man functionality removed --}}

                            @if($order['order_type']!='take_away' && $order['order_type'] != 'pos' && $order['order_type'] != 'dine_in')
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-4 d-flex gap-2 justify-content-between">
                                            <h4 class="mb-0 d-flex gap-2">
                                                <i class="tio-user text-dark"></i>
                                                {{translate('Delivery_Informatrion')}}
                                            </h4>

                                            <div class="edit-btn cursor-pointer" data-toggle="modal" data-target="#deliveryInfoModal">
                                                <i class="tio-edit"></i>
                                            </div>
                                        </div>
                                        <div class="delivery--information-single flex-column">
                                            @php($address = $order->address)
                                            <div class="d-flex">
                                                <div class="name">{{ translate('Name') }}</div>
                                                <div class="info">{{ $address? $address['contact_person_name']: '' }}</div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="name">{{translate('Contact')}}</div>
                                                <a href="tel:{{ $address? $address['contact_person_number']: '' }}" class="info">{{ $address? $address['contact_person_number']: '' }}</a>
                                            </div>
                                            <div class="d-flex">
                                                <div class="name">{{translate('floor')}}</div>
                                                <div class="info">{{$address['floor'] ?? ''}}</div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="name">{{translate('house')}}</div>
                                                <div class="info">{{$address['house'] ?? ''}}</div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="name">{{translate('address')}}</div>
                                                <div class="info">{{$address['address'] ?? ''}}</div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="name">{{translate('road')}}</div>
                                                <div class="info">{{$address['road'] ?? ''}}</div>
                                            </div>
                                            @if($order->order_area)
                                                <div class="d-flex">
                                                    <div class="name">{{translate('Area')}}</div>
                                                    <div class="info edit-btn cursor-pointer">
                                                        {{ $order?->order_area?->area?->area_name }}
                                                        @if($order?->branch?->delivery_charge_setup?->delivery_charge_type == 'area')
                                                            <i class="tio-edit" data-toggle="modal" data-target="#editArea"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                                            @if($googleMapStatus)
                                                @if(isset($address['address']) && isset($address['latitude']) && isset($address['longitude']))
                                                    <hr class="w-100">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <a target="_blank" class="text-dark"
                                                           href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                                            <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            {{$address['address']}}
                                                        </a>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($order['order_type']=='take_away' && $order['order_type'] != 'pos' && $order['order_type'] != 'dine_in')
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-4 d-flex gap-2 justify-content-between">
                                            <h4 class="mb-0 d-flex gap-2">
                                                <i class="tio-user text-dark"></i>
                                                {{translate('Contact_Information')}}
                                            </h4>
                                        </div>
                                        <div class="delivery--information-single flex-column">
                                            @php($address = $order->address)
                                            <div class="d-flex">
                                                <div class="name">{{ translate('Name') }}</div>
                                                <div class="info">{{ $address? $address['contact_person_name']: '' }}</div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="name">{{translate('Contact')}}</div>
                                                <a href="tel:{{ $address? $address['contact_person_number']: '' }}" class="info">{{ $address? $address['contact_person_number']: '' }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif


                        </div>
                    </div>

                    <!-- Offline payment functionality removed -->


                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="mb-4 d-flex gap-2">
                                <i class="tio-user text-dark"></i>
                                {{ translate('Customer Information') }}
                            </h4>
                            @if($order->is_guest == 1)
                                <div class="media flex-wrap gap-3 align-items-center">
                                    <a target="#" >
                                        <img class="avatar avatar-lg rounded-circle" src="{{asset('assets/admin/img/160x160/img1.jpg')}}" alt="Image">
                                    </a>
                                    <div class="media-body d-flex flex-column gap-1">
                                        <a target="#"  class="text-dark text-capitalize"><strong>{{translate('Guest Customer')}}</strong></a>
                                    </div>
                                </div>
                            @else
                                @if($order->customer)
                                    <div class="media flex-wrap gap-3">
                                        <a>
                                            <img class="avatar avatar-lg rounded-circle" src="{{$order->customer['imageFullPath']}}" alt="Image">
                                        </a>
                                        <div class="media-body d-flex flex-column gap-1">
                                            <a><strong>{{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong></a>
                                            <span class="text-dark">{{$order->customer['orders_count']}} {{translate('Orders')}}</span>
                                            <span class="text-dark">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <a class="text-dark break-all" href="tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                                        </span>
                                            <span class="text-dark">
                                            <i class="tio-email mr-2"></i>
                                            <a class="text-dark break-all" href="mailto:{{$order->customer['email']}}">{{$order->customer['email']}}</a>
                                        </span>
                                        </div>
                                    </div>
                                @endif
                                @if($order->user_id == null)
                                    <div class="media flex-wrap gap-3 align-items-center">
                                        <a target="#" >
                                            <img class="avatar avatar-lg rounded-circle" src="{{asset('assets/admin/img/160x160/img1.jpg')}}" alt="Image">
                                        </a>
                                        <div class="media-body d-flex flex-column gap-1">
                                            <a target="#"  class="text-dark text-capitalize"><strong>{{translate('walking_customer')}}</strong></a>
                                        </div>
                                    </div>
                                @endif
                                @if($order->user_id != null && !isset($order->customer))
                                    <div class="media flex-wrap gap-3 align-items-center">
                                        <a target="#" >
                                            <img class="avatar avatar-lg rounded-circle" src="{{asset('assets/admin/img/160x160/img1.jpg')}}" alt="Image">
                                        </a>
                                        <div class="media-body d-flex flex-column gap-1">
                                            <a target="#"  class="text-dark text-capitalize"><strong>{{translate('Customer_not_available')}}</strong></a>
                                        </div>
                                    </div>
                                @endif

                            @endif
                        </div>
                    </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="mb-4 d-flex gap-2">
                            <i class="tio-user text-dark"></i>
                            {{translate('Branch Information')}}
                        </h4>
                        <div class="media flex-wrap gap-3">
                            <div>
                                <img class="avatar avatar-lg rounded-circle" src="{{$order->branch?->imageFullPath}}" alt="Image">
                            </div>
                            <div class="media-body d-flex flex-column gap-1">
                                @if(isset($order->branch))
                                    <span class="text-dark"><span>{{$order->branch['name']}}</span></span>
                                    <span class="text-dark"> <span>{{$order->branch['orders_count']}}</span> {{translate('Orders served')}}</span>
                                    @if($order->branch['phone'])
                                        <span class="text-dark break-all">
                                        <i class="tio-call-talking-quiet mr-2"></i>
                                        <a class="text-dark" href="tel:+{{$order->branch['phone']}}">{{$order->branch['phone']}}</a>
                                    </span>
                                    @endif
                                    <span class="text-dark break-all">
                                        <i class="tio-email mr-2"></i>
                                        <a class="text-dark" href="mailto:{{$order->branch['email']}}">{{$order->branch['email']}}</a>
                                    </span>
                                @else
                                    <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                        {{translate('Branch Deleted')}}
                                    </span>
                                @endif

                            </div>
                        </div>
                        @if(isset($order->branch))
                            <hr class="w-100">
                            <div class="d-flex align-items-center text-dark gap-3">
                                <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">
                                <a target="_blank" class="text-dark"
                                   href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$order->branch['latitude']}}+{{$order->branch['longitude']}}">
                                    {{$order->branch['address']}}<br>
                                </a>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Delivery man functionality removed --}}


    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4"
                        id="mySmallModalLabel">{{translate('reference')}} {{translate('code')}} {{translate('add')}}</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                            aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>

                <form action="{{route('branch.orders.add-payment-ref-code',[$order['id']])}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" name="transaction_reference" class="form-control"
                                   placeholder="{{translate('EX : Code123')}}" required>
                        </div>
                        <button class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deliveryInfoModal" id="deliveryInfoModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">{{translate('Update_Delivery_Informatrion')}}</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>
                <form action="{{route('branch.order.update-shipping')}}" method="post">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$order->user_id}}">
                    <input type="hidden" name="order_id" value="{{$order->id}}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>{{translate('Type')}}</label>
                            <input type="text" name="address_type" class="form-control"
                                   placeholder="{{translate('EX : Home')}}" value="{{ $address['address_type'] ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{translate('Name')}}</label>
                            <input type="text" class="form-control" name="contact_person_name"
                                   placeholder="{{translate('EX : Jhon Doe')}}" value="{{ $address['contact_person_name'] ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{translate('Contact_Number')}}</label>
                            <input type="text" class="form-control" name="contact_person_number"
                                   placeholder="{{translate('EX : 01888888888')}}" value="{{ $address['contact_person_number']?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{translate('floor')}}</label>
                            <input type="text" class="form-control" name="floor"
                                   placeholder="{{translate('EX : 5')}}" value="{{ $address['floor'] ?? '' }}" >
                        </div>
                        <div class="form-group">
                            <label>{{translate('house')}}</label>
                            <input type="text" class="form-control" name="house"
                                   placeholder="{{translate('EX : 21/B')}}" value="{{ $address['house'] ?? '' }}" >
                        </div>
                        <div class="form-group">
                            <label>{{translate('road')}}</label>
                            <input type="text" class="form-control" name="road"
                                   placeholder="{{translate('EX : Baker Street')}}" value="{{ $address['road'] ?? '' }}" >
                        </div>
                        <div class="form-group">
                            <label>{{translate('Address')}}</label>
                            <input type="text" class="form-control" name="address"
                                   placeholder="{{translate('EX : Dhaka,_Bangladesh')}}" value="{{ $address['address'] ?? '' }}" required>
                        </div>
                        @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                        @if($googleMapStatus)
                            @if($order?->branch?->delivery_charge_setup?->delivery_charge_type == 'distance')
                                <div class="form-group">
                                    <label>{{translate('latitude')}}</label>
                                    <input type="text" class="form-control" name="latitude"
                                           placeholder="{{translate('EX : 23.796584198263794')}}" value="{{ $address['latitude'] ?? '' }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{translate('longitude')}}</label>
                                    <input type="text" class="form-control" name="longitude"
                                           placeholder="{{translate('EX : 23.796584198263794')}}" value="{{ $address['longitude'] ?? '' }}" required>
                                </div>
                            @endif
                        @endif
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && !in_array($order['order_status'], ['delivered', 'returned', 'canceled', 'failed', 'completed']))
        <div class="modal fade" id="counter-change" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ translate('Need time to prepare the food') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('branch.orders.increase-preparation-time', ['id' => $order->id])}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group text-center">
                                <input type="number" min="0" name="extra_minute" id="extra_minute" class="form-control" placeholder="{{translate('EX : 20')}}" required>
                            </div>

                            <div class="form-group flex-between">
                                <div class="badge text-info shadow cursor-pointer change-food-preparation-time" data-minute="10">{{ translate('10min') }}</div>
                                <div class="badge text-info shadow cursor-pointer change-food-preparation-time" data-minute="20">{{ translate('20min') }}</div>
                                <div class="badge text-info shadow cursor-pointer change-food-preparation-time" data-minute="30">{{ translate('30min') }}</div>
                                <div class="badge text-info shadow cursor-pointer change-food-preparation-time" data-minute="40">{{ translate('40min') }}</div>
                                <div class="badge text-info shadow cursor-pointer change-food-preparation-time" data-minute="50">{{ translate('50min') }}</div>
                                <div class="badge text-info shadow cursor-pointer change-food-preparation-time" data-minute="60">{{ translate('60min') }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ translate('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Offline payment modal functionality removed -->

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editArea" id="editArea"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">{{translate('Update_Delivery_Area')}}</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>
                <form action="{{ route('branch.orders.update-order-delivery-area', ['order_id' => $order->id]) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <?php
                            $branch = \App\Model\Branch::with(['delivery_charge_setup', 'delivery_charge_by_area'])
                                ->where(['id' => $order['branch_id']])
                                ->first(['id', 'name', 'status']);
                            ?>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{translate('Delivery Area')}}</label>
                                    <select name="selected_area_id" class="form-control js-select2-custom-x mx-1" id="areaDropdown" >
                                        <option value="">{{ translate('Select Area') }}</option>
                                        @foreach($branch->delivery_charge_by_area as $area)
                                            <option value="{{$area['id']}}" {{ (isset($order->order_area) && $order->order_area->area_id == $area['id']) ? 'selected' : '' }}
                                            data-charge="{{$area['delivery_charge']}}" >{{ $area['area_name'] }} - ({{ Helpers::set_symbol($area['delivery_charge']) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="input-label" for="">{{ translate('Delivery Charge') }} ({{ Helpers::currency_symbol() }})</label>
                                <input type="number" class="form-control" name="delivery_charge" id="deliveryChargeInput" value="" readonly>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary">{{translate('update')}}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        // Delivery man functionality removed

        $('.last-location-view').on('click', function() {
            toastr.warning('{{ translate("Only available when order is out for delivery!") }}', {
                CloseButton: true,
                ProgressBar: true
            });
        })

        $('.change-food-preparation-time').on('click', function (){
            let min = $(this).data('minute');
            document.getElementById("extra_minute").value = min;

        });

    </script>
    @if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && !in_array($order['order_status'], ['delivered', 'returned', 'canceled', 'failed', 'completed']))
        <script>
            const expire_time = "{{ $order['remaining_time'] }}";
            var countDownDate = new Date(expire_time).getTime();
            const time_zone = "{{ \App\CentralLogics\Helpers::get_business_settings('time_zone') ?? 'UTC' }}";

            var x = setInterval(function() {
                var now = new Date(new Date().toLocaleString("en-US", {timeZone: time_zone})).getTime();

                var distance = countDownDate - now;

                var days = Math.trunc(distance / (1000 * 60 * 60 * 24));
                var hours = Math.trunc((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.trunc((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.trunc((distance % (1000 * 60)) / 1000);


                document.getElementById("timer-icon").classList.remove("d-none");
                document.getElementById("edit-icon").classList.remove("d-none");
                var $text = (distance < 0) ? "{{ translate('over') }}" : "{{ translate('left') }}";
                document.getElementById("counter").innerHTML = Math.abs(days) + "d " + Math.abs(hours) + "h " + Math.abs(minutes) + "m " + Math.abs(seconds) + "s " + $text;
                if (distance < 0) {
                    var element = document.getElementById('counter');
                    element.classList.add('text-danger');
                }
            }, 1000);
        </script>
    @endif

    <script>
        function changeDeliveryTimeDate(t) {
            let name = t.name
            let value = t.value
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/branch/orders/ajax-change-delivery-time-date/{{$order['id']}}?' + t.name + '=' + t.value,
                data: {
                    name : name,
                    value : value
                },
                success: function (data) {
                    console.log(data)
                    if(data.status == true && name == 'delivery_date') {
                        toastr.success('{{translate("Delivery date changed successfully")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else if(data.status == true && name == 'delivery_time'){
                        toastr.success('{{translate("Delivery time changed successfully")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else {
                        toastr.error('{{translate("Order No is not valid")}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function () {
                    toastr.error('{{translate("Add valid data")}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        // Offline payment verification functionality removed

        // Offline payment functionality removed

        $('.change-payment-status').on('click', function(){
            let id = $(this).data('id');
            let status = $(this).data('status');
            let paymentStatusRoute = "{{ route('branch.orders.payment-status') }}";
            location.href = paymentStatusRoute + '?id=' + encodeURIComponent(id) + '&payment_status=' + encodeURIComponent(status);
        });

        $(document).ready(function() {
            const $areaDropdown = $('#areaDropdown');
            const $deliveryChargeInput = $('#deliveryChargeInput');

            $areaDropdown.change(function() {
                const selectedOption = $(this).find('option:selected');
                const charge = selectedOption.data('charge');
                $deliveryChargeInput.val(charge);
            });
        });

    </script>
@endpush
