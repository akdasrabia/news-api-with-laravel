<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Route;


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

        Route::middleware('auth:sanctum')->group(function () {
            // Stateful routes that require authentication.
        });
        
        Route::middleware(EnsureFrontendRequestsAreStateful::class)->group(function () {
            // Other routes that should be stateful.
        });
        
        // Unauthorized (401) durum kodu ve hata mesajı döndürmek için bir fallback rotası.
        Route::fallback(function () {
            return response()->json(['message' => 'Unauthorized.'], 401);
        });
    }
    

}
