@extends('layouts.app')
@section('title', 'Logs del Sistema')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title"><i class="fas fa-history"></i> Logs del Sistema</h4>
                    <p class="card-category text-white-90">Registro de eventos y errores del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('system.logs.download') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-download"></i> Descargar
                    </a>
                    <form action="{{ route('system.logs.clear') }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de limpiar todos los logs?');">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-trash"></i> Limpiar
                        </button>
                    </form>
                    <a href="{{ route('system.status') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-dark text-white">
                            <div class="card-body text-center py-3">
                                <h5 class="mb-1">{{ $stats['total'] }}</h5>
                                <small>Total Eventos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center py-3">
                                <h5 class="mb-1">{{ $stats['error'] + $stats['critical'] + $stats['emergency'] }}</h5>
                                <small>Errores</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center py-3">
                                <h5 class="mb-1">{{ $stats['warning'] }}</h5>
                                <small>Advertencias</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center py-3">
                                <h5 class="mb-1">{{ number_format($stats['size'] / 1024, 1) }} KB</h5>
                                <small>Tamaño Archivo</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('system.logs') }}" class="form-inline d-flex flex-wrap gap-2">
                            <div class="form-group mr-2">
                                <label class="mr-2">Nivel:</label>
                                <select name="level" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level }}" {{ request('level') === $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <label class="mr-2">Búsqueda:</label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar en logs..." value="{{ request('search') }}" style="min-width: 200px;">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            @if(request()->anyFilled(['level', 'search', 'date']))
                                <a href="{{ route('system.logs') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i> Limpiar Filtros
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Log Entries -->
                <div class="log-container" style="max-height: 600px; overflow-y: auto; background: #0a0f1a; border-radius: 8px; padding: 15px;">
                    @forelse($logContent as $entry)
                        @php
                            $levelClass = match($entry['level']) {
                                'emergency', 'alert', 'critical' => 'text-danger',
                                'error' => 'text-danger',
                                'warning' => 'text-warning',
                                'notice' => 'text-info',
                                'info' => 'text-success',
                                'debug' => 'text-muted',
                                default => 'text-light'
                            };
                        @endphp
                        <div class="log-entry mb-1 p-1" style="border-bottom: 1px solid rgba(255,255,255,0.05); font-family: 'Consolas', 'Courier New', monospace; font-size: 0.75rem; line-height: 1.4;">
                            @if($entry['date'])
                                <span class="text-muted">[{{ $entry['date'] }} {{ $entry['time'] }}]</span>
                            @endif
                            <span class="{{ $levelClass }} font-weight-bold">{{ strtoupper($entry['level']) }}:</span>
                            <span class="text-light">{{ Str::limit($entry['text'], 500) }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <p>No hay registros de log disponibles o el filtro no encontró resultados.</p>
                        </div>
                    @endforelse
                </div>

                @if(count($logContent) > 0)
                    <div class="text-center mt-3">
                        <small class="text-muted">Mostrando {{ count($logContent) }} entradas. Los logs se rotan automáticamente cada 14 días.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
