<?php

namespace App\Notifications;

use App\Models\User;

class PermisosActualizadosNotification extends BaseNotification
{
    public function __construct(
        private User $actualizadoPor,
    ) {}

    /**
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
