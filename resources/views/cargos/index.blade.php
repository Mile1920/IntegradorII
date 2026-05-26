@extends('layouts.app')
@section('title', 'Gestión de Cargos')

@section('content')
<div class="card">
    <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
        <div>
            <h4 class="card-title mb-0">Cargos Registrados</h4>
            <p class="card-category mb-0">Todos los cargos (activos e inactivos)</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="text-right mb-4">
                <a href="{{ route('cargos.create') }}" class="btn btn-primary btn-round" 
                   data-toggle="tooltip" data-placement="top" title="Crear un nuevo cargo en el sistema">
                    <img src="{{ asset('img/Logo.png') }}" alt="Nuevo" class="icon-img"> Nuevo Cargo
                </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="text-primary">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cargos as $cargo)
                    <tr class="{{ !$cargo->activo ? 'table-secondary' : '' }}">
                        <td>{{ $cargo->id }}</td>
                        <td><strong>{{ $cargo->nombre }}</strong></td>
                        <td>
                            <span class="badge badge-info">{{ ucfirst($cargo->tipo) }}</span>
                        </td>
                        <td>{{ Str::limit($cargo->descripcion, 50) }}</td>
                        <td>
                            <span class="badge badge-{{ $cargo->activo ? 'success' : 'secondary' }} badge-pill">
                                {{ $cargo->activo ? 'ACTIVO' : 'INACTIVO' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('cargos.edit', $cargo) }}" class="btn btn-warning btn-sm" 
                               data-toggle="tooltip" data-placement="top" title="Editar" @if(!$cargo->activo) disabled @endif>
                                <img src="{{ asset('img/Logo.png') }}" alt="Editar" class="icon-img">
                            </a>
                            <form action="{{ route('cargos.destroy', $cargo) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-{{ $cargo->activo ? 'danger' : 'success' }} btn-sm" 
                                    data-toggle="tooltip" data-placement="top" title="{{ $cargo->activo ? 'Desactivar cargo' : 'Reactivar cargo' }}"
                                    onclick="event.preventDefault(); systemConfirm('{{ $cargo->activo ? '¿Desactivar este cargo?' : '¿Deseas activar nuevamente este cargo?' }}').then(confirmed => { if(confirmed) this.closest('form').submit(); }); return false;">
                                    <img src="{{ asset('img/SeguridadOperativa.png') }}" alt="estado" class="icon-img">
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No hay cargos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-wrap d-flex justify-content-center">
                {{ $cargos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection