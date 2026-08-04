<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controlador de notificaciones del usuario autenticado.
 *
 * Permite listar el historial paginado de notificaciones y marcar las no
 * leídas como leídas (individual o masivamente). Solo el dueño de la
 * notificación puede marcarla como leída (se valida el `notifiable`).
 */
class NotificationController extends Controller
{
    /**
     * Lista paginada de las notificaciones del usuario.
     *
     * Cada notificación se transforma a un shape plano:
     * `id`, `type`, `title`, `message`, `created_at`, `read`.
     *
     * @return Response Vista Inertia `Notifications/Index`.
     */
    public function index(Request $request): Response
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (DatabaseNotification $notification) => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'general',
                'title' => $notification->data['title'] ?? 'Notificación',
                'message' => $notification->data['message'] ?? '',
                'created_at' => $notification->created_at,
                'read' => $notification->read_at !== null,
            ]);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Marca todas las notificaciones no leídas del usuario como leídas.
     *
     * @return RedirectResponse Vuelve a la página anterior.
     */
    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }

    /**
     * Marca una notificación concreta como leída, solo si pertenece al usuario.
     *
     * @param  DatabaseNotification  $notification  Notificación a marcar (route model binding).
     * @return RedirectResponse Vuelve a la página anterior.
     */
    public function read(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        abort_unless(
            $notification->notifiable_id === $request->user()->getKey()
                && $notification->notifiable_type === $request->user()->getMorphClass(),
            403,
        );

        $notification->markAsRead();

        return back();
    }
}
