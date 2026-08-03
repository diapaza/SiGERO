<?php

namespace App\Services;

use App\Models\Movimiento;
use App\Models\Objeto;
use App\Models\User;
use App\Notifications\CuentaCreadaNotification;
use App\Notifications\DevolucionVencidaNotification;
use App\Notifications\PermisosActualizadosNotification;
use App\Notifications\RetornoRegistradoNotification;
use App\Notifications\SalidaRegistradaNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

readonly class NotificationService
{
    public const DIAS_VENCIMIENTO = 3;

    public function salidaRegistrada(Movimiento $movimiento, User $registradoPor): void
    {
        $notification = new SalidaRegistradaNotification($movimiento, $registradoPor);

        $this->notifyOperadores($notification);
    }

    public function retornoRegistrado(Movimiento $movimiento, User $registradoPor): void
    {
        $notification = new RetornoRegistradoNotification($movimiento, $registradoPor);

        $this->notifyOperadores($notification);
    }

    public function permisosActualizados(User $usuario, User $actualizadoPor): void
    {
        $usuario->notify(new PermisosActualizadosNotification($actualizadoPor));
    }

    public function cuentaCreada(User $usuario): void
    {
        $usuario->notify(new CuentaCreadaNotification($usuario));
    }

    /**
     * Genera notificaciones de devoluciones vencidas (evita duplicados).
     */
    public function generarVencidas(): void
    {
        $corte = now()->subDays(self::DIAS_VENCIMIENTO);

        $objetosVencidos = Objeto::with('movimientoActivo.user')
            ->where('disponible', false)
            ->get()
            ->filter(function (Objeto $objeto) use ($corte) {
                $movimiento = $objeto->movimientoActivo;

                return $movimiento && $movimiento->fecha_hora < $corte;
            });

        foreach ($objetosVencidos as $objeto) {
            /** @var Movimiento $movimiento */
            $movimiento = $objeto->movimientoActivo;

            if ($this->yaNotificado($movimiento)) {
                continue;
            }

            $diasVencidos = max(1, (int) now()->diffInDays($movimiento->fecha_hora));

            $this->notifyResponsable($movimiento, new DevolucionVencidaNotification($movimiento, $diasVencidos));
            $this->notifyOperadores(new DevolucionVencidaNotification($movimiento, $diasVencidos));
        }
    }

    /**
     * Verifica si ya existe una notificación de vencimiento para ese movimiento.
     */
    private function yaNotificado(Movimiento $movimiento): bool
    {
        return DatabaseNotification::query()
            ->where('type', DevolucionVencidaNotification::class)
            ->where('data->movimiento_id', $movimiento->id)
            ->exists();
    }

    private function notifyResponsable(Movimiento $movimiento, object $notification): void
    {
        if ($movimiento->user) {
            $movimiento->user->notify($notification);
        }
    }

    private function notifyOperadores(object $notification): void
    {
        $this->operadores()->each->notify($notification);
    }

    /**
     * Usuarios con permisos operativos (registrar movimientos o ver dashboard).
     *
     * @return Collection<int, User>
     */
    private function operadores(): Collection
    {
        return User::permission(['registrar movimientos', 'ver dashboard'])
            ->get();
    }
}
