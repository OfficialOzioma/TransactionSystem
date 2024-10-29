<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
         // Define the authenticate middleware response
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
    //    $exceptions->render(function (Throwable $e, Request $request) {
    //         if ($request->is('api/*')) {
    //             return response()->json([
    //                 'message' => $e->getMessage()
    //             ], 401);
    //         }
    //     });
         // validation errors
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => $e->errors(),
                ], 422);
            }
        });
       
    })->create();
