<?php

/*
|--------------------------------------------------------------------------
| Arranque de la aplicación Laravel
|--------------------------------------------------------------------------
|
| Configura el contenedor de la aplicación: rutas web/consola, el middleware
| global de Inertia (`HandleInertiaRequests`), los alias de middleware de
| Spatie (role/permission) y los manejadores de excepciones para respuestas
| Inertia (403/404) y redirecciones por sesión expirada (419) o exceso de
| intentos de login (429).
|
*/

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (HttpException $e, Request $request) {
            if ($request->inertia()) {
                if ($e->getStatusCode() === 403) {
                    return Inertia::render('Errors/FourZeroThree', [], 403);
                }
                if ($e->getStatusCode() === 404) {
                    return Inertia::render('Errors/FourZeroFour', [], 404);
                }
            }
        });

        $exceptions->renderable(function (HttpException $e, Request $request) {
            // Sesión/CSRF expirada: volver a iniciar sesión.
            if ($e->getStatusCode() === 419) {
                return redirect()->route('signin');
            }

            // Exceso de intentos de inicio de sesión.
            if ($e->getStatusCode() === 429) {
                return back()->with('error', 'Demasiados intentos. Intente de nuevo en unos minutos.');
            }
        });
    })->create();
