<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        //customm validation rule for total probability
        Validator::extend('total_probability', function ($attribute, $value, $parameters, $validator) {
            $totalProbability = $value + array_sum($parameters);
            return $totalProbability <= 100;
        });
    }
}