<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Comando Artisan para generar notificaciones de devoluciones vencidas.
 *
 * Se ejecuta manualmente con `php artisan app:notificar-vencidas` y está
 * programado en `routes/console.php` cada 6 horas. Delega en
 * `NotificationService::generarVencidas()`.
 */
class NotificarVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notificar-vencidas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera notificaciones para objetos con devoluciones vencidas.';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $service): int
    {
        $service->generarVencidas();

        $this->info('Notificaciones de devoluciones vencidas generadas.');

        return self::SUCCESS;
    }
}
