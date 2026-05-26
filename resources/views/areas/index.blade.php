@extends('layouts.app')
@section('title', 'Gestión de Áreas')

@section('content')
<div class="card">
    <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
        <div>
            <h4 class="card-title mb-0">Áreas Registradas</h4>
            <p class="card-category mb-0">Administración de áreas de la mina</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="text-right mb-4">
            <a href="{{ route('areas.create') }}" class="btn btn-success btn-round" 
               data-toggle="tooltip" data-placement="top" title="Crear una nueva área en el sistema">
                <img src="{{ asset('img/Logo.png') }}" alt="Nueva Área" class="icon-img"> Nueva Área
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="text-primary">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Nivel</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($areas as $area)
                    <tr>
                        <td>{{ $area->id }}</td>
                        <td><strong>{{ $area->nombre }}</strong></td>
                        <td>{{ $area->nivel ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $area->activo ? 'success' : 'danger' }}">
                                {{ $area->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('areas.edit', $area) }}" class="btn btn-warning btn-sm" 
                               data-toggle="tooltip" data-placement="top" title="Editar" @if(!$area->activo) disabled @endif>
                                <img src="{{ asset('img/Logo.png') }}" alt="Editar" class="icon-img">
                            </a>
                            <form action="{{ route('areas.destroy', $area) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-{{ $area->activo ? 'danger' : 'success' }} btn-sm" 
                                    data-toggle="tooltip" data-placement="top" title="{{ $area->activo ? 'Desactivar área' : 'Reactivar área' }}"
                                    onclick="event.preventDefault(); systemConfirm('{{ $area->activo ? '¿Desactivar esta área?' : '¿Deseas activar nuevamente esta área?' }}').then(confirmed => { if(confirmed) this.closest('form').submit(); }); return false;">
                                    <img src="{{ asset('img/SeguridadOperativa.png') }}" alt="estado" class="icon-img">
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay áreas registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $areas->links() }}
        </div>
    </div>
</div>
@endsection