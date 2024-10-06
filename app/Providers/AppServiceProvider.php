<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
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
        Http::macro('system', function () {
            $headers = [
                'Authorization' => 'Bearer ' . config('app.system.authenticationToken'),
            ];

            return Http::withHeaders($headers)
                ->acceptJson()
                ->baseUrl(config('app.system.baseUrl'));
        });
    }
}
