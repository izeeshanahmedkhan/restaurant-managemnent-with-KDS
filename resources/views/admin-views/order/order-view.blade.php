@extends('layouts.admin.app')

@section('title', translate('Order Details'))

@section('content')
    <div class="content container-fluid">
        <!-- Header Section -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="24" class="avatar-img" src="{{asset('assets/admin/img/icons/order_details.png')}}" alt="">
                <span class="page-header-title">{{translate('Order_Details')}}</span>
            </h2>
            <span class="badge badge-soft-dark rounded-50 fz-14">{{$order->details->count()}} {{translate('items')}}</span>
        </div>

        <div class="row" id="printableArea">
            <!-- Main Content Area -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <!-- Order Header Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h3 class="mb-0 text-primary">#{{$order['id']}}</h3>
                                <p class="mb-0 text-muted">{{date('d M Y, H:i', strtotime($order['created_at']))}}</p>
                                            </div>
                            <div class="col-md-6 text-md-right">
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex align-items-center justify-content-md-end gap-2">
                                        <span class="text-muted">{{translate('Status')}}:</span>
                                        @if($order['order_status']=='pending')
                                            <span class="badge badge-soft-info px-3 py-2">{{translate('pending')}}</span>
                                        @elseif($order['order_status']=='confirmed')
                                            <span class="badge badge-soft-info px-3 py-2">{{translate('confirmed')}}</span>
                                        @elseif($order['order_status']=='processing')
                                            <span class="badge badge-soft-warning px-3 py-2">{{translate('processing')}}</span>
                                        @elseif($order['order_status']=='out_for_delivery')
                                            <span class="badge badge-soft-warning px-3 py-2">{{translate('out_for_delivery')}}</span>
                                        @elseif($order['order_status']=='delivered')
                                            <span class="badge badge-soft-success px-3 py-2">{{translate('delivered')}}</span>
                                        @elseif($order['order_status']=='failed')
                                            <span class="badge badge-soft-danger px-3 py-2">{{translate('failed_to_deliver')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger px-3 py-2">{{str_replace('_',' ',$order['order_status'])}}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center justify-content-md-end gap-2">
                                        <span class="text-muted">{{translate('Payment')}}:</span>
                                        @if($order['payment_status']=='paid')
                                            <span class="badge badge-soft-success px-3 py-2">{{translate('paid')}}</span>
                                        @elseif($order['payment_status']=='partial_paid')
                                            <span class="badge badge-soft-success px-3 py-2">{{translate('partial_paid')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger px-3 py-2">{{translate('unpaid')}}</span>
                                        @endif
                                    </div>
                                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="order-info-section">
                                    <h5 class="text-dark mb-3">
                                        <i class="tio-shop text-primary"></i> {{translate('Order Information')}}
                                    </h5>
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <span class="label">{{translate('Branch')}}:</span>
                                            <span class="value badge badge-soft-info">{{$order->branch?$order->branch->name:'Branch deleted!'}}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="label">{{translate('Order Type')}}:</span>
                                            <span class="value badge badge-soft-secondary">{{str_replace('_',' ',$order['order_type'])}}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="label">{{translate('Payment Method')}}:</span>
                                            <span class="value">{{str_replace('_',' ',$order['payment_method'])}}</span>
                                        </div>
                                        @if($order['order_type'] == 'dine_in')
                                            <div class="info-item">
                                                <span class="label">{{translate('Table')}}:</span>
                                                <span class="value badge badge-secondary">{{$order->table?$order->table->number:'Table deleted!'}}</span>
                                            </div>
                                            @if($order['number_of_people'] != null)
                                                <div class="info-item">
                                                    <span class="label">{{translate('People')}}:</span>
                                                    <span class="value badge badge-secondary">{{$order->number_of_people}}</span>
                                            </div>
                                        @endif
                                    @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="order-notes-section">
                                    <h5 class="text-dark mb-3">
                                        <i class="tio-note text-primary"></i> {{translate('Order Notes')}}
                                    </h5>
                                    @if($order['order_note'])
                                        <div class="note-item">
                                            <span class="label">{{translate('Order Note')}}:</span>
                                            <p class="value">{{$order['order_note']}}</p>
                                        </div>
                                        @endif
                                    @if($order['bring_change_amount'])
                                        <div class="note-item">
                                            <span class="label">{{translate('Change Note')}}:</span>
                                            <p class="value text-success">{{translate('Please ensure you have '). \App\CentralLogics\Helpers::set_symbol($order['bring_change_amount']) . translate(' in change ready for the customer')}}</p>
                                    </div>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Items Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="tio-shopping-cart text-primary"></i> {{translate('Order Items')}}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                        <th class="border-0">{{translate('Item')}}</th>
                                        <th class="border-0 text-center">{{translate('Qty')}}</th>
                                        <th class="border-0 text-right">{{translate('Price')}}</th>
                                        <th class="border-0 text-right">{{translate('Total')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($subTotal=0)
                            @php($totalDisOnPro=0)
                            @php($addOnsCost=0)
                            @foreach($order->details as $detail)
                                @php($productDetails = json_decode($detail['product_details'], true))
                                @php($addOnQtys=json_decode($detail['add_on_qtys'],true))
                                @php($addOnPrices=json_decode($detail['add_on_prices'],true))

                                <tr>
                                            <td class="border-0">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;"
                                                 src="{{ $detail->product?->imageFullPath ?? asset('assets/admin/img/160x160/img2.jpg') }}"
                                                         alt="Product Image">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 text-dark">{{$productDetails['name']}}</h6>
                                                        <div class="base-price mb-1">
                                                            <span class="text-muted">{{translate('Base Price')}}:</span>
                                                            <span class="font-weight-bold text-dark">{{Helpers::set_symbol($detail['price'])}}</span>
                                                        </div>
                                                        <div class="text-muted small">
                                                            @if (isset($detail['variation']) && $detail['variation'] != '[]' && $detail['variation'] != '')
                                                                @php($variations = json_decode($detail['variation'], true))
                                                                @if(is_array($variations) && count($variations) > 0)
                                                                    <div class="variations-compact">
                                                                        @foreach($variations as $variation)
                                                                            @if (isset($variation['name']) && isset($variation['values']))
                                                                                <div class="variation-item mb-1">
                                                                                    <span class="variation-name">{{$variation['name']}}:</span>
                                                                                    @if (is_array($variation['values']) && isset($variation['values']['label']) && is_array($variation['values']['label']))
                                                                                        {{-- Handle the actual structure: values.label is an array of selected values --}}
                                                                                        @foreach ($variation['values']['label'] as $selectedValue)
                                                                                            <span class="variation-value">{{$selectedValue}}</span>
                                                                                            @if(!$loop->last), @endif
                                                                                        @endforeach
                                                                                    @elseif (is_array($variation['values']))
                                                                                        {{-- Handle array of value objects with label and optionPrice --}}
                                                                @foreach ($variation['values'] as $value)
                                                                                            @php($optionPrice = $value['optionPrice'] ?? $value['price'] ?? $value['delta'] ?? 0)
                                                                                            <span class="variation-value">
                                                                                                {{ $value['label'] ?? $value['name'] ?? $value['value'] ?? 'Option' }}
                                                                                                @if($optionPrice != 0)
                                                                                                    <span class="price-change {{$optionPrice > 0 ? 'text-success' : 'text-danger'}}">
                                                                                                        ({{$optionPrice > 0 ? '+' : ''}}{{Helpers::set_symbol($optionPrice)}})
                                                                </span>
                                                                                                @endif
                                                                                            </span>
                                                                                            @if(!$loop->last), @endif
                                                                @endforeach
                                                            @else
                                                                                        {{-- Handle other structures --}}
                                                                                        <span class="variation-value">{{$variation['values']}}</span>
                                                                                    @endif
                                                                        </div>
                                                                            @elseif(is_array($variation))
                                                                                <div class="variation-item mb-1">
                                                                                    <span class="variation-name">{{translate('Variation')}}:</span>
                                                                                    @foreach($variation as $key1 => $variationValue)
                                                                                        @php($price = 0)
                                                                                        @if(is_array($variationValue) && isset($variationValue['price']))
                                                                                            @php($price = $variationValue['price'])
                                                                                            @php($variationValue = $variationValue['name'] ?? $variationValue['label'] ?? $key1)
                                                                @endif
                                                                                        <span class="variation-value">
                                                                                            {{$key1}}: {{$variationValue}}
                                                                                            @if($price != 0)
                                                                                                <span class="price-change {{$price > 0 ? 'text-success' : 'text-danger'}}">
                                                                                                    ({{$price > 0 ? '+' : ''}}{{Helpers::set_symbol($price)}})
                                                                                                </span>
                                                            @endif
                                                                                        </span>
                                                                                        @if(!$loop->last), @endif
                                                        @endforeach
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @elseif(is_string($variations))
                                                                    <div class="variation-item mb-1">
                                                                        <span class="variation-name">{{translate('Variation')}}:</span>
                                                                        <span class="variation-value">{{$variations}}</span>
                                                                    </div>
                                                                @endif
                                                    @else
                                                                <div class="variation-item mb-1">
                                                                    <span class="text-muted">{{translate('No variations')}}</span>
                                                                    @if(isset($detail['variation']))
                                                                        <small class="text-info d-block">Debug: {{$detail['variation']}}</small>
                                                                        <small class="text-warning d-block">Parsed: {{json_encode(json_decode($detail['variation'], true))}}</small>
                                                                    @endif
                                                        </div>
                                                    @endif

                                                    @php($addon_ids = json_decode($detail['add_on_ids'],true))
                                                    @if ($addon_ids)
                                                                <div class="addons-compact mt-1">
                                                                    <span class="addons-label">{{translate('Add-ons')}}:</span>
                                                        @foreach($addon_ids as $key2 =>$id)
                                                            @php($addon=\App\Model\AddOn::find($id))
                                                            @php($addOnQtys==null? $add_on_qty=1 : $add_on_qty=$addOnQtys[$key2] ?? 1)
                                                                        @php($addonPrice = $addOnPrices[$key2] ?? $addon->price ?? 0)
                                                                        <span class="addon-item">
                                                                            {{$addon ? $addon['name'] : translate('addon deleted')}} 
                                                                            <span class="addon-price">({{$add_on_qty}}x {{Helpers::set_symbol($addonPrice)}})</span>
                                                                    </span>
                                                                        @if(!$loop->last), @endif
                                                                        @php($addOnsCost+=$addonPrice * $add_on_qty)
                                                        @endforeach
                                                                </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                            <td class="border-0 text-center">
                                                <span class="badge badge-soft-primary px-3 py-2">{{$detail['quantity']}}</span>
                                            </td>
                                            <td class="border-0 text-right">
                                        @php($amount=$detail['price']*$detail['quantity'])
                                                <div class="text-dark font-weight-bold">{{Helpers::set_symbol($detail['price'])}}</div>
                                                @if($detail['discount_on_product'] > 0)
                                                    <div class="text-success small">-{{Helpers::set_symbol($detail['discount_on_product'])}} {{translate('discount')}}</div>
                                                @endif
                                                @php($itemAddonCost = 0)
                                                @php($itemVariationCost = 0)
                                                @if(isset($addon_ids) && $addon_ids)
                                                    @foreach($addon_ids as $key2 =>$id)
                                                        @php($addon=\App\Model\AddOn::find($id))
                                                        @php($addOnQtys==null? $add_on_qty=1 : $add_on_qty=$addOnQtys[$key2] ?? 1)
                                                        @php($itemAddonCost += ($addOnPrices[$key2] ?? $addon->price ?? 0) * $add_on_qty)
                                                    @endforeach
                                                @endif
                                                @if($itemAddonCost > 0)
                                                    <div class="text-primary small">+{{Helpers::set_symbol($itemAddonCost)}} {{translate('add-ons')}}</div>
                                                @endif
                                    </td>
                                            <td class="border-0 text-right">
                                                <div class="text-dark font-weight-bold">{{Helpers::set_symbol($amount-$detail['discount_on_product']*$detail['quantity'])}}</div>
                                    </td>
                                </tr>
                                        @php($totalDisOnPro += $detail['discount_on_product']*$detail['quantity'])
                                @php($subTotal += $amount)
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                                        </div>
                                        </div>

                <!-- Price Breakdown Card -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="tio-calculator text-primary"></i> {{translate('Price Breakdown')}}
                        </h5>
                                        </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 offset-md-4">
                                <div class="price-breakdown">
                                    <div class="price-item d-flex justify-content-between py-2">
                                        <span>{{translate('Items Price')}}</span>
                                        <span class="font-weight-bold">{{Helpers::set_symbol($subTotal)}}</span>
                                        </div>
                                    @if($totalDisOnPro > 0)
                                        <div class="price-item d-flex justify-content-between py-2 text-success">
                                            <span>{{translate('Item Discount')}}</span>
                                            <span>-{{Helpers::set_symbol($totalDisOnPro)}}</span>
                                        </div>
                                    @endif
                                    @if($addOnsCost > 0)
                                        <div class="price-item d-flex justify-content-between py-2">
                                            <span>{{translate('Add-ons Cost')}}</span>
                                            <span>{{Helpers::set_symbol($addOnsCost)}}</span>
                                        </div>
                                    @endif
                                    @if($order['extra_discount'] > 0)
                                        <div class="price-item d-flex justify-content-between py-2 text-success">
                                            <span>{{translate('Extra Discount')}}</span>
                                            <span>-{{Helpers::set_symbol($order['extra_discount'])}}</span>
                                        </div>
                                    @endif
                                    @if($order['referral_discount'] > 0)
                                        <div class="price-item d-flex justify-content-between py-2 text-success">
                                            <span>{{translate('Referral Discount')}}</span>
                                            <span>-{{Helpers::set_symbol($order['referral_discount'])}}</span>
                                        </div>
                                        @endif
                                    <div class="price-item d-flex justify-content-between py-2">
                                        <span>{{translate('Subtotal')}}</span>
                                        <span class="font-weight-bold">{{Helpers::set_symbol($subTotal = $subTotal+$addOnsCost-$totalDisOnPro - $order['extra_discount'] - $order['referral_discount'])}}</span>
                                        </div>
                                    @if($order['order_type']!='take_away')
                                        <div class="price-item d-flex justify-content-between py-2">
                                            <span>{{translate('Delivery Fee')}}</span>
                                            <span>{{Helpers::set_symbol($order['delivery_charge'])}}</span>
                                        </div>
                                    @endif
                                    <hr class="my-3">
                                    <div class="price-item d-flex justify-content-between py-2">
                                        <span class="h5 text-primary">{{translate('Total Amount')}}</span>
                                        <span class="h5 text-primary font-weight-bold">{{Helpers::set_symbol($subTotal + ($order['order_type']=='take_away' ? 0 : $order['delivery_charge']))}}</span>
                                    </div>

                                    @if ($order->order_partial_payments->isNotEmpty())
                                        <hr class="my-3">
                                        <h6 class="text-dark mb-3">{{translate('Payment Details')}}</h6>
                                        @foreach($order->order_partial_payments as $partial)
                                            <div class="price-item d-flex justify-content-between py-2">
                                                <span>{{translate('Paid By')}} ({{str_replace('_', ' ',$partial->paid_with)}})</span>
                                                <span class="text-success">{{Helpers::set_symbol($partial->paid_amount)}}</span>
                                                </div>
                                        @endforeach
                                        <?php $due_amount = $order->order_partial_payments->first()?->due_amount; ?>
                                        @if($due_amount > 0)
                                            <div class="price-item d-flex justify-content-between py-2">
                                                <span>{{translate('Due Amount')}}</span>
                                                <span class="text-danger font-weight-bold">{{Helpers::set_symbol($due_amount)}}</span>
                                                </div>
                                        @endif
                                    @endif

                                    @if($order->order_change_amount()->exists())
                                        <hr class="my-3">
                                        <h6 class="text-dark mb-3">{{translate('Payment Summary')}}</h6>
                                        <div class="price-item d-flex justify-content-between py-2">
                                            <span>{{translate('Paid Amount')}}</span>
                                            <span class="text-success">{{Helpers::set_symbol($order->order_change_amount?->paid_amount)}}</span>
                                            </div>
                                        @php($changeOrDueAmount = $order->order_change_amount?->paid_amount - $order->order_change_amount?->order_amount)
                                        <div class="price-item d-flex justify-content-between py-2">
                                            <span>{{$changeOrDueAmount < 0 ? translate('Due Amount') : translate('Change Amount')}}</span>
                                            <span class="{{$changeOrDueAmount < 0 ? 'text-danger' : 'text-success'}} font-weight-bold">{{Helpers::set_symbol($changeOrDueAmount)}}</span>
                                            </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
                <div class="col-lg-4">
                <!-- Quick Actions Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="tio-settings"></i> {{translate('Quick Actions')}}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a class="btn btn-outline-primary" href={{route('admin.orders.generate-invoice',[$order['id']])}}>
                                <i class="tio-print"></i> {{translate('Print Invoice')}}
                            </a>
                            
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">{{translate('Change Order Status')}}</label>
                                    <div class="dropdown">
                                    <button class="form-control dropdown-toggle d-flex justify-content-between align-items-center" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{translate($order['order_status'])}}
                                        </button>
                                    <div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                                        @if($order['payment_status'] != 'paid')
                                            <a class="dropdown-item offline-payment-order-alert" href="javascript:">{{translate('confirmed')}}</a>
                                                @if($order['order_type'] != 'dine_in')
                                                <a class="dropdown-item offline-payment-order-alert" href="javascript:">{{translate('processing')}}</a>
                                                <a class="dropdown-item offline-payment-order-alert" href="javascript:">{{translate('out_for_delivery')}}</a>
                                                <a class="dropdown-item offline-payment-order-alert" href="javascript:">{{translate('delivered')}}</a>
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'returned'])}}" data-message="{{translate('Change status to returned ?')}}" href="javascript:">{{translate('returned')}}</a>
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'failed'])}}" data-message="{{translate('Change status to failed ?')}}" href="javascript:">{{translate('failed')}}</a>
                                                @endif
                                                @if($order['order_type'] == 'dine_in')
                                                <a class="dropdown-item offline-payment-order-alert" href="javascript:">{{translate('cooking')}}</a>
                                                <a class="dropdown-item offline-payment-order-alert" href="javascript:">{{translate('completed')}}</a>
                                                @endif
                                            <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'canceled'])}}" data-message="{{translate('Change status to canceled ?')}}" href="javascript:">{{translate('canceled')}}</a>
                                            @else
                                                @if($order['order_type'] != 'dine_in')
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'pending'])}}" data-message="{{translate('Change status to pending ?')}}" href="javascript:">{{translate('pending')}}</a>
                                                @endif
                                            <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'confirmed'])}}" data-message="{{translate('Change status to confirmed ?')}}" href="javascript:">{{translate('confirmed')}}</a>
                                                @if($order['order_type'] != 'dine_in')
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'processing'])}}" data-message="{{translate('Change status to processing ?')}}" href="javascript:">{{translate('processing')}}</a>
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'out_for_delivery'])}}" data-message="{{translate('Change status to out for delivery ?')}}" href="javascript:">{{translate('out_for_delivery')}}</a>
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'delivered'])}}" data-message="{{translate('Change status to delivered ?')}}" href="javascript:">{{translate('delivered')}}</a>
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'returned'])}}" data-message="{{translate('Change status to returned ?')}}" href="javascript:">{{translate('returned')}}</a>
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'failed'])}}" data-message="{{translate('Change status to failed ?')}}" href="javascript:">{{translate('failed')}}</a>
                                                @endif
                                                @if($order['order_type'] == 'dine_in')
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'cooking'])}}" data-message="{{translate('Change status to cooking ?')}}" href="javascript:">{{translate('cooking')}}</a>
                                                <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'completed'])}}" data-message="{{translate('Change status to completed ?')}}" href="javascript:">{{translate('completed')}}</a>
                                                @endif
                                            <a class="dropdown-item route-alert" data-route="{{route('admin.orders.status',['id'=>$order['id'],'order_status'=>'canceled'])}}" data-message="{{translate('Change status to canceled ?')}}" href="javascript:">{{translate('canceled')}}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold text-dark">{{translate('Payment Status')}}</span>
                                    <button type="button" class="btn btn-outline-primary btn-sm change-payment-status" 
                                            data-id="{{ $order['id'] }}" 
                                            data-current-status="{{ $order->payment_status }}">
                                        @if($order->payment_status == 'paid')
                                            <i class="fas fa-undo"></i> {{translate('Refund')}}
                                        @else
                                            <i class="fas fa-check"></i> {{translate('Mark Paid')}}
                            @endif
                                    </button>
                                    </div>
                            </div>
                                        </div>
                                    </div>
                                        </div>

                <!-- Customer Information Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="tio-user text-primary"></i> {{translate('Customer Information')}}
                        </h5>
                                        </div>
                        <div class="card-body">
                            @if($order->is_guest == 1)
                            <div class="text-center">
                                <img class="avatar avatar-lg rounded-circle mb-3" src="{{asset('assets/admin/img/160x160/img1.jpg')}}" alt="">
                                <h6 class="text-dark">{{translate('Guest Customer')}}</h6>
                                </div>
                            @else
                                @if($order->customer)
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img class="avatar avatar-lg rounded-circle" src="{{$order->customer?->imageFullPath}}" alt="Customer">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{route('admin.customer.view',[$order->customer['id']])}}" class="text-dark">
                                                {{$order->customer['f_name'].' '.$order->customer['l_name']}}
                                            </a>
                                        </h6>
                                        <p class="text-muted mb-0">{{$order->customer['orders_count']}} {{translate('Orders')}}</p>
                                        </div>
                                    </div>
                                <div class="contact-info">
                                    <div class="contact-item d-flex align-items-center gap-2 mb-2">
                                        <i class="tio-call-talking-quiet text-primary"></i>
                                        <a href="tel:{{$order->customer['phone']}}" class="text-dark">{{$order->customer['phone']}}</a>
                                        </div>
                                    <div class="contact-item d-flex align-items-center gap-2">
                                        <i class="tio-email text-primary"></i>
                                        <a href="mailto:{{$order->customer['email']}}" class="text-dark">{{$order->customer['email']}}</a>
                                    </div>
                                        </div>
                            @elseif($order->user_id == null)
                                <div class="text-center">
                                    <img class="avatar avatar-lg rounded-circle mb-3" src="{{asset('assets/admin/img/160x160/img1.jpg')}}" alt="">
                                    <h6 class="text-dark">{{translate('Walking Customer')}}</h6>
                                </div>
                            @else
                                <div class="text-center">
                                    <img class="avatar avatar-lg rounded-circle mb-3" src="{{asset('assets/admin/img/160x160/img1.jpg')}}" alt="">
                                    <h6 class="text-dark">{{translate('Customer Not Available')}}</h6>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                <!-- Branch Information Card -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="tio-shop text-primary"></i> {{translate('Branch Information')}}
                        </h5>
                                </div>
                    <div class="card-body">
                                    @if(isset($order->branch))
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <img class="avatar avatar-lg rounded-circle" src="{{ $order->branch?->imageFullPath}}" alt="Branch">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-dark">{{$order->branch?->name}}</h6>
                                    <p class="text-muted mb-0">{{$order->branch['orders_count']}} {{translate('Orders served')}}</p>
                                </div>
                            </div>
                            <div class="contact-info">
                                @if($order->branch['phone'])
                                    <div class="contact-item d-flex align-items-center gap-2 mb-2">
                                        <i class="tio-call-talking-quiet text-primary"></i>
                                        <a href="tel:{{$order->branch?->phone}}" class="text-dark">{{$order->branch?->phone}}</a>
                                </div>
                            @endif
                                <div class="contact-item d-flex align-items-center gap-2 mb-2">
                                    <i class="tio-email text-primary"></i>
                                    <a href="mailto:{{$order->branch?->email}}" class="text-dark">{{$order->branch->email}}</a>
                        </div>
                                <div class="contact-item d-flex align-items-center gap-2">
                                    <i class="tio-location text-primary"></i>
                                    <a target="_blank" class="text-dark" href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$order->branch['latitude']}}+{{$order->branch['longitude']}}">
                                        {{$order->branch['address']}}
                                    </a>
                    </div>
                </div>
                        @else
                            <div class="text-center">
                                <h6 class="text-danger">{{translate('Branch Deleted')}}</h6>
        </div>
                        @endif
    </div>
                </div>

                <!-- Delivery Information Card -->
                @if($order['order_type']!='take_away' && $order['order_type'] != 'pos' && $order['order_type'] != 'dine_in')
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="tio-truck text-primary"></i> {{translate('Delivery Information')}}
                            </h5>
                            <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#deliveryInfoModal">
                                <i class="tio-edit"></i>
                    </button>
                </div>
                        <div class="card-body">
                            @php($address = $order->address)
                            <div class="delivery-info">
                                <div class="info-item mb-2">
                                    <span class="label text-muted">{{translate('Name')}}:</span>
                                    <span class="value">{{ $address? $address['contact_person_name']: '' }}</span>
                                    </div>
                                <div class="info-item mb-2">
                                    <span class="label text-muted">{{translate('Contact')}}:</span>
                                    <a href="tel:{{ $address? $address['contact_person_number']: '' }}" class="value">{{ $address? $address['contact_person_number']: '' }}</a>
                                </div>
                                @if($address['floor'] ?? '')
                                    <div class="info-item mb-2">
                                        <span class="label text-muted">{{translate('Floor')}}:</span>
                                        <span class="value">{{$address['floor']}}</span>
                                    </div>
                                @endif
                                @if($address['house'] ?? '')
                                    <div class="info-item mb-2">
                                        <span class="label text-muted">{{translate('House')}}:</span>
                                        <span class="value">{{$address['house']}}</span>
                                </div>
                                @endif
                                @if($address['road'] ?? '')
                                    <div class="info-item mb-2">
                                        <span class="label text-muted">{{translate('Road')}}:</span>
                                        <span class="value">{{$address['road']}}</span>
                                    </div>
                                @endif
                                <div class="info-item">
                                    <span class="label text-muted">{{translate('Address')}}:</span>
                                    <span class="value">{{$address['address'] ?? ''}}</span>
                                </div>
                                    </div>
                                            </div>
                                        </div>
                                @endif

                <!-- Takeaway Information Card -->
                @if($order['order_type']=='take_away' && $order['order_type'] != 'pos' && $order['order_type'] != 'dine_in')
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="tio-user text-primary"></i> {{translate('Contact Information')}}
                            </h5>
                                    </div>
                        <div class="card-body">
                            @php($address = $order->address)
                            <div class="contact-info">
                                <div class="info-item mb-2">
                                    <span class="label text-muted">{{translate('Name')}}:</span>
                                    <span class="value">{{ $address? $address['contact_person_name']: '' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label text-muted">{{translate('Contact')}}:</span>
                                    <a href="tel:{{ $address? $address['contact_person_number']: '' }}" class="value">{{ $address? $address['contact_person_number']: '' }}</a>
                            </div>
                </div>
            </div>
        </div>
    @endif
                </div>
                                </div>
                            </div>

    <!-- Modals -->
    @include('admin-views.order.partials.modals')

@endsection

@push('script_2')
    <script>
        "use strict";

        $('.change-payment-status').on('click', function(){
            let id = $(this).data('id');
            let currentStatus = $(this).data('current-status');
            
            // Set the order ID and action
            $('#orderId').val(id);
            $('#paymentAction').val(currentStatus === 'paid' ? 'refund' : 'paid');
            
            // Update modal title and show/hide relevant fields
            if (currentStatus === 'paid') {
                $('#paymentStatusModalLabel').text('{{translate("Refund Payment")}}');
                $('#refundReasonGroup').show();
                $('#referenceCodeGroup').hide();
                $('#paymentMethod').prop('required', false);
            } else {
                $('#paymentStatusModalLabel').text('{{translate("Mark as Paid")}}');
                $('#refundReasonGroup').hide();
                $('#referenceCodeGroup').show();
                $('#paymentMethod').prop('required', true);
            }
            
            // Clear form
            $('#paymentStatusForm')[0].reset();
            $('#orderId').val(id);
            $('#paymentAction').val(currentStatus === 'paid' ? 'refund' : 'paid');
            
            // Show modal
            $('#paymentStatusModal').modal('show');
        });

        // Handle payment status confirmation
        $('#confirmPaymentStatus').on('click', function(){
            let orderId = $('#orderId').val();
            let paymentAction = $('#paymentAction').val();
            let paymentMethod = $('#paymentMethod').val();
            let referenceCode = $('#referenceCode').val();
            let refundReason = $('#refundReason').val();
            let notes = $('#notes').val();
            
            // Validate required fields
            if (paymentAction === 'paid' && !paymentMethod) {
                Swal.fire({
                    title: '{{translate("Error")}}',
                    text: '{{translate("Please select a payment method")}}',
                    type: 'error'
                });
                return;
            }
            
            // Show confirmation
            let confirmText = paymentAction === 'paid' ? 
                '{{translate("Are you sure you want to mark this order as paid?")}}' : 
                '{{translate("Are you sure you want to refund this payment?")}}';
                
            Swal.fire({
                title: '{{translate("Confirm")}}',
                text: confirmText,
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#01684b',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{translate("Yes")}}',
                cancelButtonText: '{{translate("Cancel")}}'
            }).then((result) => {
                if (result.value) {
                    // Prepare data
                    let formData = {
                        order_id: orderId,
                        payment_action: paymentAction,
                        payment_method: paymentMethod,
                        reference_code: referenceCode,
                        refund_reason: refundReason,
                        notes: notes
                    };
                    
                    // Send AJAX request
                    $.ajax({
                        url: "{{ url('admin/pos/payment-status-update') }}",
                        method: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#paymentStatusModal').modal('hide');
                            Swal.fire({
                                title: '{{translate("Success")}}',
                                text: '{{translate("Payment status updated successfully")}}',
                                type: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = '{{translate("An error occurred")}}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                title: '{{translate("Error")}}',
                                text: errorMessage,
                                type: 'error'
                            });
                        }
                    });
                }
            });
        });

        $('.offline-payment-order-alert').on('click', function () {
            Swal.fire({
                title: '{{translate("Payment_is_Not_Verified")}}',
                text: '{{ translate("You can not change order status to this status. Please Check & Verify the payment information whether it is correct or not. You can only change order status to failed or cancel if payment is not verified.") }}',
                type: 'question',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: 'default',
                confirmButtonColor: '#01684b',
                cancelButtonText: '{{translate("Close")}}',
                confirmButtonText: '{{translate("Proceed")}}',
                reverseButtons: true
            }).then((result) => {
                // Handle result if needed
            })
        });

        $('.route-alert').on('click', function () {
            let route = $(this).data('route');
            let message = $(this).data('message');
            
            Swal.fire({
                title: '{{translate("Are you sure?")}}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#01684b',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{translate("Yes, change it!")}}',
                cancelButtonText: '{{translate("Cancel")}}'
            }).then((result) => {
                if (result.value) {
                    window.location.href = route;
                }
            })
        });

        $('.predefined-time-input .badge').click(function() {
            var time = $(this).data('time');
            predefined_time_input(time);
        });

        function predefined_time_input(min) {
            document.getElementById("extra_minute").value = min;
        }

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

@push('css')
    <style>
        .order-info-section, .order-notes-section {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .info-grid {
            display: grid;
            gap: 0.75rem;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-item .label {
            font-weight: 500;
            color: #6c757d;
            min-width: 120px;
        }
        
        .info-item .value {
            font-weight: 600;
            color: #212529;
        }
        
        .note-item {
            margin-bottom: 1rem;
        }
        
        .note-item .label {
            font-weight: 600;
            color: #495057;
            display: block;
            margin-bottom: 0.25rem;
        }
        
        .note-item .value {
            color: #6c757d;
            margin: 0;
        }
        
        .price-breakdown {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
        }
        
        .price-item {
            border-bottom: 1px solid #e9ecef;
        }
        
        .price-item:last-child {
            border-bottom: none;
        }
        
        .contact-info .contact-item {
            padding: 0.25rem 0;
        }
        
        .delivery-info .info-item {
            padding: 0.25rem 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .delivery-info .info-item:last-child {
            border-bottom: none;
        }
        
        .card-header.bg-light {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-header.bg-primary {
            background-color: #007bff !important;
        }
        
        .badge-soft-primary {
            background-color: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }
        
        .badge-soft-secondary {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }
        
        .badge-soft-info {
            background-color: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }
        
        .badge-soft-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .badge-soft-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .badge-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }
        
        .switcher {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        
        .switcher_input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .switcher_control {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .switcher_control:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        .switcher_input:checked + .switcher_control {
            background-color: #007bff;
        }
        
        .switcher_input:checked + .switcher_control:before {
            transform: translateX(26px);
        }
        
        /* Compact Variations and Add-ons Styles */
        .variations-compact {
            line-height: 1.4;
        }
        
        .variation-item {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem;
        }
        
        .variation-name {
            font-weight: 600;
            color: #495057;
            margin-right: 0.25rem;
        }
        
        .variation-value {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .price-change {
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .addons-compact {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem;
            line-height: 1.4;
        }
        
        .addons-label {
            font-weight: 600;
            color: #007bff;
            margin-right: 0.25rem;
        }
        
        .addon-item {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .addon-price {
            font-weight: 600;
            color: #28a745;
        }
        
        .base-price {
            font-size: 0.875rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .variation-item,
            .addons-compact {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.125rem;
            }
            
            .variation-name,
            .addons-label {
                margin-right: 0;
                margin-bottom: 0.125rem;
            }
        }
    </style>
@endpush