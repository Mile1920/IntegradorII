<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Trabajador;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ControlGrupalController extends Controller
{
    public function index(Request $request)
    {
        // Obtener filtros
        $areaId = $request->input('area_id');
        $turno = $request->input('turno');
        $fecha = $request->input('fecha', now()->format('Y-m-d'));
        $busqueda = $request->input('q');

        // Obtener todas las áreas activas
        $areas = Area::activo()->orderBy('nombre')->get();

        // Obtener trabajadores agrupados por área y turno
        $query = Trabajador::with(['area', 'cargo'])
            ->where('activo', true);

        // Búsqueda por texto
        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('ap_paterno', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('ap_materno', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('ci', 'ILIKE', "%{$busqueda}%");
            });
        }

        if ($areaId) {
            $query->where('area_id', $areaId);
        }

        if ($turno) {
            $query->where('turno', $turno);
        }

        $query->orderBy('ap_paterno')->orderBy('nombre');
        $trabajadores = $query->get();

        // Agrupar por área y turno
        $grupos = [];

        foreach ($trabajadores as $trabajador) {
            $areaNombre = $trabajador->area->nombre ?? 'Sin Área';
            $turnoTrabajador = $trabajador->turno ?? 'Sin Turno';

            if (!isset($grupos[$areaNombre])) {
                $grupos[$areaNombre] = [];
            }

            if (!isset($grupos[$areaNombre][$turnoTrabajador])) {
                $grupos[$areaNombre][$turnoTrabajador] = [];
            }

            // Obtener estado actual del trabajador para la fecha seleccionada
            $estado = $this->getEstadoTrabajador($trabajador, $fecha);
            $trabajador->estado_actual = $estado;

            $grupos[$areaNombre][$turnoTrabajador][] = $trabajador;
        }

        // Estadísticas generales
        $totalTrabajadores = $trabajadores->count();
        $trabajadoresPresentes = $trabajadores->filter(function($t) {
            return $t->estado_actual === 'presente';
        })->count();

        $trabajadoresAusentes = $trabajadores->filter(function($t) {
            return $t->estado_actual === 'ausente';
        })->count();

        return view('control-grupal.index', compact(
            'grupos',
            'areas',
            'areaId',
            'turno',
            'fecha',
            'totalTrabajadores',
            'trabajadoresPresentes',
            'trabajadoresAusentes'
        ));
    }

    public function registrarIngresoGrupal(Request $request)
    {
        $request->validate([
            'trabajador_ids' => 'required|array',
            'trabajador_ids.*' => 'exists:trabajadors,id',
            'area_id' => 'required|exists:areas,id',
            'observacion' => 'nullable|string|max:200',
        ]);

        $trabajadorIds = $request->input('trabajador_ids');
        $areaId = $request->input('area_id');
        $observacion = $request->input('observacion');
        $turnoActual = $this->determinarTurno();

        $exitosos = 0;
        $errores = [];

        foreach ($trabajadorIds as $trabajadorId) {
            try {
                $trabajador = Trabajador::findOrFail($trabajadorId);

                // Verificar que no tenga un ingreso activo
                $ultimoRegistro = Ingreso::where('trabajador_id', $trabajadorId)->latest('registrado_en')->first();

                if ($ultimoRegistro && $ultimoRegistro->tipo === 'ingreso') {
                    $errores[] = "El trabajador {$trabajador->nombre_completo} ya tiene un ingreso activo.";
                    continue;
                }

                // Actualizar turno del trabajador
                $trabajador->turno = $turnoActual;
                $trabajador->save();

                // Registrar ingreso
                Ingreso::create([
                    'trabajador_id' => $trabajadorId,
                    'area_id' => $areaId,
                    'observacion' => $observacion,
                    'tipo' => 'ingreso',
                    'registrado_en' => now(),
                ]);

                $exitosos++;

            } catch (\Exception $e) {
                $errores[] = "Error con trabajador ID {$trabajadorId}: " . $e->getMessage();
            }
        }

        $mensaje = "Se registraron {$exitosos} ingresos grupales correctamente.";
        if (!empty($errores)) {
            $mensaje .= " Errores: " . implode(', ', $errores);
        }

        return redirect()->back()->with('success', $mensaje);
    }

    public function registrarSalidaGrupal(Request $request)
    {
        $request->validate([
            'trabajador_ids' => 'required|array',
            'trabajador_ids.*' => 'exists:trabajadors,id',
            'area_id' => 'required|exists:areas,id',
            'observacion' => 'nullable|string|max:200',
        ]);

        $trabajadorIds = $request->input('trabajador_ids');
        $areaId = $request->input('area_id');
        $observacion = $request->input('observacion');

        $exitosos = 0;
        $errores = [];

        foreach ($trabajadorIds as $trabajadorId) {
            try {
                $trabajador = Trabajador::findOrFail($trabajadorId);

                // Verificar que tenga un ingreso activo que coincida con área y observación
                $ultimoRegistro = Ingreso::where('trabajador_id', $trabajadorId)->latest('registrado_en')->first();

                if (!$ultimoRegistro || $ultimoRegistro->tipo !== 'ingreso') {
                    $errores[] = "El trabajador {$trabajador->nombre_completo} no tiene un ingreso activo.";
                    continue;
                }

                if ($ultimoRegistro->area_id != $areaId || $ultimoRegistro->observacion != $observacion) {
                    $errores[] = "El área/observación no coincide para {$trabajador->nombre_completo}.";
                    continue;
                }

                // Registrar salida
                Ingreso::create([
                    'trabajador_id' => $trabajadorId,
                    'area_id' => $areaId,
                    'observacion' => $observacion,
                    'tipo' => 'salida',
                    'registrado_en' => now(),
                ]);

                $exitosos++;

            } catch (\Exception $e) {
                $errores[] = "Error con trabajador ID {$trabajadorId}: " . $e->getMessage();
            }
        }

        $mensaje = "Se registraron {$exitosos} salidas grupales correctamente.";
        if (!empty($errores)) {
            $mensaje .= " Errores: " . implode(', ', $errores);
        }

        return redirect()->back()->with('success', $mensaje);
    }

    private function getEstadoTrabajador(Trabajador $trabajador, string $fecha)
    {
        // Obtener registros del día
        $registros = Ingreso::where('trabajador_id', $trabajador->id)
            ->whereDate('registrado_en', $fecha)
            ->orderBy('registrado_en')
            ->get();

        if ($registros->isEmpty()) {
            return 'ausente';
        }

        $ultimoRegistro = $registros->last();

        // Si el último registro es ingreso, está presente
        if ($ultimoRegistro->tipo === 'ingreso') {
            return 'presente';
        }

        // Si es salida, verificar si hay más registros después
        return 'ausente';
    }

    private function determinarTurno()
    {
        $hora = now()->hour;
        $minuto = now()->minute;
        $horaDecimal = $hora + ($minuto / 60);

        // Turno 1: 7:00 AM - 4:00 PM (7.0 - 16.0)
        if ($horaDecimal >= 7 && $horaDecimal < 16) {
            return 'mañana';
        }
        // Turno 2: 3:00 PM - 11:59 PM (15.0 - 23.983)
        elseif ($horaDecimal >= 15 && $horaDecimal < 24) {
            return 'tarde';
        }
        // Turno 3: 11:00 PM - 7:00 AM (23.0 - 6.983 + 0.0 - 7.0)
        else {
            return 'noche';
        }
    }
}