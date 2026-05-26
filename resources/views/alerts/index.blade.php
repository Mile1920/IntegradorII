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

                    <!-- Lista de Alertas -->
                    <div class="row">
                        <div class="col-md-12">
                            @foreach($alerts as $alert)
                            <div class="alert alert-item mb-3 border-left border-{{ $alert['nivel'] === 'critico' ? 'warning' : ($alert['nivel'] === 'alto' ? 'warning' : ($alert['nivel'] === 'medio' ? 'info' : 'secondary')) }} border-left-4"
                                 data-nivel="{{ $alert['nivel'] }}"
                                 data-tipo="{{ $alert['tipo'] }}"
                                 data-area="{{ $alert['area'] }}">
                                <div class="d-flex align-items-start">
                                    <div class="me-3 mt-1">
                                        @switch($alert['tipo'])
                                            @case('movimiento_tierra')
                                                <i class="fas fa-mountain fa-2x text-warning"></i>
                                                @break
                                            @case('gases_toxicos')
                                                <i class="fas fa-skull-crossbones fa-2x text-warning"></i>
                                                @break
                                            @case('signos_vitales')
                                                <i class="fas fa-heartbeat fa-2x text-warning"></i>
                                                @break
                                            @default
                                                <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                                        @endswitch
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <strong>{{ $alert['sensor'] }}</strong> -
                                                    <span class="text-primary">{{ $alert['area'] }}</span>
                                                </h6>
                                                <p class="mb-1 text-muted">{{ $alert['mensaje'] }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($alert['timestamp'])->format('d/m/Y H:i:s') }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge badge-{{ $alert['nivel'] === 'critico' ? 'warning' : ($alert['nivel'] === 'alto' ? 'warning' : ($alert['nivel'] === 'medio' ? 'info' : 'secondary')) }} mb-2">
                                                    @switch($alert['nivel'])
                                                        @case('critico')
                                                            <i class="fas fa-exclamation-triangle"></i> CRÍTICA
                                                            @break
                                                        @case('alto')
                                                            <i class="fas fa-exclamation-circle"></i> ALTA
                                                            @break
                                                        @case('medio')
                                                            <i class="fas fa-info-circle"></i> MEDIA
                                                            @break
                                                        @case('bajo')
                                                            <i class="fas fa-check-circle"></i> BAJA
                                                            @break
                                                    @endswitch
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ ucfirst($alert['tipo']) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
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

        alertItems.forEach(item => {
            const nivel = item.dataset.nivel;
            const tipo = item.dataset.tipo;
            const area = item.dataset.area;

            const matchesNivel = !nivelValue || nivel === nivelValue;
            const matchesTipo = !tipoValue || tipo === tipoValue;
            const matchesArea = !areaValue || area === areaValue;

            if (matchesNivel && matchesTipo && matchesArea) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
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

    // Auto-refresh cada 30 segundos
    setInterval(() => {
        if (!nivelFilter.value && !tipoFilter.value && !areaFilter.value) {
            // Solo refrescar si no hay filtros activos
            // location.reload();
        }
    }, 30000);
});
</script>
@endpush
@endsection
