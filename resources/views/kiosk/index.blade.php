<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restaurant Kiosk</title>
    @vite(['resources/sass/app.css', 'resources/js/kiosk/main.jsx'])
</head>
<body class="bg-gray-100">
    <div id="kiosk-app" class="min-h-screen">
        <div class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading Kiosk...</p>
            </div>
        </div>
    </div>
</body>
</html>
