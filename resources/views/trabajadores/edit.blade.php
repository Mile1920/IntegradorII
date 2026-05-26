@extends('layouts.app')
@section('title', 'Editar Trabajador')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Editar Trabajador: {{ $trabajador->nombre_completo }}</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>No se pudo guardar.</strong> Revisa los datos requeridos o duplicados (CI, email, celular).
                    </div>
                @endif
                <form action="{{ route('trabajadores.update', $trabajador) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <!-- Datos personales -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nombres *</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $trabajador->nombre) }}" required data-letters-only="true">
                                @error('nombre')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Apellido Paterno *</label>
                                <input type="text" name="ap_paterno" class="form-control" value="{{ old('ap_paterno', $trabajador->ap_paterno) }}" required data-letters-only="true">
                                @error('ap_paterno')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Apellido Materno</label>
                                <input type="text" name="ap_materno" class="form-control" value="{{ old('ap_materno', $trabajador->ap_materno) }}" data-letters-only="true">
                                @error('ap_materno')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CI *</label>
                                <input type="text" name="ci" class="form-control" value="{{ old('ci', $trabajador->ci) }}" required>
                                @error('ci')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $trabajador->email) }}" required>
                                @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Celular</label>
                                <input type="text" name="celular" class="form-control" value="{{ old('celular', $trabajador->celular) }}" inputmode="tel" data-phone="true">
                                @error('celular')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $trabajador->fecha_nacimiento ? $trabajador->fecha_nacimiento->format('Y-m-d') : '') }}">
                                @error('fecha_nacimiento')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>PIN (N° Ficha)</label>
                                <input type="text" name="pin" class="form-control" value="{{ old('pin', $trabajador->pin) }}" placeholder="Número de ficha del trabajador">
                                @error('pin')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group text-center">
                                <label>Foto de Perfil</label>
                                <div class="d-flex flex-column align-items-center">
                                    <div id="photoPreview" style="width:150px;height:150px;border-radius:50%;overflow:hidden;border:3px dashed #ccc;cursor:pointer;position:relative;background:#f0f0f0;margin-bottom:8px;" onclick="document.getElementById('fotoInput').click();">
                                        <img id="photoImg" src="{{ $trabajador->foto_perfil ? asset('storage/'.$trabajador->foto_perfil) : asset('img/default-avatar.svg') }}" alt="Foto" style="width:100%;height:100%;object-fit:cover;">
                                        <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.5);color:#fff;font-size:12px;padding:4px;text-align:center;">Click para cambiar</div>
                                    </div>
                                    <input type="file" id="fotoInput" name="foto_perfil" accept="image/*" style="display:none;" onchange="previewFoto(event)">
                                </div>
                                @error('foto_perfil')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Área *</label>
                                <select name="area_id" class="form-control form-select" required>
                                    @foreach(\App\Models\Area::activo()->get() as $area)
                                        <option value="{{ $area->id }}" {{ $trabajador->area_id == $area->id ? 'selected' : '' }}>
                                            {{ $area->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('area_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cargo *</label>
                                <select name="cargo_id" class="form-control form-select" required>
                                    @foreach(\App\Models\Cargo::activo()->get() as $cargo)
                                        <option value="{{ $cargo->id }}" {{ $trabajador->cargo_id == $cargo->id ? 'selected' : '' }}>
                                            {{ $cargo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cargo_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Turno *</label>
                                <select name="turno" class="form-control form-select" required>
                                    <option value="mañana" {{ old('turno', $trabajador->turno) == 'mañana' ? 'selected' : '' }}>Mañana</option>
                                    <option value="tarde" {{ old('turno', $trabajador->turno) == 'tarde' ? 'selected' : '' }}>Tarde</option>
                                    <option value="noche" {{ old('turno', $trabajador->turno) == 'noche' ? 'selected' : '' }}>Noche</option>
                                </select>
                                @error('turno')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-4">
                        <a href="{{ route('trabajadores.index') }}" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-primary btn-round">
                            Actualizar Trabajador
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
function previewFoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoImg').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection