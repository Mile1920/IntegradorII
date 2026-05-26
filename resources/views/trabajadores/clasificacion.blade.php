@extends('layouts.app')
@section('title', 'Clasificación de Trabajadores')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Clasificación de Trabajadores</h4>
                <p class="card-category">Filtrar trabajadores por turno y nivel</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('trabajadores.clasificacion') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="turno">Turno</label>
                                <select name="turno" id="turno" class="form-control">
                                    <option value="">Todos los turnos</option>
                                    <option value="mañana" {{ request('turno') == 'mañana' ? 'selected' : '' }}>Mañana</option>
                                    <option value="tarde" {{ request('turno') == 'tarde' ? 'selected' : '' }}>Tarde</option>
                                    <option value="noche" {{ request('turno') == 'noche' ? 'selected' : '' }}>Noche</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cargo_id">Cargo</label>
                                <select name="cargo_id" id="cargo_id" class="form-control">
                                    <option value="">Todos los cargos</option>
                                    @foreach($cargos as $cargo)
                                        <option value="{{ $cargo->id }}" {{ request('cargo_id') == $cargo->id ? 'selected' : '' }}>{{ $cargo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="area_id">Área</label>
                                <select name="area_id" id="area_id" class="form-control">
                                    <option value="">Todas las áreas</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="observacion">Observación</label>
                                <select name="observacion" id="observacion" class="form-control">
                                    <option value="">Todas las observaciones</option>
                                    @foreach($observaciones as $obs)
                                        <option value="{{ $obs }}" {{ request('observacion') == $obs ? 'selected' : '' }}>{{ $obs }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="{{ route('trabajadores.clasificacion') }}" class="btn btn-secondary">Limpiar</a>
                        </div>
                    </div>
                </form>

                <hr>

                @if($trabajadores->isEmpty())
                    <div class="alert alert-warning" role="alert">
                        No se encontraron trabajadores con los filtros seleccionados.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="text-primary">
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>CI</th>
                                <th>Cargo</th>
                                <th>Área</th>
                                <th>Turno</th>
                                <th>Activo</th>
                            </thead>
                            <tbody>
                                @foreach ($trabajadores as $trabajador)
                                    <tr>
                                        <td>{{ $trabajador->id }}</td>
                                        <td>{{ $trabajador->nombre_completo }}</td>
                                        <td>{{ $trabajador->ci }}</td>
                                        <td>{{ $trabajador->cargo->nombre ?? 'N/A' }}</td>
                                        <td>{{ $trabajador->area->nombre ?? 'N/A' }}</td>
                                        <td><span class="badge badge-info">{{ ucfirst($trabajador->turno) }}</span></td>
                                        <td>
                                            @if($trabajador->activo)
                                                <span class="badge badge-success">Sí</span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
