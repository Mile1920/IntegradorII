@extends('layouts.app')
@section('title', 'Registrar Trabajador')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Nuevo Trabajador</h4>
                <p class="card-category">Se creará usuario automático y se enviarán credenciales al correo del trabajador</p>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>No se pudo guardar.</strong> Revisa los datos requeridos o duplicados (CI, email, celular).
                    </div>
                @endif
                <form action="{{ route('trabajadores.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nombres *</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required data-letters-only="true">
                                @error('nombre')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Apellido Paterno *</label>
                                <input type="text" name="ap_paterno" class="form-control" value="{{ old('ap_paterno') }}" required data-letters-only="true">
                                @error('ap_paterno')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Apellido Materno</label>
                                <input type="text" name="ap_materno" class="form-control" value="{{ old('ap_materno') }}" data-letters-only="true">
                                @error('ap_materno')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>CI *</label>
                                <input type="text" name="ci" class="form-control" value="{{ old('ci') }}" required>
                                @error('ci')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Celular</label>
                                <input type="text" name="celular" class="form-control" value="{{ old('celular') }}" inputmode="tel" data-phone="true">
                                @error('celular')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
                                @error('fecha_nacimiento')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>PIN (N° Ficha)</label>
                                <input type="text" name="pin" class="form-control" value="{{ old('pin') }}" placeholder="Número de ficha del trabajador">
                                @error('pin')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Foto de Perfil</label>
                                <input type="file" name="foto_perfil" class="form-control" accept="image/*">
                                @error('foto_perfil')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Área *</label>
                                <select name="area_id" class="form-control form-select" required>
                                    <option value="">Seleccionar</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('area_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cargo *</label>
                                <select name="cargo_id" class="form-control form-select" required>
                                    <option value="">Seleccionar</option>
                                    @foreach($cargos as $cargo)
                                        <option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('cargo_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rol del Usuario *</label>
                                <select name="rol" class="form-control form-select" required>
                                    <option value="trabajador">Trabajador</option>
                                    <option value="tecnico">Técnico</option>
                                    <option value="administrador-area">Administrador Área</option>
                                    <option value="administrador-principal">Administrador Principal</option>
                                </select>
                                @error('rol')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Turno *</label>
                                <select name="turno" class="form-control form-select" required>
                                    <option value="mañana">Mañana</option>
                                    <option value="tarde">Tarde</option>
                                    <option value="noche">Noche</option>
                                </select>
                                @error('turno')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <a href="{{ route('trabajadores.index') }}" class="btn btn-default btn-round">Cancelar</a>
                        <button type="submit" class="btn btn-primary btn-round">
                            Crear Trabajador y Enviar Credenciales
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection