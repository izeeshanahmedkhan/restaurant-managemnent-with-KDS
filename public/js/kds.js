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
    let sortOrder = {
        new: 'latest', // latest or oldest
        cooking: 'latest',
        done: 'latest'
    };

    // Initialize KDS
    $(document).ready(function() {
        initializeKDS();
        startPolling();
        attachEventHandlers();
        setupModalEventHandlers();
        setupFullscreenHandlers();
        setupSortHandlers();
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
                // Add new card to the list container, not the column
                const $listContainer = $column.find('.kds-list');
                if ($listContainer.length) {
                    $listContainer.append($card);
                } else {
                    // Fallback: append to column if list container not found
                    $column.append($card);
                }
                newOrderCount++;
            }

            // Remove from other columns if moved
            removeFromOtherColumns(order.id, columnSelector);
        });

        // Sort all columns after rendering
        sortAllColumns();

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
            <div class="kds-card kds-card--clickable" data-order-id="${order.id}" data-order-data='${JSON.stringify(order)}'>
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
     * Update column counts
     */
    function updateColumnCounts() {
        const columns = [
            { id: 'col-new', selector: '#new-orders-list' },
            { id: 'col-cooking', selector: '#cooking-orders-list' },
            { id: 'col-done', selector: '#done-orders-list' }
        ];
        
        columns.forEach(function(column) {
            const count = $(column.selector).find('.kds-card').length;
            $(`#${column.id}-count`).text(count);
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
                // Immediately remove the card from current column
                const $card = $(`.kds-card[data-order-id="${orderId}"]`);
                if ($card.length) {
                    $card.fadeOut(300, function() {
                        $(this).remove();
                        // Update the column count after removal
                        updateColumnCounts();
                    });
                }
                
                // Update statistics immediately
                updateStatistics();
                
                // Refresh data after a short delay to ensure smooth transition
                setTimeout(() => {
                    fetchUpdates();
                }, 500);
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
            $itemsList.html('<div class="kds-sidebar__loading">No active dishes</div>');
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

    /**
     * Show order details modal
     */
    function showOrderModal(orderData) {
        console.log('KDS: showOrderModal called with:', orderData);
        
        // Check if we're in fullscreen mode
        const isFullscreen = $('.kds-container').hasClass('fullscreen');
        console.log('KDS: Fullscreen mode:', isFullscreen);
        
        let $modal, $modalBody;
        
        if (isFullscreen) {
            // Create modal directly in body for fullscreen mode
            $modal = $('<div id="order-modal" class="kds-modal"></div>');
            $modal.html(`
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
            `);
            $('body').append($modal);
            $modalBody = $modal.find('#modal-body');
            console.log('KDS: Created modal directly in body for fullscreen');
            console.log('KDS: Modal in DOM:', $modal.length > 0);
            console.log('KDS: Modal body in DOM:', $modalBody.length > 0);
        } else {
            // Use existing modal for normal mode
            $modal = $('#order-modal');
            $modalBody = $('#modal-body');
        }
        
        console.log('KDS: Modal element found:', $modal.length > 0);
        console.log('KDS: Modal body found:', $modalBody.length > 0);
        
        if (!$modal.length) {
            console.error('KDS Modal: Modal element not found');
            return;
        }
        
        if (!orderData) {
            console.error('KDS Modal: No order data provided');
            return;
        }
        
        try {
            // Format order data for modal display
            const modalHtml = renderOrderModal(orderData);
            console.log('KDS: Modal HTML generated:', modalHtml);
            $modalBody.html(modalHtml);
            
            // Show modal
            console.log('KDS: Adding show class to modal');
            $modal.addClass('show');
            
            // Apply fullscreen styles if needed
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
                console.log('KDS: Applied fullscreen modal styles');
            }
            
            // Focus on modal for accessibility
            $modal.find('.kds-modal__close').focus();
            
            // Prevent body scroll
            $('body').addClass('modal-open');
            
            console.log('KDS: Modal should now be visible');
        } catch (error) {
            console.error('KDS Modal: Error showing modal:', error);
            showError('Error displaying order details');
        }
    }
    
    /**
     * Hide order details modal
     */
    function hideOrderModal() {
        const $modal = $('#order-modal');
        
        if (!$modal.length) {
            console.error('KDS Modal: Modal element not found');
            return;
        }
        
        try {
            $modal.removeClass('show');
            
            // Check if we're in fullscreen mode
            const isFullscreen = $('.kds-container').hasClass('fullscreen');
            
            if (isFullscreen) {
                // Remove the dynamically created modal
                $modal.remove();
                console.log('KDS: Removed dynamically created modal');
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
            
            // Restore body scroll
            $('body').removeClass('modal-open');
        } catch (error) {
            console.error('KDS Modal: Error hiding modal:', error);
        }
    }
    
    /**
     * Render order modal content
     */
    function renderOrderModal(order) {
        const statusClass = getStatusClass(order.status);
        const statusText = getStatusText(order.status);
        const timeAgo = getTimeAgo(order.placed_at || order.created_at);
        const itemsHtml = renderModalOrderItems(order.items || []);
        const actionButton = getModalActionButton(order);
        
        return `
            <div class="kds-modal-order">
                <div class="kds-modal-order__header">
                    <h3 class="kds-modal-order__number">#${order.number || order.id}</h3>
                    <span class="kds-modal-order__status kds-modal-order__status--${statusClass}">${statusText}</span>
                </div>
                
                <div class="kds-modal-order__meta">
                    <div class="kds-modal-order__meta-item">
                        <div class="kds-modal-order__meta-label">Order Time</div>
                        <div class="kds-modal-order__meta-value">${timeAgo}</div>
                    </div>
                    ${order.customer_name ? `
                        <div class="kds-modal-order__meta-item">
                            <div class="kds-modal-order__meta-label">Customer</div>
                            <div class="kds-modal-order__meta-value">${order.customer_name}</div>
                        </div>
                    ` : ''}
                    ${order.token ? `
                        <div class="kds-modal-order__meta-item">
                            <div class="kds-modal-order__meta-label">Token</div>
                            <div class="kds-modal-order__meta-value">${order.token}</div>
                        </div>
                    ` : ''}
                </div>
                
                <div class="kds-modal-order__items">
                    <div class="kds-modal-order__items-header">
                        <i class="fas fa-list"></i>
                        Order Items
                    </div>
                    ${itemsHtml}
                </div>
                
                ${actionButton ? `
                    <div class="kds-modal-order__actions">
                        ${actionButton}
                    </div>
                ` : ''}
            </div>
        `;
    }
    
    /**
     * Render order items for modal
     */
    function renderModalOrderItems(items) {
        if (!items || items.length === 0) {
            return '<div class="kds-modal-order__item"><div class="kds-modal-order__item-details">No items</div></div>';
        }
        
        return items.map(function(item) {
            const quantity = item.quantity || 1;
            const variationsText = item.variation_text || '';
            const addonsText = item.addon_text || '';
            
            return `
                <div class="kds-modal-order__item">
                    <div class="kds-modal-order__item-quantity">${quantity}x</div>
                    <div class="kds-modal-order__item-details">
                        <div class="kds-modal-order__item-name">${item.name}</div>
                        ${variationsText ? `<div class="kds-modal-order__item-variations">${variationsText}</div>` : ''}
                        ${addonsText ? `<div class="kds-modal-order__item-addons">+ ${addonsText}</div>` : ''}
                    </div>
                </div>
            `;
        }).join('');
    }
    
    /**
     * Get action button for modal
     */
    function getModalActionButton(order) {
        const upperStatus = order.status ? order.status.toUpperCase() : '';
        switch (upperStatus) {
            case 'NEW':
            case 'PENDING':
            case 'CONFIRMED':
            case 'PROCESSING':
                return `
                    <button class="kds-modal-order__btn kds-modal-order__btn--primary btn-modal-mark-processing" 
                            data-id="${order.id}"
                            aria-label="Mark order ${order.number || order.id} as processing">
                        <i class="fas fa-play"></i>
                        Mark Processing
                    </button>
                `;
            case 'COOKING':
                return `
                    <button class="kds-modal-order__btn kds-modal-order__btn--success btn-modal-mark-done" 
                            data-id="${order.id}"
                            aria-label="Mark order ${order.number || order.id} as done">
                        <i class="fas fa-check"></i>
                        Mark Done
                    </button>
                `;
            case 'DONE':
            case 'COMPLETED':
                return `
                    <button class="kds-modal-order__btn kds-modal-order__btn--warn btn-modal-reopen" 
                            data-id="${order.id}"
                            aria-label="Reopen order ${order.number || order.id}">
                        <i class="fas fa-undo"></i>
                        Reopen
                    </button>
                `;
            default:
                return '';
        }
    }
    
    /**
     * Setup sort event handlers
     */
    function setupSortHandlers() {
        $(document).on('click', '.kds-col__sort-btn', function() {
            const column = $(this).data('column');
            const $button = $(this);
            const $icon = $button.find('i');
            const $text = $button.contents().filter(function() {
                return this.nodeType === 3; // Text node
            });
            
            // Toggle sort order
            if (sortOrder[column] === 'latest') {
                sortOrder[column] = 'oldest';
                $button.addClass('oldest-first');
                $text[0].textContent = ' Oldest';
            } else {
                sortOrder[column] = 'latest';
                $button.removeClass('oldest-first');
                $text[0].textContent = ' Latest';
            }
            
            // Sort the column
            sortColumn(column);
        });
    }
    
    /**
     * Sort a specific column
     */
    function sortColumn(column) {
        const columnSelector = getColumnSelector(column);
        const $column = $(columnSelector);
        const $list = $column.find('.kds-list');
        const $cards = $list.find('.kds-card');
        
        if ($cards.length === 0) return;
        
        // Convert to array for sorting
        const cardsArray = $cards.toArray();
        
        // Sort by creation time
        cardsArray.sort(function(a, b) {
            const timeA = new Date($(a).data('order-data').created_at || $(a).data('order-data').placed_at);
            const timeB = new Date($(b).data('order-data').created_at || $(b).data('order-data').placed_at);
            
            if (sortOrder[column] === 'latest') {
                return timeB - timeA; // Latest first
            } else {
                return timeA - timeB; // Oldest first
            }
        });
        
        // Re-append sorted cards
        $list.empty();
        cardsArray.forEach(function(card) {
            $list.append(card);
        });
    }
    
    /**
     * Sort all columns
     */
    function sortAllColumns() {
        Object.keys(sortOrder).forEach(function(column) {
            sortColumn(column);
        });
    }

    /**
     * Setup modal event handlers
     */
    function setupModalEventHandlers() {
        // Order card click handler
        $(document).on('click', '.kds-card--clickable', function(e) {
            console.log('KDS: Card clicked', e.target);
            
            // Don't trigger if clicking on action buttons
            if ($(e.target).closest('.kds-card__actions').length) {
                console.log('KDS: Clicked on action button, ignoring');
                return;
            }
            
            const orderData = $(this).data('order-data');
            console.log('KDS: Order data:', orderData);
            
            if (orderData) {
                console.log('KDS: Showing modal');
                showOrderModal(orderData);
            } else {
                console.error('KDS: No order data found');
            }
        });
        
        // Modal close handlers
        $(document).on('click', '#modal-close, .kds-modal__overlay', function() {
            hideOrderModal();
        });
        
        // Modal action button handlers
        $(document).on('click', '.btn-modal-mark-processing', function() {
            const orderId = $(this).data('id');
            updateOrderStatus(orderId, 'COOKING');
            hideOrderModal();
        });
        
        $(document).on('click', '.btn-modal-mark-done', function() {
            const orderId = $(this).data('id');
            updateOrderStatus(orderId, 'DONE');
            hideOrderModal();
        });
        
        $(document).on('click', '.btn-modal-reopen', function() {
            const orderId = $(this).data('id');
            updateOrderStatus(orderId, 'COOKING');
            hideOrderModal();
        });
        
        // ESC key to close modal
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#order-modal').hasClass('show')) {
                hideOrderModal();
            }
        });
    }

    // Fullscreen functionality
    function setupFullscreenHandlers() {
        const fullscreenBtn = $('#fullscreen-btn');
        const container = $('.kds-container');
        
        if (fullscreenBtn.length === 0) return;
        
        fullscreenBtn.on('click', function() {
            if (container.hasClass('fullscreen')) {
                exitFullscreen();
            } else {
                enterFullscreen();
            }
        });
        
        // Listen for fullscreen change events
        $(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange msfullscreenchange', function() {
            if (!document.fullscreenElement && !document.webkitFullscreenElement && 
                !document.mozFullScreenElement && !document.msFullscreenElement) {
                container.removeClass('fullscreen');
                updateFullscreenIcon(false);
            }
        });
    }
    
    function enterFullscreen() {
        const container = $('.kds-container')[0];
        const fullscreenBtn = $('#fullscreen-btn');
        
        if (container.requestFullscreen) {
            container.requestFullscreen();
        } else if (container.webkitRequestFullscreen) {
            container.webkitRequestFullscreen();
        } else if (container.mozRequestFullScreen) {
            container.mozRequestFullScreen();
        } else if (container.msRequestFullscreen) {
            container.msRequestFullscreen();
        } else {
            // Fallback: Use CSS fullscreen
            $('.kds-container').addClass('fullscreen');
        }
        
        updateFullscreenIcon(true);
    }
    
    function exitFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        } else {
            // Fallback: Remove CSS fullscreen
            $('.kds-container').removeClass('fullscreen');
        }
        
        updateFullscreenIcon(false);
    }
    
    function updateFullscreenIcon(isFullscreen) {
        const fullscreenBtn = $('#fullscreen-btn i');
        if (isFullscreen) {
            fullscreenBtn.removeClass('fa-expand').addClass('fa-compress');
            $('.kds-container').addClass('fullscreen');
            $('body').addClass('fullscreen');
        } else {
            fullscreenBtn.removeClass('fa-compress').addClass('fa-expand');
            $('.kds-container').removeClass('fullscreen');
            $('body').removeClass('fullscreen');
        }
    }

    // Expose public API
    window.KDS = {
        fetchUpdates: fetchUpdates,
        updateOrderStatus: updateOrderStatus,
        updateStatistics: updateStatistics,
        showToaster: showToaster,
        showError: showError,
        loadItemsSummary: loadItemsSummary,
        clearItemFilter: clearItemFilter,
        showOrderModal: showOrderModal,
        hideOrderModal: hideOrderModal,
        enterFullscreen: enterFullscreen,
        exitFullscreen: exitFullscreen,
        sortColumn: sortColumn,
        sortAllColumns: sortAllColumns
    };

})(jQuery);
