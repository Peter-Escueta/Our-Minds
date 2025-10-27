<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // in case id ever want to adjust token expiration, refresh token expiration, and personal access token expiration
        //Passport::tokensExpireIn(CarbonInterval::days(amountOfDays));
        //Passport::refreshTokensExpireIn(CarbonInterval::days(amountOfDays));
        //Passport::personalAccessTokensExpireIn(CarbonInterval::days(amountOfDays));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
