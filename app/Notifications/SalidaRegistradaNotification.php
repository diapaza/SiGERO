<?php

namespace App\Notifications;

use App\Models\Movimiento;
use App\Models\Objeto;
use App\Models\User;

/**
 * Notificación de salida registrada.
 *
 * Se envía a los operadores cuando se registra una salida. `data` contiene:
 * `title`, `message`, `type` (`salida`), `movimiento_id`, `objeto_id`,
 * `responsable_id`.
 */
class SalidaRegistradaNotification extends BaseNotification
{
    public function __construct(
        private Movimiento $movimiento,
        private User $registradoPor,
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
            'title' => 'Salida registrada',
            'message' => sprintf(
                '%s registró la salida del objeto %s (%s) a nombre de %s.',
                $this->registradoPor->name,
                $objeto?->nombre ?? 'sin nombre',
                $objeto?->codigo ?? '-',
                $responsable?->name ?? '—',
            ),
            'type' => 'salida',
            'movimiento_id' => $this->movimiento->id,
            'objeto_id' => $objeto?->id,
            'responsable_id' => $responsable?->id,
        ];
    }
}
