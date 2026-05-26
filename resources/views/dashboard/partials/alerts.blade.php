<!-- Sistema de Alertas de Sensores -->
@php
    $firebaseService = app(\App\Services\FirebaseService::class);
    $sensorData = $firebaseService->getSensorData();
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

            // Verificar valores críticos automáticamente
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

    // Limitar a las últimas 5 alertas
    $alerts = array_slice(array_reverse($alerts), 0, 5);
@endphp

@if(count($alerts) > 0)
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card border-warning">
            <div class="card-header card-header-warning d-flex align-items-center">
                <div class="icon-big text-center me-3">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h4 class="card-title mb-0 text-warning">🚨 ALERTAS DE SEGURIDAD</h4>
                    <p class="card-category mb-0">Atención requerida inmediata</p>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-warning" role="alert">
                    <strong>¡Atención!</strong> Se han detectado las siguientes condiciones de riesgo:
                </div>

                @foreach($alerts as $alert)
                <div class="alert alert-{{ $alert['nivel'] === 'critico' ? 'danger' : ($alert['nivel'] === 'alto' ? 'warning' : 'info') }} d-flex align-items-center mb-2">
                    <div class="me-3">
                        @switch($alert['tipo'])
                            @case('movimiento_tierra')
                                <i class="fas fa-mountain text-warning" style="font-size: 1.5rem;"></i>
                                @break
                            @case('gases_toxicos')
                                <i class="fas fa-skull-crossbones text-danger" style="font-size: 1.5rem;"></i>
                                @break
                            @case('signos_vitales')
                                <i class="fas fa-heartbeat text-danger" style="font-size: 1.5rem;"></i>
                                @break
                            @default
                                <i class="fas fa-exclamation-circle text-warning" style="font-size: 1.5rem;"></i>
                        @endswitch
                    </div>
                    <div class="flex-grow-1">
                        <strong>{{ $alert['sensor'] }}</strong> - {{ $alert['area'] }}<br>
                        <small>{{ $alert['mensaje'] }}</small><br>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($alert['timestamp'])->format('d/m/Y H:i:s') }}</small>
                    </div>
                    <div>
                        <span class="badge badge-{{ $alert['nivel'] === 'critico' ? 'danger' : ($alert['nivel'] === 'alto' ? 'warning' : 'info') }}">
                            {{ ucfirst($alert['nivel']) }}
                        </span>
                    </div>
                </div>
                @endforeach

                <div class="text-center mt-3">
                    <a href="{{ route('sensor-dashboard') }}" class="btn btn-danger">
                        <i class="fas fa-eye"></i> Ver Dashboard de Sensores
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif