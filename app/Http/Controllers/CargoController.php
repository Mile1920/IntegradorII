<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CargoController extends Controller
{
    public function index()
    {
        $cargos = Cargo::orderBy('activo', 'desc')->orderBy('nombre')->paginate(15);
        return view('cargos.index', compact('cargos'));
    }

    public function create()
    {
        return view('cargos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:cargos,nombre|max:150',
            'descripcion' => 'nullable|string|max:500',
            'tipo' => 'required|in:mina,mantenimiento,administrativo'
        ]);

        try {
            DB::beginTransaction();
            
            $cargo = Cargo::create([
                'nombre' => trim($request->nombre),
                'descripcion' => $request->filled('descripcion') ? trim($request->descripcion) : null,
                'tipo' => $request->tipo,
                'activo' => true,
            ]);
            
            DB::commit();
            
            Log::info("Cargo creado: {$cargo->nombre} (ID: {$cargo->id})");
            return redirect()->route('cargos.index')->with('success', 'Cargo creado con éxito');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear cargo: " . $e->getMessage(), [
                'request' => $request->except(['_token'])
            ]);
            return redirect()->back()->withInput()->with('error', 'Error al crear el cargo. Verifica que el nombre no esté duplicado e intenta nuevamente.');
        }
    }

    public function show(Cargo $cargo)
    {
        return view('cargos.show', compact('cargo'));
    }

    public function edit(Cargo $cargo)
    {
        return view('cargos.edit', compact('cargo'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        $request->validate([
            'nombre' => 'required|unique:cargos,nombre,' . $cargo->id . '|max:150',
            'descripcion' => 'nullable|string|max:500',
            'tipo' => 'required|in:mina,mantenimiento,administrativo'
        ]);

        try {
            DB::beginTransaction();
            
            $cargo->update([
                'nombre' => trim($request->nombre),
                'descripcion' => $request->filled('descripcion') ? trim($request->descripcion) : null,
                'tipo' => $request->tipo,
            ]);
            
            DB::commit();
            
            Log::info("Cargo actualizado: {$cargo->nombre} (ID: {$cargo->id})");
            return redirect()->route('cargos.index')->with('success', 'Cargo actualizado con éxito');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar cargo: " . $e->getMessage(), [
                'cargo_id' => $cargo->id
            ]);
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el cargo. Verifica que el nombre no esté duplicado e intenta nuevamente.');
        }
    }

    public function destroy(Cargo $cargo)
    {
        $cargo->activo = !$cargo->activo;
        $cargo->save();

        $estado = $cargo->activo ? 'activado' : 'desactivado';
        Log::info("Cargo {$estado}: {$cargo->nombre} (ID: {$cargo->id})");
        return redirect()->route('cargos.index')->with('success', "Cargo {$estado} correctamente");
    }
}