/**
 * Kitchen Display System (KDS) - Production JavaScript
 * Handles real-time order updates, status changes, and UI interactions
 */

(function($) {
    'use strict';

    // Configuration
    const CONFIG = {
        pollInterval: 4000, // 4 seconds
        soundEnabled: true,
        toasterDuration: 3000,
        apiEndpoints: {
            orders: '/admin/kds/orders',
            updateStatus: '/admin/kds/orders/{id}/status'
        }
    };

    // Auto-detect user type and set appropriate endpoints
    function detectUserType() {
        const path = window.location.pathname;
        if (path.includes('/chef/')) {
            return {
                orders: '/chef/kds/orders',
                updateStatus: '/chef/kds/orders/{id}/status'
            };
        } else if (path.includes('/branch/')) {
            return {
                orders: '/branch/kds/orders',
                updateStatus: '/branch/kds/orders/{id}/status'
            };
        } else {
            return {
                orders: '/admin/kds/orders',
                updateStatus: '/admin/kds/orders/{id}/status'
            };
        }
    }

    // Override endpoints based on current user type
    CONFIG.apiEndpoints = detectUserType();

    // State management
    let lastSeen = new Date().toISOString();
    let isPolling = false;
    let soundContext = null;
    let currentFilter = null; // Current item filter
    let itemsData = []; // Cached items data

    // Initialize KDS
    $(document).ready(function() {
        initializeKDS();
        startPolling();
        attachEventHandlers();
        updateClock();
        setInterval(updateClock, 1000);
        loadItemsSummary();
        setupItemsToggle();
    });

    /**
     * Initialize the KDS system
     */
    function initializeKDS() {
        
        // Initialize audio context for notifications
        if (CONFIG.soundEnabled) {
            try {
                soundContext = new (window.AudioContext || window.webkitAudioContext)();
            } catch (e) {
                console.warn('KDS: Audio context not supported');
                CONFIG.soundEnabled = false;
            }
        }

        // Load initial data
        fetchUpdates();
    }

    /**
     * Start polling for updates
     */
    function startPolling() {
        if (isPolling) return;
        
        isPolling = true;
        
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                fetchUpdates();
            }
        }, CONFIG.pollInterval);
    }

    /**
     * Fetch order updates from server
     */
    function fetchUpdates() {
        const branchId = $('#branch-selector').val();
        if (!branchId) {
            console.warn('KDS: No branch selected');
            return;
        }

        $.ajax({
            url: CONFIG.apiEndpoints.orders,
            method: 'GET',
            data: {
                since: lastSeen,
                branch_id: branchId
            },
            dataType: 'json',
            timeout: 10000
        })
        .done(function(response) {
            if (!response || !response.orders) {
                console.warn('KDS: Invalid response format');
                return;
            }

            lastSeen = response.now || new Date().toISOString();
            if (typeof processOrderUpdates === 'function') {
                processOrderUpdates(response.orders);
            } else {
                console.error('KDS: processOrderUpdates function not found');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('KDS: Failed to fetch updates:', status, error);
            showError('Connection error. Retrying...');
        });
    }

    /**
     * Process order updates and update DOM
     */
    function processOrderUpdates(orders) {
        let newOrderCount = 0;
        let updatedOrderCount = 0;

        orders.forEach(function(order) {
            const $card = renderOrderCard(order);
            const columnSelector = getColumnSelector(order.status);
            const $column = $(columnSelector);
            const existingCard = $column.find(`[data-order-id="${order.id}"]`);

            if (existingCard.length) {
                // Update existing card
                existingCard.replaceWith($card);
                updatedOrderCount++;
            } else {
                // Add new card
                $column.prepend($card);
                newOrderCount++;
            }

            // Remove from other columns if moved
            removeFromOtherColumns(order.id, columnSelector);
        });

        // Update statistics
        updateStatistics();

        // Show notifications
        if (newOrderCount > 0) {
            showNewOrdersNotification(newOrderCount);
            playNotificationSound();
        }

        if (updatedOrderCount > 0) {
        }
    }

    /**
     * Get column selector based on order status
     */
    function getColumnSelector(status) {
        const upperStatus = status ? status.toUpperCase() : '';
        switch (upperStatus) {
            case 'NEW':
            case 'PENDING':
            case 'CONFIRMED':
            case 'PROCESSING':
                return '#col-new';
            case 'COOKING':
                return '#col-cooking';
            case 'DONE':
            case 'COMPLETED':
                return '#col-done';
            default:
                return '#col-new';
        }
    }

    /**
     * Remove order from other columns
     */
    function removeFromOtherColumns(orderId, currentColumnSelector) {
        const allColumns = ['#col-new', '#col-cooking', '#col-done'];
        
        allColumns.forEach(function(selector) {
            if (selector !== currentColumnSelector) {
                $(selector).find(`[data-order-id="${orderId}"]`).remove();
            }
        });
    }

    /**
     * Render order card from template
     */
    function renderOrderCard(order) {
        const statusClass = getStatusClass(order.status);
        const statusText = getStatusText(order.status);
        const actionButton = getActionButton(order);
        const itemsHtml = renderOrderItems(order.items || []);
        const timeAgo = getTimeAgo(order.created_at || order.placed_at);

        return $(`
            <div class="kds-card" data-order-id="${order.id}">
                <div class="kds-card__header">
                    <h3 class="kds-card__number">${order.order_number || '#' + order.id}</h3>
                    <div class="kds-card__status">
                        <span class="kds-chip kds-chip--${statusClass}">${statusText}</span>
                    </div>
                </div>
                <div class="kds-card__meta">
                    ${order.token_number && order.token_number !== 'N/A' ? `<div class="kds-card__token">Token: ${order.token_number}</div>` : ''}
                    ${order.customer_name ? `<div class="kds-card__customer">Customer: ${order.customer_name}</div>` : ''}
                    <div class="kds-card__time">
                        <i class="fas fa-clock"></i>
                        ${timeAgo}
                    </div>
                </div>
                <div class="kds-card__items">
                    ${itemsHtml}
                </div>
                <div class="kds-card__actions">
                    ${actionButton}
                </div>
            </div>
        `);
    }

    /**
     * Get status class for styling
     */
    function getStatusClass(status) {
        const upperStatus = status ? status.toUpperCase() : '';
        switch (upperStatus) {
            case 'NEW':
            case 'PENDING':
            case 'CONFIRMED':
            case 'PROCESSING':
                return 'new';
            case 'COOKING':
                return 'cooking';
            case 'DONE':
            case 'COMPLETED':
                return 'done';
            default:
                return 'new';
        }
    }

    /**
     * Get display text for status
     */
    function getStatusText(status) {
        const upperStatus = status ? status.toUpperCase() : '';
        switch (upperStatus) {
            case 'NEW':
            case 'PENDING':
                return 'New';
            case 'CONFIRMED':
                return 'Confirmed';
            case 'PROCESSING':
                return 'Processing';
            case 'COOKING':
                return 'Cooking';
            case 'DONE':
            case 'COMPLETED':
                return 'Done';
            default:
                return status ? status.charAt(0).toUpperCase() + status.slice(1).toLowerCase() : 'Unknown';
        }
    }

    /**
     * Get action button for order
     */
    function getActionButton(order) {
        const upperStatus = order.status ? order.status.toUpperCase() : '';
        switch (upperStatus) {
            case 'NEW':
            case 'PENDING':
            case 'CONFIRMED':
            case 'PROCESSING':
                return `
                    <button class="kds-btn kds-btn--primary btn-mark-processing" 
                            data-id="${order.id}"
                            aria-label="Mark order ${order.order_number || order.id} as processing">
                        <i class="fas fa-play"></i>
                        Mark Processing
                    </button>
                `;
            case 'COOKING':
                return `
                    <button class="kds-btn kds-btn--success btn-mark-done" 
                            data-id="${order.id}"
                            aria-label="Mark order ${order.order_number || order.id} as done">
                        <i class="fas fa-check"></i>
                        Mark Done
                    </button>
                `;
            case 'DONE':
            case 'COMPLETED':
                return `
                    <button class="kds-btn kds-btn--warn btn-reopen" 
                            data-id="${order.id}"
                            aria-label="Reopen order ${order.order_number || order.id}">
                        <i class="fas fa-undo"></i>
                        Reopen
                    </button>
                `;
            default:
                return '';
        }
    }

    /**
     * Render items list
     */
    function renderItemsList(items) {
        if (!items || items.length === 0) {
            return '<div class="kds-card__item">No items</div>';
        }

        const maxItems = 3;
        const visibleItems = items.slice(0, maxItems);
        const remainingCount = items.length - maxItems;

        let html = visibleItems.map(function(item) {
            let itemHtml = `
                <div class="kds-card__item">
                    <span class="kds-card__item-quantity">${item.quantity || 1}</span>
                    <span class="kds-card__item-name">${item.name || item.product_name || 'Unknown Item'}</span>
            `;
            
            // Add variations if available
            if (item.variation_text && item.variation_text.trim()) {
                itemHtml += `<div class="kds-card__item-variations">${item.variation_text}</div>`;
            }
            
            // Add addons if available
            if (item.addon_text && item.addon_text.trim()) {
                itemHtml += `<div class="kds-card__item-addons">+ ${item.addon_text}</div>`;
            }
            
            itemHtml += `</div>`;
            return itemHtml;
        }).join('');

        if (remainingCount > 0) {
            html += `<div class="kds-card__item"><span class="kds-card__item-name">+${remainingCount} more items</span></div>`;
        }

        return html;
    }

    /**
     * Get time ago string
     */
    function getTimeAgo(dateString) {
        const now = new Date();
        const orderDate = new Date(dateString);
        const diffMs = now - orderDate;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);

        if (diffMins < 1) {
            return 'Just now';
        } else if (diffMins < 60) {
            return `${diffMins}m ago`;
        } else if (diffHours < 24) {
            return `${diffHours}h ago`;
        } else {
            return orderDate.toLocaleDateString();
        }
    }

    /**
     * Update statistics counters
     */
    function updateStatistics() {
        const newCount = $('#col-new .kds-card').length;
        const cookingCount = $('#col-cooking .kds-card').length;
        const doneCount = $('#col-done .kds-card').length;

        $('#confirmed-count').text(newCount);
        $('#cooking-count').text(cookingCount);
        $('#done-count').text(doneCount);

        // Update column counts
        $('#col-new .kds-col__count').text(newCount);
        $('#col-cooking .kds-col__count').text(cookingCount);
        $('#col-done .kds-col__count').text(doneCount);
    }

    /**
     * Attach event handlers
     */
    function attachEventHandlers() {
        // Status change buttons
        $(document).on('click', '.btn-mark-processing', function() {
            const orderId = $(this).data('id');
            updateOrderStatus(orderId, 'COOKING');
        });

        $(document).on('click', '.btn-mark-done', function() {
            const orderId = $(this).data('id');
            updateOrderStatus(orderId, 'DONE');
        });

        $(document).on('click', '.btn-reopen', function() {
            const orderId = $(this).data('id');
            updateOrderStatus(orderId, 'COOKING');
        });

        // Search functionality
        $('#search-input').on('input', function() {
            const query = $(this).val().toLowerCase();
            filterOrders(query);
        });

        // Branch selector
        $('#branch-selector').on('change', function() {
            const branchId = $(this).val();
            if (branchId) {
                // Reload orders for selected branch
                lastSeen = new Date().toISOString();
                fetchUpdates();
                
                // Reload items board if available
                if (window.KDSItemsBoard && window.KDSItemsBoard.loadItemsBoard) {
                    window.KDSItemsBoard.loadItemsBoard();
                }
            }
        });

        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'r':
                        e.preventDefault();
                        fetchUpdates();
                        break;
                    case 'f':
                        e.preventDefault();
                        $('#search-input').focus();
                        break;
                }
            }
        });
    }

    /**
     * Update order status
     */
    function updateOrderStatus(orderId, newStatus) {
        
        const $button = $(`.btn-mark-processing[data-id="${orderId}"], .btn-mark-done[data-id="${orderId}"], .btn-reopen[data-id="${orderId}"]`);
        const originalText = $button.html();
        
        
        // Show loading state
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: CONFIG.apiEndpoints.updateStatus.replace('{id}', orderId),
            method: 'PUT',
            data: {
                status: newStatus,
                _token: window.csrf_token || $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            timeout: 10000
        })
        .done(function(response) {
            if (response.ok) {
                // Refresh data
                fetchUpdates();
            } else {
                throw new Error(response.message || 'Update failed');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('KDS: Failed to update order status:', status, error);
            console.error('KDS: XHR response:', xhr.responseText);
            console.error('KDS: XHR status:', xhr.status);
            showError('Failed to update order status: ' + (xhr.responseText || error));
            // Restore button
            $button.prop('disabled', false).html(originalText);
        });
    }

    /**
     * Filter orders by search query
     */
    function filterOrders(query) {
        $('.kds-card').each(function() {
            const $card = $(this);
            const orderNumber = $card.find('.kds-card__number').text().toLowerCase();
            const orderItems = $card.find('.kds-card__item-name').text().toLowerCase();
            const orderToken = $card.find('.kds-card__token').text().toLowerCase();
            
            const matches = orderNumber.includes(query) || 
                          orderItems.includes(query) || 
                          orderToken.includes(query);
            
            $card.toggle(matches);
        });
    }

    /**
     * Show new orders notification
     */
    function showNewOrdersNotification(count) {
        const message = count === 1 ? '1 new order' : `${count} new orders`;
        showToaster(message, 'success');
    }

    /**
     * Show toaster notification
     */
    function showToaster(message, type = 'info') {
        const $toaster = $(`
            <div class="kds-toaster kds-toaster--${type}">
                <i class="fas fa-bell"></i>
                ${message}
            </div>
        `);

        $('body').append($toaster);

        // Show toaster
        setTimeout(function() {
            $toaster.addClass('show');
        }, 100);

        // Hide toaster
        setTimeout(function() {
            $toaster.removeClass('show');
            setTimeout(function() {
                $toaster.remove();
            }, 300);
        }, CONFIG.toasterDuration);
    }

    /**
     * Show error message
     */
    function showError(message) {
        showToaster(message, 'error');
    }

    /**
     * Play notification sound
     */
    function playNotificationSound() {
        if (!CONFIG.soundEnabled || !soundContext) return;

        try {
            const oscillator = soundContext.createOscillator();
            const gainNode = soundContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(soundContext.destination);
            
            oscillator.frequency.setValueAtTime(800, soundContext.currentTime);
            oscillator.frequency.setValueAtTime(600, soundContext.currentTime + 0.1);
            
            gainNode.gain.setValueAtTime(0.1, soundContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, soundContext.currentTime + 0.2);
            
            oscillator.start(soundContext.currentTime);
            oscillator.stop(soundContext.currentTime + 0.2);
        } catch (e) {
            console.warn('KDS: Failed to play notification sound:', e);
        }
    }

    /**
     * Update clock display
     */
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: true,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        $('.kds-header__clock').text(timeString);
    }

    /**
     * Load items summary for sidebar (legacy - now handled by Items Board)
     */
    function loadItemsSummary() {
        // This function is now handled by the Items Board JavaScript
        // Keeping for backward compatibility
    }

    /**
     * Render order items for order cards
     */
    function renderOrderItems(items) {
        if (!items || !Array.isArray(items) || items.length === 0) {
            return '<div class="kds-card__items">No items</div>';
        }

        let html = '<div class="kds-card__items">';
        items.forEach(function(item) {
            const quantity = item.quantity || 1;
            const size = item.size || 'Regular';
            
            // Use the pre-formatted text from the API
            const variationsText = item.variation_text || '';
            const addonsText = item.addon_text || '';
            
            html += `
                <div class="kds-card__item">
                    <span class="kds-card__item-quantity">${quantity}x</span>
                    <span class="kds-card__item-name">${item.name}</span>
                    ${size !== 'Regular' ? `<span class="kds-card__item-size">(${size})</span>` : ''}
                    ${variationsText ? `<div class="kds-card__item-variations">${variationsText}</div>` : ''}
                    ${addonsText ? `<div class="kds-card__item-addons">+ ${addonsText}</div>` : ''}
                </div>
            `;
        });
        html += '</div>';
        
        return html;
    }

    /**
     * Render items list in sidebar (legacy function - now handled by Items Board)
     */
    function renderItemsListLegacy(items) {
        const $itemsList = $('#items-list');
        
        // Ensure items is an array
        if (!items || !Array.isArray(items) || items.length === 0) {
            $itemsList.html('<div class="kds-sidebar__loading">No items ordered today</div>');
            return;
        }

        let html = '';
        items.forEach(function(item) {
            // Ensure item has required properties
            if (!item || !item.id || !item.name) {
                console.warn('KDS: Invalid item data:', item);
                return;
            }
            
            const isActive = currentFilter === item.id ? 'active' : '';
            const quantity = item.quantity || item.total_quantity || 0;
            
            html += `
                <div class="kds-sidebar__item ${isActive}" data-item-id="${item.id}">
                    <span class="kds-sidebar__item-name">${item.name}</span>
                    <span class="kds-sidebar__item-count">${quantity}</span>
                </div>
            `;
        });

        $itemsList.html(html);
    }

    /**
     * Show items loading error
     */
    function showItemsError(message) {
        $('#items-list').html(`<div class="kds-sidebar__loading">${message}</div>`);
    }

    /**
     * Setup items toggle functionality
     */
    function setupItemsToggle() {
        $('#items-toggle').on('click', function() {
            const $itemsList = $('#items-list');
            const $toggle = $(this);
            
            $itemsList.toggleClass('collapsed');
            $toggle.toggleClass('collapsed');
        });
    }

    /**
     * Filter orders by item
     */
    function filterOrdersByItem(itemId) {
        currentFilter = itemId;
        
        // Update active item
        $('.kds-sidebar__item').removeClass('active');
        $(`.kds-sidebar__item[data-item-id="${itemId}"]`).addClass('active');
        
        // Filter visible orders
        $('.kds-card').each(function() {
            const $card = $(this);
            const orderId = $card.data('order-id');
            const hasItem = orderHasItem(orderId, itemId);
            
            if (itemId && !hasItem) {
                $card.hide();
            } else {
                $card.show();
            }
        });
        
        // Update column counts
        updateStatistics();
    }

    /**
     * Check if order has specific item
     */
    function orderHasItem(orderId, itemId) {
        // This would need to be implemented based on your order data structure
        // For now, we'll show all orders when filtering
        return true;
    }

    /**
     * Clear item filter
     */
    function clearItemFilter() {
        currentFilter = null;
        $('.kds-sidebar__item').removeClass('active');
        $('.kds-card').show();
        updateStatistics();
    }

    // Event handlers for items
    $(document).on('click', '.kds-sidebar__item', function() {
        const itemId = $(this).data('item-id');
        
        if (currentFilter === itemId) {
            clearItemFilter();
        } else {
            filterOrdersByItem(itemId);
        }
    });

    // Expose public API
    window.KDS = {
        fetchUpdates: fetchUpdates,
        updateOrderStatus: updateOrderStatus,
        updateStatistics: updateStatistics,
        showToaster: showToaster,
        showError: showError,
        loadItemsSummary: loadItemsSummary,
        clearItemFilter: clearItemFilter
    };

})(jQuery);
