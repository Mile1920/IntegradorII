<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class AlertController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        $sensorData = $this->firebaseService->getSensorData();
        $alerts = [];

        if ($sensorData) {
            foreach ($sensorData as $sensorId => $sensor) {
                if (isset($sensor['alertas']) && is_array($sensor['alertas'])) {
                    foreach ($sensor['alertas'] as $alert) {
                        $alerts[] = [
                            'sensor' => $sensorId,
                            'tipo' => $sensor['tipo'] ?? 'desconocido',
                            'area' => $sensor['area'] ?? 'Sin área',
                            'mensaje' => $alert['mensaje'] ?? 'Alerta detectada',
                            'nivel' => $alert['nivel'] ?? 'medio',
                            'timestamp' => $alert['timestamp'] ?? now()->toISOString()
                        ];
                    }
                }

                if (isset($sensor['tipo'])) {
                    switch ($sensor['tipo']) {
                        case 'gases_toxicos':
                            if (($sensor['co'] ?? 0) > 50) {
                                $alerts[] = [
                                    'sensor' => $sensorId,
                                    'tipo' => 'gases_toxicos',
                                    'area' => $sensor['area'] ?? 'Sin área',
                                    'mensaje' => 'Nivel de CO crítico: ' . ($sensor['co'] ?? 0) . ' ppm',
                                    'nivel' => 'critico',
                                    'timestamp' => now()->toISOString()
                                ];
                            }
                            break;
                        case 'movimiento_tierra':
                            if (($sensor['movimiento'] ?? 0) > 5) {
                                $alerts[] = [
                                    'sensor' => $sensorId,
                                    'tipo' => 'movimiento_tierra',
                                    'area' => $sensor['area'] ?? 'Sin área',
                                    'mensaje' => 'Movimiento de tierra excesivo: ' . ($sensor['movimiento'] ?? 0) . ' mm',
                                    'nivel' => 'critico',
                                    'timestamp' => now()->toISOString()
                                ];
                            }
                            break;
                        case 'signos_vitales':
                            if (($sensor['frecuencia_cardiaca'] ?? 0) > 120 || ($sensor['frecuencia_cardiaca'] ?? 0) < 50) {
                                $alerts[] = [
                                    'sensor' => $sensorId,
                                    'tipo' => 'signos_vitales',
                                    'area' => $sensor['area'] ?? 'Sin área',
                                    'mensaje' => 'Frecuencia cardíaca anormal: ' . ($sensor['frecuencia_cardiaca'] ?? 0) . ' bpm',
                                    'nivel' => 'critico',
                                    'timestamp' => now()->toISOString()
                                ];
                            }
                            break;
                    }
                }
            }
        }

        $alerts = array_slice(array_reverse($alerts), 0, 50);

        return view('alerts.index', compact('alerts'));
    }
}
