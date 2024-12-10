<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ForgotController;
use App\Http\Controllers\KeluhanController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PedagangController;
use App\Http\Controllers\ProfileAdminController;
use App\Http\Controllers\ProfileOperatorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LandingController::class, 'home'])->name('home');
Route::post('/keluhanLanding', [KeluhanController::class, 'tambahkeluhan'])->name('keluhan.store');
Route::get('/fitur', [LandingController::class, 'fitur'])->name('fitur');

Route::get('/Login', [LoginController::class, 'index'])->name('Login');
Route::post('/loginrole', [LoginController::class, 'login'])->name('loginakun');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/Forgot', [ForgotController::class, 'Forgot'])->name('Forgot');

Route::get('/Home', [AdminController::class, 'dashboard'])->name('Dashboard');

Route::get('/Pedagang', [AdminController::class, 'pedagang'])->name('Pedagang');
Route::post('/update-status', action: [PedagangController::class, 'updateStatus'])->name('update.status');
Route::get('/Keluhan', [AdminController::class, 'keluhan'])->name('Keluhan');


Route::get('/Mitra', [MitraController::class, 'mitra'])->name('Mitra');
Route::get('/Tambah-Mitra', [MitraController::class, 'tambahmitra'])->name('Tambah-Pesanan');
Route::post('/tambah', [MitraController::class, 'store'])->name('tambahan');
Route::put('/mitra/{id}', [MitraController::class, 'UpdateMitra'])->name('mitra.update');
Route::delete('/mitras/{id}', [MitraController::class, 'destroy'])->name('mitras.destroy');

Route::get('/Profileadmin', action: [ProfileAdminController::class, 'profile'])->name('Profile');
Route::put('/profile/update/{id}', action: [ProfileAdminController::class, 'updateprofiladmin'])->name('profile.update');
Route::put('/profileoperator/update/{id}', action: [ProfileAdminController::class, 'updateprofiloperator'])->name('profile.updateoperator');

Route::get('/DataAdmin', [AdminController::class, 'dataadmin'])->name('DataAdmin');
Route::get('/Tambahadmin', [AdminController::class, 'tambahadmin'])->name('Tambahadmin');
Route::post('/admin/operator/store', [UserController::class, 'tambahoperator'])->name('operator.store');
Route::put('/operators/{id}', [UserController::class, 'update'])->name('operator.update');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');


Route::get('/DashboardOperator', [OperatorController::class, 'DashboardOperator'])->name('DashboardOperator');
Route::get('/PedagangOperator', [OperatorController::class, 'PedagangOperator'])->name('PedagangOperator');
Route::get('/KeluhanOperator', [OperatorController::class, 'KeluhanOperator'])->name('KeluhanOperator');
Route::get('/ProfileOperator', [ProfileOperatorController::class, 'ProfileOperator'])->name('profileOperator');
