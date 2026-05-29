<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\User;
use App\Models\Ingreso;
use App\Models\Incidente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\CredencialesTrabajadorMail;
use Spatie\Permission\Models\Role;

class TrabajadorController extends Controller
{
    public function index()
    {
        $request = request();

        $query = Trabajador::with(['area', 'cargo']);

        // Si el usuario es administrador de area, limitar a su área
        $user = auth()->user();
        if ($user && $user->hasRole('administrador-area')) {
            if ($user->trabajador && $user->trabajador->area_id) {
                $query->where('area_id', $user->trabajador->area_id);
            }
        }

        // Filtros de búsqueda
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre', 'LIKE', "%{$q}%")
                    ->orWhere('ci', 'LIKE', "%{$q}%")
                    ->orWhere('ap_paterno', 'LIKE', "%{$q}%")
                    ->orWhere('ap_materno', 'LIKE', "%{$q}%");
            });
        }

        if ($request->filled('cargo_id')) {
            $query->where('cargo_id', $request->input('cargo_id'));
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->input('area_id'));
        }

        // Ordenar por nombre completo (ap_paterno ap_materno nombre) usando SQL para Postgres
        // Si se solicita exportar a PDF, generar y enviar (intenta usar DomPDF si está disponible)
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $rows = $query->orderBy('ap_paterno')->orderBy('nombre')->get();

            $filename = 'trabajadores_' . date('Ymd_His') . '.pdf';

            $viewHtml = view('trabajadores.pdf', compact('rows'))->render();

            // Preferir la fachada de Barryvdh\DomPDF si está instalada
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($viewHtml)->setPaper('letter');
                return $pdf->download($filename);
            }

            // Si Dompdf está disponible directamente
            if (class_exists(\Dompdf\Dompdf::class)) {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($viewHtml);
                $dompdf->setPaper('letter');
                $dompdf->render();
                return response($dompdf->output(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]);
            }

            // Fallback: enviar HTML (como descarga) y notificar que para PDF real se instale barryvdh/laravel-dompdf
            return response($viewHtml, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => "attachment; filename=\"trabajadores_" . date('Ymd_His') . ".html\"",
            ])->withHeaders(['X-Notice' => 'Para exportar PDF instale barryvdh/laravel-dompdf via composer']);
        }

        $trabajadores = $query->orderBy('activo', 'desc')
            ->orderByRaw("LOWER(ap_paterno || ' ' || COALESCE(ap_materno, '') || ' ' || nombre) ASC")
            ->paginate(15)
            ->withQueryString();

        $areas = Area::activo()->get();
        $cargos = Cargo::activo()->get();

        // Suggestions para typeahead (datalist)
        $suggestions = Trabajador::limit(200)->get()->map(function ($t) {
            return $t->nombre_completo;
        })->toArray();

        return view('trabajadores.index', compact('trabajadores', 'areas', 'cargos', 'suggestions'));
    }

    public function create()
    {
        $areas = Area::activo()->get();
        $cargos = Cargo::activo()->get();
        return view('trabajadores.create', compact('areas', 'cargos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'regex:/^[\pL\sáéíóúÁÉÍÓÚñÑ]+$/u'],
            'ap_paterno' => ['required', 'string', 'max:255', 'regex:/^[\pL\sáéíóúÁÉÍÓÚñÑ]+$/u'],
            'ap_materno' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\sáéíóúÁÉÍÓÚñÑ]+$/u'],
            'ci' => 'required|string|unique:trabajadors,ci',
            'email' => 'required|email|unique:trabajadors,email|unique:users,email',
            'celular' => 'nullable|string|max:20|unique:trabajadors,celular|regex:/^[0-9+\\s-]{6,20}$/',
            'fecha_nacimiento' => 'nullable|date',
            'pin' => 'nullable|string|max:20|unique:trabajadors,pin',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'area_id' => 'required|exists:areas,id',
            'cargo_id' => 'required|exists:cargos,id',
            'rol' => 'required|in:trabajador,tecnico,administrador-area,administrador-principal',
            'turno' => 'required|in:mañana,tarde,noche',
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'ap_paterno.regex' => 'El apellido paterno solo puede contener letras y espacios.',
            'ap_materno.regex' => 'El apellido materno solo puede contener letras y espacios.',
            'celular.regex' => 'El celular solo puede contener números, espacios, + y -.',
            'foto_perfil.image' => 'La foto debe ser una imagen válida.',
            'foto_perfil.mimes' => 'La foto debe ser jpeg, png, jpg, gif o webp.',
            'foto_perfil.max' => 'La foto no debe superar 2MB.',
        ]);

        // Validación de edad mínima (18 años)
        if ($request->filled('fecha_nacimiento')) {
            try {
                $fechaNacimiento = Carbon::parse($request->input('fecha_nacimiento'));
                $edad = $fechaNacimiento->age;

                if ($edad < 18) {
                    return redirect()->back()->withInput()->withErrors([
                        'fecha_nacimiento' => 'No se puede registrar trabajadores menores de 18 años. La edad mínima requerida es 18 años.'
                    ]);
                }

                // Validación adicional: no permitir fechas futuras
                if ($fechaNacimiento->isFuture()) {
                    return redirect()->back()->withInput()->withErrors([
                        'fecha_nacimiento' => 'La fecha de nacimiento no puede ser una fecha futura.'
                    ]);
                }
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors([
                    'fecha_nacimiento' => 'La fecha de nacimiento proporcionada no es válida. Use el formato AAAA-MM-DD.'
                ]);
            }
        }
        
        try {
            DB::beginTransaction();
            
            $data = $request->only([
                'nombre', 'ap_paterno', 'ap_materno', 'ci', 'email', 
                'celular', 'fecha_nacimiento', 'area_id', 'cargo_id', 'turno', 'pin'
            ]);

            // Subir foto si se adjuntó
            if ($request->hasFile('foto_perfil')) {
                $path = $request->file('foto_perfil')->store('trabajadores/fotos', 'public');
                $data['foto_perfil'] = $path;
            }

            $trabajador = Trabajador::create($data + ['activo' => true]);

            $password = Str::random(12);

            $user = User::create([
                'name' => $trabajador->nombre_completo,
                'email' => $trabajador->email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);

            // Asegurar que el rol exista antes de asignarlo
            $roleName = $request->rol;
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            // Asignar el rol al usuario
            $user->assignRole($roleName);
            
            // Limpiar el cache de permisos para que se apliquen inmediatamente
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $user->refresh();

            // Link the created user to the trabajador record
            $trabajador->user_id = $user->id;
            $trabajador->save();

            DB::commit();

            // Intentar enviar correo electrónico (fuera de la transacción)
            try {
                Mail::to($trabajador->email)->send(new CredencialesTrabajadorMail($trabajador, $password));
                Log::info("Correo de credenciales enviado a: {$trabajador->email}");
            } catch (\Exception $mailException) {
                Log::warning("No se pudo enviar correo a {$trabajador->email}: " . $mailException->getMessage());
                // No fallar la creación del trabajador si el correo falla
            }

            Log::info("Trabajador creado: {$trabajador->nombre_completo} (ID: {$trabajador->id}) con rol {$request->rol}");

            return redirect()->route('trabajadores.index')
                ->with('success', 'Trabajador agregado correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear trabajador: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['password'])
            ]);
            return redirect()->back()->withInput()->with('error', 'No se pudo crear el trabajador. Verifica datos únicos (CI, email, celular) e intenta nuevamente.');
        }
    }

    public function edit(Trabajador $trabajador)
    {
        $areas = Area::activo()->get();
        $cargos = Cargo::activo()->get();
        return view('trabajadores.edit', compact('trabajador', 'areas', 'cargos'));
    }

    public function update(Request $request, Trabajador $trabajador)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'regex:/^[\pL\sáéíóúÁÉÍÓÚñÑ]+$/u'],
            'ap_paterno' => ['required', 'string', 'max:255', 'regex:/^[\pL\sáéíóúÁÉÍÓÚñÑ]+$/u'],
            'ap_materno' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\sáéíóúÁÉÍÓÚñÑ]+$/u'],
            'ci' => 'required|string|unique:trabajadors,ci,' . $trabajador->id,
            'email' => 'required|email|unique:trabajadors,email,' . $trabajador->id . '|unique:users,email,' . ($trabajador->user_id ?? 'null'),
            'celular' => 'nullable|string|max:20|unique:trabajadors,celular,' . $trabajador->id . '|regex:/^[0-9+\\s-]{6,20}$/',
            'fecha_nacimiento' => 'nullable|date',
            'pin' => 'nullable|string|max:20|unique:trabajadors,pin,' . $trabajador->id,
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'area_id' => 'required|exists:areas,id',
            'cargo_id' => 'required|exists:cargos,id',
            'turno' => 'required|in:mañana,tarde,noche',
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'ap_paterno.regex' => 'El apellido paterno solo puede contener letras y espacios.',
            'ap_materno.regex' => 'El apellido materno solo puede contener letras y espacios.',
            'celular.regex' => 'El celular solo puede contener números, espacios, + y -.',
            'foto_perfil.image' => 'La foto debe ser una imagen válida.',
            'foto_perfil.mimes' => 'La foto debe ser jpeg, png, jpg, gif o webp.',
            'foto_perfil.max' => 'La foto no debe superar 2MB.',
        ]);

        // Validación de edad mínima (18 años) si se provee fecha
        if ($request->filled('fecha_nacimiento')) {
            try {
                $fechaNacimiento = Carbon::parse($request->input('fecha_nacimiento'));
                $edad = $fechaNacimiento->age;

                if ($edad < 18) {
                    return redirect()->back()->withInput()->withErrors([
                        'fecha_nacimiento' => 'No se puede registrar trabajadores menores de 18 años. La edad mínima requerida es 18 años.'
                    ]);
                }

                // Validación adicional: no permitir fechas futuras
                if ($fechaNacimiento->isFuture()) {
                    return redirect()->back()->withInput()->withErrors([
                        'fecha_nacimiento' => 'La fecha de nacimiento no puede ser una fecha futura.'
                    ]);
                }
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->withErrors([
                    'fecha_nacimiento' => 'La fecha de nacimiento proporcionada no es válida. Use el formato AAAA-MM-DD.'
                ]);
            }
        }
        try {
            $data = $request->except(['foto_perfil', '_token', '_method']);

            if ($request->hasFile('foto_perfil')) {
                $path = $request->file('foto_perfil')->store('trabajadores/fotos', 'public');
                $data['foto_perfil'] = $path;
            }

            $trabajador->update($data);
            
            Log::info("Trabajador actualizado: {$trabajador->nombre_completo} (ID: {$trabajador->id})");

            return redirect()->route('trabajadores.index')->with('success', 'Trabajador actualizado correctamente');
        } catch (\Exception $e) {
            Log::error("Error al actualizar trabajador: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar el trabajador. Revisa datos únicos (CI, email, celular).');
        }
    }

    public function destroy(Trabajador $trabajador)
    {
        $trabajador->activo = !$trabajador->activo;
        $trabajador->save();

        $estado = $trabajador->activo ? 'reactivado' : 'desactivado';
        
        Log::info("Trabajador {$estado}: {$trabajador->nombre_completo} (ID: {$trabajador->id})");

        return redirect()->route('trabajadores.index')->with('success', "Trabajador {$estado} correctamente");
    }

    // Registrar ingreso manual desde UI (o check-in)
    public function registrarIngreso(Request $request, Trabajador $trabajador)
    {
        $request->validate([
            'area_id' => 'nullable|exists:areas,id',
            'observacion' => 'nullable|string|max:200',
        ]);

        // Si no se proporciona area_id, usar el área del trabajador
        $areaId = $request->input('area_id') ?: $trabajador->area_id;

        $ingresosCount = Ingreso::where('trabajador_id', $trabajador->id)->where('tipo', 'ingreso')->count();
        $salidasCount = Ingreso::where('trabajador_id', $trabajador->id)->where('tipo', 'salida')->count();

        if ($ingresosCount > $salidasCount) {
            $message = 'El trabajador ya tiene un ingreso registrado sin una salida previa.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->back()->with('error', $message);
        }

        $turnoActual = $this->determinarTurno();
        $trabajador->turno = $turnoActual;
        $trabajador->save();

        try {
            Ingreso::create([
                'trabajador_id' => $trabajador->id,
                'area_id' => $areaId,
                'observacion' => $request->input('observacion'),
                'tipo' => 'ingreso',
                'registrado_en' => now(),
            ]);

            Log::info("Ingreso registrado para trabajador ID {$trabajador->id} en turno {$turnoActual} por un administrador.");
            $message = 'Ingreso registrado correctamente. Turno: ' . ucfirst($turnoActual);

            return $request->ajax()
                ? response()->json(['success' => true, 'message' => $message])
                : redirect()->route('dashboard')->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Error al registrar ingreso para trabajador ID {$trabajador->id}: " . $e->getMessage());
            $message = 'Ocurrió un error inesperado al registrar el ingreso.';

            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 500)
                : redirect()->back()->with('error', $message);
        }
    }

    // Registrar salida manual (check-out)
    public function registrarSalida(Request $request, Trabajador $trabajador)
    {
        $request->validate([
            'area_id' => 'nullable|exists:areas,id',
            'observacion' => 'nullable|string|max:200',
        ]);

        // Si no se proporciona area_id, usar el área del trabajador
        $areaId = $request->input('area_id') ?: $trabajador->area_id;

        $ultimoRegistro = Ingreso::where('trabajador_id', $trabajador->id)->latest('registrado_en')->first();

        if (!$ultimoRegistro || $ultimoRegistro->tipo !== 'ingreso') {
            $message = 'El trabajador no tiene un ingreso activo para registrar una salida.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->back()->with('error', $message);
        }

        if ($ultimoRegistro->area_id != $areaId || $ultimoRegistro->observacion != $request->input('observacion')) {
            $areaNombre = $ultimoRegistro->area ? $ultimoRegistro->area->nombre : 'N/A';
            $observacion = $ultimoRegistro->observacion ?? 'N/A';
            $message = "El área y observación de salida deben coincidir con los del ingreso (Área: {$areaNombre}, Observación: {$observacion}).";

            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->back()->with('error', $message);
        }

        try {
            Ingreso::create([
                'trabajador_id' => $trabajador->id,
                'area_id' => $areaId,
                'observacion' => $request->input('observacion'),
                'tipo' => 'salida',
                'registrado_en' => now(),
            ]);

            Log::info("Salida registrada para trabajador ID {$trabajador->id} por un administrador.");
            $message = 'Salida registrada correctamente para el trabajador.';

            return $request->ajax()
                ? response()->json(['success' => true, 'message' => $message])
                : redirect()->route('dashboard')->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Error al registrar salida para trabajador ID {$trabajador->id}: " . $e->getMessage());
            $message = 'Ocurrió un error inesperado al registrar la salida.';
            
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 500)
                : redirect()->back()->with('error', $message);
        }
    }

    // Reportar condición / incidente por parte del trabajador
    public function reportarCondicion(Request $request, Trabajador $trabajador)
    {
        $request->validate([
            'descripcion' => 'required|string|max:2000',
            'gravedad' => 'required|in:baja,media,alta,critica',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        try {
            $incidente = \App\Models\Incidente::create([
                'trabajador_id' => $trabajador->id,
                'area_id' => $request->input('area_id'),
                'descripcion' => $request->input('descripcion'),
                'gravedad' => $request->input('gravedad'),
                'estado' => 'pendiente',
                'fecha_reporte' => now(),
            ]);

            Log::info("Incidente reportado ID {$incidente->id} por trabajador ID {$trabajador->id}");

            return redirect()->route('dashboard')->with('success', 'Incidente reportado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al reportar incidente: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo reportar la condición.');
        }
    }

    // Disparo manual de SOS cuando el sensor no lo detecta
    public function enviarSos(Trabajador $trabajador)
    {
        $abierto = Incidente::where('trabajador_id', $trabajador->id)
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->where('descripcion', 'like', 'SOS%')
            ->latest()
            ->first();

        if ($abierto) {
            return back()->with('info', 'Ya existe un SOS abierto para este trabajador.');
        }

        try {
            $incidente = Incidente::create([
                'trabajador_id' => $trabajador->id,
                'area_id' => $trabajador->area_id,
                'descripcion' => 'SOS manual: accidente reportado por supervisor',
                'gravedad' => 'critica',
                'estado' => 'pendiente',
                'fecha_reporte' => now(),
            ]);

            Log::warning("SOS manual generado para trabajador {$trabajador->id} incidente {$incidente->id}");

            return back()->with('success', 'SOS enviado. Se registró un incidente crítico.');
        } catch (\Exception $e) {
            Log::error('Error al generar SOS: ' . $e->getMessage());
            return back()->with('error', 'No se pudo enviar el SOS.');
        }
    }

    // Obtener historial del día para un trabajador
    public function historialHoy(Trabajador $trabajador)
    {
        $historial = Ingreso::with('area')
            ->where('trabajador_id', $trabajador->id)
            ->whereDate('registrado_en', today())
            ->orderBy('registrado_en', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'historial' => $historial
        ]);
    }

    // Función para determinar el turno basado en la hora actual
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

    // Registrar ingreso por el trabajador autenticado (para sí mismo)
    public function registrarMiIngreso(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->trabajador) {
            return redirect()->route('login')->with('error', 'No se encontró un perfil de trabajador asociado a tu usuario.');
        }
        
        $trabajador = $user->trabajador;

        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'observacion' => 'nullable|string|max:100',
        ]);

        $ultimoRegistro = Ingreso::where('trabajador_id', $trabajador->id)->latest('registrado_en')->first();

        // Si ya hay un ingreso activo (último registro fue ingreso), no permitir otro
        if ($ultimoRegistro && $ultimoRegistro->tipo === 'ingreso') {
            $message = 'Ya tienes un ingreso activo. Registra tu salida primero.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->back()->with('error', $message);
        }

        try {
            Ingreso::create([
                'trabajador_id' => $trabajador->id,
                'area_id' => $request->input('area_id'),
                'observacion' => $request->input('observacion'),
                'tipo' => 'ingreso',
                'registrado_en' => now(),
            ]);

            Log::info("[MiIngreso] Trabajador ID {$trabajador->id} registró su ingreso.");
            $message = 'Ingreso registrado correctamente.';

            return $request->ajax()
                ? response()->json(['success' => true, 'message' => $message])
                : redirect()->route('dashboard')->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Error al registrar mi ingreso para trabajador ID {$trabajador->id}: " . $e->getMessage());
            $message = 'Ocurrió un error inesperado al intentar registrar el ingreso.';
            
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 500)
                : redirect()->back()->with('error', $message);
        }
    }

    // Registrar salida por el trabajador autenticado (para sí mismo)
    public function registrarMiSalida(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->trabajador) {
            return redirect()->route('login')->with('error', 'No se encontró un perfil de trabajador asociado a tu usuario.');
        }
        
        $trabajador = $user->trabajador;

        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'observacion' => 'nullable|string|max:100',
        ]);

        $ultimoRegistro = Ingreso::where('trabajador_id', $trabajador->id)->latest('registrado_en')->first();

        // Si no hay registro o el último no fue un ingreso, no se puede registrar salida
        if (!$ultimoRegistro || $ultimoRegistro->tipo !== 'ingreso') {
            $message = 'No tienes un ingreso activo para registrar una salida.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->back()->with('error', $message);
        }

        // Si el área o observacion no coinciden con el último ingreso
        if ($ultimoRegistro->area_id != $request->input('area_id') || $ultimoRegistro->observacion != $request->input('observacion')) {
            $areaNombre = $ultimoRegistro->area ? $ultimoRegistro->area->nombre : 'N/A';
            $observacion = $ultimoRegistro->observacion ?? 'N/A';
            $message = "El área y observación de salida deben coincidir con los del ingreso (Área: {$areaNombre}, Observación: {$observacion}).";
            
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : redirect()->back()->with('error', $message);
        }

        try {
            Ingreso::create([
                'trabajador_id' => $trabajador->id,
                'area_id' => $request->input('area_id'),
                'observacion' => $request->input('observacion'),
                'tipo' => 'salida',
                'registrado_en' => now(),
            ]);

            Log::info("[MiSalida] Trabajador ID {$trabajador->id} registró su salida.");
            $message = 'Salida registrada correctamente.';

            return $request->ajax()
                ? response()->json(['success' => true, 'message' => $message])
                : redirect()->route('dashboard')->with('success', $message);

        } catch (\Exception $e) {
            Log::error("Error al registrar mi salida para trabajador ID {$trabajador->id}: " . $e->getMessage());
            $message = 'Ocurrió un error inesperado al intentar registrar la salida.';
            
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 500)
                : redirect()->back()->with('error', $message);
        }
    }

    // Reportar condición por trabajador autenticado
    public function reportarMiCondicion(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $trabajador = $user->trabajador;
        if (!$trabajador) {
            return redirect()->back()->with('error', 'No se encontró perfil de trabajador para su usuario.');
        }

        $request->validate([
            'descripcion' => 'required|string|max:2000',
            'gravedad' => 'required|in:baja,media,alta,critica',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        try {
            $incidente = \App\Models\Incidente::create([
                'trabajador_id' => $trabajador->id,
                'area_id' => $request->input('area_id'),
                'descripcion' => $request->input('descripcion'),
                'gravedad' => $request->input('gravedad'),
                'estado' => 'pendiente',
                'fecha_reporte' => now(),
            ]);

            Log::info("[MiReporte] Incidente {$incidente->id} reportado por trabajador {$trabajador->id}");

            return redirect()->route('dashboard')->with('success', 'Incidente reportado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al reportar incidente (mi): ' . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo reportar la condición.');
        }
    }

    // Mostrar formulario de reporte para el trabajador autenticado
    public function showReportForm()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $trabajador = $user->trabajador;
        if (!$trabajador) {
            return redirect()->back()->with('error', 'No se encontró perfil de trabajador para su usuario.');
        }

        $areas = Area::activo()->get();
        return view('mi.reportar', compact('areas'));
    }

    // Panel personal del trabajador
    public function miDashboard()
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $trabajador = $user->trabajador;
        if (!$trabajador) return redirect()->back()->with('error', 'No se encontró perfil de trabajador para su usuario.');

        $areas = Area::activo()->get();
        return view('mi.index', compact('trabajador', 'areas'));
    }

    // Mostrar reportes personales (ingresos/salidas e incidentes)
    public function miReports()
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $trabajador = $user->trabajador;
        if (!$trabajador) return redirect()->back()->with('error', 'No se encontró perfil de trabajador para su usuario.');

        $request = request();

        // filtros
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $areaId = $request->input('area_id');
        $type = $request->input('type', 'all'); // all | ingresos | incidentes
        $gravedad = $request->input('gravedad');

        $ingresosQuery = Ingreso::where('trabajador_id', $trabajador->id)->orderBy('registrado_en', 'desc');
        $incidentesQuery = Incidente::where('trabajador_id', $trabajador->id)->orderBy('created_at', 'desc');

        if ($dateFrom) {
            try { $ingresosQuery->where('registrado_en', '>=', $dateFrom); } catch (\Exception $e) {}
            try { $incidentesQuery->where('created_at', '>=', $dateFrom); } catch (\Exception $e) {}
        }
        if ($dateTo) {
            try { $ingresosQuery->where('registrado_en', '<=', $dateTo . ' 23:59:59'); } catch (\Exception $e) {}
            try { $incidentesQuery->where('created_at', '<=', $dateTo . ' 23:59:59'); } catch (\Exception $e) {}
        }

        // filtro por área (aplica a ingresos e incidentes)
        if ($areaId) {
            try { $ingresosQuery->where('area_id', $areaId); } catch (\Exception $e) {}
            try { $incidentesQuery->where('area_id', $areaId); } catch (\Exception $e) {}
        }

        // filtro por gravedad para incidentes
        if ($gravedad) {
            try { $incidentesQuery->where('gravedad', $gravedad); } catch (\Exception $e) {}
        }

        // paginadores separados (evitan que la paginación de uno afecte al otro)
        $ingresos = $ingresosQuery->paginate(10, ['*'], 'ingresos_page')->withQueryString();
        $incidentes = $incidentesQuery->paginate(10, ['*'], 'incidentes_page')->withQueryString();

        // Exportar a PDF si se solicita (posibilidad de exportar solo ingresos o solo incidentes)
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $only = $request->input('only'); // ingresos | incidentes | null
            $rowsIngreso = $ingresosQuery->get();
            $rowsIncidente = $incidentesQuery->get();

            // render view para PDF
            $viewHtml = view('mi.reportes_pdf', compact('trabajador', 'rowsIngreso', 'rowsIncidente', 'dateFrom', 'dateTo', 'only'))->render();

            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($viewHtml)->setPaper('letter');
                return $pdf->download('mis_reportes_' . date('Ymd_His') . '.pdf');
            }

            if (class_exists(\Dompdf\Dompdf::class)) {
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($viewHtml);
                $dompdf->setPaper('letter');
                $dompdf->render();
                return response($dompdf->output(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="mis_reportes_' . date('Ymd_His') . '.pdf"',
                ]);
            }

            return response($viewHtml, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'attachment; filename="mis_reportes_' . date('Ymd_His') . '.html"',
            ]);
        }

        // Areas para filtro
        $areas = Area::activo()->get();

        return view('mi.reportes', compact('trabajador', 'ingresos', 'incidentes', 'dateFrom', 'dateTo', 'type', 'areas', 'gravedad', 'areaId'));
    }

    // Import feature removed per user request

    // Mostrar formulario de ingreso para trabajador autenticado
    public function showIngresoForm()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $trabajador = $user->trabajador;
        if (!$trabajador) {
            return redirect()->back()->with('error', 'No se encontró perfil de trabajador para su usuario.');
        }

        $areas = Area::activo()->get();
        return view('mi.ingreso', compact('areas'));
    }

    // Mostrar formulario de salida para trabajador autenticado
    public function showSalidaForm()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $trabajador = $user->trabajador;
        if (!$trabajador) {
            return redirect()->back()->with('error', 'No se encontró perfil de trabajador para su usuario.');
        }

        $areas = Area::activo()->get();
        return view('mi.salida', compact('areas'));
    }

    public function clasificacion(Request $request)
    {
        $query = Trabajador::with(['area', 'cargo'])->where('activo', true);

        // Filtros
        if ($request->filled('turno')) {
            $query->where('turno', $request->input('turno'));
        }

        if ($request->filled('cargo_id')) {
            $query->where('cargo_id', $request->input('cargo_id'));
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->input('area_id'));
        }

        if ($request->filled('observacion')) {
            $observacion = $request->input('observacion');
            $query->whereHas('ingresos', function ($q) use ($observacion) {
                $q->where('observacion', $observacion)->where('tipo', 'ingreso');
            });
        }

        $trabajadores = $query->orderBy('ap_paterno')->orderBy('nombre')->get();

        // Obtener datos para los filtros
        $observaciones = Ingreso::whereNotNull('observacion')
            ->where('observacion', '!=', '')
            ->distinct()
            ->orderBy('observacion')
            ->pluck('observacion');
            
        $cargos = Cargo::activo()->orderBy('nombre')->get();
        $areas = Area::activo()->orderBy('nombre')->get();

        return view('trabajadores.clasificacion', compact('trabajadores', 'observaciones', 'cargos', 'areas'));
    }
}