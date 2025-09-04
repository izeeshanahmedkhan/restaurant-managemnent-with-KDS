@extends('layouts.chef.app')

@section('title', translate('Chef Dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid mt-5">
    <div class="row align-items-center">
        <div class="col-sm mb-2 mb-sm-0">
            <h1 class="page-header-title c1">{{translate('welcome')}} {{auth('chef')->user()->f_name ?? 'Chef'}}</h1>
            <p class="text-dark font-weight-semibold">{{translate('Monitor your kitchen operations and manage orders')}}</p>
        </div>
    </div>

    <!-- Order Statistics Cards -->
    <div class="row g-2" id="order-stats">
        @include('chef-views.partials._dashboard-order-stats', ['data' => $data])
    </div>



    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{translate('Recent Orders')}}</h4>
        </div>
        <div class="card-body">
            @if(isset($data['recent_orders']) && count($data['recent_orders']) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{translate('Order ID')}}</th>
                                <th>{{translate('Customer')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Total')}}</th>
                                <th>{{translate('Date')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['recent_orders'] as $order)
                                <tr>
                                    <td>
                                        <a href="{{route('chef.orders.details', ['id' => $order->id])}}" class="text-primary">
                                            #{{$order->id}}
                                        </a>
                                    </td>
                                    <td>
                                        @if($order->customer)
                                            {{$order->customer->f_name}} {{$order->customer->l_name}}
                                        @else
                                            {{translate('Guest Customer')}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->order_status=='pending')
                                            <span class="badge badge-soft-info">{{translate('pending')}}</span>
                                        @elseif($order->order_status=='confirmed')
                                            <span class="badge badge-soft-info">{{translate('confirmed')}}</span>
                                        @elseif($order->order_status=='processing')
                                            <span class="badge badge-soft-warning">{{translate('processing')}}</span>
                                        @elseif($order->order_status=='out_for_delivery')
                                            <span class="badge badge-soft-warning">{{translate('out_for_delivery')}}</span>
                                        @elseif($order->order_status=='delivered')
                                            <span class="badge badge-soft-success">{{translate('delivered')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger">{{str_replace('_',' ',$order->order_status)}}</span>
                                        @endif
                                    </td>
                                    <td>{{ \App\CentralLogics\Helpers::set_symbol($order->order_amount + $order->delivery_charge) }}</td>
                                    <td>{{date('d M Y, h:i A', strtotime($order->created_at))}}</td>
                                    <td>
                                        <a href="{{route('chef.orders.details', ['id' => $order->id])}}" class="btn btn-sm btn-outline-primary">
                                            <i class="tio-visible"></i> {{translate('View')}}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <img src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="No orders" class="mb-3" style="width: 120px;">
                    <h5>{{translate('No orders found')}}</h5>
                    <p class="text-muted">{{translate('No recent orders to display')}}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


