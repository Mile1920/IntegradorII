<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Trabajador;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\Ingreso;
use App\Models\Incidente;
use App\Services\FirebaseService;
use Carbon\Carbon;
use PDF;

class ReporteController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_usuarios' => User::count(),
            'total_trabajadores' => Trabajador::where('activo', true)->count(),
            'total_areas' => Area::where('activo', true)->count(),
            'total_cargos' => Cargo::where('activo', true)->count(),
            'ingresos_hoy' => Ingreso::whereDate('registrado_en', today())->count(),
            'incidentes_pendientes' => Incidente::where('estado', 'pendiente')->count(),
            'incidentes_mes' => Incidente::whereMonth('created_at', now()->month)->count(),
        ];

        // Datos de sensores
        $sensorData = $this->firebaseService->getSensorData();
        $sensorStats = $this->calculateSensorStats($sensorData);

        return view('reportes.index', compact('stats', 'sensorStats'));
    }

    public function ingresos(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        $query = Ingreso::with(['trabajador.area', 'trabajador.cargo', 'area'])
            ->whereBetween('registrado_en', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        $ingresos = $query->orderBy('registrado_en', 'desc')->get();

        // Estadísticas
        $stats = [
            'total_registros' => $ingresos->count(),
            'ingresos' => $ingresos->where('tipo', 'ingreso')->count(),
            'salidas' => $ingresos->where('tipo', 'salida')->count(),
            'trabajadores_unicos' => $ingresos->pluck('trabajador_id')->unique()->count(),
        ];

        $areas = Area::where('activo', true)->orderBy('nombre')->get();
        $trabajadores = Trabajador::where('activo', true)->orderBy('ap_paterno')->orderBy('nombre')->get();

        return view('reportes.ingresos', compact('ingresos', 'stats', 'areas', 'trabajadores', 'fechaInicio', 'fechaFin'));
    }

    public function incidentes(Request $request)
    {
        $query = Incidente::with(['trabajador.area', 'trabajador.cargo']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('gravedad')) {
            $query->where('gravedad', $request->gravedad);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('created_at', [
                $request->fecha_inicio . ' 00:00:00',
                $request->fecha_fin . ' 23:59:59'
            ]);
        }

        $incidentes = $query->orderBy('created_at', 'desc')->get();

        $stats = [
            'total' => $incidentes->count(),
            'pendientes' => $incidentes->where('estado', 'pendiente')->count(),
            'en_proceso' => $incidentes->where('estado', 'en_proceso')->count(),
            'completados' => $incidentes->where('estado', 'completado')->count(),
            'criticos' => $incidentes->where('gravedad', 'critica')->count(),
            'altos' => $incidentes->where('gravedad', 'alta')->count(),
        ];

        return view('reportes.incidentes', compact('incidentes', 'stats'));
    }

    public function sensores(Request $request)
    {
        $sensorData = $this->firebaseService->getSensorData();

        if (!$sensorData) {
            return view('reportes.sensores', [
                'sensores' => collect(),
                'stats' => ['total' => 0, 'activos' => 0, 'alertas' => 0]
            ]);
        }

        $sensores = collect();
        $stats = ['total' => 0, 'activos' => 0, 'alertas' => 0];

        foreach ($sensorData as $sensorId => $sensor) {
            $sensores->push([
                'id' => $sensorId,
                'tipo' => $sensor['tipo'] ?? 'desconocido',
                'area' => $sensor['area'] ?? 'Sin área',
                'activo' => $sensor['activo'] ?? false,
                'ultima_lectura' => $sensor['ultima_lectura'] ?? null,
                'alertas_count' => isset($sensor['alertas']) ? count($sensor['alertas']) : 0,
                'datos' => $this->extractSensorData($sensor)
            ]);

            $stats['total']++;
            if ($sensor['activo'] ?? false) {
                $stats['activos']++;
            }
            if (isset($sensor['alertas'])) {
                $stats['alertas'] += count($sensor['alertas']);
            }
        }

        // Filtros
        if ($request->filled('tipo')) {
            $sensores = $sensores->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            $estado = $request->estado === 'activo';
            $sensores = $sensores->where('activo', $estado);
        }

        return view('reportes.sensores', compact('sensores', 'stats'));
    }

    public function completo(Request $request)
    {
        // Reporte completo que combina todo
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        // Estadísticas generales
        $stats = [
            'periodo' => ['inicio' => $fechaInicio, 'fin' => $fechaFin],
            'trabajadores' => [
                'total' => Trabajador::where('activo', true)->count(),
                'nuevos_mes' => Trabajador::whereBetween('created_at', [$fechaInicio, $fechaFin])->count(),
            ],
            'ingresos' => [
                'total' => Ingreso::whereBetween('registrado_en', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
                'ingresos' => Ingreso::where('tipo', 'ingreso')->whereBetween('registrado_en', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
                'salidas' => Ingreso::where('tipo', 'salida')->whereBetween('registrado_en', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
            ],
            'incidentes' => [
                'total' => Incidente::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
                'pendientes' => Incidente::where('estado', 'pendiente')->count(),
                'criticos' => Incidente::where('gravedad', 'critica')->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
            ],
        ];

        // Datos de sensores
        $sensorData = $this->firebaseService->getSensorData();
        $stats['sensores'] = $this->calculateSensorStats($sensorData);

        // Actividad reciente
        $actividad = [
            'ingresos_recientes' => Ingreso::with('trabajador')->orderBy('registrado_en', 'desc')->limit(10)->get(),
            'incidentes_recientes' => Incidente::with('trabajador')->orderBy('created_at', 'desc')->limit(10)->get(),
        ];

        return view('reportes.completo', compact('stats', 'actividad', 'fechaInicio', 'fechaFin'));
    }

    private function calculateSensorStats($sensorData)
    {
        if (!$sensorData) {
            return ['total' => 0, 'activos' => 0, 'alertas' => 0, 'tipos' => []];
        }

        $stats = ['total' => 0, 'activos' => 0, 'alertas' => 0, 'tipos' => []];

        foreach ($sensorData as $sensor) {
            $stats['total']++;
            if ($sensor['activo'] ?? false) {
                $stats['activos']++;
            }

            if (isset($sensor['alertas'])) {
                $stats['alertas'] += count($sensor['alertas']);
            }

            $tipo = $sensor['tipo'] ?? 'desconocido';
            if (!isset($stats['tipos'][$tipo])) {
                $stats['tipos'][$tipo] = 0;
            }
            $stats['tipos'][$tipo]++;
        }

        return $stats;
    }

    private function extractSensorData($sensor)
    {
        $data = [];

        switch ($sensor['tipo'] ?? null) {
            case 'movimiento_tierra':
                $data = [
                    'movimiento' => $sensor['movimiento'] ?? null,
                    'aceleracion' => $sensor['aceleracion'] ?? null,
                ];
                break;
            case 'gases_toxicos':
                $data = [
                    'co' => $sensor['co'] ?? null,
                    'co2' => $sensor['co2'] ?? null,
                    'metano' => $sensor['metano'] ?? null,
                    'oxigeno' => $sensor['oxigeno'] ?? null,
                ];
                break;
            case 'signos_vitales':
                $data = [
                    'frecuencia_cardiaca' => $sensor['frecuencia_cardiaca'] ?? null,
                    'temperatura_corporal' => $sensor['temperatura_corporal'] ?? null,
                    'saturacion_oxigeno' => $sensor['saturacion_oxigeno'] ?? null,
                    'presion_arterial' => $sensor['presion_arterial'] ?? null,
                ];
                break;
        }

        return array_filter($data, function($value) {
            return $value !== null;
        });
    }

    public function ingresosPDF(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        $query = Ingreso::with(['trabajador.area', 'trabajador.cargo', 'area'])
            ->whereBetween('registrado_en', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        $ingresos = $query->orderBy('registrado_en', 'desc')->get();

        $stats = [
            'total_registros' => $ingresos->count(),
            'ingresos' => $ingresos->where('tipo', 'ingreso')->count(),
            'salidas' => $ingresos->where('tipo', 'salida')->count(),
            'trabajadores_unicos' => $ingresos->pluck('trabajador_id')->unique()->count(),
            'periodo' => ['inicio' => $fechaInicio, 'fin' => $fechaFin],
        ];

        $pdf = PDF::loadView('reportes.pdf.ingresos', compact('ingresos', 'stats'));
        return $pdf->download('reporte-ingresos-' . $fechaInicio . '-a-' . $fechaFin . '.pdf');
    }

    public function incidentesPDF(Request $request)
    {
        $query = Incidente::with(['trabajador.area', 'trabajador.cargo']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('gravedad')) {
            $query->where('gravedad', $request->gravedad);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('created_at', [
                $request->fecha_inicio . ' 00:00:00',
                $request->fecha_fin . ' 23:59:59'
            ]);
        }

        $incidentes = $query->orderBy('created_at', 'desc')->get();

        $stats = [
            'total' => $incidentes->count(),
            'pendientes' => $incidentes->where('estado', 'pendiente')->count(),
            'en_proceso' => $incidentes->where('estado', 'en_proceso')->count(),
            'completados' => $incidentes->where('estado', 'completado')->count(),
            'criticos' => $incidentes->where('gravedad', 'critica')->count(),
            'altos' => $incidentes->where('gravedad', 'alta')->count(),
        ];

        $pdf = PDF::loadView('reportes.pdf.incidentes', compact('incidentes', 'stats'));
        return $pdf->download('reporte-incidentes-' . now()->format('Y-m-d') . '.pdf');
    }

    public function completoPDF(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        $stats = [
            'periodo' => ['inicio' => $fechaInicio, 'fin' => $fechaFin],
            'trabajadores' => [
                'total' => Trabajador::where('activo', true)->count(),
                'nuevos_mes' => Trabajador::whereBetween('created_at', [$fechaInicio, $fechaFin])->count(),
            ],
            'ingresos' => [
                'total' => Ingreso::whereBetween('registrado_en', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
                'ingresos' => Ingreso::where('tipo', 'ingreso')->whereBetween('registrado_en', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
                'salidas' => Ingreso::where('tipo', 'salida')->whereBetween('registrado_en', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
            ],
            'incidentes' => [
                'total' => Incidente::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
                'pendientes' => Incidente::where('estado', 'pendiente')->count(),
                'criticos' => Incidente::where('gravedad', 'critica')->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
            ],
        ];

        $sensorData = $this->firebaseService->getSensorData();
        $stats['sensores'] = $this->calculateSensorStats($sensorData);

        $actividad = [
            'ingresos_recientes' => Ingreso::with('trabajador')->orderBy('registrado_en', 'desc')->limit(10)->get(),
            'incidentes_recientes' => Incidente::with('trabajador')->orderBy('created_at', 'desc')->limit(10)->get(),
        ];

        $pdf = PDF::loadView('reportes.pdf.completo', compact('stats', 'actividad'));
        return $pdf->download('reporte-completo-' . $fechaInicio . '-a-' . $fechaFin . '.pdf');
    }
}
