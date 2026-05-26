@extends('layouts.app')
@section('title','Sensores - Dispositivos')

@section('content')
<div class="card">
    <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
        <div>
            <h4 class="card-title mb-0">Sensores (Dispositivos)</h4>
            <p class="card-category mb-0">Gestionar dispositivos físicos y asignarlos a áreas</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="text-right mb-3">
            <a href="{{ route('sensors.devices.create') }}" class="btn btn-success">Agregar Sensor</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>ID</th><th>Device ID</th><th>Nombre</th><th>Área/Nivel</th><th>Separación</th><th>Activo</th><th>Creado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($sensores as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ $s->device_id }}</td>
                        <td>{{ $s->nombre ?? '-' }}</td>
                        <td>
                            {{ $s->area->nombre ?? '-' }}
                            @if($s->area && $s->area->nivel)
                                <small class="text-muted d-block">{{ $s->area->nivel }}</small>
                            @endif
                        </td>
                        <td>{{ $s->separacion_m ? $s->separacion_m . ' m' : '-' }}</td>
                        <td>{{ $s->activo ? 'Sí' : 'No' }}</td>
                        <td>{{ $s->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('sensors.devices.edit', $s) }}" class="btn btn-sm btn-warning" title="Editar">Editar</a>
                            <form action="{{ route('sensors.devices.destroy', $s) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" 
                                    data-toggle="tooltip" data-placement="top" title="Eliminar"
                                    onclick="event.preventDefault(); systemConfirm('¿Eliminar sensor?').then(confirmed => { if(confirmed) this.closest('form').submit(); }); return false;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $sensores->links() }}
        </div>
    </div>
</div>
@endsection
