@extends('layouts.app')
@section('title', 'Copias de Seguridad')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="card-title"><i class="fas fa-database"></i> Copias de Seguridad</h4>
                    <p class="card-category">Administración de backups de la base de datos</p>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="crearBackup()" class="btn btn-success btn-sm">
                        <i class="fas fa-plus-circle"></i> Backup Manual
                    </button>
                    <button onclick="actualizarLista()" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sync-alt"></i> Refrescar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2">
                    <i class="fas fa-info-circle"></i>
                    Backup automático cada 48 horas (02:00). Se mantienen solo los 10 backups más recientes.
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="backupsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Archivo</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="backupsBody">
                            <tr><td colspan="4" class="text-center">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function actualizarLista() {
    const tbody = document.getElementById('backupsBody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Cargando...</td></tr>';

    fetch('{{ route("backup.list") }}')
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.backups.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay backups disponibles</td></tr>';
                return;
            }
            tbody.innerHTML = data.backups.map(b => `
                <tr>
                    <td><code>${b.file}</code></td>
                    <td>${b.size_formatted}</td>
                    <td>${b.date}</td>
                    <td>
                        <a href="{{ url('/api/backups/download') }}/${b.file}" class="btn btn-sm btn-primary" title="Descargar">
                            <i class="fas fa-download"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="eliminarBackup('${b.file}')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(() => {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al cargar backups</td></tr>';
        });
}

function crearBackup() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';

    fetch('{{ route("backup.create") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Backup creado exitosamente');
                actualizarLista();
            } else {
                alert('Error: ' + (data.message || 'desconocido'));
            }
        })
        .catch(() => alert('Error de red'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus-circle"></i> Backup Manual';
        });
}

function eliminarBackup(filename) {
    if (!confirm('¿Eliminar backup ' + filename + '?')) return;

    fetch('{{ url("api/backups") }}/' + encodeURIComponent(filename), { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                actualizarLista();
            } else {
                alert('Error: ' + (data.message || 'desconocido'));
            }
        })
        .catch(() => alert('Error de red'));
}

actualizarLista();
</script>
@endpush
@endsection