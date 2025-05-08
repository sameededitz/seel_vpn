<?php

namespace App\Providers;

use Illuminate\Http\Request;
use App\Listeners\UpdateLastLogin;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

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
        Event::listen(Verified::class, UpdateLastLogin::class);

        RateLimiter::for('api', function (Request $request) {
            $maxAttempts = 60;
            $key = optional($request->user())->id ?: $request->ip();

            return Limit::perMinute($maxAttempts)->by($key)->response(function (Request $request, array $headers) {
                return response()->json([
                    'message' => "Too many requests. Remaining attempts: {$headers['X-RateLimit-Remaining']}. Try again in {$headers['Retry-After']} seconds.",
                ], 429, $headers);
            });
        });
    }
}
