@extends('layouts.app')
@section('title', 'Reporte de Sensores')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-secondary d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="fas fa-sensor"></i> Reporte de Sensores IoT
                    </h4>
                    <p class="card-category mb-0">Estado y datos de dispositivos de monitoreo</p>
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
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['total'] }}</h4>
                                <small>Total Sensores</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['activos'] }}</h4>
                                <small>Activos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4>{{ $stats['alertas'] }}</h4>
                                <small>Alertas Activas</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Sensores -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Sensor</th>
                                <th>Tipo</th>
                                <th>Área</th>
                                <th>Estado</th>
                                <th>Última Lectura</th>
                                <th>Alertas</th>
                                <th>Datos Actuales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sensores as $sensor)
                            <tr>
                                <td>{{ $sensor['id'] }}</td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $sensor['tipo'])) }}</span>
                                </td>
                                <td>{{ $sensor['area'] }}</td>
                                <td>
                                    <span class="badge badge-{{ $sensor['activo'] ? 'success' : 'secondary' }}">
                                        {{ $sensor['activo'] ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    @if($sensor['ultima_lectura'])
                                        {{ \Carbon\Carbon::parse($sensor['ultima_lectura'])->format('d/m/Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $sensor['alertas_count'] > 0 ? 'warning' : 'success' }}">
                                        {{ $sensor['alertas_count'] }}
                                    </span>
                                </td>
                                <td>
                                    @if(count($sensor['datos']) > 0)
                                        <small>
                                            @foreach($sensor['datos'] as $key => $value)
                                                {{ ucfirst($key) }}: {{ $value }}<br>
                                            @endforeach
                                        </small>
                                    @else
                                        <small class="text-muted">Sin datos</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No hay sensores configurados</td>
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
