@extends('layouts.app')
@section('title', 'Módulos - Flujo de Negocio')

@section('content')
@if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
<div class="container">
    <h2 class="mb-4">Módulos del Sistema</h2>
    <p class="lead">Navega por los módulos para ejecutar las acciones del flujo de negocio.</p>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Trabajadores</h5>
                    <p class="card-text">Registro y administración del personal. Los trabajadores pueden registrar ingreso/salida desde su perfil.</p>
                    <a href="{{ route('trabajadores.index') }}" class="btn btn-primary">Ir a Trabajadores</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Áreas</h5>
                    <p class="card-text">Gestión de áreas y subniveles.</p>
                    <a href="{{ route('areas.index') }}" class="btn btn-primary">Ir a Áreas</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Cargos</h5>
                    <p class="card-text">Administrar cargos y permisos asociados.</p>
                    <a href="{{ route('cargos.index') }}" class="btn btn-primary">Ir a Cargos</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Incidentes</h5>
                    <p class="card-text">Validar y cerrar incidentes reportados por trabajadores o detectados por sensores.</p>
                    <a href="{{ route('incidentes.index') }}" class="btn btn-primary">Ir a Incidentes</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sensores</h5>
                    <p class="card-text">Endpoint para recibir datos de sensores y generar alertas automáticas.</p>
                    <a href="{{ route('sensors.index') }}" class="btn btn-primary">Ir a Sensores</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al Dashboard</a>
    </div>
</div>
@else
<div class="container">
    <div class="alert alert-warning">
        <h4><i class="fas fa-exclamation-triangle"></i> Acceso Restringido</h4>
        <p>No tienes permisos para acceder a esta sección. Esta página está disponible solo para administradores.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Volver al Dashboard</a>
    </div>
</div>
@endif
@endsection
