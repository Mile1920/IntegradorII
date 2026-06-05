@extends('layouts.app')
@section('title', 'Módulos - Flujo de Negocio')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
            <div>
                <h4 class="card-title mb-0">Módulos del Sistema</h4>
                <p class="card-category mb-0">Navega por los módulos para ejecutar las acciones del flujo de negocio</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">Trabajadores</h5>
                                <p class="card-text small mb-2">Registro y administración del personal</p>
                                <a href="{{ route('trabajadores.index') }}" class="btn btn-primary btn-sm">Ir</a>
                            </div>
                            <img src="{{ asset('img/Minero.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;opacity:0.7;">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">Áreas</h5>
                                <p class="card-text small mb-2">Gestión de áreas y niveles</p>
                                <a href="{{ route('areas.index') }}" class="btn btn-primary btn-sm">Ir</a>
                            </div>
                            <img src="{{ asset('img/Monitoreo.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;opacity:0.7;">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">Cargos</h5>
                                <p class="card-text small mb-2">Administrar cargos y permisos</p>
                                <a href="{{ route('cargos.index') }}" class="btn btn-primary btn-sm">Ir</a>
                            </div>
                            <img src="{{ asset('img/Minero.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;opacity:0.7;">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">Incidentes</h5>
                                <p class="card-text small mb-2">Validar y cerrar incidentes</p>
                                <a href="{{ route('incidentes.index') }}" class="btn btn-primary btn-sm">Ir</a>
                            </div>
                            <img src="{{ asset('img/Seguridad.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;opacity:0.7;">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">Sensores</h5>
                                <p class="card-text small mb-2">Monitoreo de sensores y dispositivos</p>
                                <div>
                                    <a href="{{ route('sensors.index') }}" class="btn btn-primary btn-sm">Ver datos</a>
                                    <a href="{{ route('sensors.devices.index') }}" class="btn btn-outline-primary btn-sm">Dispositivos</a>
                                </div>
                            </div>
                            <img src="{{ asset('img/Monitoreo.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;opacity:0.7;">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">Mina 2D</h5>
                                <p class="card-text small mb-2">Plano de niveles y sensores</p>
                                <a href="{{ route('business.flow.mine2d') }}" class="btn btn-primary btn-sm">Ver mapa</a>
                            </div>
                            <img src="{{ asset('img/Monitoreo.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;opacity:0.7;">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">Solicitudes</h5>
                                <p class="card-text small mb-2">Solicitudes de herramientas</p>
                                <a href="{{ route('mi.solicitar') }}" class="btn btn-primary btn-sm">Solicitar</a>
                                <a href="{{ route('tool_requests.index') }}" class="btn btn-outline-primary btn-sm">Ver</a>
                            </div>
                            <img src="{{ asset('img/Minero.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;opacity:0.7;">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">Estadísticas</h5>
                                <p class="card-text small mb-2">Reportes gráficos y exportables</p>
                                <a href="{{ route('estadisticas.index') }}" class="btn btn-primary btn-sm">Ver</a>
                            </div>
                            <img src="{{ asset('img/Logo.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;opacity:0.7;">
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">Control Grupal</h5>
                                <p class="card-text small mb-2">Ingreso/salida grupal por área</p>
                                <a href="{{ route('control-grupal') }}" class="btn btn-primary btn-sm">Ir</a>
                            </div>
                            <img src="{{ asset('img/Monitoreo.png') }}" alt="" style="width:40px;height:40px;object-fit:contain;opacity:0.7;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
