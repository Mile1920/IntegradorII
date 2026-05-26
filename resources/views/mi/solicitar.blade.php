@extends('layouts.app')
@section('title','Solicitar Herramienta')

@section('content')
<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">Solicitar Herramienta</h4>
        <p class="card-category">Envía una solicitud de herramienta al equipo de mantenimiento</p>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('mi.solicitar.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Herramienta</label>
                <input name="herramienta" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" class="form-control" min="1" value="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Área (opcional)</label>
                <select name="area_id" class="form-control">
                    <option value="">-- Ninguna --</option>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Observación</label>
                <textarea name="observacion" class="form-control"></textarea>
            </div>

            <button class="btn btn-primary">Enviar Solicitud</button>
        </form>
    </div>
</div>
@endsection
