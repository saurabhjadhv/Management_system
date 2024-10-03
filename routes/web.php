<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return view('welcome');
});


// Authentication Routes (for guests)
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserController::class, 'getRegisterForm'])->name('register');
    Route::post('/registeruser', [UserController::class, 'Register'])->name('registeruser');

    Route::get('/login', [UserController::class, 'getLoginForm'])->name('login');
    Route::post('/loginuser', [UserController::class, 'Login'])->name('loginuser');
});

// Protected Routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'getdashboard'])->name('dashboard');
    Route::get('/vendors/data', [UserController::class, 'getVendors'])->name('vendors.data');
    Route::get('/vendor/edit/{id}', [UserController::class, 'edit'])->name('vendor.edit');
    Route::put('/vendors/{id}', [UserController::class, 'update'])->name('vendor.update');
    Route::delete('/vendors/delete/{id}', [UserController::class, 'delete'])->name('vendor.delete');
    Route::post('/InsertVendordata', [UserController::class, 'InsertVendor'])->name('InsertVendordata');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});
