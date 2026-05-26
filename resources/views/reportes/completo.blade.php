@extends('layouts.app')
@section('title', 'Reporte Completo del Sistema')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> Reporte Completo del Sistema
                    </h4>
                    <p class="card-category mb-0">Vista general completa de Mina Porco</p>
                </div>
                <div>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">

                <!-- Filtros de Fecha -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Fecha Inicio</label>
                                        <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Fecha Fin</label>
                                        <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary d-block">
                                            <i class="fas fa-search"></i> Filtrar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Generales -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3"><i class="fas fa-chart-bar"></i> Estadísticas Generales</h5>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3>{{ $stats['trabajadores']['total'] }}</h3>
                                <small>Trabajadores Activos</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3>{{ $stats['ingresos']['ingresos'] }}</h3>
                                <small>Ingresos en Período</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3>{{ $stats['incidentes']['total'] }}</h3>
                                <small>Incidentes Reportados</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3>{{ $stats['sensores']['activos'] }}</h3>
                                <small>Sensores Activos</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actividad Reciente -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-clock"></i> Últimos Ingresos</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Trabajador</th>
                                                <th>Tipo</th>
                                                <th>Fecha/Hora</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($actividad['ingresos_recientes'] as $ingreso)
                                            <tr>
                                                <td>{{ $ingreso->trabajador->nombre_completo ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $ingreso->tipo === 'ingreso' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($ingreso->tipo) }}
                                                    </span>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($ingreso->registrado_en)->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No hay registros recientes</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Incidentes Recientes</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Reportado por</th>
                                                <th>Gravedad</th>
                                                <th>Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($actividad['incidentes_recientes'] as $incidente)
                                            <tr>
                                                <td>{{ $incidente->trabajador->nombre_completo ?? 'Sistema' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $incidente->gravedad === 'critica' ? 'danger' : ($incidente->gravedad === 'alta' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($incidente->gravedad) }}
                                                    </span>
                                                </td>
                                                <td>{{ $incidente->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No hay incidentes recientes</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen Ejecutivo -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-clipboard-check"></i> Resumen Ejecutivo</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Período Analizado</h6>
                                        <p class="mb-2"><strong>Desde:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</p>
                                        <p class="mb-3"><strong>Hasta:</strong> {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>

                                        <h6>Trabajadores</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Total Activos:</strong> {{ $stats['trabajadores']['total'] }}</li>
                                            <li><strong>Nuevos en el período:</strong> {{ $stats['trabajadores']['nuevos_mes'] }}</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Seguridad y Monitoreo</h6>
                                        <ul class="list-unstyled">
                                            <li><strong>Incidentes Totales:</strong> {{ $stats['incidentes']['total'] }}</li>
                                            <li><strong>Incidentes Críticos:</strong> {{ $stats['incidentes']['criticos'] }}</li>
                                            <li><strong>Incidentes Pendientes:</strong> {{ $stats['incidentes']['pendientes'] }}</li>
                                            <li><strong>Sensores Alertas:</strong> {{ $stats['sensores']['alertas'] }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
