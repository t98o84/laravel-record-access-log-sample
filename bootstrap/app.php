<?php

use App\Http\Middleware\AddInfoToContext;
use App\Http\Middleware\AssignIdToGuestUser;
use App\Http\Middleware\AssignRequestId;
use App\Http\Middleware\SaveAccessLog;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', [
            AssignIdToGuestUser::class,
            AssignRequestId::class,
            AddInfoToContext::class,
            SaveAccessLog::class,
        ]);
        $middleware->appendToGroup('api', [
            AssignRequestId::class,
            AddInfoToContext::class,
            SaveAccessLog::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
