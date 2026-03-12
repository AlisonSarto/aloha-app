<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\HomeController;

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\PriceTableController as AdminPriceTableController;
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

            Route::get('/clients', [AdminClientController::class, 'index'])->name('clients.index');
            Route::get('/clients/{client}', [AdminClientController::class, 'show'])->name('clients.show');
            Route::put('/clients/{client}/stores', [AdminClientController::class, 'updateStores'])->name('clients.stores.update');

            Route::get('/stores', [AdminStoreController::class, 'index'])->name('stores.index');
            Route::get('/stores/{store}', [AdminStoreController::class, 'show'])->name('stores.show');
            Route::get('/stores/{store}/edit', [AdminStoreController::class, 'edit'])->name('stores.edit');
            Route::put('/stores/{store}', [AdminStoreController::class, 'update'])->name('stores.update');

            Route::get('/sellers', [AdminSellerController::class, 'index'])->name('sellers');

            Route::get('/price-tables', [AdminPriceTableController::class, 'index'])->name('price-tables');

            Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        });
});
