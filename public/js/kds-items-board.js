/**
 * KDS Items Board JavaScript
 * Handles the Items Board sidebar functionality
 */

(function($) {
    'use strict';

    // Configuration
    const CONFIG = {
        pollingInterval: 15000, // 15 seconds
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
            $container.html('<div class="kds-sidebar__loading">No items found</div>');
            return;
        }

        let html = '';
        
        // Add "All Items" option
        html += `
            <div class="kds-sidebar__item-board ${currentFilter === null ? 'active' : ''}" data-item-id="all">
                <div class="kds-sidebar__item-board-header">
                    <div class="kds-sidebar__item-board-name">All Items</div>
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
        $(document).on('click', '.kds-sidebar__item-board', function() {
            const itemId = $(this).data('item-id');
            filterOrdersByItem(itemId);
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
