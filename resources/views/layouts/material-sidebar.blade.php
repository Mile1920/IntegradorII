<div class="sidebar" data-color="purple" data-background-color="black">
    <div class="logo">
        <a href="{{ route('dashboard') }}" class="simple-text logo-normal">
            Mina Porco
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a class="nav-link sidebar-link" href="{{ route('dashboard') }}">
                    <span>Dashboard</span>
                    <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img">
                </a>
            </li>

            <!-- Centro de Alertas -->
            <li class="nav-item {{ request()->routeIs('alerts.index') ? 'active' : '' }}">
                <a class="nav-link sidebar-link" href="{{ route('alerts.index') }}">
                    <span>Alertas</span>
                    <img src="{{ asset('img/Seguridad.png') }}" alt="" class="icon-img">
                </a>
            </li>

            <!-- Áreas -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('areas.index') }}">
                        <span>Áreas</span>
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Cargos -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('cargos.*') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('cargos.index') }}">
                        <span>Cargos</span>
                        <img src="{{ asset('img/Minero.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Trabajadores -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('trabajadores.*') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('trabajadores.index') }}">
                        <span>Trabajadores</span>
                        <img src="{{ asset('img/Minero.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Control Grupal -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('control-grupal.*') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('control-grupal.index') }}">
                        <span>Control Grupal</span>
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Sensores -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area', 'tecnico']))
                <li class="nav-item {{ request()->routeIs('sensor-dashboard', 'sensors.*') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ auth()->user()->hasRole('tecnico') ? route('sensors.index') : route('sensor-dashboard') }}">
                        <span>Sensores</span>
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Mapa de la Mina (vista 2D) -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area', 'tecnico', 'trabajador']))
                <li class="nav-item {{ request()->routeIs('business.flow.mine2d') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('business.flow.mine2d') }}">
                        <span>Mapa Mina 2D</span>
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Incidentes -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area', 'tecnico']))
                <li class="nav-item {{ request()->routeIs('incidentes.*') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('incidentes.index') }}">
                        <span>Incidentes</span>
                        <img src="{{ asset('img/Seguridad.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Estadísticas -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('estadisticas.index') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('estadisticas.index') }}">
                        <span>Estadísticas</span>
                        <img src="{{ asset('img/Logo.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Reportes -->
            @if(auth()->user()->hasRole('administrador-principal'))
                <li class="nav-item {{ request()->routeIs('reportes.index') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('reportes.index') }}">
                        <span>Reportes</span>
                        <img src="{{ asset('img/Logo.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Estado del Sistema -->
            @if(auth()->user()->hasRole('administrador-principal'))
                <li class="nav-item {{ request()->routeIs('system.status') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('system.status') }}">
                        <span>Estado Sistema</span>
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="" class="icon-img">
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('backups.index') }}">
                        <span>Copias Seguridad</span>
                        <img src="{{ asset('img/Logo.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            <!-- Auditoría y Seguridad -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area', 'tecnico']))
                <li class="nav-item {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('auditoria.index') }}">
                        <span>Auditoría y Seg.</span>
                        <img src="{{ asset('img/Seguridad.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif

            @if(auth()->user()->hasRole('trabajador'))
                <li class="nav-item {{ request()->routeIs('mi.ingreso.form') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('mi.ingreso.form') }}">
                        <span>Registrar Ingreso</span>
                        <img src="{{ asset('img/Minero.png') }}" alt="" class="icon-img">
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('mi.salida.form') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('mi.salida.form') }}">
                        <span>Registrar Salida</span>
                        <img src="{{ asset('img/Minero.png') }}" alt="" class="icon-img">
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('mi.reportar.form') ? 'active' : '' }}">
                    <a class="nav-link sidebar-link" href="{{ route('mi.reportar.form') }}">
                        <span>Reportar Incidente</span>
                        <img src="{{ asset('img/Minero.png') }}" alt="" class="icon-img">
                    </a>
                </li>
            @endif


            <li class="nav-item mt-5">
                <a class="nav-link sidebar-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span>Cerrar Sesión</span>
                    <img src="{{ asset('img/Logo.png') }}" alt="" class="icon-img">
                </a>
            </li>
        </ul>
    </div>
</div>