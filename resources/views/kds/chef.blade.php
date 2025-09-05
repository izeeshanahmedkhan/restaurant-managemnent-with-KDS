<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display System - Chef Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- KDS Styles -->
    <link rel="stylesheet" href="{{ asset('css/kds.css') }}">
    
    <style>
        /* Additional custom styles if needed */
        .kds-body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .kds-header__branch-info {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #fbbf24;
            border-radius: 12px;
            padding: 12px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 200px;
            font-weight: 600;
            color: #1e3a8a;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        
        .kds-header__branch-label {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        .kds-header__branch-name {
            font-size: 1.1rem;
            color: #1e3a8a;
            font-weight: 700;
        }
    </style>
</head>
<body class="kds-body">
    <div class="kds-container">
        <!-- Items Board Sidebar -->
        <aside class="kds-sidebar">
            <div class="kds-sidebar__header">
                <div class="kds-sidebar__logo">
                    <i class="fas fa-utensils"></i>
                </div>
                <h1 class="kds-sidebar__title">Active Dishes</h1>
            </div>
            
            <!-- Items Board Section -->
            <div class="kds-sidebar__section">
                <div class="kds-sidebar__section-header">
                    <h3 class="kds-sidebar__section-title">
                        <i class="fas fa-fire"></i>
                        Pending & Cooking
                    </h3>
                    <button class="kds-sidebar__toggle" id="items-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="kds-sidebar__items-list" id="items-board-list">
                    <div class="kds-sidebar__loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        Loading active dishes...
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Header -->
        <header class="kds-header">
            <div class="kds-header__left">
                <div>
                    <h1 class="kds-header__title">
                        <i class="fas fa-utensils"></i>
                        Kitchen Display System
                    </h1>
                    <p class="kds-header__subtitle">Chef Dashboard - Real-time Order Management</p>
                </div>
            </div>
            
            <div class="kds-header__controls">
                <!-- Fullscreen Button -->
                <button class="kds-fullscreen-btn" id="fullscreen-btn" title="Toggle Fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
                
                <div class="kds-header__search">
                    <i class="fas fa-search kds-header__search-icon"></i>
                    <input type="text" 
                           id="search-input" 
                           class="kds-header__search-input" 
                           placeholder="Search orders, customers, or items..."
                           aria-label="Search orders">
                </div>
                
                @if($selectedBranch)
                    <div class="kds-header__branch-info">
                        <div class="kds-header__branch-label">Branch:</div>
                        <div class="kds-header__branch-name">{{ $selectedBranch->name }}</div>
                    </div>
                    <input type="hidden" id="branch-selector" value="{{ $selectedBranch->id }}">
                @else
                    <div class="kds-header__branch-info">
                        <div class="kds-header__branch-label text-danger">No Branch Assigned</div>
                    </div>
                @endif
                
                <div class="kds-header__clock" id="clock-display">
                    {{ now()->format('h:i:s A') }}
                </div>
                
                <a href="{{ route('chef.auth.logout') }}" class="kds-header__btn kds-header__btn--danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="kds-main">
            <!-- Statistics -->
            <section class="kds-stats">
                <div class="kds-stat-card">
                    <div class="kds-stat-number kds-stat-number--new" id="confirmed-count">0</div>
                    <div class="kds-stat-label">New Orders</div>
                </div>
                <div class="kds-stat-card">
                    <div class="kds-stat-number kds-stat-number--cooking" id="cooking-count">0</div>
                    <div class="kds-stat-label">Cooking</div>
                </div>
                <div class="kds-stat-card">
                    <div class="kds-stat-number kds-stat-number--done" id="done-count">0</div>
                    <div class="kds-stat-label">Done</div>
                </div>
            </section>
            
            <!-- KDS Grid -->
            <section class="kds-grid">
                <!-- New Orders Column -->
                <div class="kds-col" id="col-new">
                    <div class="kds-col__header">
                        <h2 class="kds-col__title">
                            <i class="fas fa-plus-circle"></i>
                            New Orders
                            <span class="kds-col__count" id="col-new-count">0</span>
                        </h2>
                        <button class="kds-col__sort-btn" data-column="new" title="Toggle sort order">
                            <i class="fas fa-sort-amount-down"></i>
                            Latest
                        </button>
                    </div>
                    <div class="kds-list" id="new-orders-list">
                        @foreach($newOrders as $order)
                            @include('kds._order-card', ['order' => $order])
                        @endforeach
                        
                        @if($newOrders->isEmpty())
                            <div class="kds-empty-state">
                                <i class="fas fa-inbox kds-empty-icon"></i>
                                <p>No new orders</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Cooking Column -->
                <div class="kds-col" id="col-cooking">
                    <div class="kds-col__header">
                        <h2 class="kds-col__title">
                            <i class="fas fa-fire"></i>
                            Cooking
                            <span class="kds-col__count" id="col-cooking-count">0</span>
                        </h2>
                        <button class="kds-col__sort-btn" data-column="cooking" title="Toggle sort order">
                            <i class="fas fa-sort-amount-down"></i>
                            Latest
                        </button>
                    </div>
                    <div class="kds-list" id="cooking-orders-list">
                        @foreach($cookingOrders as $order)
                            @include('kds._order-card', ['order' => $order])
                        @endforeach
                        
                        @if($cookingOrders->isEmpty())
                            <div class="kds-empty-state">
                                <i class="fas fa-fire kds-empty-icon"></i>
                                <p>No orders cooking</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Done Column -->
                <div class="kds-col" id="col-done">
                    <div class="kds-col__header">
                        <h2 class="kds-col__title">
                            <i class="fas fa-check-circle"></i>
                            Done
                            <span class="kds-col__count" id="col-done-count">0</span>
                        </h2>
                        <button class="kds-col__sort-btn" data-column="done" title="Toggle sort order">
                            <i class="fas fa-sort-amount-down"></i>
                            Latest
                        </button>
                    </div>
                    <div class="kds-list" id="done-orders-list">
                        @foreach($doneOrders as $order)
                            @include('kds._order-card', ['order' => $order])
                        @endforeach
                        
                        @if($doneOrders->isEmpty())
                            <div class="kds-empty-state">
                                <i class="fas fa-check-circle kds-empty-icon"></i>
                                <p>No completed orders</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <!-- Order Details Modal -->
    <div id="order-modal" class="kds-modal">
        <div class="kds-modal__overlay"></div>
        <div class="kds-modal__content">
            <div class="kds-modal__header">
                <h2 class="kds-modal__title">Order Details</h2>
                <button class="kds-modal__close" id="modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="kds-modal__body" id="modal-body">
                <!-- Order details will be populated here -->
            </div>
        </div>
    </div>

    <!-- Dish Modal -->
    <div id="dish-modal" class="kds-dish-modal">
        <div class="kds-dish-modal__overlay" id="dish-modal-close"></div>
        <div class="kds-dish-modal__content">
            <div class="kds-dish-modal__header">
                <h2 class="kds-dish-modal__title">
                    <i class="fas fa-utensils"></i>
                    Dish Details
                </h2>
                <button class="kds-dish-modal__close" id="dish-modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="kds-dish-modal__body" id="dish-modal-body">
                <!-- Dish details will be populated here -->
            </div>
        </div>
    </div>
    
    <!-- Empty State Styles -->
    <style>
        .kds-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: var(--space-12);
            text-align: center;
            color: var(--muted);
        }
        
        .kds-empty-icon {
            font-size: 3rem;
            margin-bottom: var(--space-4);
            opacity: 0.5;
        }
        
        .kds-empty-state p {
            font-size: var(--font-size-lg);
            font-weight: 500;
            margin: 0;
        }
    </style>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/kds.js') }}"></script>
    <script src="{{ asset('js/kds-items-board.js') }}"></script>
    
    <script>
        // Initialize CSRF token for AJAX requests
        window.csrf_token = '{{ csrf_token() }}';
        
        // Override API endpoints for chef
        window.KDS_CONFIG = {
            apiEndpoints: {
                orders: '/chef/kds/orders',
                updateStatus: '/chef/kds/orders/{id}/status'
            }
        };
        
        // Initialize KDS when document is ready
        $(document).ready(function() {
            console.log('Chef KDS: Page loaded, initializing...');
            
            // Override the fetchUpdates function to use chef endpoints
            const originalFetchUpdates = window.KDS.fetchUpdates;
            window.KDS.fetchUpdates = function() {
                const branchId = $('#branch-selector').val();
                if (!branchId) {
                    console.warn('Chef KDS: No branch selected');
                    return;
                }

                $.ajax({
                    url: window.KDS_CONFIG.apiEndpoints.orders,
                    method: 'GET',
                    data: {
                        since: window.KDS.lastSeen || new Date().toISOString(),
                        branch_id: branchId
                    },
                    dataType: 'json',
                    timeout: 10000
                })
                .done(function(response) {
                    if (!response || !response.orders) {
                        console.warn('Chef KDS: Invalid response format');
                        return;
                    }

                    window.KDS.lastSeen = response.now || new Date().toISOString();
                    window.KDS.processOrderUpdates(response.orders);
                })
                .fail(function(xhr, status, error) {
                    console.error('Chef KDS: Failed to fetch updates:', status, error);
                    window.KDS.showError('Connection error. Retrying...');
                });
            };
            
            // Override updateOrderStatus function
            const originalUpdateOrderStatus = window.KDS.updateOrderStatus;
            window.KDS.updateOrderStatus = function(orderId, newStatus) {
                const $button = $(`.btn-mark-processing[data-id="${orderId}"], .btn-mark-done[data-id="${orderId}"], .btn-reopen[data-id="${orderId}"]`);
                const originalText = $button.html();
                
                // Show loading state
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

                $.ajax({
                    url: window.KDS_CONFIG.apiEndpoints.updateStatus.replace('{id}', orderId),
                    method: 'PUT',
                    data: {
                        status: newStatus,
                        _token: window.csrf_token
                    },
                    dataType: 'json',
                    timeout: 10000
                })
                .done(function(response) {
                    if (response.ok) {
                        console.log('Chef KDS: Order', orderId, 'status updated to', newStatus);
                        // Refresh data
                        window.KDS.fetchUpdates();
                    } else {
                        throw new Error(response.message || 'Update failed');
                    }
                })
                .fail(function(xhr, status, error) {
                    console.error('Chef KDS: Failed to update order status:', status, error);
                    window.KDS.showError('Failed to update order status');
                    // Restore button
                    $button.prop('disabled', false).html(originalText);
                });
            };
            
            // Update initial statistics
            if (typeof window.KDS !== 'undefined') {
                window.KDS.updateStatistics();
            }
        });
    </script>
</body>
</html>
