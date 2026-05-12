<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckEstatus;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registrar middleware como alias para uso en rutas
        $middleware->alias([
            'check.estatus' => CheckEstatus::class,
        ]);

        // Opcional: Agregar al grupo web después de Authenticate
        // Esto asegura que toda ruta autenticada valide el estatus
        $middleware->appendToGroup('web', [
            CheckEstatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
