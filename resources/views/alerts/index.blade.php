@extends('layouts.app')
@section('title', 'Centro de Alertas')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Centro de Alertas
                    </h3>
                    <small class="text-muted">Monitoreo centralizado de todas las alertas del sistema</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                    <span class="badge badge-info">{{ count($alerts) }} alertas activas</span>
                </div>
            </div>
            <div class="card-body">

                @if(count($alerts) > 0)
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Sistema de Alertas Activo:</strong> Se muestran las últimas alertas detectadas por los sensores IoT.
                        Las alertas críticas requieren atención inmediata.
                    </div>

                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Filtrar por Nivel</label>
                                            <select id="nivelFilter" class="form-control">
                                                <option value="">Todas las alertas</option>
                                                <option value="critico">Críticas</option>
                                                <option value="alto">Altas</option>
                                                <option value="medio">Medias</option>
                                                <option value="bajo">Bajas</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Filtrar por Tipo</label>
                                            <select id="tipoFilter" class="form-control">
                                                <option value="">Todos los tipos</option>
                                                <option value="movimiento_tierra">Movimiento Tierra</option>
                                                <option value="gases_toxicos">Gases Tóxicos</option>
                                                <option value="signos_vitales">Signos Vitales</option>
                                                <option value="salida_pendiente">Salida Pendiente</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Filtrar por Área</label>
                                            <select id="areaFilter" class="form-control">
                                                <option value="">Todas las áreas</option>
                                                @php
                                                    $areas = array_unique(array_column($alerts, 'area'));
                                                @endphp
                                                @foreach($areas as $area)
                                                    <option value="{{ $area }}">{{ $area }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Acciones</label>
                                            <button id="clearFiltersBtn" class="btn btn-secondary form-control">
                                                <i class="fas fa-times"></i> Limpiar Filtros
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Alertas -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="alertsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sensor / Origen</th>
                                    <th>Área</th>
                                    <th>Tipo</th>
                                    <th>Mensaje</th>
                                    <th>Nivel</th>
                                    <th>Fecha/Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alerts as $alert)
                                <tr class="alert-item"
                                    data-nivel="{{ $alert['nivel'] }}"
                                    data-tipo="{{ $alert['tipo'] }}"
                                    data-area="{{ $alert['area'] }}">
                                    <td><strong>{{ $alert['sensor'] }}</strong></td>
                                    <td>{{ $alert['area'] }}</td>
                                    <td>
                                        @switch($alert['tipo'])
                                            @case('movimiento_tierra')
                                                <span class="badge badge-warning"><i class="fas fa-mountain"></i> Mov. Tierra</span>
                                                @break
                                            @case('gases_toxicos')
                                                <span class="badge badge-danger"><i class="fas fa-skull-crossbones"></i> Gases Tóxicos</span>
                                                @break
                                            @case('signos_vitales')
                                                <span class="badge badge-info"><i class="fas fa-heartbeat"></i> Signos Vitales</span>
                                                @break
                                            @case('salida_pendiente')
                                                <span class="badge badge-secondary"><i class="fas fa-clock"></i> Salida Pendiente</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $alert['tipo'] }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $alert['mensaje'] }}</td>
                                    <td>
                                        @switch($alert['nivel'])
                                            @case('critico')
                                                <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> CRÍTICA</span>
                                                @break
                                            @case('alto')
                                                <span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i> ALTA</span>
                                                @break
                                            @case('medio')
                                                <span class="badge badge-info"><i class="fas fa-info-circle"></i> MEDIA</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary"><i class="fas fa-check-circle"></i> BAJA</span>
                                        @endswitch
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($alert['timestamp'])->setTimezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Estadísticas de Alertas -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Resumen de Alertas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="p-3 bg-light rounded">
                                                <h4 class="text-warning mb-1">
                                                    {{ count(array_filter($alerts, function($a) { return $a['nivel'] === 'critico'; })) }}
                                                </h4>
                                                <small class="text-muted">Críticas</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 bg-light rounded">
                                                <h4 class="text-warning mb-1">
                                                    {{ count(array_filter($alerts, function($a) { return $a['nivel'] === 'alto'; })) }}
                                                </h4>
                                                <small class="text-muted">Altas</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 bg-light rounded">
                                                <h4 class="text-info mb-1">
                                                    {{ count(array_filter($alerts, function($a) { return $a['nivel'] === 'medio'; })) }}
                                                </h4>
                                                <small class="text-muted">Medias</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="p-3 bg-light rounded">
                                                <h4 class="text-secondary mb-1">
                                                    {{ count(array_filter($alerts, function($a) { return $a['nivel'] === 'bajo'; })) }}
                                                </h4>
                                                <small class="text-muted">Bajas</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                        <h4 class="text-success mb-3">¡Todo en Orden!</h4>
                        <p class="text-muted mb-4">
                            No hay alertas activas en el sistema. Todos los sensores están funcionando correctamente.
                        </p>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <i class="fas fa-shield-alt"></i>
                                    <strong>Estado Seguro:</strong> El sistema de monitoreo está activo y no detecta condiciones de riesgo.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nivelFilter = document.getElementById('nivelFilter');
    const tipoFilter = document.getElementById('tipoFilter');
    const areaFilter = document.getElementById('areaFilter');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const alertItems = document.querySelectorAll('.alert-item');

    function filterAlerts() {
        const nivelValue = nivelFilter.value;
        const tipoValue = tipoFilter.value;
        const areaValue = areaFilter.value;

        alertItems.forEach(row => {
            const nivel = row.dataset.nivel;
            const tipo = row.dataset.tipo;
            const area = row.dataset.area;

            const matchesNivel = !nivelValue || nivel === nivelValue;
            const matchesTipo = !tipoValue || tipo === tipoValue;
            const matchesArea = !areaValue || area === areaValue;

            row.style.display = (matchesNivel && matchesTipo && matchesArea) ? '' : 'none';
        });
    }

    function clearFilters() {
        nivelFilter.value = '';
        tipoFilter.value = '';
        areaFilter.value = '';
        filterAlerts();
    }

    nivelFilter.addEventListener('change', filterAlerts);
    tipoFilter.addEventListener('change', filterAlerts);
    areaFilter.addEventListener('change', filterAlerts);
    clearFiltersBtn.addEventListener('click', clearFilters);
});
</script>
@endpush
@endsection
