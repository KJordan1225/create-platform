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
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'creator' => \App\Http\Middleware\EnsureUserIsCreator::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'creator.subscription' => \App\Http\Middleware\EnsureCreatorHasActivePlatformSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
