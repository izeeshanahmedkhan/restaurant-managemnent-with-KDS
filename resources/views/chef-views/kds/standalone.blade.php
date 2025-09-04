<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

                 body {
             font-family: 'Inter', sans-serif;
             background: #f8f9fa;
             min-height: 100vh;
             overflow-x: hidden;
         }

        .kds-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

                 .logo {
             width: 50px;
             height: 50px;
             background: #007bff;
             border-radius: 12px;
             display: flex;
             align-items: center;
             justify-content: center;
             color: white;
             font-size: 1.5rem;
             font-weight: 700;
         }

        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }

        .header-nav {
            display: flex;
            gap: 1rem;
        }

        .nav-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

                 .nav-btn.dashboard {
             background: #007bff;
             color: white;
         }

         .nav-btn.dashboard:hover {
             transform: translateY(-2px);
             box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
         }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #4a5568;
        }

                 .user-avatar {
             width: 40px;
             height: 40px;
             background: #007bff;
             border-radius: 50%;
             display: flex;
             align-items: center;
             justify-content: center;
             color: white;
             font-weight: 600;
         }

        .kds-container {
            padding: 1rem;
            max-width: 100%;
            margin: 0 auto;
            height: calc(100vh - 120px);
        }

        .items-board {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            height: 100%;
            overflow: hidden;
        }

        .board-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .items-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .item-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            min-height: 70px;
            margin-bottom: 0.75rem;
        }

        .item-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .item-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .item-quantity {
            background: #007bff;
            color: white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
        }

        .item-variations {
            margin-top: 0.5rem;
        }

        .item-variations small {
            display: block;
            color: #718096;
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }

        .no-items {
            text-align: center;
            color: #a0aec0;
            padding: 2rem;
            font-style: italic;
        }

        .order-management {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

                 .status-filters {
             display: flex;
             justify-content: space-between;
             align-items: center;
             margin-bottom: 2rem;
             padding-bottom: 1rem;
             border-bottom: 2px solid #e9ecef;
         }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
        }

        .filter-tab {
            padding: 0.75rem 1.5rem;
            border: none;
            background: transparent;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #718096;
        }

                 .filter-tab.active {
             background: #007bff;
             color: white;
             transform: translateY(-2px);
             box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
         }

                 .search-box input {
             padding: 0.75rem 1rem;
             border: 2px solid #e9ecef;
             border-radius: 8px;
             width: 250px;
             font-size: 0.9rem;
             transition: all 0.3s ease;
         }

         .search-box input:focus {
             outline: none;
             border-color: #007bff;
             box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
         }

        .order-columns {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            flex: 1;
            margin-top: 1rem;
            height: 100%;
        }

        .order-column {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
        }

        .column-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
            text-align: center;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(102, 126, 234, 0.2);
        }

        .orders-list {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            max-height: calc(120vh - 120px);
            padding-right: 5px;
        }

        .orders-list::-webkit-scrollbar {
            width: 6px;
        }

        .orders-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .orders-list::-webkit-scrollbar-thumb {
            background: #007bff;
            border-radius: 3px;
        }

        .orders-list::-webkit-scrollbar-thumb:hover {
            background: #0056b3;
        }

        .order-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
            min-height: 140px;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .order-id {
            font-weight: 700;
            color: #667eea;
            font-size: 1.1rem;
        }

        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-status.confirmed {
            background: #fef5e7;
            color: #d69e2e;
        }

        .order-status.processing, .order-status.preparing {
            background: #fed7d7;
            color: #e53e3e;
        }

        .order-status.done, .order-status.out_for_delivery {
            background: #c6f6d5;
            color: #38a169;
        }

        .order-details {
            margin-bottom: 1rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .label {
            color: #718096;
            font-weight: 500;
        }

        .value {
            font-weight: 600;
            color: #2d3748;
        }

        .order-items {
            margin-bottom: 1rem;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-name {
            color: #2d3748;
            font-weight: 500;
        }

        .item-qty {
            color: #667eea;
            font-weight: 600;
        }

        .order-actions {
            text-align: center;
        }

        .order-actions .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: white;
        }

        .btn-info {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .order-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .no-orders {
            text-align: center;
            color: #a0aec0;
            padding: 2rem;
            font-style: italic;
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 1200px) {
            .order-columns {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .col-3, .col-9 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .order-columns {
                grid-template-columns: 1fr;
            }

            .status-filters {
                flex-direction: column;
                gap: 1rem;
            }

            .filter-tabs {
                flex-wrap: wrap;
            }

            .search-box input {
                width: 100%;
            }

            .kds-container {
                padding: 0.5rem;
            }

            .order-card {
                min-height: 150px;
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .order-card {
                min-height: 120px;
                padding: 0.75rem;
            }

            .item-card {
                min-height: 60px;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Custom Header -->
    <header class="kds-header">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo">
                    <i class="fas fa-utensils"></i>
                </div>
                <h1 class="header-title">Kitchen Display System</h1>
            </div>

            <nav class="header-nav">
                <a href="{{ route('chef.dashboard') }}" class="nav-btn dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </nav>

            <div class="user-info">
                <div class="user-avatar">
                    {{ substr(auth('chef')->user()->name ?? 'C', 0, 1) }}
                </div>
                <div>
                    <div style="font-weight: 600;">{{ auth('chef')->user()->name ?? 'Chef' }}</div>
                    <div style="font-size: 0.8rem; color: #a0aec0;">Chef</div>
                </div>
            </div>
        </div>
    </header>

    <div class="kds-container">
        <div class="row">
            <!-- Items Board - Left Side (col-3) -->
            <div class="col-3">
                <div class="items-board">
                    <h4 class="board-title">
                        <i class="fas fa-clipboard-list"></i>
                        Items Board
                    </h4>
                    <div class="items-grid">
                        @forelse($allItems as $item)
                        <div class="item-card">
                            <div class="item-name">{{ $item['name'] }}</div>
                            <div class="item-quantity">{{ $item['total_quantity'] }}</div>

                        </div>
                        @empty
                        <div class="no-items">No items to prepare</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Order Management - Right Side (col-9) -->
            <div class="col-9">
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
                                 <!-- Confirmed Orders -->
                 <div class="order-column">
                     <h5 class="column-title">Confirmed</h5>
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
                                <button class="btn btn-success" onclick="updateOrderStatus({{ $order->id }}, 'processing')">Start Preparing</button>
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
                                    <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'confirmed')">Confirm</button>
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
                                <button class="btn btn-warning" onclick="updateOrderStatus({{ $order->id }}, 'out_for_delivery')">Mark Done</button>
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
                                    <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'processing')">Start Preparing</button>
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
                                <button class="btn btn-info" onclick="updateOrderStatus({{ $order->id }}, 'delivered')">Complete</button>
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
                                    <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'out_for_delivery')">Mark Ready</button>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateOrderStatus(orderId, status) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route("chef.kds.update-status") }}',
                method: 'POST',
                data: {
                    order_id: orderId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        showToast('Order status updated successfully!', 'success');
                        // Reload the page to show updated status
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast('Error updating order status: ' + response.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error updating order status:', xhr);
                    showToast('Error updating order status', 'error');
                }
            });
        }

        function showToast(message, type = 'info') {
            // Create toast element
            const toast = $(`
                <div class="toast-notification ${type}" style="
                    position: fixed;
                    top: 20px;
                    left: 20px;
                    background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
                    color: white;
                    padding: 12px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                    z-index: 9999;
                    font-weight: 500;
                    max-width: 300px;
                    animation: slideInLeft 0.3s ease-out;
                ">
                    ${message}
                </div>
            `);

            // Add to body
            $('body').append(toast);

            // Remove after 3 seconds
            setTimeout(function() {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
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
</body>
</html>
