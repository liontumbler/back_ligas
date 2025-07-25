<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\VerificarLicenciaMiddleware;
use App\Http\Middleware\ContentSecurityPolicy;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(ContentSecurityPolicy::class);
        $middleware->append(VerificarLicenciaMiddleware::class);
        $middleware->alias([
            'JWT' => JwtMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

    })->create();
