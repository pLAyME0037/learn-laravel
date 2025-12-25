<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EnsureUserHasPermission;
use App\Http\Middleware\StaffMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\AuthenticateSession;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            AuthenticateSession::class,
        ]);

        $middleware->alias([
            'role'                => RoleMiddleware::class,
            'permission'          => PermissionMiddleware::class,
            'role_has_permission' => RoleOrPermissionMiddleware::class,
            'student'             => StudentMiddleware::class,
            'staff'               => StaffMiddleware::class,
            'admin'               => AdminMiddleware::class,
            'super_user'          => SuperAdminMiddleware::class,
            'permission'          => PermissionMiddleware::class,
            'has_permission'      => EnsureUserHasPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
