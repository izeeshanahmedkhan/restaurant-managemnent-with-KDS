<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
</head>
<body>
    <h2>Password Reset Request</h2>
    <p>Hello {{ $customer_name }},</p>
    <p>You have requested to reset your password. Please use the following code to reset your password:</p>
    <h3>{{ $code }}</h3>
    <p>If you did not request this password reset, please ignore this email.</p>
    <p>Best regards,<br>{{ $company_name }}</p>
</body>
</html>

