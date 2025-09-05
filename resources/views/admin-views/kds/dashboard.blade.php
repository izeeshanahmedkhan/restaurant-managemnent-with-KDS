@extends('layouts.admin.app')

@section('title', 'Kitchen Display System')

@push('css_or_js')
<script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
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
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
    }
    
    .kds-search:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
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
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="kds-header">
                <div class="kds-header-content">
                    <div class="row align-items-center py-4">
                        <div class="col-md-6">
                            <h3 class="kds-title mb-0">
                                <i class="fas fa-utensils mr-3 text-warning"></i>Kitchen Display System
                            </h3>
                            <p class="kds-subtitle mb-0">Real-time Order Management</p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="kds-search-container me-3">
                                    <div class="d-flex">
                                        <div class="position-relative">
                                            <i class="fas fa-search kds-search-icon position-absolute" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                                            <input type="text" id="search-input" class="kds-search ps-5 pe-3" placeholder="Search orders, customers, or items..." style="width: 300px;">
                                        </div>
                                        <button type="button" id="search-btn" class="kds-search-btn ms-2">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <select id="branch-selector" class="kds-branch-selector me-3" style="width: 200px;">
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <div class="kds-user-info me-3">
                                    <div class="kds-user-name">Admin</div>
                                    <div class="kds-user-role">Master Admin</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="height: calc(100vh - 120px); overflow: hidden;">
        <!-- Items Sidebar -->
        <div class="col-md-3 kds-card" style="height: 100%; overflow-y: auto;">
            <div class="p-3">
                <h5 class="mb-3 text-dark">Items Summary</h5>
                <div id="items-summary">
                    <div class="text-center py-4 text-muted">
                        <i class="tio-refresh fa-spin text-2xl mb-2"></i>
                        <div>Loading items...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main KDS Area -->
        <div class="col-md-9" style="height: 100%; overflow: hidden;">
            <!-- Stats Cards -->
            <div class="row mb-3 p-3">
                <div class="col-md-4">
                    <div class="kds-stats-card">
                        <div class="kds-stats-number text-info" id="confirmed-count">0</div>
                        <div class="kds-stats-label">New Orders</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="kds-stats-card">
                        <div class="kds-stats-number text-warning" id="cooking-count">0</div>
                        <div class="kds-stats-label">Cooking</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="kds-stats-card">
                        <div class="kds-stats-number text-success" id="done-count">0</div>
                        <div class="kds-stats-label">Done</div>
                    </div>
                </div>
            </div>

            <!-- KDS Columns -->
            <div class="d-flex gap-3 p-3" style="height: calc(100% - 80px);">
                <!-- Confirmed Column -->
                <div class="flex-fill kds-card overflow-hidden d-flex flex-column">
                    <div class="bg-info text-white p-3 text-center fw-bold">
                        <i class="tio-checkmark-circle me-2"></i> New Orders
                    </div>
                    <div class="flex-grow-1 p-3 overflow-auto" id="confirmed-orders">
                        <div class="text-center py-4 text-muted">
                            <i class="tio-refresh fa-spin text-2xl mb-2"></i>
                            <div>Loading orders...</div>
                        </div>
                    </div>
                </div>

                <!-- Cooking Column -->
                <div class="flex-fill kds-card overflow-hidden d-flex flex-column">
                    <div class="bg-warning text-white p-3 text-center fw-bold">
                        <i class="tio-time me-2"></i> Cooking
                    </div>
                    <div class="flex-grow-1 p-3 overflow-auto" id="cooking-orders">
                        <div class="text-center py-4 text-muted">
                            <i class="tio-refresh fa-spin text-2xl mb-2"></i>
                            <div>Loading orders...</div>
                        </div>
                    </div>
                </div>

                <!-- Done Column -->
                <div class="flex-fill kds-card overflow-hidden d-flex flex-column">
                    <div class="bg-success text-white p-3 text-center fw-bold">
                        <i class="tio-checkmark me-2"></i> Done
                    </div>
                    <div class="flex-grow-1 p-3 overflow-auto" id="done-orders">
                        <div class="text-center py-4 text-muted">
                            <i class="tio-refresh fa-spin text-2xl mb-2"></i>
                            <div>Loading orders...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
// Ensure jQuery is loaded before running KDS code
document.addEventListener('DOMContentLoaded', function() {
    // Wait for jQuery to be available
    function waitForJQuery() {
        if (typeof $ !== 'undefined') {
            initKDS();
        } else {
            setTimeout(waitForJQuery, 100);
        }
    }
    
    waitForJQuery();
});

function initKDS() {
    $(document).ready(function() {
    let currentBranchId = null;
    let searchQuery = '';
    let pollingInterval = null;
    
    // Initialize KDS
    function initKDS() {
        console.log('Initializing KDS...');
        console.log('jQuery version:', $.fn.jquery);
        console.log('Available branches:', $('#branch-selector option').length);
        
        // Set default branch if only one available
        if ($('#branch-selector option').length === 2) {
            const selectedBranch = $('#branch-selector option:eq(1)').val();
            console.log('Auto-selecting branch:', selectedBranch);
            $('#branch-selector').val(selectedBranch);
            currentBranchId = selectedBranch;
            loadOrders();
            loadItemsSummary();
            startPolling();
        } else {
            console.log('Multiple branches available, waiting for user selection');
        }
    }
    
    // Load orders
    function loadOrders() {
        if (!currentBranchId) {
            console.log('No branch selected, skipping order load');
            return;
        }
        
        console.log('Loading orders for branch:', currentBranchId);
        
        $.ajax({
            url: '{{ route("admin.kds.orders") }}',
            method: 'GET',
            data: {
                branch_id: currentBranchId,
                search: searchQuery
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            success: function(response) {
                console.log('Orders loaded successfully:', response);
                displayOrders(response.orders);
                updateStats(response.orders);
            },
            error: function(xhr) {
                console.error('Error loading orders:', xhr);
                console.error('Response text:', xhr.responseText);
                console.error('Status:', xhr.status);
                if (xhr.status === 401 || xhr.status === 403) {
                    showError('Authentication required. Please refresh the page and login again.');
                } else {
                    showError('Failed to load orders: ' + xhr.responseText);
                }
            }
        });
    }
    
    // Load items summary
    function loadItemsSummary() {
        if (!currentBranchId) {
            console.log('No branch selected, skipping items summary load');
            return;
        }
        
        console.log('Loading items summary for branch:', currentBranchId);
        
        $.ajax({
            url: '{{ route("admin.kds.items-summary") }}',
            method: 'GET',
            data: {
                branch_id: currentBranchId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            success: function(response) {
                console.log('Items summary loaded successfully:', response);
                displayItemsSummary(response.items);
            },
            error: function(xhr) {
                console.error('Error loading items summary:', xhr);
                console.error('Response text:', xhr.responseText);
                console.error('Status:', xhr.status);
            }
        });
    }
    
    // Display orders in columns
    function displayOrders(orders) {
        // Group orders by status for different columns
        const confirmedOrders = orders.filter(order => 
            ['pending', 'confirmed', 'processing'].includes(order.status)
        );
        const cookingOrders = orders.filter(order => order.status === 'cooking');
        const doneOrders = orders.filter(order => order.status === 'done');
        
        $('#confirmed-orders').html(renderOrderColumn(confirmedOrders));
        $('#cooking-orders').html(renderOrderColumn(cookingOrders));
        $('#done-orders').html(renderOrderColumn(doneOrders));
    }
    
    // Render order column
    function renderOrderColumn(orders) {
        if (orders.length === 0) {
            return `
                <div class="kds-empty-state">
                    <div class="kds-empty-icon">
                        <i class="tio-inbox"></i>
                    </div>
                    <div>No orders</div>
                </div>
            `;
        }
        
        return orders.map(order => renderOrderCard(order)).join('');
    }
    
    // Render individual order card
    function renderOrderCard(order) {
        const statusClass = `kds-status-${order.status}`;
        const statusText = order.status.charAt(0).toUpperCase() + order.status.slice(1);
        const nextStatus = getNextStatus(order.status);
        
        return `
            <div class="kds-order-card" data-order-id="${order.id}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="tio-chef-hat me-2 text-primary"></i>
                        <strong>${order.order_number}</strong>
                    </div>
                    <span class="badge bg-${getStatusBadgeColor(order.status)}">${statusText}</span>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Token No:</strong> ${order.token_number}
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">${order.created_at}</small>
                    </div>
                    <div class="mb-3">
                        ${order.items.map(item => `
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <div>
                                    <div class="fw-medium">${item.quantity}x ${item.name}</div>
                                    <small class="text-muted">Size: ${item.size}</small>
                                </div>
                                <span class="badge bg-primary">${item.quantity}</span>
                            </div>
                        `).join('')}
                    </div>
                    ${nextStatus ? `
                        <button class="kds-button w-100" onclick="updateOrderStatus(${order.id}, '${nextStatus}')">
                            <i class="tio-checkmark-circle me-1"></i>
                            Mark ${nextStatus.charAt(0).toUpperCase() + nextStatus.slice(1)}
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    // Display items summary
    function displayItemsSummary(items) {
        if (Object.keys(items).length === 0) {
            $('#items-summary').html(`
                <div class="kds-empty-state">
                    <i class="tio-inbox"></i>
                    <div>No active items</div>
                </div>
            `);
            return;
        }
        
        const itemsHtml = Object.entries(items).map(([name, quantity]) => `
            <div class="kds-item-summary">
                <div class="kds-item-summary-name">${name}</div>
                <div class="kds-item-summary-quantity">${quantity}</div>
            </div>
        `).join('');
        
        $('#items-summary').html(itemsHtml);
    }
    
    // Update statistics
    function updateStats(orders) {
        const confirmedCount = orders.filter(order => 
            ['pending', 'confirmed', 'processing'].includes(order.status)
        ).length;
        const cookingCount = orders.filter(order => order.status === 'cooking').length;
        const doneCount = orders.filter(order => order.status === 'done').length;
        
        $('#confirmed-count').text(confirmedCount);
        $('#cooking-count').text(cookingCount);
        $('#done-count').text(doneCount);
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
    
    // Get status badge color
    function getStatusBadgeColor(status) {
        const colors = {
            'pending': 'info',
            'confirmed': 'info',
            'processing': 'warning',
            'cooking': 'warning',
            'done': 'success'
        };
        return colors[status] || 'secondary';
    }
    
    // Update order status
    window.updateOrderStatus = function(orderId, newStatus) {
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

                console.log('Admin KDS: Sending AJAX request:', {
                    order_id: orderId,
                    status: newStatus,
                    _token: '{{ csrf_token() }}',
                    url: '{{ route("admin.kds.update-status") }}'
                });

                $.ajax({
                    url: '{{ route("admin.kds.update-status") }}',
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: 'Order status has been updated successfully.',
                                icon: 'success',
                                timer: 2000
                            });

                            loadOrders();
                            loadItemsSummary();
                        } else {
                            // Show error message
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to update order status.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error updating order status:', xhr);
                        console.error('Response text:', xhr.responseText);
                        console.error('Status:', xhr.status);
                        
                        let errorMessage = 'Failed to update order status.';
                        if (xhr.status === 401 || xhr.status === 403) {
                            errorMessage = 'Authentication required. Please refresh the page and login again.';
                        }
                        
                        // Show error message
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    };
    
    // Start polling
    function startPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        
        pollingInterval = setInterval(function() {
            loadOrders();
            loadItemsSummary();
        }, 5000);
    }
    
    // Stop polling
    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }
    
    // Event handlers
    $('#branch-selector').on('change', function() {
        currentBranchId = $(this).val();
        if (currentBranchId) {
            loadOrders();
            loadItemsSummary();
            startPolling();
        } else {
            stopPolling();
        }
    });
    
    $('#search-btn').on('click', function() {
        searchQuery = $('#search-input').val();
        loadOrders();
    });
    
    $('#search-input').on('keypress', function(e) {
        if (e.which === 13) {
            searchQuery = $(this).val();
            loadOrders();
        }
    });
    
    // Utility functions
    function showSuccess(message) {
        toastr.success(message);
    }
    
    function showError(message) {
        toastr.error(message);
    }
    
    // Initialize KDS
    initKDS();
    
    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        stopPolling();
    });
    });
}
</script>
@endpush
