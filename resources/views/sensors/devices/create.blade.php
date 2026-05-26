@extends('layouts.app')
@section('title','Agregar Sensor')

@section('content')
<div class="card">
    <div class="card-header card-header-primary d-flex justify-content-between align-items-center">
        <div>
            <h4 class="card-title mb-0">Agregar Sensores</h4>
            <small class="text-white-50">Carga rápida en bloque por nivel/área</small>
        </div>
        <a href="{{ route('sensors.devices.index') }}" class="btn btn-outline-light btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('sensors.devices.store') }}" id="bulkSensorForm">
            @csrf
            <div id="sensorRows">
                <div class="sensor-row border rounded p-3 mb-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Código / Device ID *</label>
                            <input name="sensors[0][device_id]" class="form-control" required placeholder="Ej: GAS-A1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nombre</label>
                            <input name="sensors[0][nombre]" class="form-control" placeholder="Sensor galería">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Área/Nivel</label>
                            <select name="sensors[0][area_id]" class="form-control">
                                <option value="">Sin asignar</option>
                                @foreach($areas as $a)
                                    <option value="{{ $a->id }}">{{ $a->nombre }} @if($a->nivel) ({{ $a->nivel }}) @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Separación (m)</label>
                            <input name="sensors[0][separacion_m]" type="number" step="0.01" min="0" class="form-control" placeholder="5">
                        </div>
                        <div class="col-md-1 text-right">
                            <button type="button" class="btn btn-outline-danger btn-sm d-none remove-row">×</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" class="btn btn-outline-primary btn-sm" id="addSensorRow">
                    <i class="fas fa-plus"></i> Otro sensor
                </button>
                <small class="text-muted">Puedes repetir el nivel y cambiar solo el código y la separación.</small>
            </div>

            <button class="btn btn-success">Guardar sensores</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('sensorRows');
    const addBtn = document.getElementById('addSensorRow');

    addBtn.addEventListener('click', () => {
        const index = container.children.length;
        const template = container.firstElementChild.cloneNode(true);
        template.querySelectorAll('input, select').forEach(el => {
            el.value = '';
            const name = el.getAttribute('name');
            el.setAttribute('name', name.replace(/\d+/, index));
        });
        template.querySelector('.remove-row').classList.remove('d-none');
        container.appendChild(template);
    });

    container.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.sensor-row').remove();
        }
    });
});
</script>
@endpush
@endsection
