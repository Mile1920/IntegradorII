@extends('layouts.app')
@section('title','Reportar Incidente')

@section('content')
<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">Reportar Incidente</h4>
        <p class="card-category">Formulario para reportar una condición observada</p>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('mi.reportar') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Área (opcional)</label>
                <select name="area_id" class="form-control">
                    <option value="">-- Seleccione --</option>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Gravedad</label>
                <select name="gravedad" class="form-control">
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="critica">Crítica</option>
                </select>
            </div>

            <button class="btn btn-warning">Enviar Reporte</button>
        </form>
    </div>
</div>
@endsection
