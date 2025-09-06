<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Placed</title>
</head>
<body>
    <h2>Order Placed Successfully</h2>
    <p>Hello {{ $user_name }},</p>
    <p>Your order has been placed successfully at {{ $restaurant_name }}.</p>
    <p>Order ID: {{ $order->id }}</p>
    <p>Total Amount: {{ $order->order_amount }}</p>
    @if($delivery_man_name)
        <p>Delivery Man: {{ $delivery_man_name }}</p>
    @endif
    <p>Thank you for your order!</p>
    <p>Best regards,<br>{{ $company_name }}</p>
</body>
</html>

