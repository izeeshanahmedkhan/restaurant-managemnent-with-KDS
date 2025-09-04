// Standalone Shop POS JS (scoped) - no global pollution
(function(window, $){
  'use strict';

  if(!$ || !window.SHOP_ROUTES){ return; }

  // CSRF for all AJAX
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const ui = {
    cartContainer: '#cart-container',
    cartCount: '#cart-count',
    openCartBtn: '#open-cart',
    toastContainer: '#toast-container'
  };

  function currency(amount){
    // naive local currency formatter using server markup fallback if needed
    try { return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' }).format(amount); }
    catch(e){ return amount.toFixed(2) + '$'; }
  }

  function updateCartCountFromHtml(html){
    const $html = $(html);
    const explicit = $html.find('#cart-count-value').data('count');
    const itemCount = explicit !== undefined ? explicit : $html.find('.cart-item').length;
    $(ui.cartCount).text(itemCount);
  }

  function recalcTotalsLocal(){
    let subtotal = 0;
    const $items = $(ui.cartContainer).find('.cart-item');
    $items.each(function(){
      const price = parseFloat($(this).data('price')) || 0;
      const qty = parseInt($(this).find('.qty-input').val() || '1', 10);
      const itemSubtotal = price * qty;
      $(this).find('.item-subtotal').attr('data-raw', itemSubtotal).text(currency(itemSubtotal));
      subtotal += itemSubtotal;
    });
    $('#cart-subtotal-amount').text(currency(subtotal));
    $('#cart-total-amount').text(currency(subtotal));
    $('#cart-total-items').text($items.length);
    $(ui.cartCount).text($items.length);
  }

  function loadCart(){
    $.get(window.SHOP_ROUTES.cart + '/items', function(html){
      $(ui.cartContainer).html(html);
      updateCartCountFromHtml(html);
    });
  }

  function showQuickView(productId){
    $.post(window.SHOP_ROUTES.quickView, { product_id: productId }, function(res){
      if(res && res.success){
        $('#quick-view-body').html(res.view);
        $('#quickViewModal').modal('show');
      }
    });
  }

  function addToCart(productId, quantity){
    $.post(window.SHOP_ROUTES.add, { id: productId, quantity: quantity || 1 }, function(res){
      if(res && res.data){
        // Update badge from server-rendered count (accurate)
        $.get(window.SHOP_ROUTES.cart + '/items', function(html){
          updateCartCountFromHtml(html);
          // If sidebar is open, refresh its content quietly
          if($('#cart-sidebar').hasClass('active')){
            $(ui.cartContainer).html(html);
          }
        });
        toast('Item added to cart', 'success');
        // Optionally open sidebar to show feedback
        if($(ui.openCartBtn).length && !$('#cart-sidebar').hasClass('active')){ $(ui.openCartBtn).trigger('click'); }
      }
    }).fail(function(){ toast('Failed to add product', 'error'); });
  }

  function updateQuantity(key, quantity){
    // optimistic: update UI first
    const $item = $(ui.cartContainer).find('.cart-item[data-key="'+key+'"]');
    if($item.length){
      $item.find('.qty-input').val(quantity);
      recalcTotalsLocal();
    }
    // sync in background
    $.ajax({
      url: window.SHOP_ROUTES.qty,
      type: 'PATCH',
      data: { key, quantity },
      success: function(){ /* optionally refresh */ },
      error: function(){ loadCart(); }
    });
  }

  function removeFromCart(key){
    // optimistic: remove immediately
    const $item = $(ui.cartContainer).find('.cart-item[data-key="'+key+'"]');
    if($item.length){
      $item.remove();
      recalcTotalsLocal();
    }
    // sync in background
    $.ajax({
      url: window.SHOP_ROUTES.remove,
      type: 'DELETE',
      data: { key },
      success: function(){},
      error: function(){ loadCart(); }
    });
  }

  function emptyCart(){
    $.ajax({
      url: window.SHOP_ROUTES.empty,
      type: 'DELETE',
      success: function(){ loadCart(); toast('Cart emptied', 'info'); }
    });
  }

  function toast(message, type){
    const $t = $('<div class="toast-notification toast-'+(type||'info')+'">'+message+'</div>');
    const $container = $(ui.toastContainer);
    if($container.length){
      $container.append($t);
    } else {
      $('body').append($t);
    }
    setTimeout(function(){ $t.fadeOut(function(){ $t.remove(); }); }, 2500);
  }

  // Wire events (delegated where needed)
  $(document).ready(function(){
    loadCart();

    $(document).on('click', '.quick-view', function(){
      const id = $(this).data('id');
      if(id) showQuickView(id);
    });

    $(document).on('click', '.add-basic', function(){
      const id = $(this).data('id');
      if(id) addToCart(id, 1);
    });

    $(document).on('click', '.qty-btn', function(){
      const key = $(this).data('key');
      const action = $(this).data('action');
      const $input = $(this).siblings('.qty-input');
      const current = parseInt($input.val() || '1', 10);
      const next = action === 'plus' ? current + 1 : Math.max(1, current - 1);
      if(next !== current){ updateQuantity(key, next); }
    });

    $(document).on('change', '.qty-input', function(){
      const key = $(this).closest('.cart-item').data('key');
      const qty = Math.max(1, parseInt($(this).val() || '1', 10));
      updateQuantity(key, qty);
    });

    $(document).on('click', '.remove-item', function(){
      const key = $(this).data('key');
      if(key !== undefined){ removeFromCart(key); }
    });

    $('#btn-empty').on('click', function(){ emptyCart(); });
  });

  // Expose minimal API if needed
  window.ShopPOS = { loadCart };

})(window, window.jQuery);


