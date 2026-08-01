<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Custom handler: tampilkan pesan dalam Bahasa Indonesia saat login terkena rate limit
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('login')) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
                $minutes    = ceil($retryAfter / 60);
                $seconds    = $retryAfter;

                $message = $minutes >= 1
                    ? "Terlalu banyak percobaan login. Silakan tunggu {$minutes} menit sebelum mencoba kembali."
                    : "Terlalu banyak percobaan login. Silakan tunggu {$seconds} detik sebelum mencoba kembali.";

                return redirect()->route('login')
                    ->with('throttle_error', $message)
                    ->with('throttle_seconds', $seconds)
                    ->withInput($request->only('email'));
            }
        });
    })->create();
