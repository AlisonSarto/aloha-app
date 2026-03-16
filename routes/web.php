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

            Route::get('/sellers', [AdminSellerController::class, 'index'])->name('sellers.index');
            Route::get('/sellers/create', [AdminSellerController::class, 'create'])->name('sellers.create');
            Route::post('/sellers', [AdminSellerController::class, 'store'])->name('sellers.store');
            Route::get('/sellers/{seller}', [AdminSellerController::class, 'show'])->name('sellers.show');
            Route::get('/sellers/{seller}/edit', [AdminSellerController::class, 'edit'])->name('sellers.edit');
            Route::put('/sellers/{seller}', [AdminSellerController::class, 'update'])->name('sellers.update');
            Route::delete('/sellers/{seller}', [AdminSellerController::class, 'destroy'])->name('sellers.destroy');

            Route::get('/price-tables', [AdminPriceTableController::class, 'index'])->name('price-tables');
            Route::get('/price-tables/create', [AdminPriceTableController::class, 'create'])->name('price-tables.create');
            Route::post('/price-tables', [AdminPriceTableController::class, 'store'])->name('price-tables.store');
            Route::get('/price-tables/{priceTable}', [AdminPriceTableController::class, 'show'])->name('price-tables.show');
            Route::get('/price-tables/{priceTable}/edit', [AdminPriceTableController::class, 'edit'])->name('price-tables.edit');
            Route::put('/price-tables/{priceTable}', [AdminPriceTableController::class, 'update'])->name('price-tables.update');
            Route::delete('/price-tables/{priceTable}', [AdminPriceTableController::class, 'destroy'])->name('price-tables.destroy');
            Route::post('/price-tables/{priceTable}/set-default', [AdminPriceTableController::class, 'setDefault'])->name('price-tables.set-default');

            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        });
});
