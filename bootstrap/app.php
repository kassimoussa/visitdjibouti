<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/operator.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\AdminAuth::class,  // Notre middleware admin
            'operator.auth' => \App\Http\Middleware\OperatorAuth::class,  // Notre middleware operator
            'operator.active' => \App\Http\Middleware\EnsureOperatorIsActive::class,  // Vérification compte actif
        ]);
    })
    ->withProviders([
        // Ajoutez votre service provider ici
        App\Providers\AuthenticationServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withCommands([
        \App\Console\Commands\GeneratePostmanCollection::class,
    ])->create();
