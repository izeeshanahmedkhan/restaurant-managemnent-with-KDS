@extends('layouts.chef.app')

@section('title', 'Kitchen Display System')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="kds-container">
    <div class="row">
        <!-- Left Sidebar - Items Board -->
        <div class="col-md-3">
            <div class="items-board">
                <h4 class="board-title">Items Board</h4>
                <div class="items-list">
                    @forelse($allItems as $item)
                    <div class="item-card">
                        <div class="item-name">{{ $item['name'] }}</div>
                        <div class="item-quantity">{{ $item['total_quantity'] }}</div>
                        @if($item['variations'])
                        <div class="item-variations">
                            @foreach($item['variations'] as $key => $value)
                                @if(is_array($value))
                                    @if(isset($value['name']))
                                        <small>{{ $key }}: {{ $value['name'] }}</small>
                                    @elseif(isset($value[0]['label']))
                                        <small>{{ $key }}: {{ $value[0]['label'] }}</small>
                                    @else
                                        <small>{{ $key }}: {{ implode(', ', array_column($value, 'label')) }}</small>
                                    @endif
                                @else
                                    <small>{{ $key }}: {{ $value }}</small>
                                @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="no-items">No items to prepare</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Content - Order Management -->
        <div class="col-md-9">
            <div class="order-management">
                <!-- Status Filters -->
                <div class="status-filters">
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-status="all">All Orders</button>
                        <button class="filter-tab" data-status="confirmed">Confirmed</button>
                        <button class="filter-tab" data-status="processing">Preparing</button>
                        <button class="filter-tab" data-status="out_for_delivery">Done</button>
                    </div>
                    <div class="search-box">
                        <input type="text" class="form-control" placeholder="Search Order" id="order-search">
                    </div>
                </div>



                <!-- Order Columns -->
                <div class="order-columns">
                    <!-- Dine-In Orders -->
                    <div class="order-column">
                        <h5 class="column-title">Dine-In Orders</h5>
                        <div class="orders-list" id="dine-in-orders">
                            @forelse($ordersByStatus['confirmed'] as $order)
                            <div class="order-card" data-order-id="{{ $order->id }}">
                                <div class="order-header">
                                    <span class="order-id">Order #{{ $order->id }}</span>
                                    <span class="order-status confirmed">Confirmed</span>
                                </div>
                                <div class="order-details">


                                    <div class="detail-row">
                                        <span class="label">Time:</span>
                                        <span class="value">{{ $order->created_at->format('h:i A, d-m-Y') }}</span>
                                    </div>
                                </div>
                                <div class="order-items">
                                    @foreach($order->details as $detail)
                                    <div class="order-item">
                                        <span class="item-name">{{ $detail->product->name ?? 'Unknown' }}</span>
                                        <span class="item-qty">x{{ $detail->quantity }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="order-actions">
                                    <button class="btn btn-sm btn-success" onclick="updateOrderStatus({{ $order->id }}, 'processing')">Start Preparing</button>
                                </div>
                            </div>
                            @empty
                            @if(isset($allOrders) && $allOrders->count() > 0)
                                @foreach($allOrders->whereIn('order_status', ['confirmed', 'pending'])->take(3) as $order)
                                <div class="order-card" data-order-id="{{ $order->id }}">
                                    <div class="order-header">
                                        <span class="order-id">Order #{{ $order->id }}</span>
                                        <span class="order-status {{ $order->order_status }}">{{ ucfirst($order->order_status) }}</span>
                                    </div>
                                    <div class="order-details">


                                        <div class="detail-row">
                                            <span class="label">Time:</span>
                                            <span class="value">{{ $order->created_at->format('h:i A, d-m-Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="order-items">
                                        @foreach($order->details as $detail)
                                        <div class="order-item">
                                            <span class="item-name">{{ $detail->product->name ?? 'Unknown' }}</span>
                                            <span class="item-qty">x{{ $detail->quantity }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="order-actions">
                                        <button class="btn btn-sm btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'confirmed')">Confirm</button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="no-orders">No dine-in orders found</div>
                            @endif
                            @endforelse
                        </div>
                    </div>

                    <!-- Processing Orders -->
                    <div class="order-column">
                        <h5 class="column-title">Processing</h5>
                        <div class="orders-list" id="online-orders">
                            @forelse($ordersByStatus['processing'] as $order)
                            <div class="order-card" data-order-id="{{ $order->id }}">
                                <div class="order-header">
                                    <span class="order-id">Order #{{ $order->id }}</span>
                                    <span class="order-status processing">Preparing</span>
                                </div>
                                <div class="order-details">


                                    <div class="detail-row">
                                        <span class="label">Time:</span>
                                        <span class="value">{{ $order->created_at->format('h:i A, d-m-Y') }}</span>
                                    </div>
                                </div>
                                <div class="order-items">
                                    @foreach($order->details as $detail)
                                    <div class="order-item">
                                        <span class="item-name">{{ $detail->product->name ?? 'Unknown' }}</span>
                                        <span class="item-qty">x{{ $detail->quantity }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="order-actions">
                                    <button class="btn btn-sm btn-warning" onclick="updateOrderStatus({{ $order->id }}, 'out_for_delivery')">Mark Done</button>
                                </div>
                            </div>
                            @empty
                            @if(isset($allOrders) && $allOrders->count() > 0)
                                @foreach($allOrders->where('order_status', 'processing')->take(3) as $order)
                                <div class="order-card" data-order-id="{{ $order->id }}">
                                    <div class="order-header">
                                        <span class="order-id">Order #{{ $order->id }}</span>
                                        <span class="order-status {{ $order->order_status }}">{{ ucfirst($order->order_status) }}</span>
                                    </div>
                                    <div class="order-details">


                                        <div class="detail-row">
                                            <span class="label">Time:</span>
                                            <span class="value">{{ $order->created_at->format('h:i A, d-m-Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="order-items">
                                        @foreach($order->details as $detail)
                                        <div class="order-item">
                                            <span class="item-name">{{ $detail->product->name ?? 'Unknown' }}</span>
                                            <span class="item-qty">x{{ $detail->quantity }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="order-actions">
                                        <button class="btn btn-sm btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'processing')">Start Preparing</button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="no-orders">No online orders found</div>
                            @endif
                            @endforelse
                        </div>
                    </div>

                    <!-- Out for Delivery Orders -->
                    <div class="order-column">
                        <h5 class="column-title">Out for Delivery</h5>
                        <div class="orders-list" id="takeaway-orders">
                            @forelse($ordersByStatus['out_for_delivery'] as $order)
                            <div class="order-card" data-order-id="{{ $order->id }}">
                                <div class="order-header">
                                    <span class="order-id">Order #{{ $order->id }}</span>
                                    <span class="order-status done">Done</span>
                                </div>
                                <div class="order-details">

                                    <div class="detail-row">
                                        <span class="label">Time:</span>
                                        <span class="value">{{ $order->created_at->format('h:i A, d-m-Y') }}</span>
                                    </div>
                                </div>
                                <div class="order-items">
                                    @foreach($order->details as $detail)
                                    <div class="order-item">
                                        <span class="item-name">{{ $detail->product->name ?? 'Unknown' }}</span>
                                        <span class="item-qty">x{{ $detail->quantity }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="order-actions">
                                    <button class="btn btn-sm btn-info" onclick="updateOrderStatus({{ $order->id }}, 'delivered')">Complete</button>
                                </div>
                            </div>
                            @empty
                            @if(isset($allOrders) && $allOrders->count() > 0)
                                @foreach($allOrders->where('order_status', 'out_for_delivery')->take(3) as $order)
                                <div class="order-card" data-order-id="{{ $order->id }}">
                                    <div class="order-header">
                                        <span class="order-id">Order #{{ $order->id }}</span>
                                        <span class="order-status {{ $order->order_status }}">{{ ucfirst($order->order_status) }}</span>
                                    </div>
                                    <div class="order-details">

                                        <div class="detail-row">
                                            <span class="label">Time:</span>
                                            <span class="value">{{ $order->created_at->format('h:i A, d-m-Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="order-items">
                                        @foreach($order->details as $detail)
                                        <div class="order-item">
                                            <span class="item-name">{{ $detail->product->name ?? 'Unknown' }}</span>
                                            <span class="item-qty">x{{ $detail->quantity }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="order-actions">
                                        <button class="btn btn-sm btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'out_for_delivery')">Mark Ready</button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="no-orders">No takeaway orders found</div>
                            @endif
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kds-container {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding: 20px;
}

.items-board {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: calc(100vh - 100px);
    overflow-y: auto;
}

.board-title {
    color: #333;
    margin-bottom: 20px;
    font-weight: 600;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.item-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    position: relative;
}

.item-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.item-quantity {
    background: #333;
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    position: absolute;
    top: 10px;
    right: 10px;
}

.item-variations {
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

.no-items {
    text-align: center;
    color: #999;
    padding: 40px 20px;
}

.no-orders {
    text-align: center;
    color: #999;
    padding: 20px;
    font-style: italic;
}

.debug-info {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}

.order-management {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: calc(100vh - 100px);
    overflow-y: auto;
}

.status-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #dee2e6;
}

.filter-tabs {
    display: flex;
    gap: 10px;
}

.filter-tab {
    padding: 8px 16px;
    border: none;
    background: white;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.filter-tab.active {
    background: #007bff;
    color: white;
}

.search-box input {
    width: 200px;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
}

.order-columns {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
    height: calc(100% - 80px);
}

.order-column {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    height: 80vh;
    max-height: 80vh;
    overflow-y: auto;
    overflow-x: hidden;
}

.column-title {
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
    text-align: center;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}

.order-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-left: 4px solid #007bff;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.order-id {
    font-weight: 600;
    color: #007bff;
}

.order-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.order-status.confirmed {
    background: #ffc107;
    color: #000;
}

.order-status.processing {
    background: #fd7e14;
    color: white;
}

.order-status.done {
    background: #28a745;
    color: white;
}

.order-details {
    margin-bottom: 10px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 14px;
}

.label {
    color: #666;
}

.value {
    font-weight: 500;
}

.order-items {
    margin-bottom: 15px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
}

.order-actions {
    text-align: center;
}

.order-actions .btn {
    width: 100%;
    margin-top: 10px;
}

@media (max-width: 768px) {
    .order-columns {
        grid-template-columns: 1fr;
    }
    
    .status-filters {
        flex-direction: column;
        gap: 10px;
    }
    
    .filter-tabs {
        flex-wrap: wrap;
    }
}
</style>

<script>
function updateOrderStatus(orderId, status) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        url: '/chef/orders/status',
        method: 'POST',
        data: {
            id: orderId,
            order_status: status
        },
        success: function(response) {
            // Reload the page to show updated status
            location.reload();
        },
        error: function(xhr) {
            alert('Error updating order status');
        }
    });
}

// Filter functionality
$('.filter-tab').click(function() {
    $('.filter-tab').removeClass('active');
    $(this).addClass('active');
    
    const status = $(this).data('status');
    console.log('Filter clicked:', status);
    
    // Show/hide order cards based on status
    $('.order-card').each(function() {
        const orderStatus = $(this).find('.order-status').text().toLowerCase().trim();
        const card = $(this);
        console.log('Order status found:', orderStatus);
        
        if (status === 'all') {
            card.show();
        } else if (status === 'confirmed') {
            // Show confirmed and pending orders
            if (orderStatus === 'confirmed' || orderStatus === 'pending') {
                card.show();
            } else {
                card.hide();
            }
        } else if (status === 'processing') {
            // Show processing/preparing orders
            if (orderStatus === 'preparing' || orderStatus === 'processing') {
                card.show();
            } else {
                card.hide();
            }
        } else if (status === 'out_for_delivery') {
            // Show done/out_for_delivery orders
            if (orderStatus === 'done' || orderStatus === 'out_for_delivery') {
                card.show();
            } else {
                card.hide();
            }
        } else {
            card.hide();
        }
    });
    
    // Update column visibility
    updateColumnVisibility(status);
});

function updateColumnVisibility(status) {
    if (status === 'all') {
        $('.order-column').show();
    } else if (status === 'confirmed') {
        $('.order-column').hide();
        $('#dine-in-orders').closest('.order-column').show();
    } else if (status === 'processing') {
        $('.order-column').hide();
        $('#online-orders').closest('.order-column').show();
    } else if (status === 'out_for_delivery') {
        $('.order-column').hide();
        $('#takeaway-orders').closest('.order-column').show();
    }
}

// Search functionality
$('#order-search').on('input', function() {
    const searchTerm = $(this).val().toLowerCase();
    $('.order-card').each(function() {
        const orderId = $(this).find('.order-id').text().toLowerCase();
        if (orderId.includes(searchTerm)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

// Auto-refresh every 30 seconds
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endsection
