<!-- Dashboard para Trabajador -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Mi Panel de Control</h4>
        <p class="card-category">Accede a tus funciones diarias</p>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="d-grid gap-2">
                    <a href="{{ route('mi.ingreso.form') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Registrar Ingreso
                    </a>
                    <a href="{{ route('mi.salida.form') }}" class="btn btn-warning btn-lg">
                        <i class="fas fa-sign-out-alt"></i> Registrar Salida
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-grid gap-2">
                    <a href="{{ route('mi.reportar.form') }}" class="btn btn-warning btn-lg">
                        <i class="fas fa-exclamation-triangle"></i> Reportar Condición
                    </a>
                    <a href="{{ route('mi.solicitar') }}" class="btn btn-info btn-lg">
                        <i class="fas fa-tools"></i> Solicitar Herramientas
                    </a>
                </div>
            </div>
        </div>
        <hr>
        <div class="mt-4">
            <h5 class="text-light">Mi Historial Reciente</h5>
            <div class="table-responsive">
                <table class="table table-striped table-dark-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Área</th>
                            <th>Subnivel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse((auth()->user()->trabajador ? auth()->user()->trabajador->ingresos()->latest()->limit(5)->get() : collect()) as $ingreso)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($ingreso->registrado_en)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $ingreso->tipo === 'ingreso' ? 'badge-success' : 'badge-warning' }}">
                                        {{ ucfirst($ingreso->tipo) }}
                                    </span>
                                </td>
                                <td>{{ $ingreso->area->nombre ?? '-' }}</td>
                                <td>{{ $ingreso->area->nivel ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay registros recientes</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('mi.index') }}" class="btn btn-primary">Ver Historial Completo</a>
            </div>
        </div>
    </div>
</div>