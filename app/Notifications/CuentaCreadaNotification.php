<?php

namespace App\Notifications;

use App\Models\User;

class CuentaCreadaNotification extends BaseNotification
{
    public function __construct(
        private User $usuario,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Cuenta creada',
            'message' => sprintf(
                'Tu cuenta de usuario fue creada correctamente. Usuario: %s.',
                $this->usuario->username,
            ),
            'type' => 'cuenta',
            'username' => $this->usuario->username,
        ];
    }
}
