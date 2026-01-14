<?php

use App\Domain\Exceptions\CartItemNotFoundException;
use App\Domain\Exceptions\CartNotFoundException;
use App\Domain\Exceptions\CategoryNotFoundException;
use App\Domain\Exceptions\EmptyCartException;
use App\Domain\Exceptions\InsufficientStockException;
use App\Domain\Exceptions\InvalidTokenException;
use App\Domain\Exceptions\OrderAlreadyPaidException;
use App\Domain\Exceptions\OrderNotFoundException;
use App\Domain\Exceptions\PaymentNotFoundException;
use App\Domain\Exceptions\PermissionNotFoundException;
use App\Domain\Exceptions\ProductNotFoundException;
use App\Domain\Exceptions\RoleNotFoundException;
use App\Domain\Exceptions\TokenExpiredException;
use App\Domain\Exceptions\UserNotFoundException;
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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle Domain Exceptions for API routes
        $exceptions->render(function (UserNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }
        });

        $exceptions->render(function (RoleNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }
        });

        $exceptions->render(function (PermissionNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }
        });

        $exceptions->render(function (InvalidTokenException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 400);
            }
        });

        $exceptions->render(function (TokenExpiredException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 400);
            }
        });

        $exceptions->render(function (ProductNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }
        });

        $exceptions->render(function (CategoryNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }
        });

        $exceptions->render(function (CartNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }
        });

        $exceptions->render(function (CartItemNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });

        $exceptions->render(function (InsufficientStockException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });

        $exceptions->render(function (OrderNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });

        $exceptions->render(function (PaymentNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });

        $exceptions->render(function (EmptyCartException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });

        $exceptions->render(function (OrderAlreadyPaidException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
    })->create();
