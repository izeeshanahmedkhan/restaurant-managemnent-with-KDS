/**
 * KDS Items Board JavaScript
 * Handles the Items Board sidebar functionality
 */

(function($) {
    'use strict';

    // Configuration
    const CONFIG = {
        pollingInterval: 4000, // 4 seconds - same as orders
        apiEndpoints: {
            itemsBoard: '/admin/kds/items-board',
            orders: '/admin/kds/orders'
        }
    };

    // State
    let currentFilter = null;
    let itemsData = [];
    let pollingTimer = null;

    /**
     * Initialize Items Board
     */
    function init() {
        
        // Detect user type and set API endpoints
        detectUserType();
        
        // Load initial data
        loadItemsBoard();
        
        // Setup event handlers
        setupEventHandlers();
        
        // Start polling
        startPolling();
        
    }

    /**
     * Detect user type and set appropriate API endpoints
     */
    function detectUserType() {
        const path = window.location.pathname;
        
        if (path.includes('/chef/')) {
            CONFIG.apiEndpoints = {
                itemsBoard: '/chef/kds/items-board',
                orders: '/chef/kds/orders'
            };
        } else if (path.includes('/branch/')) {
            CONFIG.apiEndpoints = {
                itemsBoard: '/branch/kds/items-board',
                orders: '/branch/kds/orders'
            };
        } else if (path.includes('/admin/')) {
            CONFIG.apiEndpoints = {
                itemsBoard: '/admin/kds/items-board',
                orders: '/admin/kds/orders'
            };
        }
        
    }

    /**
     * Load items board data
     */
    function loadItemsBoard() {
        const branchId = $('#branch-selector').val();
        
        if (!branchId) {
            console.warn('KDS Items Board: No branch selected');
            showItemsError('Please select a branch');
            return;
        }

        $.ajax({
            url: CONFIG.apiEndpoints.itemsBoard,
            method: 'GET',
            data: {
                range: 'today',
                branch_id: branchId
            },
            dataType: 'json',
            timeout: 10000
        })
        .done(function(response) {
            if (response && Array.isArray(response)) {
                itemsData = response;
                renderItemsBoard(response);
            } else {
                console.error('KDS Items Board: Invalid response format', response);
                showItemsError('Invalid response format');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('KDS Items Board: Failed to load items:', status, error);
            showItemsError('Failed to load items');
        });
    }

    /**
     * Render items board
     */
    function renderItemsBoard(items) {
        const $container = $('#items-board-list');
        
        if (!items || !Array.isArray(items) || items.length === 0) {
            $container.html('<div class="kds-sidebar__loading">No active dishes</div>');
            return;
        }

        let html = '';
        
        // Add "All Active Dishes" option
        html += `
            <div class="kds-sidebar__item-board ${currentFilter === null ? 'active' : ''}" data-item-id="all">
                <div class="kds-sidebar__item-board-header">
                    <div class="kds-sidebar__item-board-name">All Active Dishes</div>
                    <div class="kds-sidebar__item-board-count">${items.length}</div>
                </div>
            </div>
        `;
        
        // Add individual items
        items.forEach(function(item) {
            const isActive = currentFilter === item.id;
            const metaHtml = renderItemMeta(item.meta || []);
            
            html += `
                <div class="kds-sidebar__item-board ${isActive ? 'active' : ''}" data-item-id="${item.id}">
                    <div class="kds-sidebar__item-board-header">
                        <div class="kds-sidebar__item-board-name">${item.name}</div>
                        <div class="kds-sidebar__item-board-count">${item.count}</div>
                    </div>
                    ${metaHtml}
                </div>
            `;
        });
        
        $container.html(html);
    }

    /**
     * Render item meta information
     */
    function renderItemMeta(meta) {
        if (!meta || !Array.isArray(meta) || meta.length === 0) {
            return '';
        }
        
        let html = '<div class="kds-sidebar__item-board-meta">';
        meta.forEach(function(metaItem) {
            html += `<div class="kds-sidebar__item-board-meta-item">${metaItem}</div>`;
        });
        html += '</div>';
        
        return html;
    }

    /**
     * Show items error
     */
    function showItemsError(message) {
        const $container = $('#items-board-list');
        $container.html(`
            <div class="kds-sidebar__loading" style="color: #ef4444;">
                <i class="fas fa-exclamation-triangle"></i>
                ${message}
            </div>
        `);
    }

    /**
     * Setup event handlers
     */
    function setupEventHandlers() {
        // Item click handler
        $(document).on('click', '.kds-sidebar__item-board', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const itemId = $(this).data('item-id');
            if (itemId && itemId !== 'all') {
                showDishModal(itemId);
            } else if (itemId === 'all') {
                filterOrdersByItem(itemId);
            }
        });
        
        // Branch selector change
        $(document).on('change', '#branch-selector', function() {
            currentFilter = null;
            loadItemsBoard();
        });
        
        // Items toggle
        $(document).on('click', '#items-toggle', function() {
            const $list = $('#items-board-list');
            const $icon = $(this).find('i');
            
            $list.toggleClass('collapsed');
            $icon.toggleClass('fa-chevron-down fa-chevron-up');
            $(this).toggleClass('collapsed');
        });

        // Dish modal event handlers
        setupDishModalHandlers();
    }

    /**
     * Filter orders by item
     */
    function filterOrdersByItem(itemId) {
        
        // Update active state
        $('.kds-sidebar__item-board').removeClass('active');
        $(`.kds-sidebar__item-board[data-item-id="${itemId}"]`).addClass('active');
        
        // Set current filter
        currentFilter = itemId === 'all' ? null : itemId;
        
        // Reload orders with filter
        if (typeof window.KDS !== 'undefined' && window.KDS.fetchUpdates) {
            window.KDS.fetchUpdates();
        } else {
            // Fallback: reload page with filter
            const branchId = $('#branch-selector').val();
            const url = new URL(window.location);
            url.searchParams.set('item_id', itemId === 'all' ? '' : itemId);
            url.searchParams.set('branch_id', branchId);
            window.location.href = url.toString();
        }
    }

    /**
     * Start polling for updates
     */
    function startPolling() {
        if (pollingTimer) {
            clearInterval(pollingTimer);
        }
        
        pollingTimer = setInterval(function() {
            loadItemsBoard();
        }, CONFIG.pollingInterval);
        
    }

    /**
     * Stop polling
     */
    function stopPolling() {
        if (pollingTimer) {
            clearInterval(pollingTimer);
            pollingTimer = null;
        }
    }

    /**
     * Get current filter
     */
    function getCurrentFilter() {
        return currentFilter;
    }

    /**
     * Clear filter
     */
    function clearFilter() {
        currentFilter = null;
        $('.kds-sidebar__item-board').removeClass('active');
        $('.kds-sidebar__item-board[data-item-id="all"]').addClass('active');
    }

    /**
     * Setup dish modal event handlers
     */
    function setupDishModalHandlers() {
        // Close modal handlers
        $(document).on('click', '#dish-modal-close, .kds-dish-modal__overlay', function(e) {
            e.preventDefault();
            e.stopPropagation();
            hideDishModal();
        });

        // Close on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#dish-modal').hasClass('show')) {
                hideDishModal();
            }
        });

        // Prevent modal content clicks from closing modal
        $(document).on('click', '.kds-dish-modal__content', function(e) {
            e.stopPropagation();
        });
    }

    /**
     * Show dish modal with details
     */
    function showDishModal(dishId) {
        const dish = itemsData.find(item => item.id == dishId);
        if (!dish) {
            console.error('Dish not found:', dishId);
            return;
        }

        // Check if we're in fullscreen mode
        const isFullscreen = $('.kds-container').hasClass('fullscreen');
        console.log('KDS: Fullscreen mode for dish modal:', isFullscreen);

        // Get all orders containing this dish
        const branchId = $('#branch-selector').val();
        if (!branchId) {
            console.error('No branch selected');
            return;
        }

        // Fetch orders containing this dish
        $.ajax({
            url: CONFIG.apiEndpoints.orders,
            method: 'GET',
            data: {
                branch_id: branchId,
                item_id: dishId
            },
            dataType: 'json',
            timeout: 10000
        })
        .done(function(response) {
            if (response && response.orders) {
                let $modal;
                
                if (isFullscreen) {
                    // Create modal directly in body for fullscreen mode
                    $modal = $('<div id="dish-modal" class="kds-dish-modal"></div>');
                    $modal.html(`
                        <div class="kds-dish-modal__overlay"></div>
                        <div class="kds-dish-modal__content">
                            <div class="kds-dish-modal__header">
                                <h2 class="kds-dish-modal__title">Dish Details</h2>
                                <button class="kds-dish-modal__close" id="dish-modal-close">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="kds-dish-modal__body" id="dish-modal-body">
                                <!-- Dish details will be populated here -->
                            </div>
                        </div>
                    `);
                    $('body').append($modal);
                    console.log('KDS: Created dish modal directly in body for fullscreen');
                } else {
                    // Use existing modal for normal mode
                    $modal = $('#dish-modal');
                }
                
                renderDishModal(dish, response.orders, $modal);
                $modal.addClass('show');
                
                if (isFullscreen) {
                    $modal.css({
                        'display': 'flex !important',
                        'opacity': '1 !important',
                        'visibility': 'visible !important',
                        'z-index': '999999 !important',
                        'position': 'fixed !important',
                        'top': '0 !important',
                        'left': '0 !important',
                        'width': '100vw !important',
                        'height': '100vh !important',
                        'background': 'rgba(0, 0, 0, 0.7) !important'
                    });
                    console.log('KDS: Applied fullscreen dish modal styles');
                }
                
                $('body').addClass('modal-open');
            } else {
                console.error('Invalid response format:', response);
                showError('Failed to load dish details - Invalid response format');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Failed to fetch dish details:', status, error);
            showError('Failed to load dish details');
        });
    }

    /**
     * Hide dish modal
     */
    function hideDishModal() {
        const $modal = $('#dish-modal');
        
        if (!$modal.length) {
            console.error('Dish modal not found');
            return;
        }
        
        $modal.removeClass('show');
        
        // Check if we're in fullscreen mode
        const isFullscreen = $('.kds-container').hasClass('fullscreen');
        
        if (isFullscreen) {
            // Remove the dynamically created modal
            $modal.remove();
            console.log('KDS: Removed dynamically created dish modal');
        } else {
            // Reset any forced styles for normal mode
            $modal.css({
                'display': '',
                'opacity': '',
                'visibility': '',
                'z-index': '',
                'position': '',
                'top': '',
                'left': '',
                'width': '',
                'height': ''
            });
        }
        
        $('body').removeClass('modal-open');
    }

    /**
     * Render dish modal content
     */
    function renderDishModal(dish, orders, $modal = null) {
        if (!$modal) {
            $modal = $('#dish-modal');
        }
        const $body = $modal.find('#dish-modal-body');
        
        // Ensure orders is an array
        if (!Array.isArray(orders)) {
            console.error('Orders is not an array:', orders);
            $body.html('<p>Error: Invalid data format</p>');
            return;
        }
        
        // Calculate total quantity
        let totalQuantity = 0;
        orders.forEach(order => {
            if (order.items && Array.isArray(order.items)) {
                order.items.forEach(item => {
                    if (item.product_id == dish.id) {
                        totalQuantity += item.quantity || 0;
                    }
                });
            }
        });

        let html = `
            <div class="kds-dish-modal__dish-info">
                <h3 class="kds-dish-modal__dish-name">${dish.name}</h3>
                <p class="kds-dish-modal__dish-total">Total Quantity: ${totalQuantity}</p>
            </div>
            
            <div class="kds-dish-modal__orders">
                <h4 class="kds-dish-modal__orders-title">Orders with this dish (${orders.length})</h4>
        `;

        if (orders.length === 0) {
            html += '<p>No orders found for this dish.</p>';
        } else {
            orders.forEach(order => {
                const orderItems = (order.items || []).filter(item => item.product_id == dish.id);
                
                html += `
                    <div class="kds-dish-modal__order">
                        <div class="kds-dish-modal__order-header">
                            <h5 class="kds-dish-modal__order-number">#${String(order.id).padStart(7, '0')}</h5>
                            <span class="kds-dish-modal__order-status kds-dish-modal__order-status--${order.status.toLowerCase()}">
                                ${order.status}
                            </span>
                        </div>
                        
                        <div class="kds-dish-modal__order-details">
                `;

                orderItems.forEach(item => {
                    // Use the pre-formatted text from backend
                    const variationText = item.variation_text || '';
                    const addonText = item.addon_text || '';

                    html += `
                        <div class="kds-dish-modal__order-item">
                            <div class="kds-dish-modal__order-item-info">
                                <div class="kds-dish-modal__order-item-name">${item.name || 'Unknown Item'}</div>
                                ${variationText ? `<div class="kds-dish-modal__order-item-variations">${variationText}</div>` : ''}
                                ${addonText ? `<div class="kds-dish-modal__order-item-addons">Add-ons: ${addonText}</div>` : ''}
                            </div>
                            <div class="kds-dish-modal__order-item-quantity">${item.quantity}</div>
                        </div>
                    `;
                });

                html += `
                        </div>
                        
                        <div class="kds-dish-modal__order-meta">
                            <div class="kds-dish-modal__order-customer">
                                <i class="fas fa-user"></i>
                                ${order.customer_name || (order.order_type === 'delivery' ? 'Delivery' : 'Walk-in')}
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        html += '</div>';
        $body.html(html);
    }

    /**
     * Show error message
     */
    function showError(message) {
        const $container = $('#items-board-list');
        $container.html(`
            <div class="kds-sidebar__loading" style="color: #ef4444;">
                <i class="fas fa-exclamation-triangle"></i>
                ${message}
            </div>
        `);
    }

    // Public API
    window.KDSItemsBoard = {
        init: init,
        loadItemsBoard: loadItemsBoard,
        getCurrentFilter: getCurrentFilter,
        clearFilter: clearFilter,
        stopPolling: stopPolling
    };

    // Auto-initialize when document is ready
    $(document).ready(function() {
        init();
    });

})(jQuery);
