@extends('layouts.app')
@section('title', 'Cambiar Contraseña')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Cambiar Contraseña</h4>
                <p class="card-category">Actualice su contraseña de acceso al sistema</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.change-password.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="bmd-label-floating">Contraseña Actual *</label>
                                <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                                @error('current_password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="bmd-label-floating">Nueva Contraseña *</label>
                                <input type="password" name="password" class="form-control" required autocomplete="new-password">
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="bmd-label-floating">Confirmar Nueva Contraseña *</label>
                                <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                                @error('password_confirmation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>Requisitos de contraseña:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Mínimo 8 caracteres</li>
                                    <li>Al menos una letra mayúscula</li>
                                    <li>Al menos una letra minúscula</li>
                                    <li>Al menos un número</li>
                                    <li>Al menos un símbolo especial (ej: !@#$%^&*)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('profile.edit') }}" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header card-header-info">
                <h4 class="card-title">
                    <i class="fas fa-shield-alt"></i> Seguridad
                </h4>
            </div>
            <div class="card-body">
                <h6><i class="fas fa-check text-success"></i> Último cambio:</h6>
                <p>{{ Auth::user()->updated_at->diffForHumans() }}</p>

                <h6><i class="fas fa-info-circle text-info"></i> Consejos de seguridad:</h6>
                <ul class="small">
                    <li>Cambie su contraseña periódicamente</li>
                    <li>Use contraseñas únicas para cada cuenta</li>
                    <li>No comparta sus credenciales</li>
                    <li>Use un gestor de contraseñas</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection






