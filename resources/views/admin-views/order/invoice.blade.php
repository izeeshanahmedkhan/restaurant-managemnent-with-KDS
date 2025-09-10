@extends('layouts.admin.app')

@section('title','')

@push('css_or_js')
    <style>
        @media print {
            .non-printable {
                display: none;
            }

            .printable {
                display: block;
            }
        }

        .hr-style-2 {
            border: 0;
            height: 1px;
            background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
        }

        .hr-style-1 {
            overflow: visible;
            padding: 0;
            border: none;
            border-top: medium double #000000;
            text-align: center;
        }
        #printableAreaContent * {
            font-weight: normal !important;
        }
    </style>

    <style type="text/css" media="print">
        @page {
            size: auto;   /* auto is the initial value */
            margin: 2px;
        }

    </style>
@endpush

@section('content')

    <div class="content container-fluid" style="color: black">
        <div class="row justify-content-center" id="printableArea">
            <div class="col-md-12">
                <div class="text-center">
                    <input type="button" class="btn btn-primary non-printable" onclick="printDiv('printableArea')"
                           value="{{translate('Proceed, If thermal printer is ready.')}}"/>
                    <a href="{{url()->previous()}}" class="btn btn-danger non-printable">{{translate('Back')}}</a>
                </div>
                <hr class="non-printable">
            </div>
            <div class="col-5" id="printableAreaContent">
                <div class="text-center pt-4 mb-3">
                    <h2 style="line-height: 1">{{\App\Model\BusinessSetting::where(['key'=>'restaurant_name'])->first()->value}}</h2>
                    <h5 style="font-size: 20px;font-weight: lighter;line-height: 1">
                        {{\App\Model\BusinessSetting::where(['key'=>'address'])->first()->value}}
                    </h5>
                    <h5 style="font-size: 16px;font-weight: lighter;line-height: 1">
                        Phone : {{\App\Model\BusinessSetting::where(['key'=>'phone'])->first()->value}}
                    </h5>
                </div>
                <hr class="text-dark hr-style-1">

                <div class="row mt-4">
                    <div class="col-6">
                        <h5>{{translate('Order ID : ')}}{{$order['id']}}</h5>
                    </div>
                    <div class="col-6">
                        <h5 style="font-weight: lighter">
                            <span class="font-weight-normal">{{date('d/M/Y h:m a',strtotime($order['created_at']))}}</span>
                        </h5>
                    </div>
                    <div class="col-12">
                        @if($order->is_guest == 0)
                            @if(isset($order->customer))
                                <h5>
                                    {{translate('Customer Name : ')}}<span class="font-weight-normal">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</span>
                                </h5>
                                <h5>
                                    {{translate('Phone : ')}}<span class="font-weight-normal">{{$order->customer['phone']}}</span>
                                </h5>
                                <h5>
                                    {{translate('Address : ')}}<span class="font-weight-normal">{{isset($order->address)?$order->address['address']:''}}</span>
                                </h5>
                            @endif
                        @endif
                        @if($order->is_guest == 1)
                                @if(isset($order->address))
                                    <h5>
                                        {{translate('Customer Name : ')}}<span class="font-weight-normal">{{isset($order->address)?$order->address['contact_person_name']:''}}</span>
                                    </h5>
                                    <h5>
                                        {{translate('Phone : ')}}<span class="font-weight-normal">{{isset($order->address)?$order->address['contact_person_number']:''}}</span>
                                    </h5>
                                    <h5>
                                        {{translate('Address : ')}}<span class="font-weight-normal">{{isset($order->address)?$order->address['address']:''}}</span>
                                    </h5>
                                @endif
                        @endif
                    </div>
                </div>
                <h5 class="text-uppercase"></h5>
                <hr class="text-dark hr-style-2">
                <table class="table table-bordered mt-3">
                    <thead>
                    <tr>
                        <th style="width: 10%">{{translate('QTY')}}</th>
                        <th class="">{{translate('DESC')}}</th>
                        <th style="text-align:right; padding-right:4px">{{translate('Price')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @php($subTotal=0)
                    @php($totalTax=0)
                    @php($totalDisOnPro=0)
                    @php($addOnsCost=0)
                    @php($addOnTax=0)
                    @php($add_ons_tax_cost=0)
                    @foreach($order->details as $detail)
                        @if($detail->product)
                            @php($addOnQtys=json_decode($detail['add_on_qtys'],true))
                            @php($addOnPrices=json_decode($detail['add_on_prices'],true))
                            @php($addOnTaxes=json_decode($detail['add_on_taxes'],true))

                            <tr>
                                <td class="">
                                    {{$detail['quantity']}}
                                </td>
                                <td class="">
                                    <span style="word-break: break-all;"> {{ Str::limit($detail->product['name'], 200) }}</span><br>
                                    @if (count(json_decode($detail['variation'], true)) > 0)
                                        <strong><u>{{ translate('variation') }} : </u></strong>
                                        {{-- DEBUG: Show raw variation data --}}
                                        <div style="background: yellow; padding: 5px; margin: 5px 0; font-size: 10px;">
                                            <strong>DEBUG VARIATION DATA:</strong><br>
                                            Raw JSON: {{ $detail['variation'] }}<br>
                                            Decoded: {{ json_encode(json_decode($detail['variation'], true)) }}<br>
                                            Type: {{ gettype(json_decode($detail['variation'], true)) }}<br>
                                            Count: {{ count(json_decode($detail['variation'], true)) }}
                                        </div>
                                        @php($variations = json_decode($detail['variation'], true))
                                        @foreach($variations as $index => $variation)
                                            {{-- DEBUG: Show each variation item --}}
                                            <div style="background: lightblue; padding: 3px; margin: 2px 0; font-size: 9px;">
                                                DEBUG Item {{ $index }}: {{ json_encode($variation) }}
                                            </div>
                                            @if (isset($variation['name']) && isset($variation['values']))
                                                <span class="d-block text-capitalize">
                                                    <strong>{{ $variation['name'] }} - </strong>
                                                </span>
                                                @if (isset($variation['values']['label']) && is_array($variation['values']['label']))
                                                    {{-- Handle new structure: {name: "Size", values: {label: ["Large"], price: [30.00]}} --}}
                                                    @php($labels = $variation['values']['label'])
                                                    @php($prices = $variation['values']['price'] ?? [])
                                                    @foreach($labels as $index => $label)
                                                        <span class="d-block text-capitalize">
                                                            {{ $label }} :
                                                            <strong>{{\App\CentralLogics\Helpers::set_symbol($prices[$index] ?? 0)}}</strong>
                                                        </span>
                                                    @endforeach
                                                @elseif (is_array($variation['values']))
                                                    {{-- Handle old structure with array of values --}}
                                                    @foreach ($variation['values'] as $value)
                                                        <span class="d-block text-capitalize">
                                                            {{ $value['label'] ?? $value['name'] ?? 'Option' }} :
                                                            <strong>{{\App\CentralLogics\Helpers::set_symbol($value['optionPrice'] ?? $value['price'] ?? $value['delta'] ?? 0)}}</strong>
                                                        </span>
                                                    @endforeach
                                                @else
                                                    {{-- Handle simple key-value structure --}}
                                                    <span class="d-block text-capitalize">
                                                        {{ $variation['values'] }} :
                                                        <strong>{{\App\CentralLogics\Helpers::set_symbol(0)}}</strong>
                                                    </span>
                                                @endif
                                            @elseif (isset($variation['name']) && isset($variation['selected_options']))
                                                {{-- Handle kiosk structure: {name: "Size", selected_options: [{label: "Large", price: 30.00}]} --}}
                                                <span class="d-block text-capitalize">
                                                    <strong>{{ $variation['name'] }} - </strong>
                                                </span>
                                                @foreach($variation['selected_options'] as $option)
                                                    <span class="d-block text-capitalize">
                                                        {{ $option['label'] }} :
                                                        <strong>{{\App\CentralLogics\Helpers::set_symbol($option['price'] ?? 0)}}</strong>
                                                    </span>
                                                @endforeach
                                            @elseif (is_array($variation))
                                                {{-- Handle array structure without name/values --}}
                                                @foreach($variation as $key => $value)
                                                    <div class="font-size-sm text-body">
                                                        <span>{{ $key }} : </span>
                                                        <span class="font-weight-bold">{{ $value }}</span>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="font-size-sm text-body">
                                            <span>{{ translate('Price') }} : </span>
                                            <span
                                                class="font-weight-bold">{{ \App\CentralLogics\Helpers::set_symbol($detail->price) }}</span>
                                        </div>
                                    @endif


                                    @foreach(json_decode($detail['add_on_ids'],true) as $key2 =>$id)
                                        @php($addon=\App\Model\AddOn::find($id))
                                        @if($key2==0)<strong><u>{{translate('Addons : ')}}</u></strong>@endif

                                        @if($addOnQtys==null)
                                            @php($add_on_qty=1)
                                        @else
                                            @php($add_on_qty=$addOnQtys[$key2] ?? 1)
                                        @endif

                                        <div class="font-size-sm text-body">
                                            <span>{{$addon ? $addon['name'] : translate('addon deleted')}} :  </span>
                                            <span class="font-weight-bold">
                                                {{$add_on_qty}} x {{ \App\CentralLogics\Helpers::set_symbol($addOnPrices[$key2] ?? $addon->price ?? 0) }}
                                            </span>
                                        </div>
                                        @php($addOnsCost+=($addOnPrices[$key2] ?? $addon->price ?? 0) * $add_on_qty)
                                        @php($add_ons_tax_cost +=  ($addOnTaxes[$key2] ?? 0) * $add_on_qty)
                                    @endforeach

                                    {{translate('Discount : ')}}{{ \App\CentralLogics\Helpers::set_symbol($detail['discount_on_product']) }}
                                </td>
                                <td style="width: 28%;padding-right:4px; text-align:right">
                                    @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                                    {{ \App\CentralLogics\Helpers::set_symbol($amount) }}
                                </td>
                            </tr>
                            @php($subTotal+=$amount)
                            @php($totalTax+=($detail['tax_amount']*$detail['quantity']) )
                        @endif
                    @endforeach
                    </tbody>
                </table>


                <div class="row justify-content-md-end mb-3 m-0" style="width: 99%">
                    <div class="col-md-10 p-0">
                        <dl class="row text-right" style="color: black!important;">
                            <dt class="col-6">{{translate('Items Price:')}}</dt>
                            <dd class="col-6">{{ \App\CentralLogics\Helpers::set_symbol($subTotal) }}</dd>

                            <dt class="col-6">{{translate('Addon Cost:')}}</dt>
                            <dd class="col-6">{{ \App\CentralLogics\Helpers::set_symbol($addOnsCost) }}</dd>

                            <dt class="col-6">{{translate('Coupon Discount:')}}</dt>
                            <dd class="col-6">- {{ \App\CentralLogics\Helpers::set_symbol($order['coupon_discount_amount']) }}</dd>

                            <dt class="col-6">{{translate('Extra Discount')}}:</dt>
                            <dd class="col-6">- {{ \App\CentralLogics\Helpers::set_symbol($order['extra_discount']) }}</dd>

                            <dt class="col-6">{{translate('Referral Discount')}}:</dt>
                            <dd class="col-6">- {{ \App\CentralLogics\Helpers::set_symbol($order['referral_discount']) }}</dd>

                            <dt class="col-6">{{translate('Tax / VAT:')}}</dt>
                            <dd class="col-6">{{ \App\CentralLogics\Helpers::set_symbol($totalTax + $add_ons_tax_cost) }}
                                <hr>

                            </dd>

                            <dt class="col-6">{{translate('Subtotal:')}}</dt>

                            <dd class="col-6">
                                {{ \App\CentralLogics\Helpers::set_symbol($subTotal+$totalTax+$addOnsCost+$add_ons_tax_cost-$order['coupon_discount_amount']-$order['extra_discount']-$order['referral_discount']) }}
                            </dd>
                            <dt class="col-6">{{translate('Delivery Fee:')}}</dt>
                            <dd class="col-6">
                                @if($order['order_type']=='take_away')
                                    @php($del_c=0)
                                @else
                                    @php($del_c=$order['delivery_charge'])
                                @endif
                                {{ \App\CentralLogics\Helpers::set_symbol($del_c) }}
                                <hr>
                            </dd>

                            <dt class="col-6" style="font-size: 20px">{{translate('Total:')}}</dt>
                            <dd class="col-6" style="font-size: 20px">{{ \App\CentralLogics\Helpers::set_symbol($subTotal+$del_c+$totalTax+$addOnsCost-$order['coupon_discount_amount']-$order['extra_discount']-$order['referral_discount']+$add_ons_tax_cost) }}</dd>

                            <!-- partial payment-->
                            @if ($order->order_partial_payments->isNotEmpty())
                                @foreach($order->order_partial_payments as $partial)
                                    <dt class="col-6">
                                        <div class="">
                                            <span>
                                                {{translate('Paid By')}} ({{str_replace('_', ' ',$partial->paid_with)}})</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">
                                        {{ \App\CentralLogics\Helpers::set_symbol($partial->paid_amount) }}
                                    </dd>
                                @endforeach
                                    <?php
                                    $due_amount = 0;
                                    $due_amount = $order->order_partial_payments->first()?->due_amount;
                                    ?>
                                <dt class="col-6">
                                    <div class="">
                                            <span>
                                                {{translate('Due Amount')}}</span>
                                        <span>:</span>
                                    </div>
                                </dt>
                                <dd class="col-6 text-dark text-right">
                                    {{ \App\CentralLogics\Helpers::set_symbol($due_amount) }}
                                </dd>
                            @endif
                            @if($order->order_change_amount()->exists())
                                <dt class="col-6">{{translate('paid_amount')}}<span>:</span></dt>
                                <dd class="col-6">{{ \App\CentralLogics\Helpers::set_symbol($order->order_change_amount?->paid_amount) }}</dd>

                                @php($changeOrDueAmount = $order->order_change_amount?->paid_amount - $order->order_change_amount?->order_amount)
                                <dt class="col-6">{{$changeOrDueAmount < 0 ? translate('due_amount') : translate('change_amount') }}<span>:</span></dt>
                                <dd class="col-6">{{ \App\CentralLogics\Helpers::set_symbol($changeOrDueAmount) }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
                <hr class="text-dark hr-style-2">
                <h5 class="text-center pt-3">
                    "{{translate('THANK YOU')}}"
                </h5>
                <hr class="text-dark hr-style-2">
                <div class="text-center">{{\App\Model\BusinessSetting::where(['key'=>'footer_text'])->first()->value}}</div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        "use strict";

        function printDiv(divName) {

            if($('html').attr('dir') === 'rtl') {
                $('html').attr('dir', 'ltr')
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                $('#printableAreaContent').attr('dir', 'rtl')
                window.print();
                document.body.innerHTML = originalContents;
                $('html').attr('dir', 'rtl')
            }else{
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }

        }
    </script>
@endpush
