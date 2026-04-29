<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\HomeController;

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\TenantController as AdminTenantController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\PriceTableController as AdminPriceTableController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DeliveryConfigController as AdminDeliveryConfigController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\SellerStoreClaimController as AdminSellerStoreClaimController;

use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\FinancialController as ClientFinancialController;
use App\Http\Controllers\Client\StoreController as ClientStoreController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\CouponController as ClientCouponController;

use App\Http\Controllers\Seller\HomeController as SellerHomeController;
use App\Http\Controllers\Seller\StoreController as SellerStoreController;
use App\Http\Controllers\Seller\ReportController as SellerReportController;

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

            Route::prefix('home')
                ->name('home.')
                ->controller(AdminHomeController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                });

            // Tenants
            Route::prefix('/tenants')
                ->name('tenants.')
                ->controller(AdminTenantController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                });


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
                ->group(function () {
                    Route::get('/',        [AdminSellerController::class, 'index'])->name('index');
                    Route::get('/create',  [AdminSellerController::class, 'create'])->name('create');
                    Route::post('/',       [AdminSellerController::class, 'store'])->name('store');

                    // Claims/approvals (static paths must come before /{seller})
                    Route::get('/claims',                    [AdminSellerStoreClaimController::class, 'index'])->name('claims');
                    Route::post('/claims/{claim}/approve',   [AdminSellerStoreClaimController::class, 'approveClaim'])->name('claims.approve');
                    Route::post('/claims/{claim}/reject',    [AdminSellerStoreClaimController::class, 'rejectClaim'])->name('claims.reject');
                    Route::post('/stores/{store}/approve',   [AdminSellerStoreClaimController::class, 'approveStore'])->name('stores.approve');
                    Route::post('/stores/{store}/reject',    [AdminSellerStoreClaimController::class, 'rejectStore'])->name('stores.reject');

                    // Parametric routes
                    Route::get('/{seller}',          [AdminSellerController::class, 'show'])->name('show');
                    Route::get('/{seller}/edit',     [AdminSellerController::class, 'edit'])->name('edit');
                    Route::put('/{seller}',          [AdminSellerController::class, 'update'])->name('update');
                    Route::delete('/{seller}',       [AdminSellerController::class, 'destroy'])->name('destroy');
                    Route::get('/{seller}/goals',    [AdminSellerStoreClaimController::class, 'goals'])->name('goals.edit');
                    Route::put('/{seller}/goals',    [AdminSellerStoreClaimController::class, 'updateGoals'])->name('goals.update');
                });

            // Commissions
            Route::prefix('/commissions')
                ->name('commissions.')
                ->group(function () {
                    Route::get('/',               [AdminCommissionController::class, 'index'])->name('index');
                    Route::get('/dashboard',      [AdminCommissionController::class, 'sellerDashboard'])->name('dashboard');
                    Route::post('/mark-paid',     [AdminCommissionController::class, 'markPaid'])->name('mark-paid');
                    Route::post('/adjust',        [AdminCommissionController::class, 'standaloneAdjust'])->name('standalone-adjust');
                    Route::post('/{commission}/adjust',  [AdminCommissionController::class, 'adjust'])->name('adjust');
                    Route::delete('/{commission}',       [AdminCommissionController::class, 'destroy'])->name('destroy');
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

            // Coupons
            Route::prefix('/coupons')
                ->name('coupons.')
                ->controller(AdminCouponController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                    Route::get('/{coupon}/edit', 'edit')->name('edit');
                    Route::put('/{coupon}', 'update')->name('update');
                    Route::post('/{coupon}/toggle', 'toggle')->name('toggle');
                    Route::delete('/{coupon}', 'destroy')->name('destroy');
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
                            Route::get('/create', 'create')->name('create')->middleware('no_overdue_invoices');
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

                    // Coupons
                    Route::prefix('/coupons')
                        ->name('coupons.')
                        ->controller(ClientCouponController::class)
                        ->group(function () {
                            Route::get('/', 'index')->name('index');
                            Route::post('/validate', 'validate')->name('validate');
                        });

                });

        });

    Route::middleware(['role:seller'])
        ->prefix('/seller')
        ->name('seller.')
        ->group(function() {

            Route::get('/home', [SellerHomeController::class, 'index'])->name('home');

            // Stores
            Route::prefix('/stores')
                ->name('stores.')
                ->controller(SellerStoreController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/register', 'registerForm')->name('register');
                    Route::post('/verify-cnpj', 'verifyCNPJ')->name('verify-cnpj');
                    Route::post('/step1', 'confirmStep1')->name('step1');
                    Route::post('/step2', 'confirmStep2')->name('step2');
                    Route::post('/step3', 'confirmStep3')->name('step3');
                    Route::get('/{store}/edit', 'edit')->name('edit');
                    Route::put('/{store}', 'update')->name('update');
                });

            // Reports
            Route::prefix('/reports')
                ->name('reports.')
                ->group(function () {
                    Route::get('/commissions', [SellerReportController::class, 'commissions'])->name('commissions');
                    Route::get('/stores',      [SellerReportController::class, 'stores'])->name('stores');
                    Route::get('/goals',       [SellerReportController::class, 'goals'])->name('goals');
                });

        });

    Route::middleware(['role:erp'])
        ->prefix('/erp')
        ->name('erp.')
        ->group(function() {

            Route::get('/tenants', function() {
                return 'ERP';
            })->name('home.index');

        });
});
