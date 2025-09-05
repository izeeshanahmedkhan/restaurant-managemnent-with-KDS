<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kitchen Display System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ff6b6b',
                        secondary: '#ee5a24',
                        success: '#28a745',
                        warning: '#ffc107',
                        info: '#17a2b8',
                        dark: '#343a40',
                        light: '#f8f9fa'
                    }
                }
            }
        }
    </script>
    <style>
        /* Modern KDS Styling */
        .kds-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .kds-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid #fbbf24;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .kds-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }
        
        .kds-header-content {
            position: relative;
            z-index: 1;
        }
        
        .kds-title {
            color: white;
            font-weight: 800;
            font-size: 1.75rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.025em;
        }
        
        .kds-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 4px;
        }
        
        .kds-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .kds-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        .kds-column {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .kds-order-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .kds-order-card:hover {
            transform: translateX(4px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .kds-status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .kds-status-pending { background: #fef3c7; color: #92400e; }
        .kds-status-confirmed { background: #dbeafe; color: #1e40af; }
        .kds-status-processing { background: #fde68a; color: #b45309; }
        .kds-status-cooking { background: #fed7d7; color: #c53030; }
        .kds-status-done { background: #d1fae5; color: #065f46; }
        
        .kds-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .kds-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }
        
        .kds-search {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #fbbf24;
            border-radius: 16px;
            padding: 14px 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            font-weight: 500;
        }
        
        .kds-search:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.2);
            background: white;
            transform: translateY(-1px);
        }
        
        .kds-search::placeholder {
            color: #6b7280;
            font-weight: 500;
        }
        
        .kds-search-container {
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 4px;
            backdrop-filter: blur(10px);
        }
        
        .kds-search-icon {
            color: #fbbf24;
            font-size: 1.1rem;
        }
        
        .kds-branch-selector {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #fbbf24;
            border-radius: 16px;
            padding: 14px 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            font-weight: 600;
            color: #1e3a8a;
        }
        
        .kds-branch-selector:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.2);
            background: white;
            transform: translateY(-1px);
        }
        
        .kds-search-btn {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            color: white;
            font-weight: 700;
            box-shadow: 0 4px 16px rgba(251, 191, 36, 0.3);
            transition: all 0.3s ease;
        }
        
        .kds-search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
        }
        
        .kds-user-info {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 8px 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .kds-user-name {
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
        }
        
        .kds-user-role {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .kds-logout-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 16px;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.3);
            transition: all 0.3s ease;
        }
        
        .kds-logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }
        
        .kds-stats-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        
        .kds-stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .kds-stats-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }
        
        .kds-empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }
        
        .kds-empty-icon {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        
        .kds-item-summary {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        
        .kds-item-name {
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        
        .kds-item-quantity {
            background: #667eea;
            color: white;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body class="kds-container">
    <!-- Top Navigation -->
    <nav class="kds-header sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="kds-header-content flex justify-between items-center h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="kds-title">
                            <i class="fas fa-utensils mr-3 text-yellow-300"></i>Kitchen Display System
                        </h1>
                        <p class="kds-subtitle">Real-time Order Management</p>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <!-- Search Container -->
                    <div class="kds-search-container">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search kds-search-icon"></i>
                            </div>
                            <input type="text" id="search-input" class="kds-search pl-12 pr-4 py-2 min-w-[300px]" placeholder="Search orders, customers, or items...">
                        </div>
                    </div>
                    
                    <!-- Branch Selector -->
                    <select id="branch-selector" class="kds-branch-selector min-w-[200px]">
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedBranch && $selectedBranch->id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <!-- User Info & Actions -->
                    <div class="flex items-center space-x-4">
                        <button onclick="testSweetAlert()" class="kds-search-btn">
                            <i class="fas fa-bell mr-2"></i>Test
                        </button>
                        <div class="kds-user-info">
                            <div class="kds-user-name">{{ Auth::guard('chef')->user()->f_name ?? 'Chef' }}</div>
                            <div class="kds-user-role">Kitchen Staff</div>
                        </div>
                        <a href="{{ route('chef.auth.logout') }}" class="kds-logout-btn flex items-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Items Summary Sidebar -->
            <div class="xl:col-span-1">
                <div class="kds-card p-6 h-fit">
                    <h3 class="text-xl font-bold text-dark mb-6 text-center">
                        <i class="fas fa-list mr-2"></i>Items Summary
                    </h3>
                    <div id="items-summary">
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-spinner fa-spin text-4xl mb-2"></i>
                            <p>Loading items...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KDS Columns -->
            <div class="xl:col-span-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- New Orders Column -->
                    <div class="kds-card p-6 min-h-[600px]">
                        <div class="text-center mb-6 pb-4 border-b-4 border-info">
                            <h2 class="text-xl font-bold text-info mb-2">
                                <i class="fas fa-clock mr-2"></i>New Orders
                            </h2>
                            <div class="text-3xl font-bold text-info" id="new-orders-count">0</div>
                        </div>
                        <div id="confirmed-orders" class="space-y-4">
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-spinner fa-spin text-4xl mb-2"></i>
                                <p>Loading orders...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cooking Column -->
                    <div class="kds-card p-6 min-h-[600px]">
                        <div class="text-center mb-6 pb-4 border-b-4 border-warning">
                            <h2 class="text-xl font-bold text-warning mb-2">
                                <i class="fas fa-fire mr-2"></i>Cooking
                            </h2>
                            <div class="text-3xl font-bold text-warning" id="cooking-count">0</div>
                        </div>
                        <div id="cooking-orders" class="space-y-4">
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-utensils text-4xl mb-2"></i>
                                <p>No orders cooking</p>
                            </div>
                        </div>
                    </div>

                    <!-- Done Column -->
                    <div class="kds-card p-6 min-h-[600px]">
                        <div class="text-center mb-6 pb-4 border-b-4 border-success">
                            <h2 class="text-xl font-bold text-success mb-2">
                                <i class="fas fa-check-circle mr-2"></i>Done
                            </h2>
                            <div class="text-3xl font-bold text-success" id="done-count">0</div>
                        </div>
                        <div id="done-orders" class="space-y-4">
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-check text-4xl mb-2"></i>
                                <p>No completed orders</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <script>
        // Test function for SweetAlert2
        function testSweetAlert() {
            Swal.fire({
                title: 'Test SweetAlert2',
                text: 'This is a test to verify SweetAlert2 is working properly.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        $(document).ready(function() {
            let currentBranch = $('#branch-selector').val();
            let searchTimeout;

            // Initialize KDS
            if (currentBranch) {
                loadOrders(currentBranch);
                loadItemsSummary(currentBranch);
            }

            // Branch selector change
            $('#branch-selector').on('change', function() {
                currentBranch = $(this).val();
                if (currentBranch) {
                    loadOrders(currentBranch);
                    loadItemsSummary(currentBranch);
                } else {
                    clearOrders();
                }
            });

            // Search functionality
            $('#search-input').on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val();
                
                if (query.length > 2) {
                    searchTimeout = setTimeout(() => {
                        searchOrders(currentBranch, query);
                    }, 500);
                } else if (query.length === 0) {
                    loadOrders(currentBranch);
                }
            });

            // Auto-refresh every 5 seconds
            setInterval(() => {
                if (currentBranch) {
                    loadOrders(currentBranch);
                    loadItemsSummary(currentBranch);
                }
            }, 5000);

            // Load orders
            function loadOrders(branchId) {
                if (!branchId) return;

                $.ajax({
                    url: '{{ route("chef.kds.orders") }}',
                    method: 'GET',
                    data: { branch_id: branchId },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(response) {
                        console.log('Orders loaded successfully:', response);
                        displayOrders(response.orders);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading orders:', error);
                        console.error('Response:', xhr.responseText);
                    }
                });
            }

            // Load items summary
            function loadItemsSummary(branchId) {
                if (!branchId) return;

                $.ajax({
                    url: '{{ route("chef.kds.items-summary") }}',
                    method: 'GET',
                    data: { branch_id: branchId },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(response) {
                        console.log('Items summary loaded successfully:', response);
                        displayItemsSummary(response.items);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading items summary:', error);
                    }
                });
            }

            // Search orders
            function searchOrders(branchId, query) {
                if (!branchId || !query) return;

                $.ajax({
                    url: '{{ route("chef.kds.search") }}',
                    method: 'GET',
                    data: { 
                        branch_id: branchId,
                        query: query
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(response) {
                        console.log('Search results:', response);
                        displayOrders(response.orders);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error searching orders:', error);
                    }
                });
            }

            // Display orders in columns
            function displayOrders(orders) {
                const confirmedOrders = orders.filter(order => 
                    ['pending', 'confirmed', 'processing'].includes(order.status)
                );
                const cookingOrders = orders.filter(order => order.status === 'cooking');
                const doneOrders = orders.filter(order => order.status === 'done');
                
                $('#confirmed-orders').html(renderOrderColumn(confirmedOrders));
                $('#cooking-orders').html(renderOrderColumn(cookingOrders));
                $('#done-orders').html(renderOrderColumn(doneOrders));
                
                updateStats(confirmedOrders.length, cookingOrders.length, doneOrders.length);
            }

            // Render order column
            function renderOrderColumn(orders) {
                if (orders.length === 0) {
                    return `
                        <div class="kds-empty-state">
                            <div class="kds-empty-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <p>No orders</p>
                        </div>
                    `;
                }

                return orders.map(order => {
                    const nextStatus = getNextStatus(order.status);
                    return `
                        <div class="kds-order-card p-4">
                            <div class="flex justify-between items-center mb-3">
                                <div class="font-bold text-lg text-gray-800">#${order.token_number}</div>
                                <div class="text-sm text-gray-500">${order.created_at}</div>
                            </div>
                            <div class="text-gray-700 font-medium mb-3">${order.customer_name}</div>
                            <div class="space-y-2 mb-4">
                                ${order.items.map(item => `
                                    <div class="flex justify-between items-center py-1">
                                        <span class="text-gray-700">${item.name}</span>
                                        <span class="bg-primary text-white px-2 py-1 rounded-full text-sm font-bold">${item.quantity}</span>
                                    </div>
                                `).join('')}
                            </div>
                            ${nextStatus ? `
                                <button class="kds-button w-full" onclick="updateOrderStatus(${order.id}, '${nextStatus}')">
                                    Mark ${nextStatus.charAt(0).toUpperCase() + nextStatus.slice(1)}
                                </button>
                            ` : ''}
                        </div>
                    `;
                }).join('');
            }

            // Display items summary
            function displayItemsSummary(items) {
                if (items.length === 0) {
                    $('#items-summary').html(`
                        <div class="text-center text-gray-500 py-4">
                            <i class="fas fa-list text-2xl mb-2"></i>
                            <p>No items</p>
                        </div>
                    `);
                    return;
                }

                const itemsHtml = items.map(item => `
                    <div class="kds-item-summary">
                        <div class="kds-item-name">${item.name}</div>
                        <div class="flex justify-center mt-2">
                            <span class="kds-item-quantity">${item.total_quantity}</span>
                        </div>
                    </div>
                `).join('');
                
                $('#items-summary').html(itemsHtml);
            }

            // Update column counts
            function updateStats(newCount, cookingCount, doneCount) {
                $('#new-orders-count').text(newCount);
                $('#cooking-count').text(cookingCount);
                $('#done-count').text(doneCount);
            }

            // Clear all orders
            function clearOrders() {
                $('#confirmed-orders, #cooking-orders, #done-orders').html(`
                    <div class="text-center text-gray-500 py-8">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>No orders</p>
                    </div>
                `);
                $('#items-summary').html(`
                    <div class="text-center text-gray-500 py-4">
                        <i class="fas fa-list text-2xl mb-2"></i>
                        <p>No items</p>
                    </div>
                `);
                updateStats(0, 0, 0);
            }

            // Get status color class
            function getStatusColor(status) {
                const colors = {
                    'pending': 'info',
                    'confirmed': 'info',
                    'processing': 'info',
                    'cooking': 'warning',
                    'done': 'success'
                };
                return colors[status] || 'gray';
            }

            // Get next status
            function getNextStatus(currentStatus) {
                const statusFlow = {
                    'pending': 'confirmed',
                    'confirmed': 'processing',
                    'processing': 'cooking',
                    'cooking': 'done',
                    'done': null
                };
                return statusFlow[currentStatus];
            }

            // Update order status
            function updateOrderStatus(orderId, newStatus) {
                const statusLabels = {
                    'confirmed': 'Confirmed',
                    'processing': 'Processing',
                    'cooking': 'Cooking',
                    'done': 'Done'
                };

                Swal.fire({
                    title: 'Update Order Status',
                    text: `Are you sure you want to change this order status to ${statusLabels[newStatus]}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Update Status',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Updating Status...',
                            text: 'Please wait while we update the order status.',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });

                        console.log('Sending AJAX request:', {
                            order_id: orderId,
                            status: newStatus,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        });

                        $.ajax({
                            url: '{{ route("chef.kds.update-status") }}',
                            method: 'POST',
                            data: {
                                order_id: orderId,
                                status: newStatus,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            success: function(response) {
                                console.log('Status updated successfully:', response);
                                
                                // Show success message
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Order status has been updated successfully.',
                                    icon: 'success',
                                    timer: 2000
                                });

                                // Reload orders
                                const branchId = $('#branch-selector').val();
                                if (branchId) {
                                    loadOrders(branchId);
                                    loadItemsSummary(branchId);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error updating status:', {
                                    xhr: xhr,
                                    status: status,
                                    error: error,
                                    responseText: xhr.responseText,
                                    statusCode: xhr.status
                                });
                                
                                // Show error message
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Failed to update order status. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            }

            // Make updateOrderStatus globally accessible
            window.updateOrderStatus = updateOrderStatus;
        });
    </script>
</body>
</html>
