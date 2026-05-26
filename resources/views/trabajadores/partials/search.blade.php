<form method="GET" action="{{ route('trabajadores.index') }}" class="form-inline" role="search" aria-label="Buscar trabajadores">
    <div class="input-group w-100">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
        <input list="trabajador-names" type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por nombre, apellido o CI" aria-label="Buscar por nombre, apellidos o CI" autocomplete="off">
        <datalist id="trabajador-names">
            @isset($suggestions)
                @foreach($suggestions as $s)
                    <option value="{{ $s }}">
                @endforeach
            @endisset
        </datalist>
        <select name="cargo_id" class="form-control ml-2" title="Filtrar por cargo">
            <option value="">-- Todos los Cargos --</option>
            @foreach($cargos as $cargo)
                <option value="{{ $cargo->id }}" @if(request('cargo_id') == $cargo->id) selected @endif>{{ $cargo->nombre }}</option>
            @endforeach
        </select>
        <select name="area_id" class="form-control ml-2" title="Filtrar por área">
            <option value="">-- Todas las Áreas --</option>
            @foreach($areas as $area)
                <option value="{{ $area->id }}" @if(request('area_id') == $area->id) selected @endif>{{ $area->nombre }}</option>
            @endforeach
        </select>
        <div class="input-group-append ml-2">
            <button class="btn btn-primary" type="submit" data-toggle="tooltip" data-placement="top" title="Buscar trabajadores">
                <i class="fas fa-search"></i> Buscar
            </button>
            <a href="{{ route('trabajadores.index') }}" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Limpiar filtros y mostrar todos">
                <i class="fas fa-times"></i> Limpiar
            </a>
        </div>
    </div>
</form>
