<div class="sidebar" data-color="purple" data-background-color="black">
    <div class="logo">
        <a href="{{ route('dashboard') }}" class="simple-text logo-normal">
            Mina Porco
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <img src="{{ asset('img/Monitoreo.png') }}" alt="Dashboard" class="icon-img">
                    <p>Dashboard</p>
                </a>
            </li>

            <!-- Centro de Alertas -->
            <li class="nav-item {{ request()->routeIs('alerts.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('alerts.index') }}">
                    <img src="{{ asset('img/Seguridad.png') }}" alt="Alertas" class="icon-img">
                    <p>Alertas</p>
                </a>
            </li>

            <!-- Áreas -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('areas.index') }}">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Áreas" class="icon-img">
                        <p>Áreas</p>
                    </a>
                </li>
            @endif

            <!-- Cargos -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('cargos.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('cargos.index') }}">
                        <img src="{{ asset('img/Minero.png') }}" alt="Cargos" class="icon-img">
                        <p>Cargos</p>
                    </a>
                </li>
            @endif

            <!-- Trabajadores -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('trabajadores.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('trabajadores.index') }}">
                        <img src="{{ asset('img/Minero.png') }}" alt="Trabajadores" class="icon-img">
                        <p>Trabajadores</p>
                    </a>
                </li>
            @endif

            <!-- Control Grupal -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('control-grupal.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('control-grupal.index') }}">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Control Grupal" class="icon-img">
                        <p>Control Grupal</p>
                    </a>
                </li>
            @endif

            <!-- Sensores -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area', 'tecnico']))
                <li class="nav-item {{ request()->routeIs('sensor-dashboard', 'sensors.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ auth()->user()->hasRole('tecnico') ? route('sensors.index') : route('sensor-dashboard') }}">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Sensores" class="icon-img">
                        <p>Sensores</p>
                    </a>
                </li>
            @endif

            <!-- Mapa de la Mina (vista 2D) -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area', 'tecnico', 'trabajador']))
                <li class="nav-item {{ request()->routeIs('business.flow.mine2d') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('business.flow.mine2d') }}">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Mapa Mina" class="icon-img">
                        <p>Mapa Mina 2D</p>
                    </a>
                </li>
            @endif

            <!-- Incidentes -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area', 'tecnico']))
                <li class="nav-item {{ request()->routeIs('incidentes.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('incidentes.index') }}">
                        <img src="{{ asset('img/Seguridad.png') }}" alt="Incidentes" class="icon-img">
                        <p>Incidentes</p>
                    </a>
                </li>
            @endif

            <!-- Estadísticas -->
            @if(auth()->user()->hasAnyRole(['administrador-principal', 'administrador-area']))
                <li class="nav-item {{ request()->routeIs('estadisticas.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('estadisticas.index') }}">
                        <img src="{{ asset('img/Logo.png') }}" alt="Estadísticas" class="icon-img">
                        <p>Estadísticas</p>
                    </a>
                </li>
            @endif

            <!-- Reportes -->
            @if(auth()->user()->hasRole('administrador-principal'))
                <li class="nav-item {{ request()->routeIs('reportes.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('reportes.index') }}">
                        <img src="{{ asset('img/Logo.png') }}" alt="Reportes" class="icon-img">
                        <p>Reportes</p>
                    </a>
                </li>
            @endif

            <!-- Estado del Sistema -->
            @if(auth()->user()->hasRole('administrador-principal'))
                <li class="nav-item {{ request()->routeIs('system.status') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('system.status') }}">
                        <img src="{{ asset('img/Monitoreo.png') }}" alt="Sistema" class="icon-img">
                        <p>Estado Sistema</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('backups.index') }}">
                        <img src="{{ asset('img/Logo.png') }}" alt="Backups" class="icon-img">
                        <p>Copias Seguridad</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('auditoria.index') }}">
                        <img src="{{ asset('img/Seguridad.png') }}" alt="Auditoría" class="icon-img">
                        <p>Auditoría y Seg.</p>
                    </a>
                </li>
            @endif

            <!-- Funcionalidades del Trabajador -->
            @if(auth()->user()->hasRole('trabajador'))
                <li class="nav-item {{ request()->routeIs('mi.ingreso.form') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('mi.ingreso.form') }}">
                        <img src="{{ asset('img/Minero.png') }}" alt="Ingreso" class="icon-img">
                        <p>Registrar Ingreso</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('mi.salida.form') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('mi.salida.form') }}">
                        <img src="{{ asset('img/Minero.png') }}" alt="Salida" class="icon-img">
                        <p>Registrar Salida</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('mi.reportar.form') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('mi.reportar.form') }}">
                        <img src="{{ asset('img/Minero.png') }}" alt="Reportar" class="icon-img">
                        <p>Reportar Incidente</p>
                    </a>
                </li>
            @endif


            <li class="nav-item mt-5">
                <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <img src="{{ asset('img/Logo.png') }}" alt="Salir" class="icon-img">
                    <p>Cerrar Sesión</p>
                </a>
            </li>
        </ul>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    @stack('scripts')