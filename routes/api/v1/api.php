<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\CustomerAuthController;
use App\Http\Controllers\Api\V1\Auth\KitchenLoginController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
// Banner functionality removed
use App\Http\Controllers\Api\V1\BranchController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ConfigController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\GuestUserController;
use App\Http\Controllers\Api\V1\KitchenController;
use App\Http\Controllers\Api\V1\MapApiController;
use App\Http\Controllers\Api\V1\KioskController;
// Notification and OfflinePayment functionality removed
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\WishlistController;

Route::group(['namespace' => 'Api\V1', 'middleware' => 'localization'], function () {


    // Kiosk API routes
    Route::group(['prefix' => 'kiosk'], function () {
        // Authentication routes (no middleware)
        Route::post('auth/login', [KioskController::class, 'login']);
        
        // Protected routes (require authentication)
        Route::group(['middleware' => ['kiosk.auth', 'kiosk.branch.filter']], function () {
            Route::post('auth/logout', [KioskController::class, 'logout']);
            Route::get('auth/me', [KioskController::class, 'me']);
            Route::get('branch', [KioskController::class, 'branch']);
            Route::get('settings', [KioskController::class, 'settings']);
            
            // Menu & Products
            Route::get('categories', [KioskController::class, 'getCategories']);
            Route::get('products', [KioskController::class, 'getProducts']);
            Route::get('products/search', [KioskController::class, 'searchProducts']);
            Route::get('products/{id}', [KioskController::class, 'getProduct']);
            Route::get('addons', [KioskController::class, 'getAddons']);
            Route::get('attributes', [KioskController::class, 'getAttributes']);
            
            // Cart Management
            Route::get('cart', [KioskController::class, 'getCart']);
            Route::post('cart/add', [KioskController::class, 'addToCart']);
            Route::put('cart/update', [KioskController::class, 'updateCart']);
            Route::delete('cart/remove', [KioskController::class, 'removeFromCart']);
            Route::delete('cart/clear', [KioskController::class, 'clearCart']);
            Route::post('cart/start-over', [KioskController::class, 'startOver']);
            
            // Orders
            Route::post('orders', [KioskController::class, 'createOrder']);
            Route::get('orders', [KioskController::class, 'getOrders']);
            Route::get('orders/{id}', [KioskController::class, 'getOrder']);
            Route::get('orders/{id}/receipt', [KioskController::class, 'getReceipt']);
        });
    });

    Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
        Route::post('registration', [CustomerAuthController::class, 'registration']);
        Route::post('login', [CustomerAuthController::class, 'login']);
        Route::post('social-customer-login', [CustomerAuthController::class, 'customerSocialLogin']);
        Route::post('check-phone', [CustomerAuthController::class, 'checkPhone']);
        Route::post('verify-phone', [CustomerAuthController::class, 'verifyPhone']);
        Route::post('check-email', [CustomerAuthController::class, 'checkEmail']);
        Route::post('verify-email', [CustomerAuthController::class, 'verifyEmail']);
        Route::post('verify-otp', [CustomerAuthController::class, 'verifyOTP']);
        Route::post('registration-with-otp', [CustomerAuthController::class, 'registrationWithOTP']);
        Route::post('existing-account-check', [CustomerAuthController::class, 'existingAccountCheck']);
        Route::post('registration-with-social-media', [CustomerAuthController::class, 'registrationWithSocialMedia']);

        Route::post('forgot-password', [PasswordResetController::class, 'passwordResetRequest']);
        Route::post('verify-token', [PasswordResetController::class, 'verifyToken']);
        Route::put('reset-password', [PasswordResetController::class, 'resetPasswordSubmit']);


        Route::group(['prefix' => 'kitchen'], function () {
            Route::post('login', [KitchenLoginController::class, 'login']);
            Route::post('logout', [KitchenLoginController::class, 'logout'])->middleware('auth:kitchen_api');
        });
    });


    Route::group(['prefix' => 'config'], function () {
        Route::get('/', [ConfigController::class, 'configuration']);
        Route::get('get-direction-api', [ConfigController::class, 'direction_api']);
        Route::get('delivery-fee', [ConfigController::class, 'deliveryFree']);
    });

    Route::group(['prefix' => 'products', 'middleware' => 'branch_adder'], function () {
        Route::get('latest', [ProductController::class, 'latestProducts']);
        Route::get('popular', [ProductController::class, 'popularProducts']);
        Route::get('set-menu', [ProductController::class, 'setMenus']);
        Route::post('search', [ProductController::class, 'searchedProducts']);
        Route::get('details/{id}', [ProductController::class, 'getProduct']);
        Route::get('related-products/{product_id}', [ProductController::class, 'relatedProducts']);
        // Reviews functionality removed
        Route::get('recommended', [ProductController::class, 'recommendedProducts']);
        Route::get('frequently-bought', [ProductController::class, 'frequentlyBoughtProducts']);
        Route::get('search-suggestion', [ProductController::class, 'searchSuggestion']);
        Route::post('change-branch', [ProductController::class, 'changeBranchProductUpdate']);
        Route::post('re-order', [ProductController::class, 'reOrderProducts']);
        Route::get('search-recommended', [ProductController::class, 'searchRecommendedData']);
    });

    // Banner functionality removed

    // Notification functionality removed

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [CategoryController::class, 'getCategories']);
        Route::get('childes/{category_id}', [CategoryController::class, 'getChildes']);
        Route::get('products/{category_id}', [CategoryController::class, 'getProducts'])->middleware('branch_adder');
        Route::get('products/{category_id}/all', [CategoryController::class, 'getAllProducts'])->middleware('branch_adder');
    });


    Route::group(['prefix' => 'tag'], function () {
        Route::get('popular', [TagController::class, 'getPopularTags']);
    });

    Route::group(['prefix' => 'customer', 'middleware' => ['auth:api', 'is_active']], function () {
        Route::get('info', [CustomerController::class, 'info']);
        Route::put('update-profile', [CustomerController::class, 'updateProfile']);
        Route::post('verify-profile-info', [CustomerController::class, 'verifyProfileInfo']);
        Route::get('transaction-history', [CustomerController::class, 'getTransactionHistory']);
        Route::post('update-referral-check', [CustomerController::class, 'updateReferralCheck']);

        Route::group(['prefix' => 'address'], function () {
            Route::get('list', [CustomerController::class, 'addressList'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::post('add', [CustomerController::class, 'addAddress'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::put('update/{id}', [CustomerController::class, 'updateAddress'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::delete('delete', [CustomerController::class, 'deleteAddress'])->withoutMiddleware(['auth:api', 'is_active']);
        });
        Route::get('last-ordered-address', [CustomerController::class, 'lastOrderedAddress']);

        Route::namespace('Auth')->group(function () {
            Route::delete('remove-account', [CustomerAuthController::class, 'remove_account']);
        });

        Route::group(['prefix' => 'order'], function () {
            Route::get('track', [OrderController::class, 'trackOrder'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::post('place', [OrderController::class, 'placeOrder'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::get('list', [OrderController::class, 'getOrderList'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::get('details', [OrderController::class, 'getOrderDetails'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::put('cancel', [OrderController::class, 'cancelOrder'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::put('payment-method', [OrderController::class, 'updatePaymentMethod'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::post('guest-track', [OrderController::class, 'guestTrackOrder'])->withoutMiddleware(['auth:api', 'is_active']);
            Route::post('details-guest', [OrderController::class, 'getGuestOrderDetails'])->withoutMiddleware(['auth:api', 'is_active']);
        });

        Route::group(['prefix' => 'wish-list'], function () {
            Route::get('/', [WishlistController::class, 'wishlist'])->middleware('branch_adder');
            Route::post('add', [WishlistController::class, 'addToWishlist']);
            Route::delete('remove', [WishlistController::class, 'removeFromWishlist']);
        });


    });


    //map api
    Route::group(['prefix' => 'mapapi'], function () {
        Route::get('place-api-autocomplete', [MapApiController::class, 'placeApiAutoComplete']);
        Route::get('distance-api', [MapApiController::class, 'distanceApi']);
        Route::get('place-api-details', [MapApiController::class, 'placeApiDetails']);
        Route::get('geocode-api', [MapApiController::class, 'geocodeApi']);
    });


    Route::get('pages', [PageController::class, 'index']);


    Route::group(['prefix' => 'kitchen', 'middleware' => 'auth:kitchen_api'], function () {
        Route::get('profile', [KitchenController::class, 'getProfile']);
        Route::get('order/list', [KitchenController::class, 'getOrderList']);
        Route::get('order/search', [KitchenController::class, 'search']);
        Route::get('order/filter', [KitchenController::class, 'filterByStatus']);
        Route::get('order/details', [KitchenController::class, 'getOrderDetails']);
        Route::put('order/status', [KitchenController::class, 'changeStatus']);
    });

    Route::group(['prefix' => 'guest'], function () {
        Route::post('/add', [GuestUserController::class, 'guestStore']);
    });

    // Offline payment functionality removed

    Route::group(['prefix' => 'branch'], function () {
        Route::get('list', [BranchController::class, 'list']);
        Route::get('products', [BranchController::class, 'products']);
    });

    // Kiosk API Routes
    Route::group(['prefix' => 'kiosk'], function () {
        // Authentication routes (no middleware)
        Route::post('auth/login', [App\Http\Controllers\Api\V1\KioskController::class, 'login']);
        
        // Protected routes
        Route::group(['middleware' => ['kiosk.auth', 'kiosk.branch.filter']], function () {
            Route::post('auth/logout', [App\Http\Controllers\Api\V1\KioskController::class, 'logout']);
            Route::get('auth/me', [App\Http\Controllers\Api\V1\KioskController::class, 'me']);
            Route::get('branch', [App\Http\Controllers\Api\V1\KioskController::class, 'branch']);
            Route::get('settings', [App\Http\Controllers\Api\V1\KioskController::class, 'settings']);
            Route::get('categories', [App\Http\Controllers\Api\V1\KioskController::class, 'getCategories']);
            Route::get('products', [App\Http\Controllers\Api\V1\KioskController::class, 'getProducts']);
            Route::get('products/{id}', [App\Http\Controllers\Api\V1\KioskController::class, 'getProduct']);
            Route::get('products/search', [App\Http\Controllers\Api\V1\KioskController::class, 'searchProducts']);
            Route::get('addons', [App\Http\Controllers\Api\V1\KioskController::class, 'getAddons']);
            Route::get('attributes', [App\Http\Controllers\Api\V1\KioskController::class, 'getAttributes']);
            
            Route::get('orders/{id}', [App\Http\Controllers\Api\V1\KioskController::class, 'getOrder']);
            Route::get('orders/{id}/receipt', [App\Http\Controllers\Api\V1\KioskController::class, 'getReceipt']);
        });
    });

});
