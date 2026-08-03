<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

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
