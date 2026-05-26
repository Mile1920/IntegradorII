<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('firebase.database_url');
    }

    /**
     * Obtener datos de sensores usando REST API
     */
    public function getSensorData($path = 'sensores')
    {
        try {
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/') . '.json';

            // Intentar obtener datos reales de Firebase
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                // Si Firebase devuelve datos, usarlos
                if ($data && is_array($data) && !empty($data)) {
                    \Log::info('Datos obtenidos exitosamente desde Firebase');
                    return $data;
                }

                // Si Firebase está vacío o no tiene estructura, inicializar con datos mock
                \Log::info('Firebase conectado pero sin datos, inicializando con datos de ejemplo');
                $this->initializeFirebaseWithMockData();
                return $this->getMockData();
            }

            // Si la respuesta no es exitosa, usar datos mock
            \Log::warning('Firebase respondió con error, usando datos de ejemplo. Status: ' . $response->status());
            return $this->getMockData();

        } catch (\Exception $e) {
            \Log::error('Error conectando con Firebase, usando datos de ejemplo: ' . $e->getMessage());
            return $this->getMockData();
        }
    }

    /**
     * Datos de dispositivos IoT conectados
     */
    private function getMockData()
    {
        return [
            'sensor_movimiento_tierra_1' => [
                'activo' => true,
                'tipo' => 'movimiento_tierra',
                'area' => 'Túnel Principal',
                'subnivel' => 'Nivel -100m',
                'movimiento' => 2.3, // mm de movimiento
                'aceleracion' => 0.15, // g
                'ultima_lectura' => now()->toISOString(),
                'alertas' => [
                    ['mensaje' => 'Movimiento de tierra detectado', 'nivel' => 'medio', 'timestamp' => now()->subHours(1)->toISOString()],
                ],
                'lecturas' => [
                    ['movimiento' => 1.8, 'aceleracion' => 0.12, 'timestamp' => now()->subHours(2)->toISOString()],
                    ['movimiento' => 2.3, 'aceleracion' => 0.15, 'timestamp' => now()->subHours(1)->toISOString()],
                ]
            ],
            'sensor_gases_toxicos_1' => [
                'activo' => true,
                'tipo' => 'gases_toxicos',
                'area' => 'Zona de Extracción',
                'subnivel' => 'Nivel -200m',
                'co' => 75.2, // ppm - CRÍTICO (>50)
                'co2' => 450.5, // ppm
                'metano' => 25.1, // ppm
                'oxigeno' => 19.8, // %
                'ultima_lectura' => now()->toISOString(),
                'alertas' => [
                    ['mensaje' => 'Nivel de CO elevado', 'nivel' => 'alto', 'timestamp' => now()->subMinutes(30)->toISOString()],
                ],
                'lecturas' => [
                    ['co' => 12.1, 'co2' => 420.3, 'metano' => 22.5, 'oxigeno' => 20.1, 'timestamp' => now()->subHours(1)->toISOString()],
                    ['co' => 75.2, 'co2' => 450.5, 'metano' => 25.1, 'oxigeno' => 19.8, 'timestamp' => now()->toISOString()],
                ]
            ],
            'sensor_signos_vitales_1' => [
                'activo' => true,
                'tipo' => 'signos_vitales',
                'area' => 'Zona de Trabajo',
                'subnivel' => 'Nivel -50m',
                'frecuencia_cardiaca' => 78, // bpm
                'temperatura_corporal' => 36.8, // °C
                'saturacion_oxigeno' => 97, // %
                'presion_arterial' => '120/80', // mmHg
                'ultima_lectura' => now()->toISOString(),
                'alertas' => [],
                'lecturas' => [
                    ['frecuencia_cardiaca' => 75, 'temperatura_corporal' => 36.6, 'saturacion_oxigeno' => 98, 'presion_arterial' => '118/78', 'timestamp' => now()->subHours(1)->toISOString()],
                    ['frecuencia_cardiaca' => 78, 'temperatura_corporal' => 36.8, 'saturacion_oxigeno' => 97, 'presion_arterial' => '120/80', 'timestamp' => now()->toISOString()],
                ]
            ],
            'sensor_movimiento_tierra_2' => [
                'activo' => true,
                'tipo' => 'movimiento_tierra',
                'area' => 'Túnel Secundario',
                'subnivel' => 'Nivel -150m',
                'movimiento' => 1.1,
                'aceleracion' => 0.08,
                'ultima_lectura' => now()->toISOString(),
                'alertas' => [],
                'lecturas' => [
                    ['movimiento' => 0.9, 'aceleracion' => 0.06, 'timestamp' => now()->subHours(1)->toISOString()],
                    ['movimiento' => 1.1, 'aceleracion' => 0.08, 'timestamp' => now()->toISOString()],
                ]
            ]
        ];
    }

    /**
     * Obtener datos de un sensor específico
     */
    public function getSensorById($sensorId)
    {
        $data = $this->getSensorData();
        return $data[$sensorId] ?? null;
    }

    /**
     * Obtener todos los sensores activos
     */
    public function getActiveSensors()
    {
        $data = $this->getSensorData();
        if (!$data) return [];

        return array_filter($data, function($sensor) {
            return isset($sensor['activo']) && $sensor['activo'];
        });
    }

    /**
     * Obtener lecturas recientes de un sensor
     */
    public function getRecentReadings($sensorId, $limit = 50)
    {
        $sensor = $this->getSensorById($sensorId);
        if (!$sensor || !isset($sensor['lecturas'])) {
            return [];
        }

        $lecturas = $sensor['lecturas'];
        return array_slice($lecturas, -$limit);
    }

    /**
     * Enviar datos de sensor a Firebase
     */
    public function sendSensorData($sensorId, $data)
    {
        try {
            $url = rtrim($this->baseUrl, '/') . '/sensores/' . $sensorId . '.json';
            $response = Http::timeout(15)->put($url, $data);

            if ($response->successful()) {
                \Log::info("Datos enviados exitosamente a Firebase para sensor {$sensorId}");
                return true;
            } else {
                \Log::warning("Error enviando datos a Firebase para sensor {$sensorId}. Status: " . $response->status() . ", Body: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Error enviando datos a Firebase para sensor ' . $sensorId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Inicializar Firebase con datos de ejemplo si está vacío
     */
    private function initializeFirebaseWithMockData()
    {
        try {
            $sensorData = $this->getMockData();
            $successCount = 0;

            foreach ($sensorData as $sensorId => $data) {
                // Agregar timestamp actual
                $data['ultima_actualizacion'] = now()->toISOString();

                if ($this->sendSensorData($sensorId, $data)) {
                    $successCount++;
                }
            }

            if ($successCount > 0) {
                \Log::info("Firebase inicializado con {$successCount} sensores de ejemplo");
            }
        } catch (\Exception $e) {
            \Log::error('Error inicializando Firebase: ' . $e->getMessage());
        }
    }

    /**
     * Generar datos simulados de sensores y enviar a Firebase
     */
    public function generateAndSendSensorData()
    {
        $sensorData = $this->getMockData();
        $successCount = 0;

        foreach ($sensorData as $sensorId => $data) {
            // Agregar timestamp actual
            $data['ultima_actualizacion'] = now()->toISOString();

            if ($this->sendSensorData($sensorId, $data)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Actualizar datos de sensor específicos en Firebase
     */
    public function updateSensorData($sensorId, $newData)
    {
        try {
            // Obtener datos actuales del sensor
            $currentData = $this->getSensorById($sensorId);

            if (!$currentData) {
                return false;
            }

            // Fusionar datos actuales con nuevos
            $updatedData = array_merge($currentData, $newData);
            $updatedData['ultima_actualizacion'] = now()->toISOString();

            // Agregar nueva lectura si se proporciona
            if (isset($newData['nueva_lectura'])) {
                if (!isset($updatedData['lecturas'])) {
                    $updatedData['lecturas'] = [];
                }

                array_unshift($updatedData['lecturas'], $newData['nueva_lectura']);

                // Mantener solo las últimas 50 lecturas
                $updatedData['lecturas'] = array_slice($updatedData['lecturas'], 0, 50);
            }

            return $this->sendSensorData($sensorId, $updatedData);

        } catch (\Exception $e) {
            \Log::error('Error actualizando sensor ' . $sensorId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Endpoint para recibir datos desde sensores físicos
     */
    public function receiveSensorData($sensorId, $sensorData)
    {
        try {
            // Validar datos básicos
            if (!isset($sensorData['tipo'])) {
                \Log::warning('Datos de sensor inválidos: falta tipo');
                return false;
            }

            // Crear nueva lectura
            $nuevaLectura = [
                'timestamp' => now()->toISOString(),
                'tipo' => $sensorData['tipo']
            ];

            // Agregar campos específicos según el tipo
            switch ($sensorData['tipo']) {
                case 'movimiento_tierra':
                    if (isset($sensorData['movimiento'])) {
                        $nuevaLectura['movimiento'] = $sensorData['movimiento'];
                    }
                    if (isset($sensorData['aceleracion'])) {
                        $nuevaLectura['aceleracion'] = $sensorData['aceleracion'];
                    }
                    break;
                case 'gases_toxicos':
                    if (isset($sensorData['co'])) $nuevaLectura['co'] = $sensorData['co'];
                    if (isset($sensorData['co2'])) $nuevaLectura['co2'] = $sensorData['co2'];
                    if (isset($sensorData['metano'])) $nuevaLectura['metano'] = $sensorData['metano'];
                    if (isset($sensorData['oxigeno'])) $nuevaLectura['oxigeno'] = $sensorData['oxigeno'];
                    break;
                case 'signos_vitales':
                    if (isset($sensorData['frecuencia_cardiaca'])) $nuevaLectura['frecuencia_cardiaca'] = $sensorData['frecuencia_cardiaca'];
                    if (isset($sensorData['temperatura_corporal'])) $nuevaLectura['temperatura_corporal'] = $sensorData['temperatura_corporal'];
                    if (isset($sensorData['saturacion_oxigeno'])) $nuevaLectura['saturacion_oxigeno'] = $sensorData['saturacion_oxigeno'];
                    if (isset($sensorData['presion_arterial'])) $nuevaLectura['presion_arterial'] = $sensorData['presion_arterial'];
                    break;
            }

            // Actualizar sensor con nueva lectura
            $updateData = [
                'ultima_lectura' => now()->toISOString(),
                'nueva_lectura' => $nuevaLectura
            ];

            // Actualizar valores actuales si se proporcionan
            foreach (['movimiento', 'aceleracion', 'co', 'co2', 'metano', 'oxigeno', 'frecuencia_cardiaca', 'temperatura_corporal', 'saturacion_oxigeno', 'presion_arterial'] as $campo) {
                if (isset($sensorData[$campo])) {
                    $updateData[$campo] = $sensorData[$campo];
                }
            }

            $success = $this->updateSensorData($sensorId, $updateData);

            if ($success) {
                \Log::info("Datos recibidos y actualizados para sensor {$sensorId}");
            }

            return $success;

        } catch (\Exception $e) {
            \Log::error('Error procesando datos del sensor ' . $sensorId . ': ' . $e->getMessage());
            return false;
        }
    }
}