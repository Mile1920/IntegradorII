@extends('layouts.app')
@section('title', 'Reporte de Trabajadores')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-success d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="fas fa-users"></i> Reporte de Trabajadores
                    </h4>
                    <p class="card-category mb-0">Listado completo del personal</p>
                </div>
                <div>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['total'] }}</h4>
                                <small>Total Registrados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['activos'] }}</h4>
                                <small>Activos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['inactivos'] }}</h4>
                                <small>Inactivos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['con_usuario'] }}</h4>
                                <small>Con Acceso</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Trabajadores -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Cargo</th>
                                <th>Área</th>
                                <th>Estado</th>
                                <th>Acceso Sistema</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trabajadores as $trabajador)
                            <tr>
                                <td>{{ $trabajador->nombre_completo }}</td>
                                <td>{{ $trabajador->cargo->nombre ?? '-' }}</td>
                                <td>{{ $trabajador->area->nombre ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $trabajador->activo ? 'success' : 'secondary' }}">
                                        {{ $trabajador->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    @if($trabajador->user)
                                        <span class="badge badge-success">Sí</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>{{ $trabajador->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay trabajadores registrados</td>
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
