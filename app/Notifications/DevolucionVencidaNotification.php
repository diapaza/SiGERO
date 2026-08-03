<?php

namespace App\Notifications;

use App\Models\Movimiento;
use App\Models\Objeto;
use App\Models\User;

class DevolucionVencidaNotification extends BaseNotification
{
    public function __construct(
        private Movimiento $movimiento,
        private int $dias,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        /** @var Objeto|null $objeto */
        $objeto = $this->movimiento->objeto;

        /** @var User|null $responsable */
        $responsable = $this->movimiento->user;

        return [
            'title' => 'Devolución vencida',
            'message' => sprintf(
                'El objeto %s (%s) lleva %d días prestado sin ser devuelto.',
                $objeto?->nombre ?? 'sin nombre',
                $objeto?->codigo ?? '-',
                $this->dias,
            ),
            'type' => 'vencida',
            'movimiento_id' => $this->movimiento->id,
            'objeto_id' => $objeto?->id,
            'responsable_id' => $responsable?->id,
            'responsable' => $responsable?->name,
        ];
    }
}
