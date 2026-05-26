@extends('layouts.app')
@section('title', 'Mi Perfil')

@section('content')
<div class="container mt-4">
    <h2>Mi Perfil</h2>

    @if(session('status') === 'profile-updated')
        <div class="alert alert-success">Guardado.</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Información Personal</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nombre:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Rol:</strong> {{ ucfirst($user->getRoleNames()->first()) }}</p>
                </div>
                @if($user->trabajador)
                <div class="col-md-6">
                    <p><strong>CI:</strong> {{ $user->trabajador->ci }}</p>
                    <p><strong>Área:</strong> {{ $user->trabajador->area->nombre ?? 'N/A' }}</p>
                    <p><strong>Cargo:</strong> {{ $user->trabajador->cargo->nombre ?? 'N/A' }}</p>
                    <p><strong>Fecha Nacimiento:</strong> {{ $user->trabajador->fecha_nacimiento ? \Carbon\Carbon::parse($user->trabajador->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Actualizar Información</h5>
            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus>
                    @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                @if($user->trabajador)
                <div class="mb-3">
                    <label for="celular" class="form-label">Celular</label>
                    <input id="celular" name="celular" type="text" class="form-control" value="{{ old('celular', $user->trabajador->celular) }}">
                    @error('celular')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                @endif

                <button class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Cambiar contraseña</h5>
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('patch')

                <div class="mb-3">
                    <label for="current_password" class="form-label">Contraseña actual</label>
                    <input id="current_password" name="current_password" type="password" class="form-control">
                    @error('current_password')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Nueva contraseña</label>
                    <input id="password" name="password" type="password" class="form-control">
                    @error('password')<div class="text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control">
                </div>

                <button class="btn btn-primary">Actualizar contraseña</button>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Eliminar cuenta</h5>
            <form method="post" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                @csrf
                @method('delete')

                <div class="mb-3">
                    <label for="password_delete" class="form-label">Contraseña</label>
                    <input id="password_delete" name="password" type="password" class="form-control" required>
                </div>

                <button type="button" class="btn btn-danger" onclick="confirmDeleteAccount()">Eliminar mi cuenta</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDeleteAccount() {
    systemConfirm('¿Está seguro de que desea eliminar su cuenta? Esta acción no se puede deshacer y se perderán todos sus datos.').then(confirmed => {
        if (confirmed) {
            document.getElementById('deleteAccountForm').submit();
        }
    });
}
</script>
@endpush
@endsection
