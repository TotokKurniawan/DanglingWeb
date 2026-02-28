<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController as ApiComplaintController;
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

    Route::post('/upgrade-to-seller', [SellerController::class, 'upgradeToSeller']);
    Route::get('/store-status', [SellerController::class, 'getStoreStatus']);
    Route::post('/store-status', [SellerController::class, 'updateStoreStatus']);

    Route::get('/order-history', [OrderHistoryController::class, 'getOrderHistory']);
    Route::post('/orders', [OrderController::class, 'createOrder']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::get('/sellers', [SellerProductController::class, 'getAllSellers']);
    Route::get('/sellers/{id}', [SellerProductController::class, 'getSellerById']);

    Route::get('/orders/pending', [OrderStatusController::class, 'getPendingOrders']);
    Route::put('/orders/{id}/accept', [OrderStatusController::class, 'acceptOrder']);
    Route::put('/orders/{id}/reject', [OrderStatusController::class, 'rejectOrder']);
    Route::put('/orders/{id}/complete', [OrderStatusController::class, 'completeOrder']);
    Route::put('/orders/{id}/cancel', [OrderStatusController::class, 'cancelOrder']);
    Route::put('/orders/{id}/cancel-by-buyer', [OrderStatusController::class, 'cancelByBuyer']);

    Route::put('/profile/buyer/{id}', [ProfileController::class, 'updateBuyerProfile']);
    Route::put('/profile/seller/{id}', [ProfileController::class, 'updateSellerProfile']);

    Route::put('/sellers/{id}/location', [LocationController::class, 'updateLocation']);

    Route::post('/complaints', [ApiComplaintController::class, 'store'])->middleware('throttle:10,1');
});
