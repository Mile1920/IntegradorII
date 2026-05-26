<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Incidente;

class IncidenteController extends Controller
{
    // Listar incidentes (para técnicos/administradores)
    public function index()
    {
        // autorización por roles ya aplicada en rutas; listar directamente
        $incidentes = Incidente::with(['trabajador','area','cerrador'])->orderBy('estado')->orderBy('created_at','desc')->paginate(20);
        return view('incidentes.index', compact('incidentes'));
    }

    // Validar y cambiar estado del incidente
    public function updateEstado(Request $request, Incidente $incidente)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_proceso,completado',
            'comentario' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();
            
            $incidente->estado = $request->estado;
            
            if ($request->estado === 'completado') {
                $incidente->cerrado_por = Auth::id();
                $incidente->cerrado_en = now();
            } else {
                $incidente->cerrado_por = null;
                $incidente->cerrado_en = null;
            }
            
            if ($request->filled('comentario')) {
                if (in_array('comentario', $incidente->getFillable())) {
                    $incidente->comentario = trim($request->comentario);
                }
            }
            
            $incidente->save();

            DB::commit();

            Log::info("Estado de incidente ID {$incidente->id} cambiado a {$request->estado} por usuario " . Auth::id());

            return redirect()->back()->with('success', 'Estado del incidente actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cambiando estado del incidente: ' . $e->getMessage(), [
                'incidente_id' => $incidente->id,
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'No se pudo cambiar el estado del incidente. Intenta nuevamente.');
        }
    }
}
