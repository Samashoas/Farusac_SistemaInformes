<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use App\Models\Curso;
use App\Models\Informe;
use App\Models\InformeCoordinacion;
use App\Models\User;

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
        Paginator::defaultView('vendor.pagination.farusac');
        Paginator::defaultSimpleView('vendor.pagination.farusac-simple');

        // Route Model Binding explícito
        Route::model('informe', Informe::class);
        Route::model('informeCoordinacion', InformeCoordinacion::class);
        Route::model('curso', Curso::class);
        Route::model('usuario', User::class);

        // Restricción de tipado numérico para parámetros de rutas
        Route::pattern('informe', '[0-9]+');
        Route::pattern('informeCoordinacion', '[0-9]+');
        Route::pattern('curso', '[0-9]+');
        Route::pattern('usuario', '[0-9]+');
        Route::pattern('id', '[0-9]+');
    }
}
