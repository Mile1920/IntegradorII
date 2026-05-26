@extends('layouts.app')
@section('title','Mis Reportes')

@section('content')
<div class="card">
    <div class="card-header card-header-primary">
        <h4 class="card-title">Mis Reportes</h4>
        <p class="card-category">Historial de ingresos/salidas y reportes que hayas enviado</p>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('mi.reportes') }}" class="row g-2 mb-3">
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" name="date_from" value="{{ $dateFrom ?? request('date_from') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" name="date_to" value="{{ $dateTo ?? request('date_to') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Área</label>
                <select name="area_id" class="form-control">
                    <option value="">Todas</option>
                    @foreach($areas ?? [] as $a)
                        <option value="{{ $a->id }}" {{ (request('area_id') == $a->id) ? 'selected' : '' }}>{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-control">
                    <option value="all" {{ (request('type','all')=='all')? 'selected':'' }}>Todos</option>
                    <option value="ingresos" {{ (request('type')=='ingresos')? 'selected':'' }}>Ingresos/Salidas</option>
                    <option value="incidentes" {{ (request('type')=='incidentes')? 'selected':'' }}>Incidentes</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Gravedad</label>
                <select name="gravedad" class="form-control">
                    <option value="">Todas</option>
                    <option value="baja" {{ (request('gravedad')=='baja')? 'selected':'' }}>Baja</option>
                    <option value="media" {{ (request('gravedad')=='media')? 'selected':'' }}>Media</option>
                    <option value="alta" {{ (request('gravedad')=='alta')? 'selected':'' }}>Alta</option>
                    <option value="critica" {{ (request('gravedad')=='critica')? 'selected':'' }}>Crítica</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('mi.reportes', array_merge(request()->except('page','ingresos_page','incidentes_page'), ['export'=>'pdf'])) }}" class="btn btn-outline-secondary">Exportar PDF</a>
                    <a href="{{ route('mi.reportes', array_merge(request()->except('page','ingresos_page','incidentes_page'), ['export'=>'pdf','only'=>'ingresos'])) }}" class="btn btn-outline-success">Exportar Ingresos (PDF)</a>
                    <a href="{{ route('mi.reportes', array_merge(request()->except('page','ingresos_page','incidentes_page'), ['export'=>'pdf','only'=>'incidentes'])) }}" class="btn btn-outline-danger">Exportar Incidentes (PDF)</a>
                </div>
            </div>
        </form>

        <h5>Ingresos / Salidas</h5>
        @if($ingresos->count())
            <table class="table">
                <thead>
                    <tr><th>Fecha</th><th>Tipo</th><th>Área</th><th>Subnivel</th></tr>
                </thead>
                <tbody>
                    @foreach($ingresos as $i)
                        <tr>
                            <td>{{ optional($i->registrado_en)->format('Y-m-d H:i') ?? $i->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ ucfirst($i->tipo) }}</td>
                            <td>{{ $i->area->nombre ?? '-' }}</td>
                            <td>{{ $i->area->nivel ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">{{ $ingresos->appends(request()->except('page'))->links() }}</div>
        @else
            <p class="text-muted">No se encontraron registros de ingreso/salida.</p>
        @endif

        <hr>
        <h5>Incidentes reportados</h5>
        @if($incidentes->count())
            <table class="table">
                <thead>
                    <tr><th>Fecha</th><th>Área</th><th>Gravedad</th><th>Descripción</th></tr>
                </thead>
                <tbody>
                    @foreach($incidentes as $inc)
                        <tr>
                            <td>{{ $inc->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $inc->area->nombre ?? '-' }}</td>
                            <td>{{ ucfirst($inc->gravedad) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($inc->descripcion, 120) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">{{ $incidentes->appends(request()->except('page'))->links() }}</div>
        @else
            <p class="text-muted">No has reportado incidentes todavía.</p>
        @endif
    </div>
</div>
@endsection
