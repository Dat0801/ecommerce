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
use App\Http\Controllers\Api\V1\ShippingController;
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
    
    // Public Variant Attributes
    Route::get('/variant-attributes', [\App\Http\Controllers\Api\V1\VariantAttributeController::class, 'index']);
    
    // Public Reviews
    Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);

    // Public Order Tracking
    Route::get('/orders/track/{trackingNumber}', [OrderController::class, 'trackOrder']);

    // Webhooks (no auth required, but should be verified by signature)
    Route::post('/webhooks/stripe', [\App\Http\Controllers\Api\V1\WebhookController::class, 'stripe']);
    Route::post('/webhooks/paypal', [\App\Http\Controllers\Api\V1\WebhookController::class, 'paypal']);

    // Public Coupon Validation
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);

    // Public Shipping Methods
    Route::get('/shipping/methods', [ShippingController::class, 'getMethods']);
    Route::post('/shipping/calculate', [ShippingController::class, 'calculateCost']);

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
        Route::put('/user/profile', [AuthController::class, 'updateProfile']);
        Route::post('/user/change-password', [AuthController::class, 'changePassword']);
        Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);

        // Orders (customer)
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
        Route::get('/orders/{id}/status-history', [OrderController::class, 'getStatusHistoryCustomer']);
        Route::get('/orders/{id}/notes', [OrderController::class, 'getNotesCustomer']);

        // Invoices
        Route::get('/orders/{orderId}/invoice', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'show']);
        Route::get('/orders/{orderId}/invoice/download', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'download']);
        Route::get('/orders/{orderId}/invoice/html', [\App\Http\Controllers\Api\V1\InvoiceController::class, 'html']);

        // Returns (customer)
        Route::get('/returns', [\App\Http\Controllers\Api\V1\ReturnController::class, 'index']);
        Route::post('/returns', [\App\Http\Controllers\Api\V1\ReturnController::class, 'store']);
        Route::get('/returns/{id}', [\App\Http\Controllers\Api\V1\ReturnController::class, 'show']);
        Route::post('/returns/{id}/cancel', [\App\Http\Controllers\Api\V1\ReturnController::class, 'cancel']);

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

            // Product Image Management
            Route::delete('/product-images/{id}', [\App\Http\Controllers\Api\V1\ProductImageController::class, 'destroy']);
            Route::post('/product-images/{id}/set-primary', [\App\Http\Controllers\Api\V1\ProductImageController::class, 'setPrimary']);
            Route::post('/product-images/reorder', [\App\Http\Controllers\Api\V1\ProductImageController::class, 'reorder']);

            // Variant Attributes Management
            Route::apiResource('variant-attributes', \App\Http\Controllers\Api\V1\VariantAttributeController::class);
            Route::post('/variant-attributes/{id}/values', [\App\Http\Controllers\Api\V1\VariantAttributeController::class, 'addValue']);

            // Product Variants Management
            Route::get('/products/{productId}/variants', [\App\Http\Controllers\Api\V1\ProductVariantController::class, 'index']);
            Route::post('/products/{productId}/variants', [\App\Http\Controllers\Api\V1\ProductVariantController::class, 'store']);
            Route::put('/products/{productId}/variants/{variantId}', [\App\Http\Controllers\Api\V1\ProductVariantController::class, 'update']);
            Route::delete('/products/{productId}/variants/{variantId}', [\App\Http\Controllers\Api\V1\ProductVariantController::class, 'destroy']);

            // Order Management
            Route::get('/orders', [OrderController::class, 'adminIndex']);
            Route::get('/orders/{id}', [OrderController::class, 'adminShow']);
            Route::put('/orders/{id}/status', [OrderController::class, 'adminUpdateStatus']);
            Route::get('/orders/{id}/valid-statuses', [OrderController::class, 'getValidStatuses']);
            Route::get('/orders/{id}/status-history', [OrderController::class, 'getStatusHistory']);
            Route::get('/orders/{id}/notes', [OrderController::class, 'getNotes']);
            Route::post('/orders/{id}/notes', [OrderController::class, 'addNote']);
            Route::put('/orders/{id}/notes/{noteId}', [OrderController::class, 'updateNote']);
            Route::delete('/orders/{id}/notes/{noteId}', [OrderController::class, 'deleteNote']);

            // Review Management
            Route::post('/reviews/{id}/approve', [ReviewController::class, 'approve']);
            Route::post('/reviews/{id}/reject', [ReviewController::class, 'reject']);

            // Coupon Management
            Route::apiResource('coupons', CouponController::class)->except(['show']);

            // Shipping Method Management
            Route::apiResource('shipping-methods', ShippingMethodController::class);

            // User Management
            Route::apiResource('users', \App\Http\Controllers\Api\V1\UserController::class);
            Route::get('/users/{id}/statistics', [\App\Http\Controllers\Api\V1\UserController::class, 'statistics']);

            // Analytics & Reporting
            Route::get('/analytics/dashboard', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'dashboard']);
            Route::get('/analytics/sales', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'sales']);
            Route::get('/analytics/products', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'products']);
            Route::get('/analytics/customers', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'customers']);
            Route::get('/analytics/orders', [\App\Http\Controllers\Api\V1\AnalyticsController::class, 'orders']);

            // Return Management
            Route::get('/returns', [\App\Http\Controllers\Api\V1\ReturnController::class, 'index']);
            Route::get('/returns/{id}', [\App\Http\Controllers\Api\V1\ReturnController::class, 'show']);
            Route::post('/returns/{id}/approve', [\App\Http\Controllers\Api\V1\ReturnController::class, 'approve']);
            Route::post('/returns/{id}/reject', [\App\Http\Controllers\Api\V1\ReturnController::class, 'reject']);
            Route::post('/returns/{id}/process-refund', [\App\Http\Controllers\Api\V1\ReturnController::class, 'processRefund']);
        });

        // Customer Routes
        Route::middleware('role:customer')->prefix('customer')->group(function () {
            Route::get('/dashboard', function () {
                return response()->json(['message' => 'Customer Dashboard']);
            });
        });
    });
});
