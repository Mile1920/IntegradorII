@extends('layouts.app')
@section('title','Incidentes')

@section('content')
<div class="card">
    <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
        <div>
            <h4 class="card-title mb-0">Incidentes</h4>
            <p class="card-category mb-0">Lista de incidentes reportados</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Gravedad</th>
                        <th>Estado</th>
                        <th>Trabajador</th>
                        <th>Área</th>
                        <th>Creado</th>
                        <th>Fecha Reporte</th>
                        <th>Cambiar Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidentes as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td class="inc-desc">{{ Str::limit($i->descripcion,120) }}</td>
                        <td>{{ strtoupper($i->gravedad) }}</td>
                        <td>{{ strtoupper($i->estado) }}</td>
                        <td>{{ $i->trabajador->nombre_completo ?? '-' }}</td>
                        <td>{{ $i->area->nombre ?? '-' }}</td>
                        <td>
                            @if($i->estado === 'completado')
                            <span class="text-success">Completado</span>
                            @elseif($i->estado === 'en_proceso')
                            <span class="text-warning">En Proceso</span>
                            @else
                            <span class="text-danger">Aún no atendido</span>
                            @endif
                        </td>
                        <td>{{ $i->fecha_reporte ? $i->fecha_reporte->format('d/m/Y H:i') : ($i->created_at ? $i->created_at->format('d/m/Y H:i') : '-') }}</td>
                        <td>
                            <form action="{{ route('incidentes.updateEstado', $i) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <select name="estado" class="form-control form-control-sm">
                                        <option value="pendiente" {{ $i->estado === 'pendiente' ? 'selected' : '' }}>Aún no atendido</option>
                                        <option value="en_proceso" {{ $i->estado === 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                                        <option value="completado" {{ $i->estado === 'completado' ? 'selected' : '' }}>Completado</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No hay incidentes</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $incidentes->links() }}
        </div>
    </div>
</div>
@endsection
