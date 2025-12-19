<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login.',
                    'error'   => 'unauthenticated',
                ], 401);
            }
        });

        // ✅ 403 Forbidden (policy / gate)
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This action is unauthorized.',
                    'error'   => 'forbidden',
                ], 403);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e->getStatusCode() === 401) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated. Please login.',
                        'error'   => 'unauthenticated',
                    ], 401);
                }

                if ($e->getStatusCode() === 403) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This action is unauthorized.',
                        'error'   => 'forbidden',
                    ], 403);
                }
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors'  => $e->validator->errors(),
                ], 422);
            }
        });
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e->getPrevious() instanceof ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found.',
                        'error'   => 'resource_not_found',
                    ], 404);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint not found.',
                    'error'   => 'route_not_found',
                ], 404);
            }
        });

        $exceptions->render(function (QueryException $e, Request $request) {
            if ($request->is('api/*')) {
                logger()->error('Database error', [
                    'exception' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'A server error occurred. Please try again later.',
                    'error'   => 'database_error',
                ], 500);
            }
        });
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                logger()->critical('Unhandled exception', [
                    'exception' => $e,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unexpected server error.',
                    'error'   => 'server_error',
                ], 500);
            }
        });
    })
    ->create();
