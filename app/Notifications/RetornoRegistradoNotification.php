<?php

namespace App\Notifications;

use App\Models\Movimiento;
use App\Models\Objeto;
use App\Models\User;

/**
 * Notificación de retorno registrado.
 *
 * Se envía a los operadores cuando se registra un retorno. `data` contiene:
 * `title`, `message`, `type` (`retorno`), `movimiento_id`, `objeto_id`,
 * `responsable_id`.
 */
class RetornoRegistradoNotification extends BaseNotification
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
            'title' => 'Retorno registrado',
            'message' => sprintf(
                '%s registró el retorno del objeto %s (%s) que estaba a cargo de %s.',
                $this->registradoPor->name,
                $objeto?->nombre ?? 'sin nombre',
                $objeto?->codigo ?? '-',
                $responsable?->name ?? '—',
            ),
            'type' => 'retorno',
            'movimiento_id' => $this->movimiento->id,
            'objeto_id' => $objeto?->id,
            'responsable_id' => $responsable?->id,
        ];
    }
}
