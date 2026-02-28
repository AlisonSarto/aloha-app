<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\HomeController;

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::get('/login', [AuthController::class, 'form'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function() {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::middleware(['role:admin'])
        ->prefix('/admin')
        ->name('admin.')
        ->group(function() {
            Route::get('/home', [AdminHomeController::class, 'index'])->name('home');

            Route::get('/clients', [AdminClientController::class, 'index'])->name('clients');

            Route::get('/stores', [AdminStoreController::class, 'index'])->name('stores');

            Route::get('/sellers', [AdminSellerController::class, 'index'])->name('sellers');

            Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        });
});
