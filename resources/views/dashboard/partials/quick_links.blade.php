<div class="card">
    <div class="card-header">
        <h4 class="card-title">Accesos Rápidos</h4>
    </div>
    <div class="card-body">
        <div class="d-grid gap-2">
            @if(auth()->user()->hasRole('trabajador'))
                <a href="{{ route('mi.index') }}" class="btn btn-primary">Mi Panel</a>
            @endif

            @hasrole('administrador-principal')
                <a href="{{ route('trabajadores.create') }}" class="btn btn-success">Nuevo Trabajador</a>
            @endhasrole

            @hasanyrole('administrador-principal|administrador-area')
                <a href="{{ route('trabajadores.index') }}?export=pdf" class="btn btn-outline-secondary">Exportar PDF</a>
                <a href="{{ route('incidentes.index') }}" class="btn btn-outline-danger">Incidentes</a>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-primary">Reportes de todo</a>
            @endhasanyrole

            @hasanyrole('administrador-principal|administrador-area')
                <a href="{{ route('business.flow') }}" class="btn btn-info">Flujo de Negocio</a>
            @endhasanyrole
        </div>
    </div>
</div>
