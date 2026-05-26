@extends('layouts.app')
@section('title', 'Gestión de Áreas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Gestión de Áreas y Niveles</h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="p-3 text-end">
                    @hasrole('administrador-principal')
                    <a href="{{ route('admin.areas.create') }}" class="btn bg-gradient-success">
                        <img src="{{ asset('img/Logo.png') }}" alt="Nueva Área" class="icon-img"> Nueva Área
                    </a>
                    @endhasrole
                </div>

                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nivel</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Estado</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areas as $area)
                            <tr>
                                <td class="ps-4">
                                    <h6 class="mb-0 text-sm">{{ $area->nombre }}</h6>
                                </td>
                                <td>
                                    <p class="text-xs text-secondary mb-0">{{ $area->nivel ?? '-' }}</p>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-sm {{ $area->activo ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                        {{ $area->activo ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-warning btn-sm">
                                        <img src="{{ asset('img/Logo.png') }}" alt="Editar" class="icon-img">
                                    </a>

                                    @hasrole('administrador-principal')
                                    <form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                data-toggle="tooltip" data-placement="top" title="Desactivar área"
                                                onclick="event.preventDefault(); systemConfirm('¿Desactivar esta área?').then(confirmed => { if(confirmed) this.closest('form').submit(); }); return false;">
                                            <img src="{{ asset('img/Logo.png') }}" alt="Eliminar" class="icon-img">
                                        </button>
                                    </form>
                                    @endhasrole
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $areas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection