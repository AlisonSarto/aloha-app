<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust the HTTPS headers forwarded by Traefik/Dockploy.
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo('/');
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'has_store' => \App\Http\Middleware\EnsureUserHasStore::class,
            'active_store' => \App\Http\Middleware\EnsureActiveStore::class,
            'no_overdue_invoices' => \App\Http\Middleware\EnsureNoOverdueInvoices::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $exception, \Illuminate\Http\Request $request) {
            if ($request->isMethod('post') && $request->is('login')) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Sua sessão expirou. Entre novamente.');
            }
        });
    })->create();
