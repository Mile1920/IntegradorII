@extends('layouts.app')
@section('title','Solicitudes de Herramientas')

@section('content')
<div class="card">
    <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
        <div>
            <h4 class="card-title mb-0">Solicitudes de Herramientas</h4>
            <p class="card-category mb-0">Lista de solicitudes enviadas por trabajadores</p>
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
                    <tr><th>ID</th><th>Trabajador</th><th>Herramienta</th><th>Cantidad</th><th>Área</th><th>Observación</th><th>Estado</th><th>Creado</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($solicitudes as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ $s->trabajador->nombre_completo ?? 'N/A' }}</td>
                        <td>{{ $s->herramienta }}</td>
                        <td>{{ $s->cantidad ?? 1 }}</td>
                        <td>{{ $s->area->nombre ?? '-' }}</td>
                        <td>{{ Str::limit($s->observacion,80) }}</td>
                        <td>{{ ucfirst($s->estado) }}</td>
                        <td>{{ $s->created_at->diffForHumans() }}</td>
                        <td>
                            <form method="POST" action="{{ route('tool_requests.update', $s) }}">
                                @csrf @method('PUT')
                                <select name="estado" class="form-control form-control-sm d-inline-block" style="width:110px;display:inline-block">
                                    <option value="pendiente" @if($s->estado=='pendiente') selected @endif>Pendiente</option>
                                    <option value="aprobado" @if($s->estado=='aprobado') selected @endif>Aprobado</option>
                                    <option value="rechazado" @if($s->estado=='rechazado') selected @endif>Rechazado</option>
                                </select>
                                <button class="btn btn-sm btn-primary">Actualizar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $solicitudes->links() }}
        </div>
    </div>
</div>
@endsection
