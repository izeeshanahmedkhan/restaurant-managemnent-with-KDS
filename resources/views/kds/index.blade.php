<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display System - {{ config('app.name', 'Restaurant') }}</title>
    
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
                <h1 class="kds-sidebar__title">Items Board</h1>
            </div>
            
            <!-- Items Board Section -->
            <div class="kds-sidebar__section">
                <div class="kds-sidebar__section-header">
                    <h3 class="kds-sidebar__section-title">
                        <i class="fas fa-list"></i>
                        Today's Items
                    </h3>
                    <button class="kds-sidebar__toggle" id="items-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="kds-sidebar__items-list" id="items-board-list">
                    <div class="kds-sidebar__loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        Loading items...
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
                    <p class="kds-header__subtitle">Real-time Order Management</p>
                </div>
            </div>
            
            <div class="kds-header__controls">
                <div class="kds-header__search">
                    <i class="fas fa-search kds-header__search-icon"></i>
                    <input type="text" 
                           id="search-input" 
                           class="kds-header__search-input" 
                           placeholder="Search orders, customers, or items..."
                           aria-label="Search orders">
                </div>
                
                <select id="branch-selector" class="kds-header__branch-select" aria-label="Select branch">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" 
                                {{ $selectedBranch && $selectedBranch->id == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                
                <div class="kds-header__clock" id="clock-display">
                    {{ now()->format('h:i:s A') }}
                </div>
                
                <a href="{{ route('admin.dashboard') }}" class="kds-header__btn kds-header__btn--primary">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
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
        
        // Initialize KDS when document is ready
        $(document).ready(function() {
            console.log('KDS: Page loaded, initializing...');
            
            // Update initial statistics
            if (typeof window.KDS !== 'undefined') {
                window.KDS.updateStatistics();
            }
        });
    </script>
</body>
</html>
