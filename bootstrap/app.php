<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/login',
            users: function () {
                $user = auth()->user();
                if ($user?->hasRole(\App\Enums\UserRole::Admin->value)) {
                    return route('admin.dashboard');
                }
                if ($user?->hasRole(\App\Enums\UserRole::PetugasPresensi->value)) {
                    return route('admin.checkin.index');
                }
                if ($user?->hasRole(\App\Enums\UserRole::Voters->value)) {
                    return route('voter.dashboard');
                }
                return '/login';
            }
        );

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'single.session' => \App\Http\Middleware\EnsureSingleSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
