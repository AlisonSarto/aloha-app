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
use App\Http\Controllers\Admin\DeliveryConfigController as AdminDeliveryConfigController;

use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\FinancialController as ClientFinancialController;
use App\Http\Controllers\Client\StoreController as ClientStoreController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::controller(AuthController::class)
    ->group(function () {
        Route::get('/login', 'form')->name('login');
        Route::post('/login', 'login');
        Route::get('/register', 'registerForm')->name('register');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout')->name('logout');
    });


Route::middleware(['auth'])->group(function() {

    Route::middleware(['role:admin'])
        ->prefix('/admin')
        ->name('admin.')
        ->group(function() {

            Route::get('/home', [AdminHomeController::class, 'index'])->name('home');

            // Clients
            Route::prefix('/clients')
                ->name('clients.')
                ->controller(AdminClientController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/{client}', 'show')->name('show');
                    Route::put('/{client}/stores', 'updateStores')->name('stores.update');
                });

            // Stores
            Route::prefix('/stores')
                ->name('stores.')
                ->controller(AdminStoreController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/{store}', 'show')->name('show');
                    Route::get('/{store}/edit', 'edit')->name('edit');
                    Route::put('/{store}', 'update')->name('update');
                });

            // Sellers
            Route::prefix('/sellers')
                ->name('sellers.')
                ->controller(AdminSellerController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{seller}', 'show')->name('show');
                    Route::get('/{seller}/edit', 'edit')->name('edit');
                    Route::put('/{seller}', 'update')->name('update');
                    Route::delete('/{seller}', 'destroy')->name('destroy');
                });

            // Price Tables
            Route::prefix('/price-tables')
                ->name('price-tables.')
                ->controller(AdminPriceTableController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{priceTable}', 'show')->name('show');
                    Route::get('/{priceTable}/edit', 'edit')->name('edit');
                    Route::put('/{priceTable}', 'update')->name('update');
                    Route::delete('/{priceTable}', 'destroy')->name('destroy');
                    Route::post('/{priceTable}/set-default', 'setDefault')->name('set-default');
                });

            // Users
            Route::prefix('/users')
                ->name('users.')
                ->controller(AdminUserController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{user}', 'show')->name('show');
                    Route::get('/{user}/edit', 'edit')->name('edit');
                    Route::put('/{user}', 'update')->name('update');
                    Route::delete('/{user}', 'destroy')->name('destroy');
                });

            // Delivery Config
            Route::prefix('/delivery-config')
                ->name('delivery-config.')
                ->controller(AdminDeliveryConfigController::class)
                ->group(function () {
                    Route::get('/', 'edit')->name('edit');
                    Route::put('/', 'update')->name('update');
                });
        });

    Route::middleware(['role:client'])
        ->prefix('/client')
        ->name('client.')
        ->group(function() {

            Route::post('/set-store', [ClientStoreController::class, 'setActive'])->name('set.store');

            // Stores
            Route::prefix('/stores')
                ->name('stores.')
                ->controller(ClientStoreController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/register', 'registerForm')->name('register');
                    Route::post('/verify-cnpj', 'verifyCNPJ')->name('verify-cnpj');
                    Route::post('/step1', 'confirmStep1')->name('step1');
                    Route::post('/step2', 'confirmStep2')->name('step2');
                    Route::post('/step3', 'confirmStep3')->name('step3');
                    Route::post('/confirm', 'confirm')->name('confirm');
                    Route::get('/{store}/edit', 'edit')->name('edit');
                    Route::put('/{store}', 'update')->name('update');
                    Route::delete('/{store}/unlink', 'unlink')->name('unlink');
                });

                // Profile
                Route::prefix('/profile')
                    ->name('profile.')
                    ->controller(ClientProfileController::class)
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::put('/', 'update')->name('update');
                        Route::put('/password', 'updatePassword')->name('password');
                        Route::delete('/', 'destroy')->name('destroy');
                    });

            Route::middleware(['has_store', 'active_store'])
                ->group(function() {

                    // Orders
                    Route::prefix('/orders')
                        ->name('orders.')
                        ->controller(ClientOrderController::class)
                        ->group(function () {
                            Route::get('/', 'index')->name('index');
                            Route::get('/create', 'create')->name('create');
                            Route::get('/{id}', 'show')->name('show');
                            Route::post('/', 'store')->name('store');
                        });

                    // Financial
                    Route::prefix('/financial')
                        ->name('financial.')
                        ->controller(ClientFinancialController::class)
                        ->group(function () {
                            Route::get('/', 'index')->name('index');
                        });

                });

        });

    Route::middleware(['role:seller'])
        ->prefix('/seller')
        ->name('seller')
        ->group(function() {



        });
});
