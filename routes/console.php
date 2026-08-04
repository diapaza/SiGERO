<?php

/*
|--------------------------------------------------------------------------
| Rutas de consola y programación de tareas
|--------------------------------------------------------------------------
|
| Define los comandos Artisan registrados en la consola y la programación
| (schedule) de tareas. En producción, el cron debe invocar
| `php artisan schedule:run` cada minuto para ejecutar la tarea programada.
|
*/

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Comando de ejemplo (proporcionado por Laravel)
|--------------------------------------------------------------------------
*/
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tarea programada: notificaciones de devoluciones vencidas
|--------------------------------------------------------------------------
| Ejecuta `app:notificar-vencidas` cada 6 horas para generar las
| notificaciones de préstamos vencidos.
*/
Schedule::command('app:notificar-vencidas')->everySixHours();
