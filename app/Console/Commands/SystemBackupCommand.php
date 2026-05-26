<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\Trabajador;
use App\Models\Ingreso;
use App\Models\Incidente;
use App\Models\Sensor;
use App\Models\SensorData;
use App\Models\User;

class SystemBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un respaldo lógico de la información crítica del sistema en formato JSON.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando respaldo lógico del sistema Mina Porco...');

        try {
            $timestamp = now()->format('Ymd_His');
            $directory = 'backups';
            $filename = "mina_porco_backup_{$timestamp}.json";
            $path = "{$directory}/{$filename}";

            // Asegurar que exista el directorio de backups
            Storage::disk('local')->makeDirectory($directory);

            $payload = [
                'meta' => [
                    'generated_at' => now()->toIso8601String(),
                    'app_env' => config('app.env'),
                    'app_url' => config('app.url'),
                    'db_connection' => config('database.default'),
                    'description' => 'Respaldo lógico en formato JSON. Pensado para restauraciones manuales o auditoría.',
                ],
                'tables' => [
                    'areas' => Area::all(),
                    'cargos' => Cargo::all(),
                    'trabajadors' => Trabajador::all(),
                    'ingresos' => Ingreso::limit(5000)->get(),
                    'incidentes' => Incidente::limit(5000)->get(),
                    'sensors' => Sensor::all(),
                    'sensor_data_recent' => SensorData::whereDate('created_at', '>=', now()->subDays(7))->get(),
                    'users' => User::select('id', 'name', 'email', 'created_at', 'updated_at')->get(),
                ],
            ];

            Storage::disk('local')->put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $sizeBytes = Storage::disk('local')->size($path);
            $sizeMb = $sizeBytes / 1048576;

            $message = sprintf(
                'Backup lógico creado en storage/app/%s (%.2f MB).',
                $path,
                $sizeMb
            );

            $this->info($message);
            Log::info('[SystemBackup] ' . $message, ['path' => $path, 'size_mb' => $sizeMb]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Ocurrió un error al generar el respaldo: ' . $e->getMessage());
            Log::error('[SystemBackup] Error al generar backup', [
                'exception' => $e,
            ]);

            return self::FAILURE;
        }
    }
}

