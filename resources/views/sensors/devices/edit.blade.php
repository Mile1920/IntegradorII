@extends('layouts.app')
@section('title','Editar Sensor')

@section('content')
<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">Editar Sensor</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('sensors.devices.update', $sensor) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Device ID</label>
                <input name="device_id" class="form-control" value="{{ old('device_id', $sensor->device_id) }}" required>
                @error('device_id')<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre (opcional)</label>
                <input name="nombre" class="form-control" value="{{ old('nombre', $sensor->nombre) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Separación (m)</label>
                <input name="separacion_m" type="number" step="0.01" min="0" class="form-control" value="{{ old('separacion_m', $sensor->separacion_m) }}" placeholder="Distancia respecto a otros sensores">
                <small class="text-muted">Úsalo para marcar la distancia o separación física dentro del mismo nivel.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Área (opcional)</label>
                <select name="area_id" class="form-control">
                    <option value="">-- Ninguna --</option>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}" @if(old('area_id', $sensor->area_id)==$a->id) selected @endif>{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Activo</label>
                <select name="activo" class="form-control">
                    <option value="1" @if($sensor->activo) selected @endif>Sí</option>
                    <option value="0" @if(!$sensor->activo) selected @endif>No</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="activo" @if($sensor->estado === 'activo') selected @endif>Activo</option>
                    <option value="inactivo" @if($sensor->estado === 'inactivo') selected @endif>Inactivo</option>
                    <option value="alerta" @if($sensor->estado === 'alerta') selected @endif>Con Alerta</option>
                </select>
            </div>

            <button class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</div>
@endsection
