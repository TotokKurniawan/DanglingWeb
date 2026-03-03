<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController as ApiComplaintController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderHistoryController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\SellerProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Base URL: /api
| Auth: Bearer token (Laravel Passport). Send header: Authorization: Bearer {token}
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user(),
    ]);
});

// Auth (public) — rate limited
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/sign_up', [AuthController::class, 'register']); // deprecated: use /register
    Route::post('/login', [AuthController::class, 'login']);
});

// Auth required
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Buyer-only routes
    Route::middleware('role:buyer')->group(function () {
        Route::post('/upgrade-to-seller', [SellerController::class, 'upgradeToSeller']);
        Route::post('/orders', [OrderController::class, 'createOrder']);
        Route::post('/orders/{id}/reorder', [OrderController::class, 'reorder']);
        Route::put('/orders/{id}/cancel-by-buyer', [OrderStatusController::class, 'cancelByBuyer']);
        Route::put('/profile/buyer/{id}', [ProfileController::class, 'updateBuyerProfile']);
        Route::post('/complaints', [ApiComplaintController::class, 'store'])->middleware('throttle:10,1');

        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{seller_id}', [FavoriteController::class, 'destroy']);
    });

    // Seller-only routes
    Route::middleware('role:seller')->group(function () {
        // Store status & configuration
        Route::get('/store-status', [SellerController::class, 'getStoreStatus']);
        Route::post('/store-status', [SellerController::class, 'updateStoreStatus']);

        // Pending orders for this seller
        Route::get('/orders/pending', [OrderStatusController::class, 'getPendingOrders']);
        Route::put('/orders/{id}/accept', [OrderStatusController::class, 'acceptOrder']);
        Route::put('/orders/{id}/reject', [OrderStatusController::class, 'rejectOrder']);
        Route::put('/orders/{id}/complete', [OrderStatusController::class, 'completeOrder']);
        Route::put('/orders/{id}/cancel', [OrderStatusController::class, 'cancelOrder']);

        // Product management for the authenticated seller
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::patch('/products/{id}/toggle-active', [ProductController::class, 'toggleActive']);

        Route::put('/profile/seller/{id}', [ProfileController::class, 'updateSellerProfile']);
        Route::put('/sellers/{id}/location', [LocationController::class, 'updateLocation']);

        // Seller stats
        Route::get('/sellers/me/stats', [SellerController::class, 'getStats']);
    });

    // Shared buyer or seller
    Route::middleware('role:buyer|seller')->group(function () {
        Route::get('/order-history', [OrderHistoryController::class, 'getOrderHistory']);
    });

    // Shared (any authenticated API user)
    Route::get('/sellers', [SellerProductController::class, 'getAllSellers']);
    Route::get('/sellers/{id}', [SellerProductController::class, 'getSellerById']);

    // Device token FCM (buyer dan seller)
    Route::post('/device-token', [DeviceTokenController::class, 'store']);
    Route::delete('/device-token', [DeviceTokenController::class, 'destroy']);
});
