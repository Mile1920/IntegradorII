<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Esp32SensorController extends Controller
{
    public function index()
    {
        $ip = config('esp32.ip', env('ESP32_IP', '192.168.1.205'));
        $mac = config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00'));

        $macClean = str_replace(':', '', $mac);
        $lecturas = SensorData::where(function ($q) use ($mac, $macClean) {
                $q->where('device_id', 'esp32_001')
                  ->orWhere('device_id', 'esp32_gases_01')
                  ->orWhere('device_id', $mac)
                  ->orWhere('device_id', 'ESP32-' . $macClean)
                  ->orWhere('device_id', 'like', 'esp32_%')
                  ->orWhere('device_id', 'like', 'ESP32%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $ultimaConexion = $lecturas->first()?->created_at;

        return view('sensors.esp32', compact('ip', 'mac', 'lecturas', 'ultimaConexion'));
    }

    public function recibir(Request $request)
    {
        $payload = $request->all();

        $rules = [
            'device_id' => 'required|string|max:100',
            'tipo' => 'required|string|max:50',
        ];

        if (isset($payload['mediciones']) && is_array($payload['mediciones'])) {
            $rules['mediciones'] = 'array';
        }

        $validator = validator($payload, $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            SensorData::create([
                'device_id' => $payload['device_id'],
                'tipo' => $payload['tipo'],
                'payload' => $payload['mediciones'] ?? $payload,
                'recibido_en' => now(),
            ]);

            Log::info("[ESP32] Datos recibidos de {$payload['device_id']} (tipo: {$payload['tipo']})");

            return response()->json([
                'success' => true,
                'message' => 'Datos recibidos correctamente',
                'recibido' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error("[ESP32] Error al guardar datos: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function health()
    {
        return response()->json([
            'status' => 'ok',
            'ip' => config('esp32.ip', env('ESP32_IP', '192.168.1.205')),
            'mac' => config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00')),
            'timestamp' => now()->toIso8601String(),
            'version' => '1.0',
            'server' => 'Mina Porco - API ESP32'
        ]);
    }

    public function connect()
    {
        $ip = config('esp32.ip', env('ESP32_IP', '192.168.1.205'));
        $mac = config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00'));

        $connected = false;
        $metodo = 'datos_recientes';
        $ultimaConexion = null;

        // El ESP32 no tiene servidor TCP activo. Solo envía datos via HTTP POST.
        // Verificamos si hay datos recientes en la BD (últimos 10 minutos).
        $macClean = str_replace(':', '', $mac);
        $ultimoDato = SensorData::where(function ($q) use ($mac, $macClean) {
                $q->where('device_id', 'esp32_001')
                  ->orWhere('device_id', 'esp32_gases_01')
                  ->orWhere('device_id', $mac)
                  ->orWhere('device_id', 'ESP32-' . $macClean)
                  ->orWhere('device_id', 'like', 'esp32_%')
                  ->orWhere('device_id', 'like', 'ESP32%');
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($ultimoDato && $ultimoDato->created_at && now()->diffInMinutes($ultimoDato->created_at) <= 10) {
            $connected = true;
            $ultimaConexion = $ultimoDato->created_at;
        }

        if ($connected) {
            cache(['esp32_connected' => true, 'esp32_checked_at' => now()], 300);
            Log::info("[ESP32] Conectado vía {$metodo}");
        } else {
            cache(['esp32_connected' => false, 'esp32_error' => 'Sin datos recientes en BD'], 60);
        }

        return response()->json([
            'connected' => $connected,
            'ip' => $ip,
            'mac' => $mac,
            'metodo' => $metodo,
            'ultima_conexion' => $ultimaConexion?->diffForHumans(),
            'mensaje' => $connected
                ? "ESP32 conectado (datos recibidos hace {$ultimaConexion->diffForHumans()})"
                : "ESP32 no conectado. No se recibieron datos en los últimos 10 minutos. Verificá que el ESP32 esté encendido y enviando datos a {$ip}:8000/api/sensor/esp32/recibir",
            'checked_at' => now()->toIso8601String()
        ]);
    }

    public function status()
    {
        $ip = config('esp32.ip', env('ESP32_IP', '192.168.1.205'));
        $mac = config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00'));

        $connected = false;
        $ultimaConexion = null;
        $minutosSinDatos = null;

        // Verificar si hay datos recientes en BD
        $macClean = str_replace(':', '', $mac);
        $ultimoDato = SensorData::where(function ($q) use ($mac, $macClean) {
                $q->where('device_id', 'esp32_001')
                  ->orWhere('device_id', 'esp32_gases_01')
                  ->orWhere('device_id', $mac)
                  ->orWhere('device_id', 'ESP32-' . $macClean)
                  ->orWhere('device_id', 'like', 'esp32_%')
                  ->orWhere('device_id', 'like', 'ESP32%');
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($ultimoDato && $ultimoDato->created_at) {
            $ultimaConexion = $ultimoDato->created_at;
            $minutosSinDatos = now()->diffInMinutes($ultimaConexion);
            $connected = $minutosSinDatos <= 10;
        }

        // Desconexión manual tiene prioridad
        $manualDisconnect = cache('esp32_disconnected_at');
        if ($manualDisconnect && $manualDisconnect->gt($ultimaConexion ?? now()->subYear())) {
            $connected = false;
        }

        return response()->json([
            'connected' => $connected,
            'ip' => $ip,
            'ultima_conexion' => $ultimaConexion?->toIso8601String(),
            'minutos_sin_datos' => $minutosSinDatos,
            'checked_at' => now()->toIso8601String()
        ]);
    }

    public function updateConfig(Request $request)
    {
        $data = $request->validate([
            'ip' => 'required|string',
            'mac' => 'required|string|max:30',
        ]);

        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $envContent = preg_replace('/^ESP32_IP=.*/m', 'ESP32_IP=' . $data['ip'], $envContent);
        if (!preg_match('/^ESP32_IP=/m', $envContent)) {
            $envContent .= "\nESP32_IP={$data['ip']}";
        }

        $envContent = preg_replace('/^ESP32_MAC=.*/m', 'ESP32_MAC=' . $data['mac'], $envContent);
        if (!preg_match('/^ESP32_MAC=/m', $envContent)) {
            $envContent .= "\nESP32_MAC={$data['mac']}";
        }

        file_put_contents($envPath, $envContent);

        Log::info("[ESP32] Configuración actualizada: IP={$data['ip']}, MAC={$data['mac']}");

        return response()->json([
            'success' => true,
            'message' => 'Configuración del ESP32 actualizada correctamente'
        ]);
    }

    public function getConfig()
    {
        return response()->json([
            'ip' => config('esp32.ip', env('ESP32_IP', '192.168.1.205')),
            'mac' => config('esp32.mac', env('ESP32_MAC', '00:4B:12:35:3E:00')),
        ]);
    }
}
