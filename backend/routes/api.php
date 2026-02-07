<?php

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\AdminDashboardController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Health check
    Route::get('/ping', [HealthController::class, 'ping']);

    // Public Product & Category Routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    
    // Public Reviews
    Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);

    // Public Order Tracking
    Route::get('/orders/track/{trackingNumber}', [OrderController::class, 'trackOrder']);

    // Public Coupon Validation
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);

    // Public Auth Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/email/verify', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

    // Checkout
    Route::post('/checkout', [OrderController::class, 'checkout']);

    // Cart Routes (both guest and authenticated)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{itemId}', [CartController::class, 'update']);
    Route::delete('/cart/{itemId}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);

        // Orders (customer)
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);

        // Address Management
        Route::apiResource('addresses', AddressController::class);
        Route::post('/addresses/{id}/set-default', [AddressController::class, 'setDefault']);

        // Reviews
        Route::post('/products/{productId}/reviews', [ReviewController::class, 'store']);
        Route::put('/reviews/{id}', [ReviewController::class, 'update']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist', [WishlistController::class, 'store']);
        Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);
        Route::get('/wishlist/check/{productId}', [WishlistController::class, 'check']);
        Route::get('/wishlist/count', [WishlistController::class, 'count']);

        // Cart merge (after login)
        Route::post('/cart/merge', [CartController::class, 'merge']);

        // Admin Routes
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', AdminDashboardController::class);

            // Category Management
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::put('/categories/{id}', [CategoryController::class, 'update']);
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

            // Product Management
            Route::post('/products', [ProductController::class, 'store']);
            Route::put('/products/{id}', [ProductController::class, 'update']);
            Route::delete('/products/{id}', [ProductController::class, 'destroy']);

            // Order Management
            Route::get('/orders', [OrderController::class, 'adminIndex']);
            Route::put('/orders/{id}/status', [OrderController::class, 'adminUpdateStatus']);

            // Review Management
            Route::post('/reviews/{id}/approve', [ReviewController::class, 'approve']);
            Route::post('/reviews/{id}/reject', [ReviewController::class, 'reject']);

            // Coupon Management
            Route::apiResource('coupons', CouponController::class)->except(['show']);
        });

        // Customer Routes
        Route::middleware('role:customer')->prefix('customer')->group(function () {
            Route::get('/dashboard', function () {
                return response()->json(['message' => 'Customer Dashboard']);
            });
        });
    });
});
