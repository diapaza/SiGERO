<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Base de todas las notificaciones del sistema.
 *
 * Todas las notificaciones se entregan por el canal `database` (tabla
 * `notifications`) y de forma síncrona (sin colas). Las subclases deben
 * implementar `toArray()` con la estructura de `data` que se persiste.
 */
abstract class BaseNotification extends Notification
{
    use Queueable;

    /**
     * Canal de entrega: solo base de datos.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Representación en base de datos.
     *
     * @return array<string, mixed>
     */
    abstract public function toArray(object $notifiable): array;
}
