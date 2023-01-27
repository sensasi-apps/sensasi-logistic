<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

use App\Models\MaterialIn;
use App\Repositories\MaterialInRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(MaterialInRepository::class)
            ->needs(MaterialIn::class)
            ->give(function () {
                $materialInId = Route::current()->parameter('material_in');
                $with = [
                    'details.material',
                    'details.outDetails',
                    'details.stock'
                ];

                return $materialInId ? MaterialIn::with($with)->findOrFail($materialInId) : new MaterialIn();
            });
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
