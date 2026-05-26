@extends('layouts.app')
@section('title', 'Mapa 2D Mina')
@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Mapa 2D — Mina Subterránea</h2>
            <p class="text-muted mb-0">Niveles, galerías y sensores posicionados.</p>
        </div>
        <a href="{{ route('business.flow') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="mine-legend d-flex flex-wrap gap-3 mb-3">
                <span class="badge badge-primary">Nivel</span>
                <span class="badge badge-info">Galería</span>
                <span class="badge badge-success">Sensor activo</span>
                <span class="badge badge-secondary">Sensor sin área</span>
            </div>
            <div class="mine-map">
                @php
                    $levels = \App\Models\Area::with('trabajadores')->orderBy('nivel')->get()->groupBy('nivel');
                    $sensors = \App\Models\Sensor::with('area')->get();
                @endphp
                @forelse($levels as $nivel => $areas)
                    <div class="mine-level">
                        <div class="level-label">
                            <span class="level-pill">{{ $nivel ?: 'Sin nivel' }}</span>
                        </div>
                        <div class="level-track">
                            @foreach($areas as $area)
                                <div class="gallery">
                                    <div class="gallery-name">{{ $area->nombre }}</div>
                                    <div class="sensor-row">
                                        @php
                                            $levelSensors = $sensors->where('area_id', $area->id);
                                        @endphp
                                        @forelse($levelSensors as $sensor)
                                            <div class="sensor-dot {{ $sensor->activo ? 'active' : 'inactive' }}" 
                                                 title="{{ $sensor->device_id }} {{ $sensor->separacion_m ? '· ' . $sensor->separacion_m . 'm' : '' }}">
                                                {{ Str::limit($sensor->device_id, 6, '') }}
                                            </div>
                                        @empty
                                            <span class="text-muted small">Sin sensores</span>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info mb-0">Aún no hay niveles definidos.</div>
                @endforelse

                @if($sensors->whereNull('area_id')->count())
                    <div class="mine-level mt-3">
                        <div class="level-label text-muted">
                            <span class="level-pill">Sin asignar</span>
                        </div>
                        <div class="level-track">
                            <div class="gallery">
                                <div class="sensor-row">
                                    @foreach($sensors->whereNull('area_id') as $sensor)
                                        <div class="sensor-dot inactive" title="{{ $sensor->device_id }}">
                                            {{ Str::limit($sensor->device_id, 6, '') }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.mine-map {
    position: relative;
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 14px;
    padding: 20px 26px 22px 40px;
    background: radial-gradient(circle at top, rgba(120, 144, 156, 0.35), transparent 60%),
                radial-gradient(circle at bottom, rgba(69, 90, 100, 0.45), transparent 60%),
                linear-gradient(135deg, #050b18, #050b20 45%, #02040a);
    overflow: hidden;
}

.mine-map::before {
    /* Pozo vertical principal */
    content: '';
    position: absolute;
    left: 16px;
    top: 18px;
    bottom: 18px;
    width: 8px;
    border-radius: 4px;
    background: linear-gradient(to bottom, #90a4ae, #455a64);
    box-shadow: 0 0 12px rgba(0,0,0,0.6);
}

.mine-map::after {
    /* Rejilla sutil de fondo */
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 40px 36px;
    opacity: 0.7;
    pointer-events: none;
}

.mine-level {
    position: relative;
    margin-bottom: 22px;
    padding-left: 22px;
}

.mine-level::before {
    /* Conexión del nivel con el pozo */
    content: '';
    position: absolute;
    left: -10px;
    top: 18px;
    width: 26px;
    height: 2px;
    background: linear-gradient(to right, #90caf9, #42a5f5);
}

.level-label {
    font-weight: 700;
    margin-bottom: 8px;
    color: #c5d0e6;
}

.level-pill {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    background: linear-gradient(135deg, #263238, #37474f);
    border: 1px solid rgba(144, 202, 249, 0.5);
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.level-track {
    border-left: 2px dashed rgba(144, 202, 249, 0.7);
    padding-left: 18px;
}

.gallery {
    margin-bottom: 12px;
    padding: 6px 10px 8px;
    border-radius: 8px;
    background: radial-gradient(circle at left, rgba(96, 125, 139, 0.45), transparent 65%);
}

.gallery-name {
    font-weight: 600;
    color: #e3f2fd;
    margin-bottom: 6px;
}

.sensor-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.sensor-dot {
    width: 52px;
    height: 32px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: #0b1c2e;
    background: #cfd8dc;
    box-shadow: 0 3px 7px rgba(0,0,0,0.35);
    border: 1px solid rgba(0,0,0,0.4);
}

.sensor-dot.active {
    background: radial-gradient(circle at top, #b9f6ca, #00c853);
    color: #043017;
}

.sensor-dot.inactive {
    background: radial-gradient(circle at top, #eceff1, #90a4ae);
    color: #263238;
}

@media (max-width: 768px) {
    .mine-map {
        padding: 18px 16px 18px 30px;
    }
    .sensor-dot {
        width: 44px;
        height: 28px;
        font-size: 10px;
    }
}
</style>
@endpush
@endsection

