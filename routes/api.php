<?php

use App\Http\Controllers\ForgotPasswordController;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/sign_up', [apiController::class, 'sign_up']);
Route::post('/logout', [apiController::class, 'logout']);
Route::match(['get', 'post'], '/login', [apiController::class, 'login']);
Route::post('/forgot_password', [apiController::class, 'forgot_password']);
Route::post('/reset_password', [apiController::class, 'reset_password']);
Route::middleware('auth:api')->post('/upgradeToSeller', [apiController::class, 'upgradeToSeller']);
Route::middleware('auth:api')->get('/getStoreStatus', [apiController::class, 'getStoreStatus']);
Route::middleware('auth:api')->post('/updateStatus', [apiController::class, 'updateStatus']);
Route::middleware('auth:api')->get('/orderHistory', [apiController::class, 'orderHistory']);
Route::middleware('auth:api')->get('/pesananMasuk', [apiController::class, 'pesananMasuk']);
Route::middleware('auth:api')->match(['get', 'post'],'/tambahProduk', [apiController::class, 'tambahProduk']);
Route::middleware('auth:api')->get('/tampilSeluruhPedagang', [apiController::class, 'tampilSeluruhPedagang']);
Route::middleware('auth:api')->get('/tampilPedagangBerdasarkanID', [apiController::class, 'tampilPedagangBerdasarkanID']);
Route::middleware('auth:api')->get('/updateLocation', [apiController::class, 'updateLocation']);
// Route::get('getPedagangByUserId/{id}', [apiController::class, 'getPedagangByUserId']);