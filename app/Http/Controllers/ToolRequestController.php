<?php

namespace App\Http\Controllers;

use App\Models\ToolRequest;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolRequestController extends Controller
{
    // Worker submits request
    public function create()
    {
        $areas = Area::orderBy('nombre')->get();
        return view('mi.solicitar', compact('areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'herramienta' => 'required|string',
            'cantidad' => 'required|integer|min:1',
            'area_id' => 'nullable|exists:areas,id',
            'observacion' => 'nullable|string',
        ]);

        $data['trabajador_id'] = Auth::user()->trabajador->id ?? null;
        ToolRequest::create($data);

        return redirect()->route('dashboard')->with('success','Solicitud enviada');
    }

    // Admin view list
    public function index()
    {
        $solicitudes = ToolRequest::with('trabajador','area')->orderBy('created_at','desc')->paginate(25);
        return view('tool_requests.index', compact('solicitudes'));
    }

    public function update(Request $request, ToolRequest $toolRequest)
    {
        $data = $request->validate(['estado' => 'required|in:pendiente,aprobado,rechazado']);
        $toolRequest->update($data);
        return back()->with('success','Estado actualizado');
    }
}
