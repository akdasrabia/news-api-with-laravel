<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use App\Http\Middleware\CustomAuthMiddleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\AuthenticateSession;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
           $middleware->alias([
            'custom-auth'=>CustomAuthMiddleware::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            ]);
            $middleware->validateCsrfTokens(except: [
                'stripe/*',
                'http://localhost:3000',
            ]);
    })->withMiddleware(function (Middleware $middleware) {
        //$middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Validation\UnauthorizedException $e, Request $request) {
            return response()->json(
                ['message' => 'Login olmadınız'],
                Illuminate\Http\Response::HTTP_UNAUTHORIZED
            );
        });
    })->create();
