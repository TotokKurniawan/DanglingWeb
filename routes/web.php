<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\ComplaintController;
use App\Http\Controllers\Web\ForgotController;
use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\OperatorController;
use App\Http\Controllers\Web\PartnerController;
use App\Http\Controllers\Web\ProfileAdminController;
use App\Http\Controllers\Web\ProfileOperatorController;
use App\Http\Controllers\Web\SellerController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing & general
Route::controller(LandingController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/features', 'features')->name('features');
});
Route::post('/complaints', [ComplaintController::class, 'storeComplaint'])->name('complaints.store')->middleware('throttle:5,1');

// Auth
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login.show');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
});
Route::get('/forgot-password', [ForgotController::class, 'showForgotForm'])->name('forgot.show');

// Admin (harus login + role admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/sellers', [AdminController::class, 'indexSellers'])->name('sellers.index');
    Route::get('/complaints', [AdminController::class, 'indexComplaints'])->name('complaints.index');
    Route::get('/operators', [AdminController::class, 'indexOperators'])->name('operators.index');
    Route::get('/operators/create', [AdminController::class, 'createOperatorForm'])->name('operators.create');
    Route::get('/profile', [AdminController::class, 'showProfile'])->name('profile.show');
});
Route::post('/seller-status', [SellerController::class, 'updateSellerStatus'])->name('seller.status.update')->middleware(['auth', 'role:admin']);

// Partners (Mitra) — hanya admin
Route::middleware(['auth', 'role:admin'])->prefix('partners')->name('partners.')->group(function () {
    Route::get('/', [PartnerController::class, 'index'])->name('index');
    Route::get('/create', [PartnerController::class, 'createForm'])->name('create');
    Route::post('/', [PartnerController::class, 'store'])->name('store');
    Route::put('/{id}', [PartnerController::class, 'update'])->name('update');
    Route::delete('/{id}', [PartnerController::class, 'destroy'])->name('destroy');
});

// Profile (admin/operator hanya untuk role masing-masing)
Route::middleware(['auth'])->prefix('profile')->group(function () {
    Route::put('/admin/{id}', [ProfileAdminController::class, 'updateAdminProfile'])->name('admin.profile.update')->middleware('role:admin');
    Route::put('/operator/{id}', [ProfileAdminController::class, 'updateOperatorProfile'])->name('operator.profile.update')->middleware('role:operator');
});

// Operators (CRUD) — hanya admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/admin/operators', [UserController::class, 'storeOperator'])->name('admin.operators.store');
    Route::put('/operators/{id}', [UserController::class, 'updateOperator'])->name('admin.operators.update');
    Route::delete('/users/{id}', [UserController::class, 'destroyUser'])->name('admin.users.destroy');
});

// Operator panel (harus login + role operator)
Route::middleware(['auth', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/dashboard', [OperatorController::class, 'dashboard'])->name('dashboard');
    Route::get('/sellers', [OperatorController::class, 'indexSellers'])->name('sellers.index');
    Route::get('/complaints', [OperatorController::class, 'indexComplaints'])->name('complaints.index');
    Route::get('/profile', [ProfileOperatorController::class, 'showProfile'])->name('profile.show');
});
