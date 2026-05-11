<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use NumberFormatter;

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
        // Configuración regional para México
        if (App::getLocale() === 'es_MX') {
            // Carbon: locales para español mexicano
            Carbon::setLocale('es_MX');
            
            // Macros personalizadas para formato mexicano
            Carbon::macro('toMexicanDate', function() {
                return $this->format('d/m/Y');
            });
            
            Carbon::macro('toMexicanDateTime', function() {
                return $this->format('d/m/Y H:i');
            });
            
            Carbon::macro('toMexicanFull', function() {
                return $this->format('l d \d\e F \d\e Y'); // Ej: "lunes 11 de mayo de 2026"
            });

            // Configurar locale para formateo de números y moneda
            $locale = setlocale(LC_ALL, 'es_MX.UTF-8', 'es_MX', 'es-MX', 'Spanish_Mexico', 'spanish', 'es');
            
            // Configurar formato por defecto de Carbon para strings
            Carbon::setToStringFormat('d/m/Y H:i');
        }
    }
}
