<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Trabajador;
use App\Models\Ingreso;
use App\Models\Incidente;
use App\Models\Sensor;
use App\Models\SensorData;

class SystemController extends Controller
{
    public function status()
    {
        // Estado de la base de datos
        try {
            DB::connection()->getPdo();
            $databaseStatus = [
                'status' => 'success',
                'message' => 'Conectado',
                'version' => 'PostgreSQL ' . DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION)
            ];
        } catch (\Exception $e) {
            $databaseStatus = [
                'status' => 'warning',
                'message' => 'Error de conexión'
            ];
        }

        // Estado del correo electrónico
        try {
            $mailStatus = [
                'status' => 'success',
                'message' => 'Configurado',
                'mailer' => config('mail.default')
            ];
        } catch (\Exception $e) {
            $mailStatus = [
                'status' => 'warning',
                'message' => 'No configurado'
            ];
        }

        // Estado del almacenamiento
        try {
            $storagePath = storage_path('app');
            $freeSpace = disk_free_space($storagePath);
            $totalSpace = disk_total_space($storagePath);
            $usedSpace = $totalSpace - $freeSpace;
            $percentage = round(($usedSpace / $totalSpace) * 100, 1);

            $storageStatus = [
                'status' => $percentage > 90 ? 'warning' : 'success',
                'message' => number_format($usedSpace / 1073741824, 2) . ' GB / ' . number_format($totalSpace / 1073741824, 2) . ' GB (' . $percentage . '%)'
            ];
        } catch (\Exception $e) {
            $storageStatus = [
                'status' => 'warning',
                'message' => 'No disponible'
            ];
        }

        // Estado de backups
        $backupPath = storage_path('app/backups');
        $backups = [];
        
        if (File::exists($backupPath)) {
            $backupFiles = File::files($backupPath);
            foreach ($backupFiles as $file) {
                $backups[] = [
                    'file' => $file->getFilename(),
                    'date' => date('d/m/Y H:i:s', $file->getMTime()),
                    'size' => $file->getSize()
                ];
            }
            
            usort($backups, function($a, $b) {
                return strtotime(str_replace('/', '-', $b['date'])) - strtotime(str_replace('/', '-', $a['date']));
            });
        }

        $backupStatus = [
            'status' => count($backups) > 0 ? 'success' : 'warning',
            'message' => count($backups) > 0 ? count($backups) . ' backups disponibles' : 'No hay backups disponibles',
            'last_backup' => count($backups) > 0 ? $backups[0] : null,
            'total_backups' => count($backups)
        ];

        // Estado de colas
        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            
            $queueStatus = [
                'status' => $failedJobs > 0 ? 'warning' : 'success',
                'message' => $failedJobs > 0 ? 'Hay trabajos fallidos' : 'Todo funcionando correctamente',
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs
            ];
        } catch (\Exception $e) {
            $queueStatus = [
                'status' => 'secondary',
                'message' => 'No disponible',
                'pending_jobs' => 0,
                'failed_jobs' => 0
            ];
        }

        // Estadísticas del sistema
        $stats = [
            'trabajadores' => Trabajador::count(),
            'trabajadores_activos' => Trabajador::where('activo', true)->count(),
            'ingresos_hoy' => Ingreso::whereDate('created_at', today())->count(),
            'incidentes_abiertos' => Incidente::where('estado', 'abierto')->count(),
            'sensores_locales' => Sensor::where('activo', true)->count(),
            'datos_sensores_hoy' => SensorData::whereDate('created_at', today())->count(),
        ];

        return view('system.status', compact(
            'databaseStatus',
            'mailStatus',
            'storageStatus',
            'backupStatus',
            'queueStatus',
            'stats'
        ));
    }

    /**
     * Documentación técnica simplificada para exposición del sistema.
     */
    public function docs()
    {
        return view('system.docs');
    }
}