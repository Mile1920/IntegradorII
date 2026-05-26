<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Si el usuario es administrador de área, solo mostrar su área
        if ($user && $user->hasRole('administrador-area')) {
            $areas = Area::where('id', $user->area)->orderBy('activo', 'desc')->orderBy('nombre')->paginate(15);
        } else {
            // Administrador principal ve todas las áreas
            $areas = Area::orderBy('activo', 'desc')->orderBy('nombre')->paginate(15);
        }

        return view('areas.index', compact('areas'));
    }

    public function create()
    {
        $user = auth()->user();

        // Solo administrador principal puede crear áreas
        if ($user && $user->hasRole('administrador-area')) {
            return redirect()->route('areas.index')->with('error', 'No tiene permisos para crear áreas.');
        }

        return view('areas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:areas,nombre|max:150',
            'nivel' => 'nullable|string|max:100'
        ]);

        try {
            DB::beginTransaction();
            
            $area = Area::create([
                'nombre' => trim($request->nombre),
                'nivel' => $request->filled('nivel') ? trim($request->nivel) : null,
                'activo' => true,
            ]);
            
            DB::commit();
            
            Log::info("Área creada: {$area->nombre} (ID: {$area->id})");
            return redirect()->route('areas.index')->with('success', 'Área creada con éxito');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear área: " . $e->getMessage(), [
                'request' => $request->except(['_token'])
            ]);
            return redirect()->back()->withInput()->with('error', 'Error al crear el área. Verifica que el nombre no esté duplicado e intenta nuevamente.');
        }
    }

    public function show(Area $area)
    {
        return view('areas.show', compact('area'));
    }

    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'nombre' => 'required|unique:areas,nombre,' . $area->id . '|max:150',
            'nivel' => 'nullable|string|max:100'
        ]);

        try {
            DB::beginTransaction();
            
            $area->update([
                'nombre' => trim($request->nombre),
                'nivel' => $request->filled('nivel') ? trim($request->nivel) : null,
            ]);
            
            DB::commit();
            
            Log::info("Área actualizada: {$area->nombre} (ID: {$area->id})");
            return redirect()->route('areas.index')->with('success', 'Área actualizada con éxito');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar área: " . $e->getMessage(), [
                'area_id' => $area->id
            ]);
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el área. Verifica que el nombre no esté duplicado e intenta nuevamente.');
        }
    }

    public function destroy(Area $area)
    {
        $area->activo = !$area->activo;
        $area->save();

        $estado = $area->activo ? 'activada' : 'desactivada';
        Log::info("Área {$estado}: {$area->nombre} (ID: {$area->id})");
        return redirect()->route('areas.index')->with('success', "Área {$estado} correctamente");
    }
}