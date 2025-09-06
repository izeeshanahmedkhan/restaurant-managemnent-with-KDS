<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - Order #{{ $order->id }}</title>
</head>
<body>
    <h2>Invoice</h2>
    <p>Order ID: {{ $order->id }}</p>
    <p>Customer: {{ $order->customer->f_name }} {{ $order->customer->l_name }}</p>
    <p>Total Amount: {{ $order->order_amount }}</p>
    <p>Date: {{ $order->created_at }}</p>
</body>
</html>

