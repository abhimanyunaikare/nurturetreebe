<?php

namespace App\Providers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

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
        // ✅ Register the API route prefix and middleware
        Route::prefix('api')
        ->middleware('api')
        ->group(base_path('routes/api.php'));

        // ✅ Define the 'api' rate limiter
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id) // Authenticated users
                : Limit::perMinute(30)->by($request->ip()); // Guests
        });
    }
}
