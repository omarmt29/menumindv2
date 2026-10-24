<?php

namespace App\Providers;

use App\Models\Restaurant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        Route::bind('restaurant', function (string $value) {
            return Restaurant::query()->whereKey($value)->firstOrFail();
        });
    }
}
