<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FirebaseSensorController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Mostrar dashboard de sensores Firebase
     */
    public function index()
    {
        $sensorData = $this->firebaseService->getSensorData();
        $activeSensors = $this->firebaseService->getActiveSensors();

        // Estadísticas
        $totalSensors = count($activeSensors);
        $totalReadings = 0;
        $alerts = 0;

        if ($sensorData) {
            foreach ($sensorData as $sensorId => $sensor) {
                if (isset($sensor['lecturas'])) {
                    $totalReadings += count($sensor['lecturas']);
                }
                if (isset($sensor['alertas'])) {
                    $alerts += count($sensor['alertas']);
                }
            }
        }

        return view('firebase-sensors.index', compact(
            'sensorData',
            'activeSensors',
            'totalSensors',
            'totalReadings',
            'alerts'
        ));
    }

    /**
     * Mostrar detalles de un sensor específico
     */
    public function show($sensorId)
    {
        $sensor = $this->firebaseService->getSensorById($sensorId);
        $recentReadings = $this->firebaseService->getRecentReadings($sensorId);

        if (!$sensor) {
            abort(404, 'Sensor no encontrado');
        }

        return view('firebase-sensors.show', compact('sensor', 'sensorId', 'recentReadings'));
    }

    /**
     * API endpoint para obtener datos de sensores
     */
    public function apiData(Request $request): JsonResponse
    {
        $sensorData = $this->firebaseService->getSensorData();

        return response()->json([
            'success' => true,
            'data' => $sensorData,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Dashboard de sensores para admins
     */
    public function dashboard()
    {
        $sensorData = $this->firebaseService->getSensorData();
        $activeSensorsData = $this->firebaseService->getActiveSensors();
        $activeSensors = array_keys($activeSensorsData);

        // Estadísticas
        $totalSensors = count($activeSensors);
        $totalReadings = 0;
        $alerts = 0;
        $avgTemperature = 0;
        $avgHumidity = 0;
        $count = 0;

        if ($sensorData) {
            foreach ($sensorData as $sensorId => $sensor) {
                if (isset($sensor['lecturas'])) {
                    $totalReadings += count($sensor['lecturas']);
                    foreach ($sensor['lecturas'] as $reading) {
                        if (isset($reading['temperatura'])) {
                            $avgTemperature += $reading['temperatura'];
                            $count++;
                        }
                        if (isset($reading['humedad'])) {
                            $avgHumidity += $reading['humedad'];
                        }
                    }
                }
                if (isset($sensor['alertas'])) {
                    $alerts += count($sensor['alertas']);
                }
            }
        }

        if ($count > 0) {
            $avgTemperature /= $count;
            $avgHumidity /= $count;
        }

        $esp32Mac = config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00'));
        $esp32Lecturas = \App\Models\SensorData::where(function ($q) use ($esp32Mac) {
                $q->where('device_id', 'esp32_001')
                  ->orWhere('device_id', $esp32Mac)
                  ->orWhere('device_id', 'ESP32-' . str_replace(':', '', $esp32Mac));
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        $esp32Ip = config('esp32.ip', env('ESP32_IP', '192.168.1.205'));

        return view('sensor-dashboard', compact(
            'sensorData',
            'activeSensors',
            'totalSensors',
            'totalReadings',
            'alerts',
            'avgTemperature',
            'avgHumidity',
            'esp32Lecturas',
            'esp32Ip',
            'esp32Mac'
        ));
    }

    /**
     * Mostrar formulario para crear sensor
     */
    public function create()
    {
        return view('firebase-sensors.create');
    }

    /**
     * Guardar nuevo sensor en Firebase
     */
    public function store(Request $request)
    {
        $request->validate([
            'sensor_id' => 'required|string|max:100',
            'tipo' => 'required|in:movimiento_tierra,gases_toxicos,signos_vitales',
            'area' => 'required|string|max:100',
        ]);

        // Resolver area_id desde el nombre
        $area = \App\Models\Area::where('nombre', $request->area)->first();
        $areaId = $area ? $area->id : null;

        $sensorData = [
            'activo' => true,
            'tipo' => $request->tipo,
            'area' => $request->area,
            'ultima_lectura' => now()->toISOString(),
            'alertas' => [],
            'lecturas' => [],
            'ultima_actualizacion' => now()->toISOString(),
        ];

        switch ($request->tipo) {
            case 'movimiento_tierra':
                $sensorData['movimiento'] = 0.0;
                $sensorData['aceleracion'] = 0.0;
                break;
            case 'gases_toxicos':
                $sensorData['co'] = 0.0;
                $sensorData['co2'] = 400.0;
                $sensorData['metano'] = 0.0;
                $sensorData['oxigeno'] = 21.0;
                break;
            case 'signos_vitales':
                $sensorData['frecuencia_cardiaca'] = 70;
                $sensorData['temperatura_corporal'] = 36.5;
                $sensorData['saturacion_oxigeno'] = 98;
                $sensorData['presion_arterial'] = '120/80';
                break;
        }

        // Guardar en base de datos local (siempre)
        $localSensor = Sensor::firstOrCreate(
            ['device_id' => $request->sensor_id],
            [
                'nombre' => $request->sensor_id,
                'area_id' => $areaId,
                'activo' => true,
                'estado' => 'activo',
            ]
        );

        // Intentar guardar en Firebase
        $firebaseSuccess = $this->firebaseService->sendSensorData($request->sensor_id, $sensorData);

        $message = 'Sensor creado exitosamente en el sistema local';
        $type = 'success';

        if ($firebaseSuccess) {
            $message = 'Sensor creado exitosamente en Firebase y sistema local';
        } else {
            $message .= ' (Firebase no disponible, funciona solo localmente)';
            $type = 'warning';
        }

        // Registrar en auditoría
        \App\Helpers\AuditLogger::log(
            'CREAR_SENSOR',
            'sensors',
            $localSensor->id,
            "Sensor {$request->sensor_id} de tipo {$request->tipo} creado en área {$request->area}"
        );

        return redirect()->route('sensor-dashboard')->with($type, $message);
    }

    /**
     * API endpoint para datos de un sensor específico
     */
    public function apiSensorData($sensorId): JsonResponse
    {
        $sensor = $this->firebaseService->getSensorById($sensorId);
        $readings = $this->firebaseService->getRecentReadings($sensorId);

        return response()->json([
            'success' => true,
            'sensor' => $sensor,
            'readings' => $readings,
            'timestamp' => now()->toISOString()
        ]);
    }
}