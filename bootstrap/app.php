<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Middleware\EnsureDealerIsActive;
use App\Http\Middleware\EnsureRoleIsActive;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsDealer;
use App\Http\Middleware\TeamsPermission;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            TeamsPermission::class,
        ]);
        $middleware->priority([
            TeamsPermission::class,
            SubstituteBindings::class,
        ]);
        $middleware->appendToGroup('all.active', [
            EnsureUserIsActive::class,
            EnsureDealerIsActive::class,
            EnsureRoleIsActive::class,
        ]);
        $middleware->alias([
            'user.active'   => EnsureUserIsActive::class,
            'dealer.active' => EnsureDealerIsActive::class,
            'role.active'   => EnsureRoleIsActive::class,
            'isAdmin'       => IsAdmin::class,
            'isDealer'      => IsDealer::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // 404 — Page Not Found
        // Log nahi — normal user behavior hai
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Resource not found.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // 403 — Unauthorized Access
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            AuditLogger::warning($request, 'Unauthorized access attempt', [
                'user_id' => auth()->id()           ?? 'guest',
                'email'   => auth()->user()?->email ?? 'guest',
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            return response()->view('errors.403', [], 403);
        });

        // 500 — Unexpected Server Error
        // Log automatic — Laravel khud report() karta hai
        // $exceptions->render(function (\Throwable $e, Request $request) {
        //     if ($e instanceof \Illuminate\Validation\ValidationException) {
        //         return null;
        //     }
        //     if ($e instanceof AuthorizationException) {
        //         return null;
        //     }
        //     if ($request->expectsJson()) {
        //         return response()->json(['message' => 'Something went wrong.'], 500);
        //     }
        //     if (config('app.debug')) {
        //         return null;
        //     }
        //     return response()->view('errors.500', [], 500);
        // });

    })->create();