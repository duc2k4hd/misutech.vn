<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tin tưởng proxy (Cloudflare, Nginx, cPanel SSL reverse proxy) để nhận diện đúng HTTPS
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo(fn (Request $request) =>
            $request->is('admin/*') ? route('admin.login') : route('login')
        );
        $middleware->redirectUsersTo(fn (Request $request) =>
            $request->is('admin/*') || $request->is('admin') ? route('admin.dashboard') : '/'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Trả JSON thay vì HTML khi có exception trên các route API
        // Quan trọng: phải bao gồm cả admin/api/* để DataTables không nhận HTML error
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*') ||
                $request->is('admin/api/*') ||
                $request->expectsJson(),
        );
    })->create();

