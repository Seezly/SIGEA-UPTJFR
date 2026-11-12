<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
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
        $response = function (Request $request, array $headers) {
            return back()
                ->with('flash', [
                    'error' => 'Has excedido el número de intentos. Por favor, inténtalo de nuevo más tarde.'
                ])
                ->withHeaders($headers);
        };

        RateLimiter::for('registration', function (Request $request) use ($response) {
            return [
                Limit::perMinute(5)
                    ->by(
                        $request->ip() . '|' . strtolower(trim($request->input('email', '')))
                    )
                    ->response($response),

                Limit::perMinute(30)
                    ->by($request->ip())
                    ->response($response),
            ];
        });
    }
}
