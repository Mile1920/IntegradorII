<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SensorDeviceController extends Controller
{
    public function index()
    {
        $sensores = Sensor::with('area')->orderBy('created_at','desc')->paginate(25);
        return view('sensors.devices.index', compact('sensores'));
    }

    public function create()
    {
        $areas = Area::orderBy('nombre')->get();
        return view('sensors.devices.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sensors' => 'required|array|min:1',
            'sensors.*.device_id' => 'required|string|distinct|unique:sensors,device_id',
            'sensors.*.nombre' => 'nullable|string|max:255',
            'sensors.*.area_id' => 'nullable|exists:areas,id',
            'sensors.*.separacion_m' => 'nullable|numeric|min:0|max:99999.99',
        ]);

        try {
            DB::beginTransaction();
            
            $created = 0;
            collect($request->input('sensors'))
                ->filter(fn ($row) => filled($row['device_id'] ?? null))
                ->each(function ($row) use (&$created) {
                    Sensor::create([
                        'device_id' => trim($row['device_id']),
                        'nombre' => !empty($row['nombre']) ? trim($row['nombre']) : null,
                        'area_id' => $row['area_id'] ?? null,
                        'separacion_m' => isset($row['separacion_m']) ? (float)$row['separacion_m'] : null,
                        'activo' => true,
                    ]);
                    $created++;
                });

            DB::commit();
            
            Log::info("Se crearon {$created} sensores");
            
            return redirect()->route('sensors.devices.index')
                ->with('success', "Se crearon {$created} sensor(es) correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear sensores: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al crear los sensores. Verifica los datos e intenta nuevamente.');
        }
    }

    public function edit(Sensor $sensor)
    {
        $areas = Area::orderBy('nombre')->get();
        return view('sensors.devices.edit', compact('sensor','areas'));
    }

    public function update(Request $request, Sensor $sensor)
    {
        $data = $request->validate([
            'device_id' => 'required|string|unique:sensors,device_id,'.$sensor->id,
            'nombre' => 'nullable|string|max:255',
            'area_id' => 'nullable|exists:areas,id',
            'separacion_m' => 'nullable|numeric|min:0|max:99999.99',
            'activo' => 'nullable|boolean'
        ]);

        try {
            $sensor->update($data);
            Log::info("Sensor actualizado: {$sensor->device_id} (ID: {$sensor->id})");
            return redirect()->route('sensors.devices.index')->with('success','Sensor actualizado correctamente');
        } catch (\Exception $e) {
            Log::error("Error al actualizar sensor: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el sensor. Intenta nuevamente.');
        }
    }

    public function destroy(Sensor $sensor)
    {
        try {
            $deviceId = $sensor->device_id;
            $sensor->delete();
            Log::info("Sensor eliminado: {$deviceId} (ID: {$sensor->id})");
            return back()->with('success','Sensor eliminado correctamente');
        } catch (\Exception $e) {
            Log::error("Error al eliminar sensor: " . $e->getMessage());
            return back()->with('error', 'Error al eliminar el sensor. Intenta nuevamente.');
        }
    }
}
