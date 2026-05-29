<?php

namespace App\Http\Controllers;

use App\Models\Ingreso;
use App\Models\Trabajador;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;

class AlertController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        $alerts = [];

        // 1. Alertas de Firebase (sensores IoT)
        $sensorData = $this->firebaseService->getSensorData();
        if ($sensorData) {
            foreach ($sensorData as $sensorId => $sensor) {
                if (isset($sensor['alertas']) && is_array($sensor['alertas'])) {
                    foreach ($sensor['alertas'] as $alert) {
                        $alerts[] = $this->makeAlert(
                            $sensorId, $sensor['tipo'] ?? 'desconocido',
                            $sensor['area'] ?? 'Sin área',
                            $alert['mensaje'] ?? 'Alerta detectada',
                            $alert['nivel'] ?? 'medio',
                            $alert['timestamp'] ?? now()->toISOString()
                        );
                    }
                }
                $alerts = array_merge($alerts, $this->checkSensorThresholds($sensorId, $sensor));
            }
        }

        // 2. Alertas de trabajadores con ingreso sin salida (>8 horas)
        $limite = now()->subHours(8);
        $ingresosSinSalida = Ingreso::where('tipo', 'ingreso')
            ->where('registrado_en', '<=', $limite)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('ingresos', 'salidas')
                    ->whereColumn('salidas.trabajador_id', 'ingresos.trabajador_id')
                    ->where('salidas.tipo', 'salida')
                    ->whereRaw('salidas.registrado_en > ingresos.registrado_en');
            })
            ->with('trabajador')
            ->get();

        foreach ($ingresosSinSalida as $ingreso) {
            $trabajador = $ingreso->trabajador;
            if (!$trabajador) continue;

            $horas = now()->diffInHours($ingreso->registrado_en);
            $areaNombre = $trabajador->area?->nombre ?? 'Sin área';
            $horaIngreso = $ingreso->registrado_en ? $ingreso->registrado_en->format('H:i') : '--:--';
            $timestamp = $ingreso->registrado_en ? $ingreso->registrado_en->toISOString() : now()->toISOString();
            $alerts[] = $this->makeAlert(
                'TRABAJADOR-' . $trabajador->id,
                'salida_pendiente',
                $areaNombre,
                "{$trabajador->nombre_completo} lleva {$horas}h sin registrar salida (ingreso: {$horaIngreso})",
                $horas >= 12 ? 'critico' : 'alto',
                $timestamp
            );
        }

        // 3. Ordenar: más recientes primero
        usort($alerts, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        $alerts = array_slice($alerts, 0, 100);

        return view('alerts.index', compact('alerts'));
    }

    private function makeAlert($sensor, $tipo, $area, $mensaje, $nivel, $timestamp)
    {
        return compact('sensor', 'tipo', 'area', 'mensaje', 'nivel', 'timestamp');
    }

    private function checkSensorThresholds($sensorId, $sensor)
    {
        $alerts = [];
        if (!isset($sensor['tipo'])) return $alerts;

        switch ($sensor['tipo']) {
            case 'gases_toxicos':
                if (($sensor['co'] ?? 0) > 50) {
                    $alerts[] = $this->makeAlert($sensorId, 'gases_toxicos', $sensor['area'] ?? 'Sin área',
                        'Nivel de CO crítico: ' . ($sensor['co'] ?? 0) . ' ppm', 'critico', now()->toISOString());
                }
                break;
            case 'movimiento_tierra':
                if (($sensor['movimiento'] ?? 0) > 5) {
                    $alerts[] = $this->makeAlert($sensorId, 'movimiento_tierra', $sensor['area'] ?? 'Sin área',
                        'Movimiento de tierra excesivo: ' . ($sensor['movimiento'] ?? 0) . ' mm', 'critico', now()->toISOString());
                }
                break;
            case 'signos_vitales':
                $hr = $sensor['frecuencia_cardiaca'] ?? 0;
                if ($hr > 120 || ($hr < 50 && $hr > 0)) {
                    $alerts[] = $this->makeAlert($sensorId, 'signos_vitales', $sensor['area'] ?? 'Sin área',
                        'Frecuencia cardíaca anormal: ' . $hr . ' bpm', 'critico', now()->toISOString());
                }
                break;
        }
        return $alerts;
    }
}
