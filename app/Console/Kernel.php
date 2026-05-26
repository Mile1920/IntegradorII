<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        \App\Console\Commands\SystemBackupCommand::class,
        \App\Console\Commands\AlertarSalidaPendiente::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Backup lógico cada 48 horas de la información crítica del sistema
        $schedule->command('system:backup')
            ->cron('0 2 */2 * *')
            ->withoutOverlapping()
            ->onOneServer();

        // Alerta cada hora si hay ingresos sin salida después de 8 horas
        $schedule->command('alertar:salida-pendiente')
            ->hourly()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

