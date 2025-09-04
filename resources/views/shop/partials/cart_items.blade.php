@if(session()->has('cart') && count(session()->get('cart')) > 0)
    @php
        $cart = session()->get('cart');
        $total = 0;
        $itemCount = 0;
        $quantityCount = 0;
    @endphp
    
    @foreach(session()->get('cart') as $key => $cartItem)
        @if(is_array($cartItem))
            @php
                $itemCount++;
                $quantityCount += (int)($cartItem['quantity'] ?? 1);
                $productSubtotal = ($cartItem['price']) * $cartItem['quantity'];
                $total += $productSubtotal;
            @endphp
            <div class="cart-item d-flex align-items-center mb-2 p-2 border-bottom" data-key="{{ $key }}" data-price="{{ $cartItem['price'] }}">
                <img src="{{ asset('storage/app/product') }}/{{ $cartItem['image'] }}" 
                     onerror="this.src='{{ asset('assets/admin/img/160x160/img2.jpg') }}'" 
                     alt="{{ $cartItem['name'] }}" 
                     class="cart-item-image" style="width: 50px; height: 50px; object-fit: cover;">
                <div class="flex-grow-1 ml-2">
                    <h6 class="mb-1">{{ Str::limit($cartItem['name'], 20) }}</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="qty-controls d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary qty-btn" data-action="minus" data-key="{{ $key }}">-</button>
                            <input type="number" class="form-control form-control-sm qty-input text-center mx-1" 
                                   value="{{ $cartItem['quantity'] }}" min="1" style="width: 50px;">
                            <button class="btn btn-sm btn-outline-secondary qty-btn" data-action="plus" data-key="{{ $key }}">+</button>
                        </div>
                        <div class="text-right">
                            <div class="font-weight-bold item-subtotal" data-raw="{{ $productSubtotal }}">{{ \App\CentralLogics\Helpers::set_symbol($productSubtotal) }}</div>
                            <button class="btn btn-sm btn-danger px-2 remove-item" data-key="{{ $key }}" aria-label="Remove">&times;</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    
    <div class="cart-summary mt-3 p-3 bg-light">
        <div class="d-flex justify-content-between mb-2">
            <span>Total Items:</span>
            <span class="font-weight-bold" id="cart-total-items">{{ $itemCount }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>Subtotal:</span>
            <span class="font-weight-bold" id="cart-subtotal-amount">{{ \App\CentralLogics\Helpers::set_symbol($total) }}</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
            <span class="font-weight-bold">Total:</span>
            <span class="font-weight-bold text-primary" id="cart-total-amount">{{ \App\CentralLogics\Helpers::set_symbol($total) }}</span>
        </div>
    </div>
    <span id="cart-count-value" data-count="{{ $itemCount }}" data-qty="{{ $quantityCount }}" class="d-none"></span>
@else
    <div class="text-center p-4">
        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
        <p class="text-muted">Your cart is empty</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary">Start Shopping</a>
    </div>
@endif