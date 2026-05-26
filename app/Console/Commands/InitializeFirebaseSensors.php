<?php

namespace App\Console\Commands;

use App\Services\FirebaseService;
use Illuminate\Console\Command;

class InitializeFirebaseSensors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:init-sensors {--force : Forzar reinicialización incluso si ya existen datos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializar Firebase con datos de sensores de ejemplo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $firebaseService = app(FirebaseService::class);

        $this->info('Verificando conexión con Firebase...');

        // Verificar si ya existen datos
        $existingData = $firebaseService->getSensorData();

        if ($existingData && !$this->option('force')) {
            $this->warn('Ya existen datos en Firebase. Use --force para reinicializar.');
            $this->info('Datos existentes encontrados: ' . count($existingData) . ' sensores');

            if ($this->confirm('¿Desea ver los datos existentes?')) {
                foreach ($existingData as $sensorId => $sensor) {
                    $this->line("- {$sensorId}: " . ($sensor['tipo'] ?? 'desconocido') .
                               " en " . ($sensor['area'] ?? 'sin área'));
                }
            }

            return;
        }

        $this->info('Inicializando sensores en Firebase...');

        $successCount = $firebaseService->generateAndSendSensorData();

        if ($successCount > 0) {
            $this->info("✅ {$successCount} sensores inicializados exitosamente en Firebase");
            $this->info('Firebase URL: ' . config('firebase.database_url'));
        } else {
            $this->error('❌ Error al inicializar sensores en Firebase');
            $this->error('Verifique la configuración de Firebase y la conexión a internet');
        }

        return $successCount > 0 ? 0 : 1;
    }
}