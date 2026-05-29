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
        $connected = false;
        $latency = null;
        $error = null;

        $puertos = [80, 8080, 443, 81, 3000, 5000];

        foreach ($puertos as $port) {
            try {
                $start = microtime(true);
                $response = Http::timeout(2)->get("http://{$ip}:{$port}/");
                $latency = round((microtime(true) - $start) * 1000);
                if ($response->successful()) {
                    $connected = true;
                    Log::info("[ESP32] Conectado en puerto {$port}, latencia {$latency}ms");
                    break;
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
                continue;
            }
        }

        if ($connected) {
            cache(['esp32_connected' => true, 'esp32_checked_at' => now()], 300);
        } else {
            cache(['esp32_connected' => false, 'esp32_error' => $error], 60);
        }

        return response()->json([
            'connected' => $connected,
            'ip' => $ip,
            'latency_ms' => $latency,
            'error' => $connected ? null : ($error ?? 'Sin respuesta en ningún puerto'),
            'checked_at' => now()->toIso8601String()
        ]);
    }

    public function disconnect()
    {
        cache(['esp32_connected' => false, 'esp32_disconnected_at' => now()], 300);
        Log::info("[ESP32] Desconexión manual");

        return response()->json([
            'connected' => false,
            'message' => 'ESP32 desconectado manualmente',
            'disconnected_at' => now()->toIso8601String()
        ]);
    }

    public function status()
    {
        $connected = cache('esp32_connected', false);
        $checkedAt = cache('esp32_checked_at');
        $error = cache('esp32_error');

        return response()->json([
            'connected' => $connected,
            'ip' => config('esp32.ip', env('ESP32_IP', '192.168.1.205')),
            'latency_ms' => null,
            'error' => $error,
            'checked_at' => $checkedAt?->toIso8601String(),
            'disconnected_at' => cache('esp32_disconnected_at')?->toIso8601String()
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
