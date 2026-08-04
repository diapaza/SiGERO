<?php

namespace App\Notifications;

use App\Models\User;

/**
 * Notificación de permisos actualizados.
 *
 * Se envía al usuario afectado cuando un administrador cambia sus roles o
 * permisos. `data` contiene: `title`, `message`, `type` (`permisos`) y
 * `actualizado_por`.
 */
class PermisosActualizadosNotification extends BaseNotification
{
    public function __construct(
        private User $actualizadoPor,
    ) {}

    /**
     * Estructura almacenada en la tabla `notifications`.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Permisos actualizados',
            'message' => sprintf(
                '%s actualizó tus permisos o rol en el sistema.',
                $this->actualizadoPor->name,
            ),
            'type' => 'permisos',
            'actualizado_por' => $this->actualizadoPor->name,
        ];
    }
}
