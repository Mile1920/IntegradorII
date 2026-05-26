<div class="collapse navbar-collapse">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" data-toggle="dropdown">
                <img src="{{ asset('img/Minero.png') }}" alt="Mi Perfil" class="icon-img">
                <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header">
                    {{ Auth::user()->name }}<br>
                    <small class="text-muted">{{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}</small>
                </div>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <img src="{{ asset('img/Minero.png') }}" alt="Mi Perfil" class="icon-img"> Mi Perfil
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <img src="{{ asset('img/Logo.png') }}" alt="Cerrar Sesión" class="icon-img"> Cerrar Sesión
                </a>
            </div>
        </li>
    </ul>
</div>