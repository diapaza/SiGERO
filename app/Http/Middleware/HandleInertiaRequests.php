<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de Inertia.
 *
 * Comparte con todas las respuestas Inertia las props comunes que consumen
 * las vistas y el layout: usuario autenticado (con roles y permisos),
 * notificaciones recientes, errores de validación y mensajes flash.
 */
class HandleInertiaRequests
{
    /**
     * Vista raíz de la SPA.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Comparte las props globales de Inertia y continúa con la petición.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var ViewErrorBag $errors */
        $errors = $request->session()->get('errors');

        Inertia::share([
            // Usuario autenticado con sus roles y permisos efectivos.
            'auth' => [
                'user' => fn () => $request->user() ? [
                    'id' => $request->user()->id,
                    'username' => $request->user()->username,
                    'name' => $request->user()->name,
                    'roles' => $request->user()->getRoleNames()->toArray(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name')->toArray(),
                ] : null,
            ],
            // Últimas 10 notificaciones del usuario (más recientes primero).
            'notifications' => fn () => $request->user()
                ? $request->user()
                    ->notifications()
                    ->latest()
                    ->take(10)
                    ->get()
                    ->map(fn ($notification) => [
                        'id' => $notification->id,
                        'type' => $notification->data['type'] ?? 'general',
                        'title' => $notification->data['title'] ?? 'Notificación',
                        'message' => $notification->data['message'] ?? '',
                        'created_at' => $notification->created_at,
                        'read' => $notification->read_at !== null,
                    ])
                : [],
            // Contador de notificaciones sin leer (para la campana).
            'unreadNotifications' => fn () => $request->user()?->unreadNotifications()->count() ?? 0,
            // Errores de validación aplanados (primer mensaje por campo).
            'errors' => fn () => (object) collect($errors?->getBag('default')->toArray())
                ->map(fn (array $messages) => $messages[0] ?? '')
                ->filter()
                ->all(),
            // Mensajes flash para toasts.
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'appName' => config('app.name', 'SiGERO'),
        ]);

        return $next($request);
    }
}
