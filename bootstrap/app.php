<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use App\Http\Middleware\ApiForceJsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // here is the prepend of the forceJsonResponse class!
        $middleware->api(prepend: [
            ApiForceJsonResponse::class,
        ]);

         // Define the authenticate middleware response
         $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            } else {
                return route('login');
            }
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
    
         // validation errors
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // authentication errors
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 401);
            }
        });

        // 404 errors
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }
        });

        // 500 errors
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 500);
            }
        });
    })->create();
