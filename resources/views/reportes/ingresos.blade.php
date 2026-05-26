@extends('layouts.app')
@section('title', 'Reporte de Ingresos')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-info d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clock"></i> Reporte de Ingresos y Salidas
                    </h4>
                    <p class="card-category mb-0">Control de acceso del personal</p>
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
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['total_registros'] }}</h4>
                                <small>Total Registros</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['ingresos'] }}</h4>
                                <small>Ingresos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['salidas'] }}</h4>
                                <small>Salidas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['trabajadores_unicos'] }}</h4>
                                <small>Trabajadores Únicos</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Ingresos -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Trabajador</th>
                                <th>Área</th>
                                <th>Tipo</th>
                                <th>Fecha/Hora</th>
                                <th>Subnivel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ingresos as $ingreso)
                            <tr>
                                <td>{{ $ingreso->trabajador->nombre_completo ?? 'N/A' }}</td>
                                <td>{{ $ingreso->area->nombre ?? $ingreso->area_id }}</td>
                                <td>
                                    <span class="badge badge-{{ $ingreso->tipo === 'ingreso' ? 'success' : 'warning' }}">
                                        {{ ucfirst($ingreso->tipo) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($ingreso->registrado_en)->format('d/m/Y H:i') }}</td>
                                <td>{{ $ingreso->area->nivel ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay registros de ingresos en el período seleccionado</td>
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
