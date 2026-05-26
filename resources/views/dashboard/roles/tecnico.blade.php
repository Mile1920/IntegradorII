<!-- Dashboard para Técnico -->
<div class="row">
    <div class="col-md-6">
        <div class="card card-stats">
            <div class="card-body">
                <p class="card-category">Incidentes abiertos</p>
                <h3 class="card-title">{{ $incidentesAbiertos ?? 0 }}</h3>
            </div>
            <div class="card-footer">
                <div class="stats"><a href="{{ route('incidentes.index') }}">Ver incidentes</a></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-stats">
            <div class="card-body">
                <p class="card-category">Lecturas de sensores (24h)</p>
                <h3 class="card-title">{{ $sensorRecientes ?? 0 }}</h3>
            </div>
            <div class="card-footer">
                <div class="stats"><a href="{{ route('sensor-dashboard') }}">Ver sensores</a></div>
            </div>
        </div>
    </div>
</div>

<!-- Sensores por Área -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <img src="{{ asset('img/Monitoreo.png') }}" alt="Sensores" class="icon-img-small me-2" style="width: 24px; height: 24px;">
                    Sensores por Área
                </h4>
                <p class="card-category">Monitoreo de dispositivos IoT organizados por ubicación</p>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $areas = \App\Models\Area::where('activo', true)->get();
                    @endphp
                    @foreach($areas as $area)
                    <div class="col-md-6 mb-3">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    {{ $area->nombre }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $sensoresArea = \App\Models\SensorData::whereHas('sensor', fn($q) => $q->where('area_id', $area->id))
                                        ->selectRaw('tipo, COUNT(*) as total')
                                        ->groupBy('tipo')
                                        ->get();
                                @endphp

                                @if($sensoresArea->isNotEmpty())
                                    <div class="row">
                                        @foreach($sensoresArea as $sensor)
                                        <div class="col-6 mb-2">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">{{ ucfirst(str_replace('_', ' ', $sensor->tipo ?? 'N/A')) }}</small>
                                                <strong class="text-primary">{{ $sensor->total }}</strong>
                                                <small class="d-block">lecturas</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-sensor fa-2x mb-2"></i>
                                        <p>No hay sensores activos</p>
                                    </div>
                                @endif

                                <div class="text-center mt-2">
                                    <a href="{{ route('sensor-dashboard') }}?area={{ $area->id }}" class="btn btn-sm btn-outline-primary">
                                        Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Datos Recientes de Sensores -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <img src="{{ asset('img/Seguridad.png') }}" alt="Datos" class="icon-img-small me-2" style="width: 24px; height: 24px;">
                    Datos Recientes de Sensores
                </h4>
                <p class="card-category">Últimas lecturas registradas en sensor_data</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Tipo Sensor</th>
                                <th>Área</th>
                                <th>Valor</th>
                                <th>Unidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\SensorData::with('sensor.area')->latest('recibido_en')->limit(10)->get() as $dato)
                            @php
                                $tipo = $dato->tipo ?? 'n/a';
                                $valor = is_array($dato->payload) ? ($dato->payload['valor'] ?? $dato->payload['value'] ?? '-') : '-';
                            @endphp
                            <tr>
                                <td>{{ ($dato->recibido_en ?? $dato->created_at)->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $tipo)) }}</span>
                                </td>
                                <td>{{ $dato->sensor->area->nombre ?? '-' }}</td>
                                <td>
                                    @switch($tipo)
                                        @case('temperatura')
                                            {{ $valor }}°C
                                            @break
                                        @case('humedad')
                                            {{ $valor }}%
                                            @break
                                        @case('movimiento')
                                            {{ $valor }} mm
                                            @break
                                        @case('co')
                                            {{ $valor }} ppm
                                            @break
                                        @default
                                            {{ $valor }}
                                    @endswitch
                                </td>
                                <td>
                                    @switch($tipo)
                                        @case('temperatura')
                                            °C
                                            @break
                                        @case('humedad')
                                            %
                                            @break
                                        @case('movimiento')
                                            mm
                                            @break
                                        @case('co')
                                            ppm
                                            @break
                                        @default
                                            -
                                    @endswitch
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay datos de sensores registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('sensor-dashboard') }}" class="btn btn-outline-primary">
                        Ver Dashboard Completo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        @include('dashboard.partials.recent_incidentes')
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Accesos Rápidos</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('incidentes.index') }}" class="btn btn-outline-info">Incidentes</a>
                    <a href="{{ route('sensors.index') }}" class="btn btn-outline-primary">Sensores</a>
                    <a href="{{ route('tool_requests.index') }}" class="btn btn-outline-secondary">Solicitudes de Herramientas</a>
                </div>
            </div>
        </div>
    </div>
</div>