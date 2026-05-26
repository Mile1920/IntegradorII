@extends('layouts.app')
@section('title', 'Editar Área')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-warning shadow-warning border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Editar Área</h6>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.areas.update', $area) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Nombre del Área *</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $area->nombre }}" required>
                    </div>
                    <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Nivel</label>
                        <input type="text" name="nivel" class="form-control" value="{{ $area->nivel }}">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn bg-gradient-warning btn-lg">Actualizar Área</button>
                        <a href="{{ route('admin.areas.index') }}" class="btn bg-gradient-secondary btn-lg">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection