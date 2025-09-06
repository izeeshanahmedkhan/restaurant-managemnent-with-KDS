<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Man Registration</title>
</head>
<body>
    <h2>Delivery Man Registration</h2>
    <p>Hello {{ $dm_name }},</p>
    @if($status == 'approved')
        <p>Congratulations! Your delivery man registration has been approved.</p>
    @elseif($status == 'denied')
        <p>Unfortunately, your delivery man registration has been denied.</p>
    @else
        <p>Your delivery man registration is being reviewed.</p>
    @endif
    <p>Best regards,<br>{{ $company_name }}</p>
</body>
</html>

