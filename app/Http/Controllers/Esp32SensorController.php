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

        $lecturas = SensorData::where(function ($q) use ($mac) {
                $q->where('device_id', 'esp32_001')
                  ->orWhere('device_id', $mac)
                  ->orWhere('device_id', 'ESP32-' . str_replace(':', '', $mac));
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
        $metodo = null;
        $puertosAbiertos = [];

        // 1. Probar conexión TCP directa al ESP32 (rápido, no necesita HTTP)
        foreach ([80, 8080, 81, 23, 443, 5000] as $port) {
            $errno = null;
            $errstr = null;
            $conn = @fsockopen($ip, $port, $errno, $errstr, 1);
            if ($conn) {
                fclose($conn);
                $puertosAbiertos[] = $port;
                $connected = true;
                $metodo = "TCP puerto {$port}";
                break;
            }
        }

        // 2. Si no responde TCP, verificar si hay datos recientes en BD
        if (!$connected) {
            $ultimoDato = SensorData::where(function ($q) use ($mac) {
                    $q->where('device_id', 'esp32_001')
                      ->orWhere('device_id', $mac)
                      ->orWhere('device_id', 'ESP32-' . str_replace(':', '', $mac));
                })
                ->orderBy('created_at', 'desc')
                ->first();

            if ($ultimoDato && $ultimoDato->created_at && now()->diffInMinutes($ultimoDato->created_at) <= 10) {
                $connected = true;
                $metodo = 'datos_recientes';
            }
        }

        if ($connected) {
            cache(['esp32_connected' => true, 'esp32_checked_at' => now()], 300);
            Log::info("[ESP32] Conectado vía {$metodo}");
        } else {
            cache(['esp32_connected' => false, 'esp32_error' => 'No responde'], 60);
        }

        return response()->json([
            'connected' => $connected,
            'ip' => $ip,
            'mac' => $mac,
            'metodo' => $metodo,
            'puertos_abiertos' => $puertosAbiertos,
            'mensaje' => $connected
                ? "ESP32 conectado ({$metodo})"
                : "ESP32 no responde en {$ip}. Verificá que esté encendido y en la misma red.",
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
        $ultimoDato = SensorData::where(function ($q) use ($mac) {
                $q->where('device_id', 'esp32_001')
                  ->orWhere('device_id', $mac)
                  ->orWhere('device_id', 'ESP32-' . str_replace(':', '', $mac));
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
