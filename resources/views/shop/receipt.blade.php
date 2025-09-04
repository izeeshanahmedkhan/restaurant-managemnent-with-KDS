<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #{{ $order->id }}</title>
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
                <p class="mb-0"><strong>Order #{{ $order->id }}</strong></p>
            </div>

            <!-- Body -->
            <div class="receipt-body">
                <!-- Order Info -->
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Date:</strong><br>
                        {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
                    </div>
                    <div class="col-6">
                        <strong>Time:</strong><br>
                        {{ \Carbon\Carbon::parse($order->created_at)->format('h:i A') }}
                    </div>
                </div>

                <!-- Customer Info -->
                @if($order->customer)
                <div class="mb-3">
                    <strong>Customer:</strong><br>
                    {{ $order->customer->f_name }} {{ $order->customer->l_name }}<br>
                    <small class="text-muted">{{ $order->customer->phone }}</small>
                </div>
                @endif

                <!-- Order Type -->
                <div class="mb-3">
                    <strong>Order Type:</strong> 
                    <span class="badge bg-primary">
                        {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                    </span>
                </div>

                <!-- Items -->
                <div class="mb-3">
                    <h6><strong>Items Ordered:</strong></h6>
                    @foreach($order->details as $detail)
                        <div class="receipt-item">
                            <div>
                                <strong>{{ $detail->product_details['name'] ?? 'Product' }}</strong>
                                <br>
                                <small class="text-muted">Qty: {{ $detail->quantity }} × ${{ number_format($detail->price, 2) }}</small>
                            </div>
                            <div class="text-end">
                                ${{ number_format($detail->price * $detail->quantity, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="border-top pt-3">
                    <div class="receipt-item">
                        <span>Subtotal:</span>
                        <span>${{ number_format($order->order_amount, 2) }}</span>
                    </div>
                    @if($order->total_tax_amount > 0)
                    <div class="receipt-item">
                        <span>Tax:</span>
                        <span>${{ number_format($order->total_tax_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($order->delivery_charge > 0)
                    <div class="receipt-item">
                        <span>Delivery Charge:</span>
                        <span>${{ number_format($order->delivery_charge, 2) }}</span>
                    </div>
                    @endif
                    <div class="receipt-item receipt-total">
                        <span>Total:</span>
                        <span>${{ number_format($order->order_amount + $order->total_tax_amount + $order->delivery_charge, 2) }}</span>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="mt-3">
                    <strong>Payment Method:</strong> 
                    <span class="badge bg-success">{{ ucfirst($order->payment_method) }}</span>
                    <br>
                    <strong>Payment Status:</strong> 
                    <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
                </div>

                @if($order->table_id)
                <div class="mt-2">
                    <strong>Table:</strong> {{ $order->table_id }}
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="receipt-footer">
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
