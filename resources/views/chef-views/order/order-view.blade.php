@extends('layouts.chef.app')

@section('title', translate('Order Details'))


@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-1">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/order_details.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Order_Details')}}
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-50 fz-14">{{$order->details->count()}}</span>
        </div>

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="card mb-3 mb-lg-5">
                    <div class="px-card py-3">
                        <div class="row gy-2">
                            <div class="col-sm-6 d-flex flex-column justify-content-between">
                                <div>
                                    <h2 class="page-header-title h1 mb-3">{{translate('order')}} #{{$order['id']}}</h2>
                                    <h5 class="text-capitalize">
                                        <i class="tio-shop"></i>
                                        {{translate('branch')}} :
                                        <label class="badge-soft-info px-2 rounded">
                                            {{$order->branch?$order->branch->name:'Branch deleted!'}}
                                        </label>
                                    </h5>

                                    <div class="mt-2 d-flex flex-column">
                                        @if($order['order_type'] == 'dine_in')
                                            <div class="hs-unfold">
                                                <h5 class="text-capitalize">
                                                    <i class="tio-table"></i>
                                                    {{translate('table no')}} : <label
                                                        class="badge badge-secondary">{{$order->table?$order->table->number:'Table deleted!'}}</label>
                                                </h5>
                                            </div>
                                            @if($order['number_of_people'] != null)
                                                <div class="hs-unfold">
                                                    <h5 class="text-capitalize">
                                                        <i class="tio-user"></i>
                                                        {{translate('number of people')}} : <label
                                                            class="badge badge-secondary">{{$order->number_of_people}}</label>
                                                    </h5>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex flex-column align-items-sm-end">
                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                        <span class="text-capitalize">{{translate('order_status')}} :</span>
                                        @if($order['order_status']=='pending')
                                            <span class="badge-soft-info px-2 py-1 rounded">{{translate('pending')}}</span>
                                        @elseif($order['order_status']=='confirmed')
                                            <span class="badge-soft-info px-2 py-1 rounded">{{translate('confirmed')}}</span>
                                        @elseif($order['order_status']=='processing')
                                            <span class="badge-soft-warning px-2 py-1 rounded">{{translate('processing')}}</span>
                                        @elseif($order['order_status']=='out_for_delivery')
                                            <span class="badge-soft-warning px-2 py-1 rounded">{{translate('out_for_delivery')}}</span>
                                        @elseif($order['order_status']=='delivered')
                                            <span class="badge-soft-success px-2 py-1 rounded">{{translate('delivered')}}</span>
                                        @else
                                            <span class="badge-soft-danger px-2 py-1 rounded">{{str_replace('_',' ',$order['order_status'])}}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                        <span class="text-capitalize">{{translate('payment_status')}} :</span>
                                        @if($order->payment_status=='paid')
                                            <span class="badge badge-soft-success">{{translate('paid')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger">{{translate('unpaid')}}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                        <span class="text-capitalize">{{translate('order_type')}} :</span>
                                        @if($order['order_type']=='take_away')
                                            <span class="badge-soft-success px-2 rounded">{{translate('take_away')}}</span>
                                        @elseif($order['order_type']=='dine_in')
                                            <span class="badge-soft-info px-2 rounded">{{translate('dine_in')}}</span>
                                        @else
                                            <span class="badge-soft-success px-2 rounded">{{translate('delivery')}}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-card py-3">
                        <div class="row gy-2">
                            <div class="col-sm-6">
                                <h5 class="text-capitalize">{{translate('order_time')}} :</h5>
                                <div>{{date('d M Y',strtotime($order['created_at']))}}</div>
                                <div>{{date('h:i A',strtotime($order['created_at']))}}</div>
                            </div>
                            @if($order['delivery_date'])
                                <div class="col-sm-6">
                                    <h5 class="text-capitalize">{{translate('delivery_time')}} :</h5>
                                    <div>{{date('d M Y',strtotime($order['delivery_date']))}}</div>
                                    <div>{{date('h:i A',strtotime($order['delivery_time']))}}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="px-card py-3">
                        <div class="row gy-2">
                            <div class="col-sm-6">
                                <h5 class="text-capitalize">{{translate('customer_info')}} :</h5>
                                @if($order->is_guest == 0)
                                    @if($order->customer)
                                        <div class="text-capitalize">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</div>
                                        <div>{{$order->customer['phone']}}</div>
                                        <div>{{$order->customer['email']}}</div>
                                    @else
                                        <div class="text-capitalize text-muted">{{translate('Customer_Unavailable')}}</div>
                                    @endif
                                @else
                                    <div class="text-capitalize text-info">{{translate('Guest Customer')}}</div>
                                @endif
                            </div>
                            @if($order['delivery_address'])
                                <div class="col-sm-6">
                                    <h5 class="text-capitalize">{{translate('delivery_address')}} :</h5>
                                    <div>{{$order['delivery_address']['address']}}</div>
                                    <div>{{$order['delivery_address']['contact_person_name']}}</div>
                                    <div>{{$order['delivery_address']['contact_person_number']}}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="px-card py-3">
                        <h5 class="text-capitalize">{{translate('order_items')}} :</h5>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>{{translate('item')}}</th>
                                        <th>{{translate('quantity')}}</th>
                                        <th>{{translate('price')}}</th>
                                        <th>{{translate('total')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->details as $detail)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img class="avatar avatar-sm mr-2" src="{{asset('storage/app/public/product')}}/{{$detail->product->image}}" alt="">
                                                    <div>
                                                        <div class="text-capitalize">{{$detail->product->name}}</div>
                                                        @if($detail->variation)
                                                            @php
                                                                $variation = is_string($detail->variation) ? json_decode($detail->variation, true) : $detail->variation;
                                                            @endphp
                                                            @if(is_array($variation))
                                                                @foreach($variation as $var)
                                                                    @if(isset($var['name']))
                                                                        <div class="text-muted">
                                                                            <strong>{{$var['name']}}:</strong>
                                                                            @if(isset($var['values']) && is_array($var['values']))
                                                                                @foreach($var['values'] as $value)
                                                                                    {{$value['label'] ?? $value}}
                                                                                    @if(isset($value['optionPrice']) && $value['optionPrice'] > 0)
                                                                                        (+{{ \App\CentralLogics\Helpers::set_symbol($value['optionPrice']) }})
                                                                                    @endif
                                                                                    @if(!$loop->last), @endif
                                                                                @endforeach
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <div class="text-muted">{{$detail->variation}}</div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{$detail->quantity}}</td>
                                            <td>{{ \App\CentralLogics\Helpers::set_symbol($detail->price) }}</td>
                                            <td>{{ \App\CentralLogics\Helpers::set_symbol($detail->price * $detail->quantity) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="px-card py-3">
                        <h5 class="text-capitalize">{{translate('order_summary')}}</h5>
                        <div class="d-flex justify-content-between">
                            <span>{{translate('subtotal')}} :</span>
                            <span>{{ \App\CentralLogics\Helpers::set_symbol($order['order_amount']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{translate('delivery_charge')}} :</span>
                            <span>{{ \App\CentralLogics\Helpers::set_symbol($order['delivery_charge']) }}</span>
                        </div>
                        @if($order['coupon_discount_amount'])
                            <div class="d-flex justify-content-between">
                                <span>{{translate('coupon_discount')}} :</span>
                                <span>-{{ \App\CentralLogics\Helpers::set_symbol($order['coupon_discount_amount']) }}</span>
                            </div>
                        @endif
                        @if($order['extra_discount'])
                            <div class="d-flex justify-content-between">
                                <span>{{translate('extra_discount')}} :</span>
                                <span>-{{ \App\CentralLogics\Helpers::set_symbol($order['extra_discount']) }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>{{translate('total')}} :</strong>
                            <strong>{{ \App\CentralLogics\Helpers::set_symbol($order['order_amount'] + $order['delivery_charge'] - $order['coupon_discount_amount'] - $order['extra_discount']) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card mt-3 no-print">
                    <div class="px-card py-3">
                        <h5 class="text-capitalize">{{translate('actions')}}</h5>
                        <div class="d-flex flex-column gap-2">
                            <button type="button" class="btn btn-primary" onclick="printDiv('printableArea')">
                                <i class="tio-print"></i> {{translate('print')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            margin: 10px 0 !important;
            padding: 15px !important;
            box-shadow: none !important;
        }
        body {
            font-family: Arial, sans-serif !important;
            margin: 20px !important;
        }
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        th, td {
            border: 1px solid #ddd !important;
            padding: 8px !important;
            text-align: left !important;
        }
        th {
            background-color: #f2f2f2 !important;
        }
    }
</style>

<script>
    function printDiv(divName) {

        var printContents = document.getElementById(divName).innerHTML;
        

        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = printContents;
        

        var noPrintElements = tempDiv.querySelectorAll('.no-print');
        noPrintElements.forEach(function(element) {
            element.remove();
        });
        

        var rightColumn = tempDiv.querySelector('.col-lg-4');
        if (rightColumn) {
            rightColumn.remove();
        }
        

        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Order Details</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
        printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
        printWindow.document.write('th { background-color: #f2f2f2; }');
        printWindow.document.write('.card { border: 1px solid #ddd; margin: 10px 0; padding: 15px; }');
        printWindow.document.write('.no-print { display: none !important; }');
        printWindow.document.write('.col-lg-4 { display: none !important; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(tempDiv.innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        

        printWindow.onload = function() {
            printWindow.print();
            printWindow.close();
        };
    }
</script>
@endpush
