<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chef\Auth\LoginController;
use App\Http\Controllers\Chef\DashboardController;
use App\Http\Controllers\Chef\OrderController;

Route::group(['namespace' => 'Chef', 'as' => 'chef.'], function () {
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'submit']);
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::group(['middleware' => ['chef', 'chef_status']], function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/kds', [OrderController::class, 'kds'])->name('kds');
        Route::post('/kds/update-status', [OrderController::class, 'updateOrderStatus'])->name('kds.update-status');
        Route::get('/test-data', function() {
            $chef = auth('chef')->user();
            $chefBranch = \App\Model\ChefBranch::where('user_id', $chef->id)->first();
            $totalOrders = \App\Model\Order::count();
            $ordersWithBranch = \App\Model\Order::whereNotNull('branch_id')->count();
            $sampleOrders = \App\Model\Order::take(5)->get(['id', 'branch_id', 'order_status']);
            
            return response()->json([
                'chef_id' => $chef->id,
                'chef_user_type' => $chef->user_type,
                'chef_branch' => $chefBranch ? $chefBranch->branch_id : null,
                'total_orders' => $totalOrders,
                'orders_with_branch' => $ordersWithBranch,
                'sample_orders' => $sampleOrders
            ]);
        })->name('test-data');
        Route::post('order-stats', [DashboardController::class, 'orderStats'])->name('order-stats');
        Route::get('order-statistics', [DashboardController::class, 'orderStatistics'])->name('order-statistics');
        Route::get('earning-statistics', [DashboardController::class, 'earningStatistics'])->name('earning-statistics');
        
        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
            Route::post('increase-preparation-time/{id}', [OrderController::class, 'preparationTime'])->name('increase-preparation-time');
            Route::get('status', [OrderController::class, 'status'])->name('status');
            Route::get('payment-status', [OrderController::class, 'paymentStatus'])->name('payment-status');

            Route::post('add-payment-ref-code/{id}', [OrderController::class, 'addPaymentReferenceCode'])->name('add-payment-ref-code');

            Route::get('ajax-change-delivery-time-date/{order_id}', [OrderController::class, 'changeDeliveryTimeDate'])->name('ajax-change-delivery-time-date');
            Route::get('verify-offline-payment/{order_id}/{status}', [OrderController::class, 'verifyOfflinePayment']);
            Route::post('update-order-delivery-area/{order_id}', [OrderController::class, 'updateOrderDeliveryArea'])->name('update-order-delivery-area');
        });
    });
});
