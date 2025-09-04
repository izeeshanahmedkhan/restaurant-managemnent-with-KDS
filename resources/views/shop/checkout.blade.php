@extends('layouts.store')

@section('title', 'Checkout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-shopping-cart"></i>
                        Order Summary
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $subtotal = 0;
                                    $totalTax = 0;
                                @endphp
                                @foreach($cart as $item)
                                    @if(is_array($item))
                                        @php
                                            $itemTotal = ($item['price'] - $item['discount']) * $item['quantity'];
                                            $subtotal += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset('storage/product/' . $item['image']) }}"
                                                         alt="{{ $item['name'] }}"
                                                         class="img-thumbnail me-2"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <strong>{{ $item['name'] }}</strong>
                                                        @if(isset($item['variations']) && !empty($item['variations']))
                                                            <br><small class="text-muted">
                                                                @foreach($item['variations'] as $variation)
                                                                    {{ $variation['name'] ?? '' }}: {{ implode(', ', $variation['values']['label'] ?? []) }}
                                                                @endforeach
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>${{ number_format($item['price'] - $item['discount'], 2) }}</td>
                                            <td>{{ $item['quantity'] }}</td>
                                            <td>${{ number_format($itemTotal, 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card"></i>
                        Checkout
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('shop.checkout') }}" method="POST" id="checkoutForm">
                        @csrf

                        <!-- Order Type -->
                        <div class="mb-3">
                            <label class="form-label">Order Type</label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="order_type" id="pickup" value="take_away" checked>
                                        <label class="form-check-label" for="pickup">
                                            <i class="fas fa-store"></i> Pickup
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="order_type" id="delivery" value="home_delivery">
                                        <label class="form-check-label" for="delivery">
                                            <i class="fas fa-truck"></i> Delivery
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            @if($user)
                                <div class="alert alert-info">
                                    <i class="fas fa-user"></i>
                                    <strong>Logged in as:</strong> {{ $user->f_name }} {{ $user->l_name }}
                                    <br>
                                    <small>Email: {{ $user->email }} | Phone: {{ $user->phone }}</small>
                                </div>
                                <input type="hidden" name="customer_id" value="{{ $user->id }}">
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Not logged in!</strong> Please login to continue.
                                </div>
                            @endif
                        </div>



                        <!-- Delivery Address (for delivery) -->
                        <div class="mb-3" id="deliverySection" style="display: none;">
                            <label class="form-label">Delivery Address</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" class="form-control" name="contact_person_name" placeholder="Contact Name">
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control" name="contact_person_number" placeholder="Phone Number">
                                </div>
                            </div>
                            <textarea class="form-control mt-2" name="address" placeholder="Full Address" rows="3"></textarea>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked onchange="updatePaymentMethod()">
                                        <label class="form-check-label" for="cash">
                                            <i class="fas fa-money-bill-wave"></i> Cash
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="card" value="card" onchange="updatePaymentMethod()">
                                        <label class="form-check-label" for="card">
                                            <i class="fas fa-credit-card"></i> Card
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Paid -->
                        <div class="mb-3">
                            <label class="form-label">Amount Paid</label>
                            <input type="number" class="form-control" name="paid_amount" id="paidAmount" step="0.01" min="0" value="{{ $subtotal }}">
                        </div>

                        <!-- Order Summary -->
                        <!-- Order Total -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between">
                                <span>Subtotal:</span>
                                <span id="subtotal">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Tax:</span>
                                <span id="tax">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Delivery Charge:</span>
                                <span id="deliveryCharge">$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong id="total">${{ number_format($subtotal, 2) }}</strong>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 mt-3">
                            @if($user)
                                <button type="button" class="btn btn-success btn-lg" onclick="submitOrder()">
                                    <i class="fas fa-receipt"></i>
                                    Place Order
                                </button>
                                <script>
                                function submitOrder() {
                                    console.log('Submit order button clicked');

                                    const form = document.getElementById('checkoutForm');
                                    const submitBtn = document.querySelector('button[onclick="submitOrder()"]');
                                    const originalText = submitBtn.innerHTML;

                                    // Show loading
                                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                                    submitBtn.disabled = true;

                                    // Submit form via AJAX
                                    fetch(form.action, {
                                        method: 'POST',
                                        body: new FormData(form),
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        console.log('Order response:', data);
                                        if (data.success) {
                                            // Show receipt in modal
                                            showReceiptModal(data.order);
                                        } else {
                                            alert(data.message || 'Error placing order');
                                            submitBtn.innerHTML = originalText;
                                            submitBtn.disabled = false;
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('Error placing order. Please try again.');
                                        submitBtn.innerHTML = originalText;
                                        submitBtn.disabled = false;
                                    });
                                }

                                function showReceiptModal(order) {
                                    const modalHtml = `
                                        <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="receiptModalLabel">Print Invoice</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Invoice Content (Thermal Print Style) -->
                                                        <div style="font-family: monospace; font-size: 14px; line-height: 1.4; max-width: 300px; margin: 0 auto;">
                                                            <!-- Restaurant Header -->
                                                            <div style="text-align: center; margin-bottom: 15px;">
                                                                <div style="font-weight: bold; font-size: 16px;">FoodKing Restaurant</div>
                                                                <div style="font-size: 12px; color: #666;">Restaurant & Food Delivery</div>
                                                                <div style="font-size: 12px;">Phone : +1234567890</div>
                                                            </div>

                                                            <!-- Separator Line -->
                                                            <div style="border-top: 1px dashed #000; margin: 10px 0;"></div>

                                                            <!-- Order Details -->
                                                            <div style="margin-bottom: 10px;">
                                                                <div><strong>Order ID :</strong> ${order.id}</div>
                                                                <div><strong>Date :</strong> ${new Date(order.created_at).toLocaleDateString('en-GB')} ${new Date(order.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}</div>
                                                                <div><strong>Order Type :</strong> ${order.order_type === 'pos' ? 'Take Away' : 'Home Delivery'}</div>
                                                                <div><strong>Payment :</strong> ${order.payment_method.charAt(0).toUpperCase() + order.payment_method.slice(1)}</div>
                                                            </div>

                                                            <!-- Separator Line -->
                                                            <div style="border-top: 1px dashed #000; margin: 10px 0;"></div>

                                                            <!-- Items Table Header -->
                                                            <div style="display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 5px;">
                                                                <div style="width: 20%;">QTY</div>
                                                                <div style="width: 50%;">DESC</div>
                                                                <div style="width: 30%; text-align: right;">Price</div>
                                                            </div>

                                                            <!-- Items (Placeholder - you can add actual items here) -->
                                                            <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                                                <div style="width: 20%;">1</div>
                                                                <div style="width: 50%;">Order Items</div>
                                                                <div style="width: 30%; text-align: right;">$${parseFloat(order.order_amount).toFixed(2)}</div>
                                                            </div>

                                                            <!-- Separator Line -->
                                                            <div style="border-top: 1px dashed #000; margin: 10px 0;"></div>

                                                            <!-- Totals -->
                                                            <div style="margin-bottom: 5px;">
                                                                <div style="display: flex; justify-content: space-between;">
                                                                    <span>Subtotal:</span>
                                                                    <span>$${parseFloat(order.order_amount).toFixed(2)}</span>
                                                                </div>
                                                                ${order.total_tax_amount > 0 ? `
                                                                <div style="display: flex; justify-content: space-between;">
                                                                    <span>Tax:</span>
                                                                    <span>$${parseFloat(order.total_tax_amount).toFixed(2)}</span>
                                                                </div>
                                                                ` : ''}
                                                                ${order.delivery_charge > 0 ? `
                                                                <div style="display: flex; justify-content: space-between;">
                                                                    <span>Delivery:</span>
                                                                    <span>$${parseFloat(order.delivery_charge).toFixed(2)}</span>
                                                                </div>
                                                                ` : ''}
                                                                <div style="display: flex; justify-content: space-between; font-weight: bold; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
                                                                    <span>TOTAL:</span>
                                                                    <span>$${(parseFloat(order.order_amount) + parseFloat(order.total_tax_amount) + parseFloat(order.delivery_charge)).toFixed(2)}</span>
                                                                </div>
                                                            </div>

                                                            <!-- Separator Line -->
                                                            <div style="border-top: 1px dashed #000; margin: 10px 0;"></div>

                                                            <!-- Footer -->
                                                            <div style="text-align: center; font-size: 12px; color: #666;">
                                                                <div style="margin-bottom: 5px;">Payment: ${order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1)}</div>
                                                                <div>Thank you for your order!</div>
                                                                <div>Visit us again soon</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-warning" onclick="window.print()" style="background-color: #fd7e14; border-color: #fd7e14; color: white;">
                                                            Proceed, If thermal printer is ready.
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('shop.index') }}'" style="background-color: #e83e8c; border-color: #e83e8c; color: white;">
                                                            Back
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;

                                    // Remove existing modal if any
                                    const existingModal = document.getElementById('receiptModal');
                                    if (existingModal) {
                                        existingModal.remove();
                                    }

                                    // Add modal to body
                                    document.body.insertAdjacentHTML('beforeend', modalHtml);

                                    // Show modal
                                    const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
                                    modal.show();
                                }
                                </script>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Login to Continue
                                </a>
                            @endif
                            <a href="{{ route('shop.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back to Shop
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel">
                    <i class="fas fa-receipt"></i>
                    Invoice
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="invoiceContent">
                <!-- Invoice content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printInvoice()">
                    <i class="fas fa-print"></i>
                    Print Invoice
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Global function for submitting orders
function submitOrder() {
    console.log('Submit order button clicked');

    const form = document.getElementById('checkoutForm');
    const submitBtn = document.querySelector('button[onclick="submitOrder()"]');
    const originalText = submitBtn.innerHTML;

    // Show loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitBtn.disabled = true;

    // Submit form via AJAX
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Order response:', data);
        if (data.success) {
            // Show success message
            showToast('Order placed successfully!', 'success');

            // Redirect to shop page after 2 seconds
            setTimeout(function() {
                window.location.href = '{{ route("shop.index") }}';
            }, 2000);
        } else {
            showToast(data.message || 'Error placing order', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error placing order. Please try again.', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

$(document).ready(function() {
    // Order type change handler
    $('input[name="order_type"]').change(function() {
        const orderType = $(this).val();

        if (orderType === 'home_delivery') {
            // Delivery: Show address fields
            $('#deliverySection').show();
            $('input[name="contact_person_name"]').prop('required', true);
            $('input[name="contact_person_number"]').prop('required', true);
            $('textarea[name="address"]').prop('required', true);
        } else {
            // Pickup: Hide address fields
            $('#deliverySection').hide();
            $('input[name="contact_person_name"]').prop('required', false);
            $('input[name="contact_person_number"]').prop('required', false);
            $('textarea[name="address"]').prop('required', false);
        }
    });

    // Initialize order type visibility
    $('input[name="order_type"]:checked').trigger('change');

    // Initialize payment method
    updatePaymentMethod();



    // Form submission
    $('#checkoutForm').submit(function(e) {
        console.log('Form submitted via AJAX');
        e.preventDefault();

        // Show loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);

        // Submit form
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Order response:', response);
                console.log('Receipt URL:', response.receipt_url);

                if (response.success) {
                    // Show success message
                    showToast('Order placed successfully!', 'success');

                    // Redirect to shop page after 2 seconds
                    setTimeout(function() {
                        window.location.href = '{{ route("shop.index") }}';
                    }, 2000);
                } else {
                    showToast(response.message || 'Error placing order', 'error');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.log('AJAX Error:', xhr);
                console.log('Response Text:', xhr.responseText);

                let errorMessage = 'Error placing order. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showToast(errorMessage, 'error');
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
});

function updatePaymentMethod() {
    const paymentMethod = $('input[name="payment_method"]:checked').val();
    const totalAmount = parseFloat($('#total').text().replace('$', ''));

    if (paymentMethod === 'card') {
        // For card payment, set amount paid to total and make readonly
        $('#paidAmount').val(totalAmount).prop('readonly', true);
    } else {
        // For cash payment, make editable
        $('#paidAmount').prop('readonly', false);
    }


}



function showReceipt(order) {
    console.log('Showing receipt for order:', order);

    const receiptHtml = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Receipt - Order #${order.id}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                @media print {
                    .no-print { display: none !important; }
                    body { font-size: 12px; }
                    .container { max-width: none !important; }
                }

                .receipt-container {
                    max-width: 400px;
                    margin: 0 auto;
                    background: white;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                }

                .receipt-header {
                    text-align: center;
                    padding: 20px;
                    border-bottom: 2px solid #007bff;
                }

                .receipt-body {
                    padding: 20px;
                }

                .receipt-item {
                    display: flex;
                    justify-content: space-between;
                    padding: 8px 0;
                    border-bottom: 1px dotted #ccc;
                }

                .receipt-total {
                    font-weight: bold;
                    font-size: 1.2em;
                    color: #007bff;
                }

                .receipt-footer {
                    text-align: center;
                    padding: 20px;
                    border-top: 2px solid #007bff;
                    font-size: 0.9em;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="container mt-4">
                <div class="receipt-container">
                    <!-- Header -->
                    <div class="receipt-header">
                        <h3 class="mb-2">
                            <i class="fas fa-utensils text-primary"></i>
                            FoodKing Restaurant
                        </h3>
                        <p class="mb-1">Order Receipt</p>
                        <p class="mb-0"><strong>Order #${order.id}</strong></p>
                    </div>

                    <!-- Body -->
                    <div class="receipt-body">
                        <!-- Order Info -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Date:</strong><br>
                                ${new Date(order.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                            </div>
                            <div class="col-6">
                                <strong>Time:</strong><br>
                                ${new Date(order.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}
                            </div>
                        </div>

                        <!-- Order Type -->
                        <div class="mb-3">
                            <strong>Order Type:</strong>
                            <span class="badge bg-primary">
                                ${order.order_type === 'pos' ? 'Take Away' : 'Home Delivery'}
                            </span>
                        </div>

                        <!-- Payment Info -->
                        <div class="mb-3">
                            <strong>Payment Method:</strong>
                            <span class="badge bg-success">${order.payment_method.charAt(0).toUpperCase() + order.payment_method.slice(1)}</span>
                            <br>
                            <strong>Payment Status:</strong>
                            <span class="badge bg-success">${order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1)}</span>
                        </div>

                        <!-- Totals -->
                        <div class="border-top pt-3">
                            <div class="receipt-item">
                                <span>Subtotal:</span>
                                <span>$${parseFloat(order.order_amount).toFixed(2)}</span>
                            </div>
                            ${order.total_tax_amount > 0 ? `
                            <div class="receipt-item">
                                <span>Tax:</span>
                                <span>$${parseFloat(order.total_tax_amount).toFixed(2)}</span>
                            </div>
                            ` : ''}
                            ${order.delivery_charge > 0 ? `
                            <div class="receipt-item">
                                <span>Delivery Charge:</span>
                                <span>$${parseFloat(order.delivery_charge).toFixed(2)}</span>
                            </div>
                            ` : ''}
                            <div class="receipt-item receipt-total">
                                <span>Total:</span>
                                <span>$${(parseFloat(order.order_amount) + parseFloat(order.total_tax_amount) + parseFloat(order.delivery_charge)).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="receipt-footer">
                        <div class="alert alert-success mb-3" role="alert">
                            <i class="fas fa-check-circle"></i> <strong>Payment Completed Successfully!</strong>
                        </div>
                        <p class="mb-2">Thank you for your order!</p>
                        <p class="mb-0">Visit us again soon</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4 no-print">
                    <button onclick="window.print()" class="btn btn-primary me-2">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    <a href="{{ route('shop.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Shop
                    </a>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>
        </html>
    `;

    $('body').html(receiptHtml);
}

function showToast(message, type = 'info') {
    const toast = $(`
        <div class="toast-notification ${type}" style="
            position: fixed;
            top: 20px;
            left: 20px;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
            font-weight: 500;
            max-width: 300px;
        ">
            ${message}
    </div>
    `);

    $('body').append(toast);

    setTimeout(function() {
        toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

function printInvoice() {
    const invoiceContent = document.getElementById('invoiceContent');
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Invoice</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .invoice-header { text-align: center; margin-bottom: 20px; }
                    .invoice-details { margin-bottom: 20px; }
                    .invoice-table { width: 100%; border-collapse: collapse; }
                    .invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    .invoice-total { margin-top: 20px; text-align: right; }
                </style>
            </head>
            <body>
                ${invoiceContent.innerHTML}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>
@endsection
