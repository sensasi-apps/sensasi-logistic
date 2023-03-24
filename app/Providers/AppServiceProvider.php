<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('number', function (mixed $expression, int $min_fraction_digits = 0, int $max_fraction_digits = 4) {
            return "<?= Helper::numberFomat($expression, $min_fraction_digits, $max_fraction_digits); ?>";
        });
    }
}
