<?php

use App\Model\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

/**
 * Asset route for development server
 * This handles /assets/* requests when using php artisan serve
 */
Route::get('assets/{path}', function ($path) {
    $filePath = public_path("assets/{$path}");
    
    if (file_exists($filePath)) {
        $mimeType = mime_content_type($filePath);
        return response()->file($filePath, ['Content-Type' => $mimeType]);
    }
    
    abort(404);
})->where('path', '.*');

/**
 * Homepage with login options
 */
Route::get('/', [HomeController::class, 'homepage'])->name('homepage');

// Add kiosk route
Route::get('/kiosk', function () {
    return view('kiosk.index');
})->name('kiosk');

Route::get('/image-proxy', function () {
    $url = request('url');
    if (!$url) {
        abort(400, 'Missing url parameter');
    }

    $response = Http::withHeaders([
        'User-Agent' => 'Laravel-Image-Proxy'
    ])->get($url);

    return response($response->body(), $response->status())
        ->header('Content-Type', $response->header('Content-Type'))
        ->header('Access-Control-Allow-Origin', '*');
});

Route::post('/subscribeToTopic', [FirebaseController::class, 'subscribeToTopic']);

/**
 * Pages
 */
Route::get('about-us', [HomeController::class, 'about_us'])->name('about-us');
Route::get('terms-and-conditions', [HomeController::class, 'terms_and_conditions'])->name('terms-and-conditions');
Route::get('privacy-policy', [HomeController::class, 'privacy_policy'])->name('privacy-policy');
Route::get('return-policy', [HomeController::class, 'return'])->name('return-policy');
Route::get('refund-policy', [HomeController::class, 'refund'])->name('refund-policy');
Route::get('cancellation-policy', [HomeController::class, 'cancellation_policy'])->name('cancellation-policy');

/**
 * Auth
 */
Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthenticated.']);
    return response()->json([
        'errors' => $errors,
    ], 401);
})->name('authentication-failed');

/**
 * Payment
 */
// Payment gateway functionality removed

$is_published = 0;
try {
    $full_data = include('Modules/Gateways/Addon/info.php');
    $is_published = $full_data['is_published'] == 1 ? 1 : 0;
} catch (\Exception $exception) {}

// Payment gateway functionality removed

// Test route removed
