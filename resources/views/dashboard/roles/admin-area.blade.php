<!-- Dashboard para Administrador de Área -->
@include('dashboard.partials.stats')

<div class="row mt-4">
    <div class="col-lg-12">
        @include('dashboard.partials.recent_incidentes')
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Funciones Administrativas</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('trabajadores.index') }}" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-users"></i><br>
                            <small>Ver Trabajadores</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('incidentes.index') }}" class="btn btn-danger btn-lg w-100 mb-3">
                            <i class="fas fa-exclamation-triangle"></i><br>
                            <small>Gestionar Incidentes</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('sensor-dashboard') }}" class="btn btn-info btn-lg w-100 mb-3">
                            <i class="fas fa-chart-line"></i><br>
                            <small>Monitoreo de Sensores</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('estadisticas.index') }}" class="btn btn-success btn-lg w-100 mb-3">
                            <i class="fas fa-chart-bar"></i><br>
                            <small>Estadísticas</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('trabajadorSelect').addEventListener('change', function() {
    const form = document.getElementById('ingresoForm');
    const trabajadorId = this.value;
    if (trabajadorId) {
        form.action = form.action.replace('__PLACEHOLDER__', trabajadorId);
    }
});
</script>