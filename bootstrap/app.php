<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aplikasi berjalan di belakang proxy HTTPS (Vercel): percayai X-Forwarded-* agar IP, skema, dan HSTS benar
        $middleware->trustProxies(at: '*');
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Daftarkan alias middleware 'pemilik'
        $middleware->alias([
            'pemilik' => \App\Http\Middleware\PemilikMiddleware::class,
            'aktif'   => \App\Http\Middleware\EnsureUserAktif::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
