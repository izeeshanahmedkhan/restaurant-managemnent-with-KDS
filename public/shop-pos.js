// Shop POS JavaScript functionality
$(document).ready(function() {
    // Initialize cart
    loadCart();
    
    // Quick view functionality
    $('.quick-view').click(function() {
        const productId = $(this).data('id');
        showQuickView(productId);
    });
    
    // Add to cart functionality
    $('.add-basic').click(function() {
        const productId = $(this).data('id');
        addToCart(productId, 1);
    });
    
    // Empty cart
    $('#btn-empty').click(function() {
        emptyCart();
    });
    
    // Update cart display
    function loadCart() {
        $.get(SHOP_ROUTES.cart + '/items', function(data) {
            $('#cart-container').html(data);
            updateCartCount();
        });
    }
    
    // Show quick view modal
    function showQuickView(productId) {
        $.post(SHOP_ROUTES.quickView, {
            product_id: productId,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(response) {
            if (response.success) {
                $('#quick-view-body').html(response.view);
                $('#quickViewModal').modal('show');
            }
        });
    }
    
    // Add product to cart
    function addToCart(productId, quantity = 1) {
        $.post(SHOP_ROUTES.add, {
            id: productId,
            quantity: quantity,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(response) {
            if (response.data) {
                loadCart();
                // Immediately refresh cart badge and optionally open sidebar
                $.get(SHOP_ROUTES.cart + '/items', function(data){
                    const itemCount = $(data).find('.cart-item').length;
                    $('#cart-count').text(itemCount);
                });
                // Auto-open cart sidebar if present
                if($('#open-cart').length){
                    $('#open-cart').trigger('click');
                }
                showToast('Product added to cart!', 'success');
            }
        }).fail(function() {
            showToast('Failed to add product to cart', 'error');
        });
    }
    
    // Empty cart
    function emptyCart() {
        if (confirm('Are you sure you want to empty the cart?')) {
            $.ajax({
                url: SHOP_ROUTES.empty,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    loadCart();
                    showToast('Cart emptied', 'info');
                }
            });
        }
    }
    
    // Update cart count
    function updateCartCount() {
        $.get(SHOP_ROUTES.cart + '/items', function(data) {
            const itemCount = $(data).find('.cart-item').length;
            $('#cart-count').text(itemCount);
        });
    }
    
    // Show toast notification
    function showToast(message, type = 'info') {
        // Simple toast implementation
        const toast = $('<div class="toast-notification toast-' + type + '">' + message + '</div>');
        $('body').append(toast);
        setTimeout(function() {
            toast.fadeOut(function() {
                toast.remove();
            });
        }, 3000);
    }
    
    // Update quantity
    $(document).on('click', '.qty-btn', function() {
        const key = $(this).data('key');
        const action = $(this).data('action');
        const currentQty = parseInt($(this).siblings('.qty-input').val());
        let newQty = currentQty;
        
        if (action === 'plus') {
            newQty = currentQty + 1;
        } else if (action === 'minus' && currentQty > 1) {
            newQty = currentQty - 1;
        }
        
        if (newQty !== currentQty) {
            updateQuantity(key, newQty);
        }
    });
    
    // Remove item from cart
    $(document).on('click', '.remove-item', function() {
        const key = $(this).data('key');
        removeFromCart(key);
    });
    
    // Update quantity function
    function updateQuantity(key, quantity) {
        $.ajax({
            url: SHOP_ROUTES.qty,
            type: 'PATCH',
            data: {
                key: key,
                quantity: quantity,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                loadCart();
            }
        });
    }
    
    // Remove from cart function
    function removeFromCart(key) {
        $.ajax({
            url: SHOP_ROUTES.remove,
            type: 'DELETE',
            data: {
                key: key,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                loadCart();
                showToast('Item removed from cart', 'info');
            }
        });
    }
});

// Add CSRF token to all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
