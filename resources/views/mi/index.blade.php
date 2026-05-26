@extends('layouts.app')
@section('title','Mis Acciones')

@section('content')
<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">Mis Acciones</h4>
        <p class="card-category">Accesos rápidos para trabajadores</p>
    </div>
    <div class="card-body">
        <div class="row gx-3 gy-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Registro</h5>
                        <p class="text-muted">Registrar su ingreso o salida desde formularios para completar información adicional.</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('mi.ingreso.form') }}" class="btn btn-success">Formulario Ingreso</a>
                            <a href="{{ route('mi.salida.form') }}" class="btn btn-warning">Formulario Salida</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Reportes y Solicitudes</h5>
                        <p class="text-muted">Reportar incidentes o solicitar herramientas, y ver sus reportes personales.</p>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('mi.reportar.form') }}" class="btn btn-warning">Reportar Incidente</a>
                            <a href="{{ route('mi.solicitar') }}" class="btn btn-primary">Solicitar Herramienta</a>
                            <a href="{{ route('mi.reportes') }}" class="btn btn-outline-primary">Mis Reportes</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Perfil</h5>
                        <p class="text-muted">Actualizar su información personal.</p>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">Mi Perfil</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Más</h5>
                        <p class="text-muted">Acceder a módulos y documentación.</p>
                        <a href="{{ route('business.flow') }}" class="btn btn-info">Ver Módulos</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial Reciente -->
        <hr>
        <h5 class="text-light">Mi Historial Reciente</h5>
        <div class="table-responsive">
            <table class="table table-striped table-sm table-dark-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Área</th>
                        <th>Subnivel</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trabajador->ingresos()->latest()->limit(10)->get() as $ingreso)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($ingreso->registrado_en)->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge {{ $ingreso->tipo === 'ingreso' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($ingreso->tipo) }}
                                </span>
                            </td>
                            <td>{{ $ingreso->area->nombre ?? '-' }}</td>
                            <td>{{ $ingreso->area->nivel ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay registros recientes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('mi.reportes') }}" class="btn btn-outline-primary">Ver Reportes Completos</a>
        </div>
    </div>
</div>
@endsection
