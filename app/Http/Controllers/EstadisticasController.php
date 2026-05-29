<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Area;
use App\Models\Ingreso;
use App\Models\Incidente;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EstadisticasController extends Controller
{
    public function index()
    {
        $data = $this->getData(auth()->user());

        return view('estadisticas.index', $data);
    }

    public function pdf()
    {
        $data = $this->getData(auth()->user());

        $pdf = Pdf::loadView('estadisticas.pdf', $data);
        return $pdf->download('estadisticas-mina-porco-' . now()->format('Y-m-d') . '.pdf');
    }

    private function getData($user)
    {
        $totalTrabajadores = Trabajador::where('activo', true)->count();
        $totalAreas = Area::where('activo', true)->count();
        $totalCargos = Cargo::where('activo', true)->count();

        $ingresosHoy = Ingreso::whereDate('registrado_en', today())->count();
        $salidasHoy = Ingreso::whereDate('registrado_en', today())->where('tipo', 'salida')->count();
        $ingresosMes = Ingreso::whereMonth('registrado_en', now()->month)->count();
        $salidasMes = Ingreso::whereMonth('registrado_en', now()->month)->where('tipo', 'salida')->count();

        $incidentesAbiertos = Incidente::where('estado', 'pendiente')->count();
        $incidentesPendientes = Incidente::where('estado', 'pendiente')->count();
        $incidentesCerrados = Incidente::where('estado', 'completado')->count();
        $incidentesCriticos = Incidente::where('gravedad', 'critica')->count();

        $turnos = Trabajador::where('activo', true)
            ->select('turno', DB::raw('count(*) as total'))
            ->groupBy('turno')
            ->pluck('total', 'turno');

        $trabajadoresPorArea = Area::withCount(['trabajadores' => function ($q) {
            $q->where('activo', true);
        }])->get()->map(function ($a) {
            return ['label' => $a->nombre, 'value' => $a->trabajadores_count];
        });

        $ingresos7d = collect();
        $salidas7d = collect();
        $labels7d = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dia = today()->subDays($i);
            $labels7d->push($dia->format('d/m'));
            $ingresos7d->push(Ingreso::whereDate('registrado_en', $dia)->where('tipo', 'ingreso')->count());
            $salidas7d->push(Ingreso::whereDate('registrado_en', $dia)->where('tipo', 'salida')->count());
        }

        $horasPico = Ingreso::whereDate('registrado_en', today())
            ->select(DB::raw("EXTRACT(HOUR FROM registrado_en) as hora"), DB::raw('count(*) as total'))
            ->groupBy('hora')
            ->orderBy('hora')
            ->pluck('total', 'hora');

        $incidentesPorGravedad = Incidente::select('gravedad', DB::raw('count(*) as total'))
            ->groupBy('gravedad')
            ->pluck('total', 'gravedad');

        $incidentes6m = collect();
        $labels6m = collect();
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $labels6m->push($mes->format('M Y'));
            $incidentes6m->push(Incidente::whereYear('created_at', $mes->year)->whereMonth('created_at', $mes->month)->count());
        }

        $areaRestringida = null;
        if ($user->hasRole('administrador-area') && $user->trabajador && $user->trabajador->area_id) {
            $areaRestringida = $user->trabajador->area_id;
            $totalTrabajadores = Trabajador::where('activo', true)->where('area_id', $areaRestringida)->count();
            $trabajadoresPorArea = $trabajadoresPorArea->filter(fn($a) => $a['label'] === $user->trabajador->area->nombre);
            $ingresosHoy = Ingreso::whereDate('registrado_en', today())->whereHas('trabajador', fn($q) => $q->where('area_id', $areaRestringida))->count();
            $salidasHoy = Ingreso::whereDate('registrado_en', today())->where('tipo', 'salida')->whereHas('trabajador', fn($q) => $q->where('area_id', $areaRestringida))->count();
            $ingresosMes = Ingreso::whereMonth('registrado_en', now()->month)->whereHas('trabajador', fn($q) => $q->where('area_id', $areaRestringida))->count();
            $salidasMes = Ingreso::whereMonth('registrado_en', now()->month)->where('tipo', 'salida')->whereHas('trabajador', fn($q) => $q->where('area_id', $areaRestringida))->count();
            $incidentesAbiertos = Incidente::where('estado', 'pendiente')->where('area_id', $areaRestringida)->count();
            $incidentesPendientes = Incidente::where('estado', 'pendiente')->where('area_id', $areaRestringida)->count();
            $incidentesCerrados = Incidente::where('estado', 'completado')->where('area_id', $areaRestringida)->count();
            $incidentesCriticos = Incidente::where('gravedad', 'critica')->where('area_id', $areaRestringida)->count();
        }

        return compact(
            'totalTrabajadores', 'totalAreas', 'totalCargos',
            'ingresosHoy', 'salidasHoy', 'ingresosMes', 'salidasMes',
            'incidentesAbiertos', 'incidentesPendientes', 'incidentesCerrados', 'incidentesCriticos',
            'turnos', 'trabajadoresPorArea',
            'ingresos7d', 'salidas7d', 'labels7d', 'horasPico',
            'incidentesPorGravedad', 'incidentes6m', 'labels6m',
            'areaRestringida'
        );
    }
}
