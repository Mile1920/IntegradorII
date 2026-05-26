@extends('layouts.app')
@section('title', 'Reporte de Incidentes')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-warning d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Reporte de Incidentes
                    </h4>
                    <p class="card-category mb-0">Incidentes reportados y estado de resolución</p>
                </div>
                <div>
                    <a href="{{ route('reportes.incidentes.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">

                <!-- Filtros -->
                <form method="GET" action="{{ route('reportes.incidentes') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select name="estado" id="estado" class="form-control">
                                <option value="">Todos</option>
                                <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="en_proceso" {{ request('estado') === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="completado" {{ request('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="gravedad" class="form-label">Gravedad</label>
                            <select name="gravedad" id="gravedad" class="form-control">
                                <option value="">Todas</option>
                                <option value="critica" {{ request('gravedad') === 'critica' ? 'selected' : '' }}>Crítica</option>
                                <option value="alta" {{ request('gravedad') === 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="media" {{ request('gravedad') === 'media' ? 'selected' : '' }}>Media</option>
                                <option value="baja" {{ request('gravedad') === 'baja' ? 'selected' : '' }}>Baja</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('reportes.incidentes') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['total'] }}</h4>
                                <small>Total Incidentes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['completados'] }}</h4>
                                <small>Completados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['pendientes'] }}</h4>
                                <small>Pendientes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['en_proceso'] }}</h4>
                                <small>En Proceso</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Incidentes -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Reportado por</th>
                                <th>Descripción</th>
                                <th>Gravedad</th>
                                <th>Estado</th>
                                <th>Fecha Reporte</th>
                                <th>Fecha Cierre</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($incidentes as $incidente)
                            <tr>
                                <td>{{ $incidente->trabajador->nombre_completo ?? 'Sistema' }}</td>
                                <td>{{ Str::limit($incidente->descripcion, 50) }}</td>
                                <td>
                                    <span class="badge badge-{{ $incidente->gravedad === 'critica' ? 'danger' : ($incidente->gravedad === 'alta' ? 'warning' : ($incidente->gravedad === 'media' ? 'info' : 'secondary')) }}">
                                        {{ ucfirst($incidente->gravedad) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $incidente->estado === 'completado' ? 'success' : ($incidente->estado === 'en_proceso' ? 'warning' : 'danger') }}">
                                        {{ $incidente->estado === 'pendiente' ? 'Aún no atendido' : ($incidente->estado === 'en_proceso' ? 'En proceso' : 'Completado') }}
                                    </span>
                                </td>
                                <td>{{ $incidente->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $incidente->updated_at != $incidente->created_at ? $incidente->updated_at->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay incidentes registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
