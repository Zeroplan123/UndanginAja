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
        $middleware->alias([
            'is_admin' => \App\Http\Middleware\AdminMiddleware::class,
            'is_user' => \App\Http\Middleware\IsUser::class,
            'check.user.status' => \App\Http\Middleware\CheckUserStatus::class,
            'check.banned' => \App\Http\Middleware\CheckBannedUser::class,
        ]);
        
        // Apply banned user check to all authenticated routes
        $middleware->web(append: [
            \App\Http\Middleware\CheckBannedUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
