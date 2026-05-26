@extends('layouts.app')
@section('title', 'Sensores - Datos')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2>Datos de Sensores</h2>
            <p class="text-muted mb-0">Últimos 50 registros</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>

    @if($datos->isEmpty())
        <div class="alert alert-info">No hay datos recientes de sensores.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Device</th>
                        <th>Tipo</th>
                        <th>Payload</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datos as $d)
                    <tr>
                        <td>{{ $d->id }}</td>
                        <td>{{ $d->device_id }}</td>
                        <td>{{ $d->tipo }}</td>
                        <td><pre class="sensor-payload">{{ json_encode($d->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></td>
                        <td>{{ $d->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <a href="{{ route('business.flow') }}" class="btn btn-secondary">Volver a Módulos</a>
</div>
@endsection
