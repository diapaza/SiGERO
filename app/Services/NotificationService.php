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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Servicio de notificaciones.
 *
 * Centraliza el envío de las 5 notificaciones del sistema (salida, retorno,
 * devolución vencida, permisos actualizados y cuenta creada) y la generación
 * de las notificaciones de vencimiento (sin duplicados). Las notificaciones
 * usan el canal `database` y se envían en línea (no en cola).
 */
readonly class NotificationService
{
    /**
     * Días sin retorno a partir de los cuales una salida se considera vencida.
     */
    public const DIAS_VENCIMIENTO = 3;

    /**
     * Notifica a los operadores cuando se registra una salida.
     */
    public function salidaRegistrada(Movimiento $movimiento, User $registradoPor): void
    {
        $notification = new SalidaRegistradaNotification($movimiento, $registradoPor);

        $this->notifyOperadores($notification);
    }

    /**
     * Notifica a los operadores cuando se registra un retorno.
     */
    public function retornoRegistrado(Movimiento $movimiento, User $registradoPor): void
    {
        $notification = new RetornoRegistradoNotification($movimiento, $registradoPor);

        $this->notifyOperadores($notification);
    }

    /**
     * Notifica al usuario cuando cambian sus roles o permisos.
     */
    public function permisosActualizados(User $usuario, User $actualizadoPor): void
    {
        $usuario->notify(new PermisosActualizadosNotification($actualizadoPor));
    }

    /**
     * Notifica a un usuario recién creado con los datos de su cuenta.
     */
    public function cuentaCreada(User $usuario): void
    {
        $usuario->notify(new CuentaCreadaNotification($usuario));
    }

    /**
     * Genera notificaciones de devoluciones vencidas (evita duplicados).
     *
     * Revisa los objetos prestados cuya salida superó `DIAS_VENCIMIENTO` y, si
     * aún no se notificó ese movimiento, avisa al responsable y a los
     * operadores. Se ejecuta desde el comando `app:notificar-vencidas`
     * (programado cada 6 horas).
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

    /**
     * Notifica al responsable del objeto (si existe).
     */
    private function notifyResponsable(Movimiento $movimiento, object $notification): void
    {
        if ($movimiento->user) {
            $movimiento->user->notify($notification);
        }
    }

    /**
     * Notifica a todos los operadores.
     */
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
