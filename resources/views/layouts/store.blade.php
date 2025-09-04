<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop')</title>
    <!-- Bootstrap 4.5.3 -->
{{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">--}}
{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>--}}
{{--    <style>--}}
{{--        body{background:#f8fafc}--}}
{{--        .product-card{transition:.2s}--}}
{{--        .product-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.08)}--}}
{{--        .sticky-cart{position:sticky;top:1rem;max-height:calc(100vh - 2rem);overflow:auto}--}}
{{--        .cursor-pointer{cursor:pointer}--}}
{{--        .line-through{text-decoration:line-through}--}}
{{--    </style>--}}
    @stack('head')
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/style.css?v=1.0">
    <style>
        body{overflow-x:hidden}
        /* Off-canvas cart sidebar */
        .cart-overlay{position:fixed;inset:0;background:rgba(0,0,0,.35);opacity:0;visibility:hidden;transition:all .2s;z-index:1040}
        .cart-overlay.active{opacity:1;visibility:visible}
        .cart-sidebar{position:fixed;top:0;right:-380px;width:360px;max-width:90vw;height:100vh;background:#fff;box-shadow:-8px 0 24px rgba(0,0,0,.1);z-index:1050;transition:right .25s}
        .cart-sidebar.active{right:0}
        .cart-sidebar .cart-header{padding:12px 16px;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between}
        .cart-sidebar .cart-body{height:calc(100vh - 132px);overflow:auto}
        .cart-sidebar .cart-footer{padding:12px 16px;border-top:1px solid #eee}
        .navbar .cart-toggle{border:0;background:#ff6b57;color:#fff;border-radius:6px;padding:.35rem .6rem;display:flex;align-items:center;gap:.35rem}
        .navbar .cart-toggle .badge{background:#fff;color:#ff6b57}

        /* Product grid cards */
        .product-card{transition:.2s;border:1px solid #eee}
        .product-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.08)}
        .product-card .img-wrap{position:relative;width:100%;padding-top:75%;background:#f6f6f6;border-bottom:1px solid #eee}
        .product-card .img-wrap img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
        .product-card .card-body{display:flex;flex-direction:column}
        .product-card .card-title{font-size:14px;font-weight:600}
        .product-card .price{font-weight:700}
        .product-card .actions{margin-top:auto;display:flex;gap:.5rem}

        /* Toasts - top-left */
        #toast-container{position:fixed;top:12px;left:12px;z-index:1080;display:flex;flex-direction:column;gap:8px}
        .toast-notification{background:#333;color:#fff;padding:8px 12px;border-radius:6px;box-shadow:0 6px 20px rgba(0,0,0,.18);max-width:280px;font-size:14px;opacity:.95}
    </style>
    @stack('css_or_js')

    <script
        src="{{asset('assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">
</head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm">
    <a class="navbar-brand" href="{{ route('shop.index') }}">My Store</a>
    <button class="cart-toggle" id="open-cart" type="button">
        <i class="tio-shopping-cart"></i>
        Cart <span class="badge" id="cart-count">0</span>
    </button>
 </nav>
<main class="container-fluid py-4">@yield('content')</main>


<!-- Modals -->
<div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body" id="quick-view-body"><div class="text-center py-5">Loading…</div></div>
        </div>
    </div>
</div>

<!-- Off-canvas Cart Sidebar -->
<div class="cart-overlay" id="cart-overlay"></div>
<aside class="cart-sidebar" id="cart-sidebar">
    <div class="cart-header">
        <h6 class="mb-0">Cart</h6>
        <div>
            <button class="btn btn-sm btn-outline-danger" id="cart-empty-btn">Empty</button>
            <button class="btn btn-sm btn-outline-secondary" id="close-cart">Close</button>
        </div>
    </div>
    <div class="cart-body" id="cart-container">
        <div class="p-3 text-center text-muted">Loading cart…</div>
    </div>
    <div class="cart-footer">
        <a href="{{ route('shop.checkout.page') }}" class="btn btn-primary btn-block">Checkout</a>
    </div>
 </aside>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.SHOP_ROUTES = {
        quickView: "{{ route('shop.quick-view') }}",
        variantPrice: "{{ route('shop.variant-price') }}",
        add: "{{ route('shop.add') }}",
        cart: "{{ route('shop.cart.page') }}",
        qty: "{{ route('shop.qty') }}",
        remove: "{{ route('shop.remove') }}",
        empty: "{{ route('shop.empty') }}",
        orderType: "{{ route('shop.order-type') }}",
        deliveryInfo: "{{ route('shop.delivery-info') }}",
        checkout: "{{ route('shop.checkout') }}",
    };</script>
    <div id="toast-container"></div>
    <script src="{{ asset('js/shop-pos-standalone.js') }}"></script>
@stack('scripts')

<script>
    "use strict";

    $('.print-button').click(function() {
        printDiv('printableArea');
    });

    $('.delivery-address-update-button').click(function() {
        deliveryAdressStore();
    });

    $('.quick-view-trigger').click(function() {
        var productId = $(this).data('product-id');
        quickView(productId);
    });

    $('.category').change(function() {
        var selectedCategory = $(this).val();
        set_category_filter(selectedCategory);
    });

    $('.customer').change(function() {
        var selectedCustomerId = $(this).val();
        store_key('customer_id', selectedCustomerId);
    });

    $('.branch').change(function() {
        var selectedBranchId = $(this).val();
        store_key('branch_id', selectedBranchId);
    });

    $('.order-type-radio').change(function() {
        var selectedOrderType = $(this).val();
        select_order_type(selectedOrderType);
    });

    $('.select-table').change(function() {
        var selectedTableId = $(this).val();
        store_key('table_id', selectedTableId);
    });

    $('#number_of_people').keyup(function() {
        var numberOfPeople = $(this).val().replace(/[^\d]/g, '');
        $(this).val(numberOfPeople);
        store_key('people_number', numberOfPeople);
    });

    $('.sign-out-trigger').click(function(event) {
        event.preventDefault();
        Swal.fire({
            title: '{{translate('Do you want to logout')}}?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonColor: '#FC6A57',
            cancelButtonColor: '#363636',
            confirmButtonText: '{{translate('Yes')}}',
            denyButtonText: `{{translate('Do not Logout')}}`
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{route('admin.auth.logout')}}';
            } else {
                Swal.fire('Canceled', '', 'info');
            }
        });
    });



    function printDiv(divName) {

        if($('html').attr('dir') === 'rtl') {
            $('html').attr('dir', 'ltr')
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            $('#printableAreaContent').attr('dir', 'rtl')
            window.print();
            document.body.innerHTML = originalContents;
            $('html').attr('dir', 'rtl')
            location.reload();
        }else{
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }

    }

    function set_category_filter(id) {
        var nurl = new URL('{!!url()->full()!!}');
        nurl.searchParams.set('category_id', id);
        location.href = nurl;
    }
    // Cart sidebar toggle + wire routes from SHOP_ROUTES
    (function(){
        const sidebar = $('#cart-sidebar');
        const overlay = $('#cart-overlay');
        const openBtn = $('#open-cart');
        const closeBtn = $('#close-cart');
        const emptyBtn = $('#cart-empty-btn');

        function openCart(){ sidebar.addClass('active'); overlay.addClass('active'); loadCartItems(); }
        function closeCart(){ sidebar.removeClass('active'); overlay.removeClass('active'); }
        function loadCartItems(){
            $.get(window.SHOP_ROUTES.cart + '/items', function(html){
                $('#cart-container').html(html);
                const count = $(html).find('.cart-item').length;
                $('#cart-count').text(count);
            });
        }
        openBtn.on('click', openCart);
        closeBtn.on('click', closeCart);
        overlay.on('click', closeCart);
        emptyBtn.on('click', function(){
            $.ajax({url: window.SHOP_ROUTES.empty, type: 'DELETE', data:{_token: $('meta[name="csrf-token"]').attr('content')}, success: loadCartItems});
        });

        // initial load to set badge
        loadCartItems();
    })();



    $('#search-form').on('submit', function (e) {
        e.preventDefault();
        var keyword= $('#datatableSearch').val();
        var nurl = new URL('{!!url()->full()!!}');
        nurl.searchParams.set('keyword', keyword);
        location.href = nurl;
    });

    function addon_quantity_input_toggle(e)
    {
        var cb = $(e.target);
        if(cb.is(":checked"))
        {
            cb.siblings('.addon-quantity-input').css({'visibility':'visible'});
        }
        else
        {
            cb.siblings('.addon-quantity-input').css({'visibility':'hidden'});
        }
    }
    function quickView(product_id) {
        $.ajax({
            url: '{{route('admin.pos.quick-view')}}',
            type: 'GET',
            data: {
                product_id: product_id
            },
            dataType: 'json',
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                $('#quick-view').modal('show');
                $('#quick-view-modal').empty().html(data.view);
            },
            complete: function () {
                $('#loading').hide();
            },
        });

    }

    function checkAddToCartValidity() {
        return true;
    }

    function cartQuantityInitialize() {
        $('.btn-number').click(function (e) {
            e.preventDefault();

            var fieldName = $(this).attr('data-field');
            var type = $(this).attr('data-type');
            var stock_type = $(this).attr('data-stock_type');
            var input = $("input[name='" + fieldName + "']");
            var currentVal = parseInt(input.val());
            var minVal = parseInt(input.attr('min'));
            var maxVal = parseInt(input.attr('max'));

            if (!isNaN(currentVal)) {
                if (type === 'minus') {
                    if (currentVal > minVal) {
                        input.val(currentVal - 1).change();
                    }
                    if (parseInt(input.val()) <= minVal) {
                        $(this).attr('disabled', true);
                    }

                    // Enable plus button when minus clicked
                    $(".btn-number[data-type='plus'][data-field='" + fieldName + "']").removeAttr('disabled');
                }
                else if (type === 'plus') {
                    if (stock_type === 'unlimited' || currentVal < maxVal) {
                        input.val(currentVal + 1).change();
                        $(".btn-number[data-type='minus'][data-field='" + fieldName + "']").removeAttr('disabled');
                    }

                    if (stock_type !== 'unlimited' && currentVal + 1 >= maxVal + 1) {
                        $(this).attr('disabled', true);

                        Swal.fire({
                            icon: 'warning',
                            title: '{{ translate("Cart") }}',
                            text: '{{ translate("You have reached the maximum available stock.") }}',
                            confirmButtonText: '{{ translate("OK") }}'
                        });
                    }
                }
            } else {
                input.val(1);
            }
        });

        $('.input-number').focusin(function () {
            $(this).data('oldValue', $(this).val());
        });

        $('.input-number').change(function () {

            var minValue = parseInt($(this).attr('min'));
            var maxValue = parseInt($(this).attr('max'));
            var stock_type = $("button[data-field='" + $(this).attr('name') + "']").data('stock_type');
            var valueCurrent = parseInt($(this).val());
            var name = $(this).attr('name');

            if (valueCurrent >= minValue) {
                $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
            } else {
                Swal.fire({
                    icon: 'error',
                    title:'{{translate("Cart")}}',
                    text: '{{translate('Sorry, the minimum value was reached')}}'
                });
                $(this).val($(this).data('oldValue'));
            }

            if (valueCurrent < minValue) {
                Swal.fire({
                    icon: 'error',
                    title: '{{translate("Cart")}}',
                    text: '{{translate("Sorry, the minimum value was reached")}}'
                });
                $(this).val($(this).data('oldValue'));
                return;
            }

            if (stock_type !== 'unlimited' && valueCurrent > maxValue) {
                Swal.fire({
                    icon: 'error',
                    title: '{{translate("Cart")}}',
                    confirmButtonText: '{{translate("Ok")}}',
                    text: '{{translate("Sorry, stock limit exceeded")}}.'
                });
                $(this).val($(this).data('oldValue'));
                return;
            }

            $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled');
            $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled');
        });

        $(".input-number").keydown(function (e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
    }

    function getVariantPrice() {
        if ($('#add-to-cart-form input[name=quantity]').val() > 0 && checkAddToCartValidity()) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: '{{ route('admin.pos.variant_price') }}',
                data: $('#add-to-cart-form').serializeArray(),
                success: function (data) {
                    if(data.error == 'quantity_error'){
                        toastr.error(data.message);
                    }
                    else{
                        $('#add-to-cart-form #chosen_price_div').removeClass('d-none');
                        $('#add-to-cart-form #chosen_price_div #chosen_price').html(data.price);
                    }
                }
            });
        }
    }

    function addToCart(form_id = 'add-to-cart-form') {
        if (checkAddToCartValidity()) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('admin.pos.add-to-cart') }}',
                data: $('#' + form_id).serializeArray(),
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    if (data.data == 1) {
                        Swal.fire({
                            confirmButtonColor: '#FC6A57',
                            icon: 'info',
                            title: '{{translate("Cart")}}',
                            confirmButtonText:'{{translate("Ok")}}',
                            text: "{{translate('Product already added in cart')}}"
                        });
                        return false;
                    } else if (data.data == 0) {
                        Swal.fire({
                            confirmButtonColor: '#FC6A57',
                            icon: 'error',
                            title: '{{translate("Cart")}}',
                            confirmButtonText:'{{translate("Ok")}}',
                            text: '{{translate('Sorry, product out of stock')}}.'
                        });
                        return false;
                    }
                    else if (data.data == 'variation_error') {
                        Swal.fire({
                            confirmButtonColor: '#FC6A57',
                            icon: 'error',
                            title: 'Cart',
                            text: data.message
                        });
                        return false;
                    }
                    else if (data.data == 'stock_limit') {
                        Swal.fire({
                            confirmButtonColor: '#FC6A57',
                            icon: 'error',
                            title: 'Cart',
                            text: data.message
                        });
                        return false;
                    }
                    $('.call-when-done').click();

                    toastr.success('{{translate('Item has been added in your cart')}}!', {
                        CloseButton: true,
                        ProgressBar: true
                    });

                    updateCart();
                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        } else {
            Swal.fire({
                confirmButtonColor: '#FC6A57',
                type: 'info',
                title: '{{translate("Cart")}}',
                confirmButtonText:'{{translate("Ok")}}',
                text: '{{translate('Please choose all the options')}}'
            });
        }
    }

    function removeFromCart(key) {
        $.post('{{ route('admin.pos.remove-from-cart') }}', {_token: '{{ csrf_token() }}', key: key}, function (data) {
            if (data.errors) {
                for (var i = 0; i < data.errors.length; i++) {
                    toastr.error(data.errors[i].message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            } else {
                updateCart();
                toastr.info('{{translate('Item has been removed from cart')}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            }

        });
    }

    function emptyCart() {
        $.post('{{ route('admin.pos.emptyCart') }}', {_token: '{{ csrf_token() }}'}, function (data) {
            updateCart();
            toastr.info('{{translate('Item has been removed from cart')}}', {
                CloseButton: true,
                ProgressBar: true
            });
            location.reload();
        });
    }

    function updateCart() {
        $.post('<?php echo e(route('admin.pos.cart_items')); ?>', {_token: '<?php echo e(csrf_token()); ?>'}, function (data) {
            $('#cart').empty().html(data);
        });
    }

    $(function(){
        $(document).on('click','input[type=number]',function(){ this.select(); });
    });


    function updateQuantity(e){
        var element = $( e.target );
        var minValue = parseInt(element.attr('min'));
        var valueCurrent = parseInt(element.val());

        var key = element.data('key');
        if (valueCurrent >= minValue) {
            $.post('{{ route('admin.pos.updateQuantity') }}', {_token: '{{ csrf_token() }}', key: key, quantity:valueCurrent}, function (data) {
                updateCart();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '{{translate("Cart")}}',
                confirmButtonText:'{{translate("Ok")}}',
                text: '{{translate('Sorry, the minimum value was reached')}}'
            });
            element.val(element.data('oldValue'));
        }
        if(e.type == 'keydown')
        {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        }

    };

    $('.branch-data-selector').select2();
    $('.table-data-selector').select2();

    $('.js-data-example-ajax').select2({
        ajax: {
            url: '{{route('admin.pos.customers')}}',
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            __port: function (params, success, failure) {
                var $request = $.ajax(params);

                $request.then(success);
                $request.fail(failure);

                return $request;
            }
        }
    });


    $('#order_place').submit(function(eventObj) {
        if($('#customer').val())
        {
            $(this).append('<input type="hidden" name="user_id" value="'+$('#customer').val()+'" /> ');
        }
        return true;
    });

    function store_key(key, value) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });
        $.post({
            url: '{{route('admin.pos.store-keys')}}',
            data: {
                key:key,
                value:value,
            },
            success: function (data) {
                var selected_field_text = key;
                var selected_field = selected_field_text.replace("_", " ");
                var selected_field = selected_field.replace("id", " ");
                var message = selected_field+' '+'selected!';
                var new_message = message.charAt(0).toUpperCase() + message.slice(1);
                toastr.success((new_message), {
                    CloseButton: true,
                    ProgressBar: true
                });
            },

        });
    };


    $(document).ready(function (){
        $('#change-branch').on('change', function (){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: "{{ url('admin/pos/session-destroy') }}",
                success: function() {
                    location.reload();
                }
            });
        });
    });

    $(document).ready(function() {
        var orderType = {!! json_encode(session('order_type')) !!};

        if (orderType === 'dine_in') {
            $('#dine_in_section').removeClass('d-none');
        } else if (orderType === 'home_delivery') {
            $('#home_delivery_section').removeClass('d-none');
            $('#dine_in_section').addClass('d-none');
        } else {
            $('#home_delivery_section').addClass('d-none');
            $('#dine_in_section').addClass('d-none');
        }
    });

    function select_order_type(order_type) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{csrf_token()}}"
            }
        });
        $.post({
            url: '{{route('admin.pos.order_type.store')}}',
            data: {
                order_type:order_type,
            },
            success: function (data) {
                updateCart();
            },
        });

        if (order_type == 'dine_in') {
            $('#dine_in_section').removeClass('d-none');
            $('#home_delivery_section').addClass('d-none')
        } else if(order_type == 'home_delivery') {
            $('#home_delivery_section').removeClass('d-none');
            $('#dine_in_section').addClass('d-none');
        }else{
            $('#home_delivery_section').addClass('d-none')
            $('#dine_in_section').addClass('d-none');
        }
    }


    // Update paid-by radio button handler
    $('.paid-by').change(function() {
        var selectedPaymentOption = $(this).val();

        // Get total order amount from the displayed value
        var totalOrderAmount = $('.hidden-paid-amount').val();

        // Toggle collect cash section visibility
        if (selectedPaymentOption == 'pay_after_eating') {
            $('.collect-cash-section').addClass('d-none');
        } else {
            $('.collect-cash-section').removeClass('d-none');
        }

        // Toggle readonly attribute for paid amount input
        if (selectedPaymentOption == 'card') {
            $('#paid-amount').attr('readonly', true);
            $('#paid-amount').addClass('bg-F5F5F5');
            // Reset paid amount to order amount
            $('#paid-amount').val(totalOrderAmount);
            calculateAmountDifference();
        } else {
            $('#paid-amount').removeAttr('readonly');
            $('#paid-amount').removeClass('bg-F5F5F5');
        }
    });


    $( document ).ready(function() {
        function initAutocomplete() {
            var myLatLng = {

                lat: 23.811842872190343,
                lng: 90.356331
            };
            const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                center: {
                    lat: 23.811842872190343,
                    lng: 90.356331
                },
                zoom: 13,
                mapTypeId: "roadmap",
            });

            var marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
            });

            marker.setMap(map);
            var geocoder = geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(map, 'click', function(mapsMouseEvent) {
                var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                var coordinates = JSON.parse(coordinates);
                var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                marker.setPosition(latlng);
                map.panTo(latlng);

                document.getElementById('latitude').value = coordinates['lat'];
                document.getElementById('longitude').value = coordinates['lng'];

                geocoder.geocode({
                    'latLng': latlng
                }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            document.getElementById('address').value = results[1].formatted_address;
                        }
                    }
                });
            });

            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });
            let markers = [];

            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];

                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        return;
                    }
                    var mrkr = new google.maps.Marker({
                        map,
                        title: place.name,
                        position: place.geometry.location,
                    });
                    google.maps.event.addListener(mrkr, "click", function(event) {
                        document.getElementById('latitude').value = this.position.lat();
                        document.getElementById('longitude').value = this.position.lng();

                    });

                    markers.push(mrkr);

                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        };
        initAutocomplete();
    });

    function deliveryAdressStore(form_id = 'delivery_address_store') {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.post({
            url: '{{ route('admin.pos.add-delivery-address') }}',
            data: $('#' + form_id).serializeArray(),
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(data) {
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    $('#del-add').empty().html(data.view);
                }
                updateCart();
                $('.call-when-done').click();
            },
            complete: function() {
                $('#loading').hide();
            }
        });
    }

    $(document).on('ready', function () {
        $('.js-select2-custom-x').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });
    });

    $(document).ready(function() {
        const $areaDropdown = $('#areaDropdown');
        const $deliveryChargeInput = $('#deliveryChargeInput');

        $areaDropdown.change(function() {
            const selectedOption = $(this).find('option:selected');
            const charge = selectedOption.data('charge');
            $deliveryChargeInput.val(charge);
        });
    });

</script>

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>

</body>
</html>
