@extends('layouts.app')
@section('title', 'Estadísticas del Sistema')
@section('content')

<style>
.chart-card { min-height: 400px; }
.chart-card .card-body { position: relative; padding: 15px; }
.chart-container { position: relative; height: 300px; width: 100%; }
.chart-fallback { display: none; text-align: center; padding: 40px 20px; color: #999; }
.stat-value { font-size: 2rem; font-weight: 700; line-height: 1.2; }
.stat-label { font-size: 0.85rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.05em; }
.mini-card { padding: 1.25rem; border-radius: 12px; transition: transform 0.2s; }
.mini-card:hover { transform: translateY(-3px); }
.mini-card .icon-circle { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
</style>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title mb-0"><i class="material-icons" style="vertical-align: middle;">bar_chart</i> Estadísticas del Sistema</h4>
                    <p class="card-category mb-0">Datos agregados y tendencias — {{ now()->format('d/m/Y H:i') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('estadisticas.pdf') }}" class="btn btn-outline-light btn-sm">
                        <i class="material-icons" style="vertical-align: middle; font-size: 1rem;">picture_as_pdf</i> PDF
                    </a>
                    <button class="btn btn-outline-light btn-sm" onclick="location.reload()">
                        <i class="material-icons" style="vertical-align: middle; font-size: 1rem;">refresh</i> Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtro por fechas -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Desde</label>
                        <input type="date" name="desde" class="form-control" value="{{ request('desde', today()->subDays(30)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="hasta" class="form-control" value="{{ request('hasta', today()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary form-control">
                            <i class="material-icons" style="vertical-align: middle; font-size: 1rem;">filter_list</i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('estadisticas.index') }}" class="btn btn-secondary form-control">
                            <i class="material-icons" style="vertical-align: middle; font-size: 1rem;">clear</i> Limpiar
                        </a>
                    </div>
                    <div class="col-md-2 text-end">
                        <small class="text-muted">
                            Período: {{ request('desde', today()->subDays(30)->format('d/m/Y')) }} — {{ request('hasta', today()->format('d/m/Y')) }}
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="mini-card" style="background: linear-gradient(135deg, #50c878, #2e8b57); color: #fff;">
            <div class="d-flex align-items-center">
                <div class="icon-circle me-3" style="background: rgba(255,255,255,0.2);">
                    <i class="material-icons">people</i>
                </div>
                <div>
                    <div class="stat-value">{{ $totalTrabajadores }}</div>
                    <div class="stat-label">Trabajadores Activos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="mini-card" style="background: linear-gradient(135deg, #2196f3, #0d47a1); color: #fff;">
            <div class="d-flex align-items-center">
                <div class="icon-circle me-3" style="background: rgba(255,255,255,0.2);">
                    <i class="material-icons">login</i>
                </div>
                <div>
                    <div class="stat-value">{{ $ingresosHoy }}</div>
                    <div class="stat-label">Ingresos Hoy</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="mini-card" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: #fff;">
            <div class="d-flex align-items-center">
                <div class="icon-circle me-3" style="background: rgba(255,255,255,0.2);">
                    <i class="material-icons">logout</i>
                </div>
                <div>
                    <div class="stat-value">{{ $salidasHoy }}</div>
                    <div class="stat-label">Salidas Hoy</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="mini-card" style="background: linear-gradient(135deg, #e91e63, #880e4f); color: #fff;">
            <div class="d-flex align-items-center">
                <div class="icon-circle me-3" style="background: rgba(255,255,255,0.2);">
                    <i class="material-icons">warning</i>
                </div>
                <div>
                    <div class="stat-value">{{ $incidentesAbiertos }}</div>
                    <div class="stat-label">Incidentes Abiertos</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de datos del período -->
@if(isset($dataTable) && count($dataTable) > 0)
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="material-icons" style="vertical-align: middle;">table_chart</i> Todos los registros del período</h5>
                <div class="d-flex gap-2">
                    <input type="text" id="searchTable" class="form-control form-control-sm" placeholder="Buscar en tabla..." style="width:250px;">
                    <small class="text-muted align-self-center">{{ count($dataTable) }} registros</small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                    <table class="table table-sm table-hover table-striped" id="dataTable">
                        <thead class="table-dark" style="position:sticky; top:0;">
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Usuario / Origen</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataTable as $row)
                            <tr>
                                <td>{{ $row['fecha'] ?? '-' }}</td>
                                <td><span class="badge badge-{{ $row['tipo_badge'] ?? 'secondary' }}">{{ $row['tipo'] ?? '-' }}</span></td>
                                <td>{{ $row['descripcion'] ?? '-' }}</td>
                                <td>{{ $row['origen'] ?? '-' }}</td>
                                <td>{{ $row['estado'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<?php
// Pre-process data for charts
$turnosLabels = $turnos->keys()->map(function($v) { return ucfirst($v); })->values();
$turnosData = $turnos->values();
$areasLabels = $trabajadoresPorArea->pluck('label');
$areasData = $trabajadoresPorArea->pluck('value');
$horasLabels = $horasPico->keys()->map(function($v) { return sprintf('%02d:00', $v); })->values();
$horasData = $horasPico->values();
$gravedadLabels = $incidentesPorGravedad->keys()->map(function($v) { return ucfirst($v); })->values();
$gravedadData = $incidentesPorGravedad->values();
?>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">trending_up</i> Ingresos/Salidas — Últimos 7 Días</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chart7d" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallback7d">Gráfico no disponible</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">pie_chart</i> Trabajadores por Turno</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartTurnos" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackTurnos">Gráfico no disponible</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">assessment</i> Trabajadores por Área</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartAreas" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackAreas">Gráfico no disponible</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">access_time</i> Ingresos Hoy por Hora</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartHoras" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackHoras">Gráfico no disponible</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">error</i> Incidentes por Gravedad</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartGravedad" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackGravedad">Gráfico no disponible</div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card chart-card">
            <div class="card-header">
                <h4 class="card-title"><i class="material-icons" style="vertical-align: middle;">date_range</i> Incidentes — Últimos 6 Meses</h4>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="chartIncMes" style="height:300px;"></canvas></div>
                <div class="chart-fallback" id="fallbackIncMes">Gráfico no disponible</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function loadChartJS(callback) {
        if (typeof Chart !== 'undefined') { callback(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        s.onload = callback;
        s.onerror = function() {
            document.querySelectorAll('.chart-container').forEach(function(el) { el.style.display = 'none'; });
            document.querySelectorAll('.chart-fallback').forEach(function(el) { el.style.display = 'block'; });
        };
        document.head.appendChild(s);
    }

    loadChartJS(function() {
        var isDark = document.body.classList.contains('dark-edition');
        var textColor = isDark ? '#e0e6f0' : '#333';
        var gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';

        var chartDefaults = {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor, font: { size: 12 } } } }
        };
        var scales = {
            x: { ticks: { color: textColor }, grid: { color: gridColor } },
            y: { ticks: { color: textColor }, grid: { color: gridColor } }
        };

        try {
            new Chart(document.getElementById('chart7d'), {
                type: 'bar',
                data: {
                    labels: @json($labels7d),
                    datasets: [
                        { label: 'Ingresos', data: @json($ingresos7d), backgroundColor: '#50c878', borderRadius: 6 },
                        { label: 'Salidas', data: @json($salidas7d), backgroundColor: '#ff9800', borderRadius: 6 }
                    ]
                },
                options: Object.assign({}, chartDefaults, { scales: scales, plugins: { legend: { labels: { color: textColor, font: { size: 12 } }, position: 'top' } } })
            });
        } catch(e) { document.getElementById('fallback7d').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartTurnos'), {
                type: 'doughnut',
                data: {
                    labels: @json($turnosLabels),
                    datasets: [{ data: @json($turnosData), backgroundColor: ['#50c878', '#2196f3', '#9c27b0'], borderWidth: 0 }]
                },
                options: Object.assign({}, chartDefaults, { plugins: { legend: { labels: { color: textColor, font: { size: 12 } }, position: 'bottom' } } })
            });
        } catch(e) { document.getElementById('fallbackTurnos').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartAreas'), {
                type: 'bar',
                data: {
                    labels: @json($areasLabels),
                    datasets: [{ label: 'Trabajadores', data: @json($areasData), backgroundColor: '#00a5cf', borderRadius: 6 }]
                },
                options: Object.assign({}, chartDefaults, { scales: scales, indexAxis: 'y', plugins: { legend: { display: false } } })
            });
        } catch(e) { document.getElementById('fallbackAreas').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartHoras'), {
                type: 'line',
                data: {
                    labels: @json($horasLabels),
                    datasets: [{ label: 'Ingresos', data: @json($horasData), borderColor: '#50c878', backgroundColor: 'rgba(80,200,120,0.15)', fill: true, tension: 0.4, pointRadius: 4 }]
                },
                options: Object.assign({}, chartDefaults, { scales: scales, plugins: { legend: { display: false } } })
            });
        } catch(e) { document.getElementById('fallbackHoras').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartGravedad'), {
                type: 'doughnut',
                data: {
                    labels: @json($gravedadLabels),
                    datasets: [{ data: @json($gravedadData), backgroundColor: ['#ff9800', '#e91e63', '#9c27b0', '#f44336'], borderWidth: 0 }]
                },
                options: Object.assign({}, chartDefaults, { plugins: { legend: { labels: { color: textColor, font: { size: 12 } }, position: 'bottom' } } })
            });
        } catch(e) { document.getElementById('fallbackGravedad').style.display = 'block'; }

        try {
            new Chart(document.getElementById('chartIncMes'), {
                type: 'line',
                data: {
                    labels: @json($labels6m),
                    datasets: [{ label: 'Incidentes', data: @json($incidentes6m), borderColor: '#e91e63', backgroundColor: 'rgba(233,30,99,0.12)', fill: true, tension: 0.4, pointRadius: 4 }]
                },
                options: Object.assign({}, chartDefaults, { scales: scales, plugins: { legend: { display: false } } })
            });
        } catch(e) { document.getElementById('fallbackIncMes').style.display = 'block'; }
    });
});

// Tabla de datos: búsqueda en tabla
var searchInput = document.getElementById('searchTable');
if (searchInput) {
    searchInput.addEventListener('keyup', function() {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#dataTable tbody tr').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
</script>
@endpush
@endsection