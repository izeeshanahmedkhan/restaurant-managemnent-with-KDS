<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chef\Auth\LoginController;
use App\Http\Controllers\Chef\DashboardController;
use App\Http\Controllers\Chef\KDSController;

Route::group(['namespace' => 'Chef', 'as' => 'chef.', 'middleware' => 'maintenance_mode'], function () {
    // Authentication Routes (Only accessible when NOT logged in)
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.', 'middleware' => 'guest:chef'], function () {
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'submit']);
    });

    // Logout Route (Only accessible when logged in)
    Route::get('auth/logout', [LoginController::class, 'logout'])->name('auth.logout')->middleware('chef');

    // Protected Routes (Require Chef Authentication)
    Route::group(['middleware' => ['chef']], function () {
        // Dashboard (KDS) - Root chef route
        Route::get('/', [KDSController::class, 'dashboard'])->name('dashboard');
        Route::get('dashboard', [KDSController::class, 'dashboard'])->name('dashboard');

        // KDS Routes
        Route::group(['prefix' => 'kds', 'as' => 'kds.'], function () {
            Route::get('orders', [KDSController::class, 'getOrders'])->name('orders');
            Route::put('orders/{id}/status', [KDSController::class, 'updateStatus'])->name('update-status');
            Route::get('search', [KDSController::class, 'searchOrders'])->name('search');
            Route::get('items-summary', [KDSController::class, 'getItemsSummary'])->name('items-summary');
            Route::get('items-board', [KDSController::class, 'getItemsBoard'])->name('items-board');
        });
    });
});
