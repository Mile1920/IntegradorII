<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SensorData;
use App\Models\Incidente;
use App\Services\FirebaseService;

class SensorController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    // Receive sensor payloads. Optional header X-SENSOR-KEY must match env SENSOR_SECRET if set.
    public function receive(Request $request)
    {
        $secret = env('SENSOR_SECRET');
        if ($secret) {
            $key = $request->header('X-SENSOR-KEY') ?? $request->input('key');
            if (!$key || $key !== $secret) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        }

        $data = $request->all();
        $sensorId = $request->input('device_id') ?: $request->input('sensor_id');

        if (!$sensorId) {
            return response()->json(['message' => 'Sensor ID requerido'], 400);
        }

        try {
            // Guardar en base de datos local
            $record = SensorData::create([
                'device_id' => $sensorId,
                'tipo' => $request->input('tipo'),
                'payload' => $data,
            ]);

            // Sincronizar con Firebase
            $firebaseSuccess = $this->firebaseService->receiveSensorData($sensorId, $data);

            if ($firebaseSuccess) {
                Log::info("Datos del sensor {$sensorId} sincronizados con Firebase");
            } else {
                Log::warning("Error sincronizando sensor {$sensorId} con Firebase");
            }

            // Si el payload trae un flag de alerta crítica, crear incidente automático
            if (isset($data['alert']) && $data['alert'] === true) {
                $incidente = Incidente::create([
                    'trabajador_id' => null,
                    'area_id' => $request->input('area_id'),
                    'descripcion' => 'Alerta generada por sensor: ' . json_encode($data),
                    'gravedad' => 'critica',
                    'estado' => 'pendiente',
                ]);

                Log::info("Incidente crítico creado automáticamente por sensor {$sensorId}, ID: {$incidente->id}");
            }

            return response()->json([
                'status' => 'ok',
                'firebase_sync' => $firebaseSuccess,
                'record_id' => $record->id
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error recibiendo datos sensor: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno'], 500);
        }
    }

    /**
     * Mostrar listados de datos de sensores recientes (página de módulo).
     */
    public function index(Request $request)
    {
        $recent = SensorData::orderBy('created_at', 'desc')->limit(50)->get();

        $esp32Mac = config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00'));
        $macClean = str_replace(':', '', $esp32Mac);
        $esp32Lecturas = SensorData::where(function ($q) use ($esp32Mac, $macClean) {
                $q->where('device_id', 'esp32_001')
                  ->orWhere('device_id', 'esp32_gases_01')
                  ->orWhere('device_id', $esp32Mac)
                  ->orWhere('device_id', 'ESP32-' . $macClean)
                  ->orWhere('device_id', 'like', 'esp32_%')
                  ->orWhere('device_id', 'like', 'ESP32%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        $esp32Ip = config('esp32.ip', env('ESP32_IP', '192.168.1.205'));

        $datos = $recent;

        return view('sensors.index', compact('datos', 'esp32Lecturas', 'esp32Ip', 'esp32Mac'));
    }
}
