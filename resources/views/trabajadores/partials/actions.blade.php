<div class="col-md-3">
    <div class="card card-profile">
        <div class="card-header card-header-success">
            <h4 class="card-title">Acciones Rápidas</h4>
            <p class="card-category">Acciones para el trabajador seleccionado</p>
        </div>
        <div class="card-body">
            <p class="mb-3">Selecciona un trabajador y usa las acciones rápidas:</p>
            <div class="d-grid gap-2">
                <!-- Acciones administrativas: el trabajador tiene sus propias rutas (mi/*) -->
                @hasrole('administrador-principal')
                    <a href="{{ route('trabajadores.create') }}" class="btn btn-success btn-block">Nuevo Trabajador</a>
                @endhasrole

                @hasanyrole('administrador-principal|administrador-area')
                    <a href="{{ route('trabajadores.index') }}?export=pdf" class="btn btn-outline-secondary btn-block">Exportar PDF</a>
                @endhasanyrole
            </div>
        </div>
    </div>
</div>
