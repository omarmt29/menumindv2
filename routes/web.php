<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/entrar', [LoginController::class, 'create'])->name('login');
    Route::post('/entrar', [LoginController::class, 'store'])->middleware('throttle:8,1');
});

Route::get('/m/{menu:qr_code}', [PublicMenuController::class, 'show'])
    ->name('menus.public')
    ->where('menu', '[a-z0-9]{12}');

Route::middleware('auth')->group(function () {
    Route::post('/salir', [LoginController::class, 'destroy'])->name('logout');

    Route::prefix('panel')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::middleware('role:super_admin')->group(function () {
            Route::resource('restaurantes', RestaurantController::class)
                ->parameters(['restaurantes' => 'restaurant'])
                ->names('restaurants');

            Route::prefix('restaurantes/{restaurant}')->scopeBindings()->group(function () {
                Route::resource('productos', ProductController::class)
                    ->parameters(['productos' => 'product'])
                    ->names('restaurants.products')
                    ->except(['show']);

                Route::get('menus/{menu}/qr.svg', [MenuController::class, 'qr'])->name('restaurants.menus.qr');
                Route::resource('menus', MenuController::class)->names('restaurants.menus');
            });
        });

        Route::resource('productos', ProductController::class)
            ->parameters(['productos' => 'product'])
            ->names('products')
            ->except(['show']);

        Route::get('menus/{menu}/qr.svg', [MenuController::class, 'qr'])->name('menus.qr');
        Route::resource('menus', MenuController::class)->names('menus');

        Route::get('configuracion', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('configuracion', [SettingController::class, 'update'])->name('settings.update');
    });
});
