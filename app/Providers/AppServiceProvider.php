<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFour();
        // ESTA ES LA FORMA CORRECTA PARA SPATIE v6+ EN LARAVEL 12
        app('router')->aliasMiddleware('role', \Spatie\Permission\Middleware\RoleMiddleware::class);
        app('router')->aliasMiddleware('permission', \Spatie\Permission\Middleware\PermissionMiddleware::class);
        app('router')->aliasMiddleware('role_or_permission', \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class);
        // Auto-logout middleware: cierra sesión tras 5 minutos de inactividad
        app('router')->pushMiddlewareToGroup('web', \App\Http\Middleware\AutoLogout::class);
    }
}