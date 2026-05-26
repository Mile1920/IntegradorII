<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ScheduleBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:schedule {--daily : Ejecutar backup diario completo} {--hourly : Ejecutar backup incremental por hora}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecutar backups programados (diario o por hora)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDaily = $this->option('daily');
        $isHourly = $this->option('hourly');

        if (!$isDaily && !$isHourly) {
            $this->error('Debe especificar --daily o --hourly');
            return 1;
        }

        if ($isDaily) {
            $this->info('Ejecutando backup diario completo...');
            return $this->runDailyBackup();
        }

        if ($isHourly) {
            $this->info('Ejecutando backup incremental por hora...');
            return $this->runHourlyBackup();
        }

        return 0;
    }

    /**
     * Ejecutar backup diario completo
     */
    private function runDailyBackup()
    {
        $this->info('Iniciando backup completo diario...');

        try {
            $exitCode = Artisan::call('backup:database', [
                '--full' => true
            ]);

            if ($exitCode === 0) {
                $this->info('✅ Backup diario completado exitosamente');

                // Limpiar backups antiguos (mantener 7 días de backups diarios)
                $this->cleanupOldDailyBackups();

                return 0;
            } else {
                $this->error('❌ Error en backup diario');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error ejecutando backup diario: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Ejecutar backup incremental por hora
     */
    private function runHourlyBackup()
    {
        $this->info('Iniciando backup incremental por hora...');

        try {
            $exitCode = Artisan::call('backup:database', [
                '--incremental' => true
            ]);

            if ($exitCode === 0) {
                $this->info('✅ Backup incremental completado exitosamente');

                // Limpiar backups antiguos (mantener 24 horas de backups incrementales)
                $this->cleanupOldHourlyBackups();

                return 0;
            } else {
                $this->error('❌ Error en backup incremental');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error ejecutando backup incremental: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Limpiar backups diarios antiguos (mantener 7 días)
     */
    private function cleanupOldDailyBackups()
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            return;
        }

        $files = glob($backupDir . '/**/**/full_backup_*.sql');
        $dailyBackups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            if (preg_match('/full_backup_(\d{4}-\d{2}-\d{2})_(\d{2}-\d{2}-\d{2})\.sql/', $filename, $matches)) {
                $date = $matches[1];
                $dailyBackups[$date] = $file;
            }
        }

        // Mantener solo los últimos 7 días
        krsort($dailyBackups); // Ordenar por fecha descendente
        $toKeep = array_slice($dailyBackups, 0, 7, true);
        $toDelete = array_diff_key($dailyBackups, $toKeep);

        foreach ($toDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
                $this->info("🗑️ Eliminado backup diario antiguo: " . basename($file));
            }
        }
    }

    /**
     * Limpiar backups incrementales antiguos (mantener 24 horas)
     */
    private function cleanupOldHourlyBackups()
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            return;
        }

        $files = glob($backupDir . '/**/**/incremental_backup_*.sql');

        // Mantener solo los archivos de las últimas 24 horas
        $cutoffTime = time() - (24 * 60 * 60); // 24 horas atrás

        foreach ($files as $file) {
            $fileTime = filemtime($file);
            if ($fileTime < $cutoffTime) {
                unlink($file);
                $this->info("🗑️ Eliminado backup incremental antiguo: " . basename($file));
            }
        }
    }
}