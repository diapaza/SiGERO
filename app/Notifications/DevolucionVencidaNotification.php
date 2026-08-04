<?php

namespace App\Notifications;

use App\Models\Movimiento;
use App\Models\Objeto;
use App\Models\User;

/**
 * Notificación de devolución vencida.
 *
 * Se envía al responsable del objeto y a los operadores cuando una salida
 * supera `NotificationService::DIAS_VENCIMIENTO` días sin retorno. `data`
 * contiene: `title`, `message`, `type` (`vencida`), `movimiento_id`,
 * `objeto_id`, `responsable_id` y `responsable`.
 */
class DevolucionVencidaNotification extends BaseNotification
{
    public function __construct(
        private Movimiento $movimiento,
        private int $dias,
    ) {}

    /**
     * Estructura almacenada en la tabla `notifications`.
     *
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
