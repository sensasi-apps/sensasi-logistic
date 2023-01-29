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
        Blade::directive('number', function (mixed $expression, int $decimals = null, string $decPoint = null, string $thousandsSep = null) {

            if (app()->getLocale() == 'id') {
                $decimals = $decimals ?? 0;
                $decPoint = $decPoint ?? "','";
                $thousandsSep = $thousandsSep ?? "'.'";
            } else {
                $decimals = $decimals ?? 2;
                $decPoint = $decPoint ?? "'.'";
                $thousandsSep = $thousandsSep ?? "','";
            }

            return "<?= number_format($expression, $decimals, $decPoint, $thousandsSep) ?>";
        });
    }
}
