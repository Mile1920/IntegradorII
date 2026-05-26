@extends('layouts.app')
@section('title','Registrar Ingreso')

@section('content')

<div class="card">
    <div class="card-header card-header-info">
        <h4 class="card-title">Registrar Ingreso</h4>
        <p class="card-category">Formulario para registrar su ingreso</p>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('mi.ingreso') }}">
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
                <input name="observacion" class="form-control" placeholder="Nota adicional sobre el ingreso" />
            </div>

            <button class="btn btn-primary">Registrar Ingreso</button>
        </form>
    </div>
</div>
@endsection
