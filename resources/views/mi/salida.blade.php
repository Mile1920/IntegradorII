@extends('layouts.app')
@section('title','Registrar Salida')

@section('content')

<div class="card">
    <div class="card-header card-header-secondary">
        <h4 class="card-title">Registrar Salida</h4>
        <p class="card-category">Formulario para registrar su salida</p>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('mi.salida') }}">
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
                <label class="form-label">Observación (opcional)</label>
                <input name="observacion" class="form-control" placeholder="Nota adicional sobre la salida" />
            </div>

            <button class="btn btn-secondary">Registrar Salida</button>
        </form>
    </div>
</div>
@endsection
