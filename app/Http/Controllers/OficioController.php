<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oficio;
use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OficioController extends Controller
{
    // 1. Este método SOLO carga el Dashboard (Métricas)
    public function principal()
    {
        $user = Auth::user();
        $totalOficios = Oficio::count();
        $pendientesArea = DB::table('area_oficio')->whereNull('user_id')->count();
        $misTareas = DB::table('area_oficio')->where('user_id', $user->id)->count();

        // No enviamos $oficios aquí, porque el Dashboard no necesita esa tabla
        return view('principal', compact('totalOficios', 'pendientesArea', 'misTareas'));
    }

    // 2. Este método carga la tabla de correspondencia recibida (Entrada de Correspondencia)
    public function index(Request $request)
    {
        $user = Auth::user();

        // Seguridad: Solo admin, correspondencia, o jefe_area de Gestión Institucional (area_id = 2)
        if ($user->role !== 'admin' && $user->role !== 'correspondencia' && !($user->role == 'jefe_area' && $user->area_id == 2)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $query = Oficio::where('tipo_correspondencia', '!=', 'Interna');

        // Búsqueda por número, remitente o asunto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_oficio', 'like', "%{$search}%")
                    ->orWhere('remitente', 'like', "%{$search}%")
                    ->orWhere('asunto', 'like', "%{$search}%");
            });
        }

        // Filtro por estatus. Por defecto es 'Pendiente' (Pendiente de Turnar)
        $estatus = $request->input('estatus', 'Pendiente');
        if ($estatus !== 'Todos') {
            if ($estatus === 'Pendiente') {
                $query->where(function ($q) {
                    $q->whereNull('estatus')
                        ->orWhereNotIn('estatus', ['Turnado', 'En Proceso', 'Atendido', 'Solventado']);
                });
            } else {
                $query->where('estatus', $estatus);
            }
        }

        // Ordenamos por número de oficio de forma descendente (año y consecutivo)
        $oficios = $query->orderByRaw("IF(numero_oficio LIKE '%/%', CAST(SUBSTRING_INDEX(numero_oficio, '/', -1) AS UNSIGNED), YEAR(fecha_recepcion)) DESC")
            ->orderByRaw("CAST(SUBSTRING_INDEX(numero_oficio, '/', 1) AS UNSIGNED) DESC")
            ->paginate(10);

        return view('oficios.index', compact('oficios'));
    }

    public function create()
    {
        $currentYear = date('Y');
        $ultimoNumero = Oficio::withTrashed()
            ->where('numero_oficio', 'like', "%/$currentYear")
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(numero_oficio, '/', 1) AS UNSIGNED)) as max_val")
            ->value('max_val');
        $siguienteConsecutivoVal = ($ultimoNumero ?? 0) + 1;
        $siguienteConsecutivo = $siguienteConsecutivoVal . '/' . $currentYear;
        return view('oficios.create', compact('siguienteConsecutivo'));
    }

    public function store(Request $request)
    {
        $currentYear = date('Y');
        $ultimoNumero = Oficio::withTrashed()
            ->where('numero_oficio', 'like', "%/$currentYear")
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(numero_oficio, '/', 1) AS UNSIGNED)) as max_val")
            ->value('max_val');
        $siguienteConsecutivo = ($ultimoNumero ?? 0) + 1;
        $request->merge(['numero_oficio' => (string) ($siguienteConsecutivo . '/' . $currentYear)]);

        $request->validate([
            'numero_oficio' => 'required|string|max:255',
            'remitente' => 'required|string|max:255',
            'municipio' => 'required|string|max:255',
            'asunto' => 'required|string',
            'fecha_recepcion' => 'required|date',
            'prioridad' => 'required|string',
            'numero_oficio_dependencia' => 'required|string|max:255',
            'fecha_limite' => 'nullable|date',
            'localidad' => 'required|string|max:255',
            'observaciones' => 'nullable|string',
            'archivo_pdf' => 'required|mimes:pdf|max:10240',
            'tipo_correspondencia' => 'nullable|string',
        ]);

        $data = $request->all();

        if (!$request->has('tipo_correspondencia') || $request->input('tipo_correspondencia') === 'Interna') {
            $data['tipo_correspondencia'] = 'Externa';
        }

        if ($request->hasFile('archivo_pdf')) {
            $path = $request->file('archivo_pdf')->store('oficios_pdf', 'public');
            $data['pdf_path'] = $path;
        }

        $oficio = Oficio::create($data);

        \App\Models\OficioHistorial::registrar(
            $oficio->id,
            'Registrado',
            "El personal de correspondencia registró el oficio externo: {$oficio->numero_oficio} (Origen/Oficio: {$oficio->numero_oficio_dependencia})."
        );

        return redirect()->route('principal')->with('success', 'Oficio y PDF registrados correctamente.');
    }

    public function vistaTurnado(Oficio $oficio)
    {
        // Cargamos lo necesario para que el Admin pueda turnar
        $areasDisponibles = \App\Models\Area::all();
        return view('oficios.turnar', compact('oficio', 'areasDisponibles'));
    }

    public function show(Request $request, Oficio $oficio)
    {
        $user = Auth::user();
        $oficio->load('areas');

        // Determinamos qué turnos mostrar según el rol del usuario de forma automática
        if (in_array($user->role, ['admin', 'recepcionista', 'correspondencia']) || ($user->role == 'jefe_area' && $user->area_id == 2)) {
            $turnosParaMostrar = $oficio->areas;
        } elseif ($user->role == 'jefe_area' || $user->role == 'secretaria_area') {
            $turnosParaMostrar = $oficio->areas->where('id', $user->area_id);
        } elseif ($user->role === 'subdirector' || ($user->role === 'admin' && $user->subarea_id !== null)) {
            // El subdirector (o admin con subdirección) ve los turnos de su área si tiene una subarea_oficio asignada
            $turnosParaMostrar = $oficio->areas->where('id', $user->area_id)->filter(function ($area) use ($user) {
                // Ve el turno si tiene una subarea_oficio asignada a su subdirección
                $hasSubareaAssignment = \App\Models\SubareaOficio::where('area_oficio_id', $area->pivot->id)
                    ->where('subarea_id', $user->subarea_id)
                    ->exists();
                return $hasSubareaAssignment;
            });
        } else {
            // El personal operativo solo ve los turnos donde tiene una subarea_oficio asignada directamente a él
            $turnosParaMostrar = $oficio->areas->filter(function ($area) use ($user) {
                return \App\Models\SubareaOficio::where('area_oficio_id', $area->pivot->id)
                    ->where('user_id', $user->id)
                    ->exists();
            });
        }

        $areasAsignadasIds = $oficio->areas->pluck('id')->toArray();
        $areasDisponibles = Area::whereNotIn('id', $areasAsignadasIds)->get();

        // Cargamos las subdirecciones asignadas (subarea_oficio) por cada area_oficio
        $subareaOficiosPorArea = [];
        $subareasDisponiblesPorArea = [];
        $personalDirectoDisponiblesPorArea = [];
        $personalPorSubarea = [];
        foreach ($oficio->areas as $areaTurnada) {
            $pivoteId = $areaTurnada->pivot->id;
            $subareaOficios = \App\Models\SubareaOficio::where('area_oficio_id', $pivoteId)
                ->with(['subarea', 'user'])
                ->get();
            $subareaOficiosPorArea[$pivoteId] = $subareaOficios;

            // Subdirecciones disponibles para asignar (las que aún no están asignadas)
            $subareasAsignadasIds = $subareaOficios->pluck('subarea_id')->toArray();
            $subareasDisponiblesPorArea[$pivoteId] = \App\Models\Subarea::where('area_id', $areaTurnada->id)
                ->whereNotIn('id', $subareasAsignadasIds)
                ->get();

            // Personal directo (sin subdirección) de esta Dirección (incluyendo secretarias de área)
            $assignedUserIds = $subareaOficios->whereNull('subarea_id')->pluck('user_id')->toArray();
            $queryDirecto = User::where('area_id', $areaTurnada->id)
                ->whereIn('role', ['user', 'secretaria_area'])
                ->whereNull('subarea_id')
                ->orderBy('name', 'asc');
            if (!empty($assignedUserIds)) {
                $queryDirecto->whereNotIn('id', $assignedUserIds);
            }
            $personalDirectoDisponiblesPorArea[$pivoteId] = $queryDirecto->get();

            // Personal operativo por subdirección para delegación del subdirector (incluyendo secretarias de área)
            foreach ($subareaOficios as $so) {
                $personalPorSubarea[$so->id] = User::where('area_id', $areaTurnada->id)
                    ->where('subarea_id', $so->subarea_id)
                    ->where(function ($q) use ($so) {
                        $q->whereIn('role', ['user', 'secretaria_area', 'subdirector'])
                            ->orWhere(function ($adminQ) use ($so) {
                                $adminQ->where('role', 'admin')
                                    ->where('subarea_id', $so->subarea_id);
                            });
                    })
                    ->get();
            }
        }

        // Mantenemos personalPorArea para retrocompatibilidad con áreas sin subdirecciones
        $personalPorArea = [];
        foreach ($oficio->areas as $areaTurnada) {
            $hasSubareas = \App\Models\Subarea::where('area_id', $areaTurnada->id)->exists();
            if (!$hasSubareas) {
                $query = User::where('area_id', $areaTurnada->id)
                    ->where(function ($q) {
                        $q->where('role', 'jefe_area')
                            ->orWhere('role', 'user')
                            ->orWhere('role', 'secretaria_area')
                            ->orWhere('role', 'admin');
                    });
                $personalPorArea[$areaTurnada->id] = $query->get();
            } else {
                $personalPorArea[$areaTurnada->id] = collect();
            }
        }

        $idsTurnos = $oficio->areas()->pluck('area_oficio.id');
        $respuestas = \App\Models\OficioRespuesta::whereIn('area_oficio_id', $idsTurnos)
            ->orderBy('created_at', 'desc')
            ->get();

        // Determinamos el modo de visualización
        if ($request->has('mode')) {
            $mode = $request->input('mode');
        } else {
            $mode = in_array($user->role, ['admin', 'recepcionista', 'correspondencia']) ? 'recepcion' : (in_array($user->role, ['jefe_area', 'secretaria_area']) ? 'gestion' : 'operativo');
        }

        $historialQuery = \App\Models\OficioHistorial::where('oficio_id', $oficio->id)
            ->with(['user', 'area', 'subarea'])
            ->orderBy('created_at', 'asc');

        if (!in_array($user->role, ['admin', 'correspondencia', 'recepcionista'])) {
            $historialQuery->where(function($q) use ($user) {
                $q->whereNull('area_id')
                  ->orWhere('area_id', $user->area_id);
            });
        }

        $historial = $historialQuery->get();

        return view('oficios.show', compact(
            'oficio',
            'areasDisponibles',
            'personalPorArea',
            'turnosParaMostrar',
            'mode',
            'respuestas',
            'subareaOficiosPorArea',
            'subareasDisponiblesPorArea',
            'personalPorSubarea',
            'personalDirectoDisponiblesPorArea',
            'historial'
        ));
    }

    public function turnar(Request $request, Oficio $oficio)
    {
        $request->validate([
            'areas' => 'required|array',
            'instrucciones' => 'required|array',
        ]);

        foreach ($request->areas as $index => $area_id) {
            if ($area_id) {
                $instruccion = $request->instrucciones[$index];

                // Usamos attach para añadir sin borrar lo anterior
                $oficio->areas()->attach($area_id, [
                    'instruccion' => $instruccion,
                    'estatus' => 'Turnado'
                ]);

                $area = \App\Models\Area::find($area_id);
                \App\Models\OficioHistorial::registrar(
                    $oficio->id,
                    'Turnado',
                    "Se turnó el oficio a la " . ($area ? $area->name : "Dirección ID {$area_id}") . " con la instrucción: \"{$instruccion}\".",
                    $area_id
                );

                // Notificar a jefe_area del área turnada
                $jefes = User::where('area_id', $area_id)
                    ->where('role', 'jefe_area')
                    ->get();

                foreach ($jefes as $jefe) {
                    if (!$jefe->recibir_correos) {
                        continue;
                    }
                    $data = [
                        'oficio' => $oficio,
                        'instruccion' => $instruccion,
                        'usuario' => $jefe,
                    ];

                    try {
                        Mail::send('emails.oficio_turnado', $data, function ($message) use ($jefe, $oficio) {
                            $message->to($jefe->email)
                                ->subject('[' . $oficio->prioridad . '] Nuevo Oficio Turnado - No. ' . $oficio->numero_oficio);
                        });
                    } catch (\Exception $e) {
                        Log::error("Error al enviar correo de oficio turnado a {$jefe->email}: " . $e->getMessage());
                    }
                }
            }
        }

        $oficio->update(['estatus' => 'Turnado']);
        return redirect()->route('oficios.show', $oficio)->with('success', 'Nuevas áreas añadidas correctamente.');
    }

    public function cancelarTurno(Request $request, $pivote_id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'correspondencia'])) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $request->validate([
            'motivo_cancelacion' => 'required|string',
        ]);

        DB::table('area_oficio')
            ->where('id', $pivote_id)
            ->update([
                'estatus' => 'Cancelado',
                'motivo_cancelacion' => $request->motivo_cancelacion,
                'updated_at' => now()
            ]);

        $pivot = DB::table('area_oficio')->where('id', $pivote_id)->first();
        if ($pivot) {
            $area = \App\Models\Area::find($pivot->area_id);
            \App\Models\OficioHistorial::registrar(
                $pivot->oficio_id,
                'Turno Cancelado',
                "Se canceló el turno asignado a la " . ($area ? $area->name : "Dirección ID {$pivot->area_id}") . ". Motivo: \"{$request->motivo_cancelacion}\".",
                $pivot->area_id
            );
        }

        return back()->with('success', 'El turno ha sido cancelado correctamente.');
    }

    /**
     * Asignar oficio a subdirecciones (crea registros en subarea_oficio).
     * Para áreas sin subdirecciones, asigna directamente al user_id en area_oficio.
     */
    public function asignar(Request $request, Oficio $oficio)
    {
        $currentUser = Auth::user();
        $pivoteId = $request->input('pivote_id');

        $areaOficio = DB::table('area_oficio')->where('id', $pivoteId)->first();
        if (!$areaOficio) {
            return back()->with('error', 'Registro de turno no encontrado.');
        }

        // --- Caso 1: Asignar a destinatarios vía checkboxes (subdirecciones, Director o personal directo) ---
        if ($request->has('subarea_ids')) {
            $request->validate([
                'subarea_ids' => 'required|array|min:1',
                'pivote_id' => 'required|exists:area_oficio,id',
            ]);

            // Generar folio interno de la dirección si no existe
            if (empty($areaOficio->folio_interno)) {
                $area = Area::find($areaOficio->area_id);
                if ($area) {
                    $currentYear = now()->year;
                    $ultimoConsecutivo = DB::table('area_oficio')
                        ->where('area_id', $area->id)
                        ->where('anio', $currentYear)
                        ->max('consecutivo');
                    $siguienteConsecutivo = $ultimoConsecutivo ? $ultimoConsecutivo + 1 : 1;
                    $prefijo = !empty($area->prefijo) ? $area->prefijo : 'OIC';
                    $folioInterno = $prefijo . '-INT-' . str_pad($siguienteConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;

                    DB::table('area_oficio')->where('id', $pivoteId)->update([
                        'folio_interno' => $folioInterno,
                        'consecutivo' => $siguienteConsecutivo,
                        'anio' => $currentYear,
                    ]);
                }
            }

            // Crear registros en subarea_oficio por cada subdirección seleccionada o Director
            foreach ($request->subarea_ids as $subareaId) {
                if ($subareaId === 'director') {
                    // Evitar duplicados
                    $exists = \App\Models\SubareaOficio::where('area_oficio_id', $pivoteId)
                        ->whereNull('subarea_id')
                        ->exists();
                    if ($exists) {
                        continue;
                    }

                    // Buscar al director titular de esta dirección
                    $director = User::where('area_id', $areaOficio->area_id)
                        ->where('role', 'jefe_area')
                        ->first();

                    $instruccion = $request->input('instruccion_director') ?: $areaOficio->instruccion;
                    \App\Models\SubareaOficio::create([
                        'area_oficio_id' => $pivoteId,
                        'subarea_id' => null,
                        'user_id' => $director ? $director->id : null,
                        'instruccion' => $instruccion,
                        'estatus' => 'Asignado',
                    ]);

                    \App\Models\OficioHistorial::registrar(
                        $oficio->id,
                        'Asignado',
                        "Se delegó la tarea de atención directa al Director titular con la instrucción: \"{$instruccion}\".",
                        $areaOficio->area_id
                    );

                    // Notificar al director por correo
                    if ($director && $director->recibir_correos) {
                        $folioInterno = DB::table('area_oficio')->where('id', $pivoteId)->value('folio_interno');
                        $data = [
                            'usuario' => $director,
                            'oficio' => $oficio,
                            'folio_interno' => $folioInterno,
                            'instruccion' => $instruccion,
                        ];
                        try {
                            Mail::send('emails.oficio_asignado', $data, function ($message) use ($director, $oficio, $folioInterno) {
                                $message->to($director->email)
                                    ->subject('[' . $oficio->prioridad . '] Oficio asignado a su atención - Folio: ' . ($folioInterno ?? $oficio->numero_oficio));
                            });
                        } catch (\Exception $e) {
                            Log::error("Error al enviar correo a director {$director->email}: " . $e->getMessage());
                        }
                    }
                } elseif (strpos($subareaId, 'user_') === 0) {
                    $userId = (int) str_replace('user_', '', $subareaId);

                    // Evitar duplicados
                    $exists = \App\Models\SubareaOficio::where('area_oficio_id', $pivoteId)
                        ->whereNull('subarea_id')
                        ->where('user_id', $userId)
                        ->exists();
                    if ($exists) {
                        continue;
                    }

                    $user = User::find($userId);
                    if (!$user || $user->area_id != $areaOficio->area_id) {
                        continue; // Seguridad: solo personal de la misma área
                    }

                    $instruccion = $request->input('instruccion_user_' . $userId) ?: $areaOficio->instruccion;
                    \App\Models\SubareaOficio::create([
                        'area_oficio_id' => $pivoteId,
                        'subarea_id' => null,
                        'user_id' => $userId,
                        'instruccion' => $instruccion,
                        'estatus' => 'Asignado',
                    ]);

                    \App\Models\OficioHistorial::registrar(
                        $oficio->id,
                        'Asignado',
                        "Se delegó la tarea al personal directo: {$user->name} con la instrucción: \"{$instruccion}\".",
                        $areaOficio->area_id
                    );

                    // Notificar al usuario por correo
                    if ($user && $user->recibir_correos) {
                        $folioInterno = DB::table('area_oficio')->where('id', $pivoteId)->value('folio_interno');
                        $data = [
                            'usuario' => $user,
                            'oficio' => $oficio,
                            'folio_interno' => $folioInterno,
                            'instruccion' => $instruccion,
                        ];
                        try {
                            Mail::send('emails.oficio_asignado', $data, function ($message) use ($user, $oficio, $folioInterno) {
                                $message->to($user->email)
                                    ->subject('[' . $oficio->prioridad . '] Oficio asignado a su atención - Folio: ' . ($folioInterno ?? $oficio->numero_oficio));
                            });
                        } catch (\Exception $e) {
                            Log::error("Error al enviar correo a usuario {$user->email}: " . $e->getMessage());
                        }
                    }
                } else {
                    $subarea = \App\Models\Subarea::find($subareaId);
                    if (!$subarea || $subarea->area_id != $areaOficio->area_id) {
                        continue; // Validación adicional: solo subdirecciones de la misma área
                    }

                    // Evitar duplicados
                    $exists = \App\Models\SubareaOficio::where('area_oficio_id', $pivoteId)
                        ->where('subarea_id', $subareaId)
                        ->exists();
                    if ($exists) {
                        continue;
                    }

                    // Buscar al subdirector titular de esta subdirección
                    $subdirector = User::where('subarea_id', $subareaId)
                        ->whereIn('role', ['subdirector', 'admin'])
                        ->first();

                    $instruccion = $request->input('instruccion_' . $subareaId) ?: $areaOficio->instruccion;
                    \App\Models\SubareaOficio::create([
                        'area_oficio_id' => $pivoteId,
                        'subarea_id' => $subareaId,
                        'user_id' => null, // El subdirector lo asignará después
                        'instruccion' => $instruccion,
                        'estatus' => 'Asignado',
                    ]);

                    \App\Models\OficioHistorial::registrar(
                        $oficio->id,
                        'Asignado',
                        "Se delegó la tarea a la " . ($subarea ? $subarea->name : "subdirección") . " con la instrucción: \"{$instruccion}\".",
                        $areaOficio->area_id,
                        $subareaId
                    );

                    // Notificar al subdirector por correo
                    if ($subdirector && $subdirector->recibir_correos) {
                        $folioInterno = DB::table('area_oficio')->where('id', $pivoteId)->value('folio_interno');
                        $data = [
                            'usuario' => $subdirector,
                            'oficio' => $oficio,
                            'folio_interno' => $folioInterno,
                            'instruccion' => $instruccion,
                        ];
                        try {
                            Mail::send('emails.oficio_asignado', $data, function ($message) use ($subdirector, $oficio, $folioInterno) {
                                $message->to($subdirector->email)
                                    ->subject('[' . $oficio->prioridad . '] Oficio asignado a su Subdirección - Folio: ' . ($folioInterno ?? $oficio->numero_oficio));
                            });
                        } catch (\Exception $e) {
                            Log::error("Error al enviar correo a subdirector {$subdirector->email}: " . $e->getMessage());
                        }
                    }
                }
            }

            // Actualizar estatus del area_oficio
            DB::table('area_oficio')->where('id', $pivoteId)->update(['estatus' => 'Asignado']);
            $oficio->update(['estatus' => 'En Proceso']);

            return redirect()->route('oficios.show', $oficio)->with('success', 'Oficio asignado a las subdirecciones seleccionadas.');
        }

        // --- Caso 2: Área SIN subdirecciones → asignar directamente al user_id (comportamiento original) ---
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pivote_id' => 'required|exists:area_oficio,id',
        ]);

        $assignedUser = User::findOrFail($request->user_id);

        $updateData = [
            'user_id' => $request->user_id,
            'estatus' => 'Asignado'
        ];

        if (empty($areaOficio->folio_interno)) {
            $area = Area::find($areaOficio->area_id);
            if ($area) {
                $currentYear = now()->year;
                $ultimoConsecutivo = DB::table('area_oficio')
                    ->where('area_id', $area->id)
                    ->where('anio', $currentYear)
                    ->max('consecutivo');
                $siguienteConsecutivo = $ultimoConsecutivo ? $ultimoConsecutivo + 1 : 1;
                $prefijo = !empty($area->prefijo) ? $area->prefijo : 'OIC';
                $folioInterno = $prefijo . '-INT-' . str_pad($siguienteConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;
                $updateData['folio_interno'] = $folioInterno;
                $updateData['consecutivo'] = $siguienteConsecutivo;
                $updateData['anio'] = $currentYear;
            }
        }

        DB::table('area_oficio')->where('id', $pivoteId)->update($updateData);
        $oficio->update(['estatus' => 'En Proceso']);

        \App\Models\OficioHistorial::registrar(
            $oficio->id,
            'Asignado',
            "Se delegó la tarea de atención directa al usuario: {$assignedUser->name} con la instrucción: \"{$areaOficio->instruccion}\".",
            $areaOficio->area_id
        );

        // Notificar al usuario asignado por correo
        $folioInterno = $updateData['folio_interno'] ?? $areaOficio->folio_interno;
        $data = [
            'usuario' => $assignedUser,
            'oficio' => $oficio,
            'folio_interno' => $folioInterno,
            'instruccion' => $areaOficio->instruccion,
        ];
        if ($assignedUser->recibir_correos) {
            try {
                Mail::send('emails.oficio_asignado', $data, function ($message) use ($assignedUser, $oficio, $folioInterno) {
                    $message->to($assignedUser->email)
                        ->subject('[' . $oficio->prioridad . '] Nuevo Oficio Asignado - Folio: ' . ($folioInterno ?? $oficio->numero_oficio));
                });
            } catch (\Exception $e) {
                Log::error("Error al enviar correo de oficio asignado a {$assignedUser->email}: " . $e->getMessage());
            }
        }

        return redirect()->route('oficios.show', $oficio)->with('success', 'Oficio asignado y estatus actualizado.');
    }

    /**
     * Subdirector delega el oficio a personal de su subdirección dentro de subarea_oficio.
     */
    public function asignarSubarea(Request $request, $subareaOficioId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $subareaOficio = \App\Models\SubareaOficio::findOrFail($subareaOficioId);
        $currentUser = Auth::user();
        $assignedUser = User::findOrFail($request->user_id);

        // Validar que el usuario asignado pertenece a la misma subdirección
        if ($assignedUser->subarea_id !== $subareaOficio->subarea_id) {
            return back()->with('error', 'Acción no permitida: Solo puede asignar a personal de su propia subdirección.');
        }

        $subareaOficio->update([
            'user_id' => $request->user_id,
        ]);

        // Notificar al personal por correo
        $areaOficio = DB::table('area_oficio')->where('id', $subareaOficio->area_oficio_id)->first();
        $oficio = Oficio::find($areaOficio->oficio_id);
        $folioInterno = $areaOficio->folio_interno;

        \App\Models\OficioHistorial::registrar(
            $oficio->id,
            'Asignado',
            "El subdirector titular delegó la tarea al personal: {$assignedUser->name} con la instrucción: \"" . ($subareaOficio->instruccion ?? $areaOficio->instruccion) . "\".",
            $areaOficio->area_id,
            $subareaOficio->subarea_id
        );

        $data = [
            'usuario' => $assignedUser,
            'oficio' => $oficio,
            'folio_interno' => $folioInterno,
            'instruccion' => $subareaOficio->instruccion ?? $areaOficio->instruccion,
        ];
        if ($assignedUser->recibir_correos) {
            try {
                Mail::send('emails.oficio_asignado', $data, function ($message) use ($assignedUser, $oficio, $folioInterno) {
                    $message->to($assignedUser->email)
                        ->subject('[' . $oficio->prioridad . '] Oficio Delegado - Folio: ' . ($folioInterno ?? $oficio->numero_oficio));
                });
            } catch (\Exception $e) {
                Log::error("Error al enviar correo de delegación a {$assignedUser->email}: " . $e->getMessage());
            }
        }

        return back()->with('success', 'Oficio delegado a ' . $assignedUser->name . ' correctamente.');
    }



    public function destroy(Oficio $oficio)
    {
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $oficio->delete();

        return redirect()->route('principal')->with('success', 'Oficio eliminado correctamente.');
    }

    public function generarOficio(Request $request, Oficio $oficio)
    {
        $user = Auth::user();
        $oficio->load('areas');

        $turnosParaImprimir = $oficio->areas;

        if ($request->filled('area_id')) {
            $turnosParaImprimir = $oficio->areas->filter(function ($area) use ($request) {
                return $area->id == $request->area_id;
            });
        } else {
            // Si se imprimen todos los turnos masivamente, omitir los que estén cancelados
            $turnosParaImprimir = $oficio->areas->filter(function ($area) {
                return $area->pivot->estatus !== 'Cancelado';
            });

            if (!in_array($user->role, ['admin', 'correspondencia', 'recepcionista'])) {
                $turnosParaImprimir = $turnosParaImprimir->filter(function ($area) use ($user) {
                    return $area->id == $user->area_id;
                });
            }
        }

        $subareaOficio = null;
        if ($request->filled('subarea_oficio_id')) {
            $subareaOficio = \App\Models\SubareaOficio::with(['subarea', 'user'])->find($request->subarea_oficio_id);
        }

        return view('oficios.generar', compact('oficio', 'turnosParaImprimir', 'subareaOficio'));
    }
    public function gestion(Request $request)
    {
        $user = Auth::user();

        // Si es administrador o rol de gestión del área, ve todos los turnos del área.
        // Si es operativo o subdirector, se filtra según la asignación a su subárea o personal.
        $query = Oficio::where('tipo_correspondencia', '!=', 'Interna')->whereHas('areas', function ($query) use ($user) {
            $query->where('area_id', $user->area_id);

            if ($user->role === 'subdirector' || ($user->role === 'admin' && $user->subarea_id !== null)) {
                $query->where(function ($q) use ($user) {
                    $q->whereExists(function ($subQ) use ($user) {
                        $subQ->select(DB::raw(1))
                            ->from('subarea_oficio')
                            ->whereColumn('subarea_oficio.area_oficio_id', 'area_oficio.id')
                            ->where('subarea_oficio.subarea_id', $user->subarea_id);
                    })
                        ->orWhere('area_oficio.user_id', $user->id)
                        ->orWhereExists(function ($subQ) use ($user) {
                            $subQ->select(DB::raw(1))
                                ->from('users')
                                ->whereColumn('users.id', 'area_oficio.user_id')
                                ->where('users.subarea_id', $user->subarea_id);
                        });
                });
            } elseif (!in_array($user->role, ['admin', 'jefe_area', 'secretaria_area'])) {
                $query->where(function ($q) use ($user) {
                    $q->whereExists(function ($subQ) use ($user) {
                        $subQ->select(DB::raw(1))
                            ->from('subarea_oficio')
                            ->whereColumn('subarea_oficio.area_oficio_id', 'area_oficio.id')
                            ->where('subarea_oficio.user_id', $user->id);
                    })
                        ->orWhere('area_oficio.user_id', $user->id);
                });
            }
        });

        // Búsqueda en la bandeja de gestión de turnos del área
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $user) {
                $q->where('numero_oficio', 'like', "%{$search}%")
                    ->orWhere('asunto', 'like', "%{$search}%")
                    ->orWhere('remitente', 'like', "%{$search}%")
                    ->orWhereHas('areas', function ($sub) use ($search, $user) {
                        $sub->where('area_id', $user->area_id)
                            ->where(function ($subQ) use ($search) {
                                $subQ->where('area_oficio.instruccion', 'like', "%{$search}%")
                                    ->orWhereExists(function ($existsQ) use ($search) {
                                        $existsQ->select(DB::raw(1))
                                            ->from('users')
                                            ->whereColumn('users.id', 'area_oficio.user_id')
                                            ->where('users.name', 'like', "%{$search}%");
                                    })
                                    ->orWhereExists(function ($existsQ) use ($search) {
                                        $existsQ->select(DB::raw(1))
                                            ->from('subarea_oficio')
                                            ->join('users', 'subarea_oficio.user_id', '=', 'users.id')
                                            ->whereColumn('subarea_oficio.area_oficio_id', 'area_oficio.id')
                                            ->where('users.name', 'like', "%{$search}%");
                                    });
                            });
                    });
            });
        }

        $oficiosTurnados = $query->latest()->paginate(10);

        return view('oficios.gestion', compact('oficiosTurnados'));
    }

    public function reporteDiario(Request $request)
    {
        $user = Auth::user();

        // Seguridad: Solo admin, correspondencia, o jefe_area de Gestión Institucional (area_id = 2)
        if ($user->role !== 'admin' && $user->role !== 'correspondencia' && !($user->role == 'jefe_area' && $user->area_id == 2)) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        // Obtener la fecha del reporte. Por defecto es HOY.
        $fecha = $request->input('fecha', \Carbon\Carbon::today()->format('Y-m-d'));

        // Obtener todos los turnos creados en esa fecha
        $turnos = DB::table('area_oficio')
            ->join('oficios', 'area_oficio.oficio_id', '=', 'oficios.id')
            ->join('areas', 'area_oficio.area_id', '=', 'areas.id')
            ->leftJoin('users', 'area_oficio.user_id', '=', 'users.id')
            ->whereDate('area_oficio.created_at', $fecha)
            ->select(
                'area_oficio.instruccion',
                'area_oficio.estatus as turno_estatus',
                'area_oficio.created_at as turnado_fecha',
                'oficios.numero_oficio',
                'oficios.remitente',
                'oficios.asunto',
                'oficios.fecha_recepcion',
                'areas.name as area_name',
                'users.name as operativo_name'
            )
            ->orderBy('areas.name', 'asc')
            ->get();

        $directorGestion = \App\Models\User::where('area_id', 2)
            ->where('role', 'jefe_area')
            ->first();

        return view('oficios.reporte', compact('turnos', 'fecha', 'directorGestion'));
    }

    public function reporteEntradas(Request $request)
    {
        $user = Auth::user();

        // Seguridad: Solo admin, correspondencia, o jefe_area de Gestión Institucional (area_id = 2)
        if ($user->role !== 'admin' && $user->role !== 'correspondencia' && !($user->role == 'jefe_area' && $user->area_id == 2)) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        // Obtener rango de fechas. Por defecto es HOY en ambos extremos.
        $fechaInicio = $request->input('fecha_inicio', \Carbon\Carbon::today()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', \Carbon\Carbon::today()->format('Y-m-d'));

        // Obtener todos los oficios registrados en el rango de fechas (sea cual sea su estatus)
        $oficios = Oficio::where('tipo_correspondencia', '!=', 'Interna')
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->orderByRaw("IF(numero_oficio LIKE '%/%', CAST(SUBSTRING_INDEX(numero_oficio, '/', -1) AS UNSIGNED), YEAR(fecha_recepcion)) ASC")
            ->orderByRaw("CAST(SUBSTRING_INDEX(numero_oficio, '/', 1) AS UNSIGNED) ASC")
            ->get();

        $directorGestion = \App\Models\User::where('area_id', 2)
            ->where('role', 'jefe_area')
            ->first();

        return view('oficios.reporte_entradas', compact('oficios', 'fechaInicio', 'fechaFin', 'directorGestion'));
    }

    public function reporteInternos(Request $request)
    {
        $user = Auth::user();

        // Seguridad: Solo admin, correspondencia, jefe_area o secretaria_area
        if (!in_array($user->role, ['admin', 'correspondencia', 'jefe_area', 'secretaria_area'])) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        // Obtener rango de fechas. Por defecto es HOY.
        $fechaInicio = $request->input('fecha_inicio', \Carbon\Carbon::today()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', \Carbon\Carbon::today()->format('Y-m-d'));

        // Filtrado por área
        $areaId = null;
        if (in_array($user->role, ['admin', 'correspondencia'])) {
            if ($request->filled('area_id')) {
                $areaId = $request->area_id;
            }
        } else {
            $areaId = $user->area_id;
        }

        // Obtener los folios internos
        // Consultamos la tabla pivot area_oficio
        $query = DB::table('area_oficio')
            ->join('oficios', 'area_oficio.oficio_id', '=', 'oficios.id')
            ->join('areas', 'area_oficio.area_id', '=', 'areas.id')
            ->leftJoin('areas as origen', 'oficios.area_origen_id', '=', 'origen.id')
            ->whereNull('oficios.deleted_at')
            ->whereNotNull('area_oficio.folio_interno')
            ->whereDate('area_oficio.created_at', '>=', $fechaInicio)
            ->whereDate('area_oficio.created_at', '<=', $fechaFin);

        if ($areaId) {
            $query->where('area_oficio.area_id', $areaId);
        }

        // Ordenamos por folio_interno
        $folios = $query->select(
            'area_oficio.id as area_oficio_id',
            'area_oficio.folio_interno',
            'area_oficio.estatus as status_turno',
            'area_oficio.created_at as fecha_registro',
            'oficios.id as oficio_id',
            'oficios.numero_oficio as numero_original',
            'oficios.numero_oficio_dependencia as original_dependencia',
            'oficios.tipo_correspondencia',
            'oficios.remitente',
            'oficios.asunto',
            'areas.name as area_destino',
            'origen.name as area_origen'
        )
        ->orderByRaw("IF(area_oficio.folio_interno LIKE '%/%', CAST(SUBSTRING_INDEX(area_oficio.folio_interno, '/', -1) AS UNSIGNED), area_oficio.anio) ASC")
        ->orderByRaw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(area_oficio.folio_interno, '/', 1), '-', -1) AS UNSIGNED) ASC")
        ->get();

        // Obtener todas las asignaciones internas de subáreas/personal para estos pivots en una sola consulta
        $pivotIds = collect($folios)->pluck('area_oficio_id')->toArray();
        $asignaciones = collect();
        if (!empty($pivotIds)) {
            $asignaciones = \App\Models\SubareaOficio::whereIn('area_oficio_id', $pivotIds)
                ->with(['subarea', 'user'])
                ->get()
                ->groupBy('area_oficio_id');
        }

        // Si es admin/correspondencia, cargamos todas las áreas para el select de filtrado
        $areas = [];
        if (in_array($user->role, ['admin', 'correspondencia'])) {
            $areas = \App\Models\Area::orderBy('name', 'asc')->get();
        }

        $directorGestion = \App\Models\User::where('area_id', 2)
            ->where('role', 'jefe_area')
            ->first();

        return view('oficios.internos.reporte', compact('folios', 'fechaInicio', 'fechaFin', 'areas', 'areaId', 'directorGestion', 'asignaciones'));
    }

    public function seguimiento(Request $request)
    {
        $user = Auth::user();

        // Seguridad: Solo admin, correspondencia, o jefe_area de Gestión Institucional (area_id = 2)
        if ($user->role !== 'admin' && $user->role !== 'correspondencia' && !($user->role == 'jefe_area' && $user->area_id == 2)) {
            abort(403, 'No tienes permiso para ver esta sección.');
        }

        // Obtener todos los turnos con relaciones
        $query = DB::table('area_oficio')
            ->join('oficios', 'area_oficio.oficio_id', '=', 'oficios.id')
            ->join('areas', 'area_oficio.area_id', '=', 'areas.id')
            ->leftJoin('users', 'area_oficio.user_id', '=', 'users.id')
            ->select(
                'area_oficio.id as pivote_id',
                'area_oficio.instruccion',
                'area_oficio.estatus as turno_estatus',
                'area_oficio.user_id as operativo_id',
                'oficios.id as oficio_id',
                'oficios.numero_oficio',
                'oficios.asunto',
                'oficios.remitente',
                'oficios.prioridad',
                'areas.name as area_name',
                'users.name as operativo_name'
            );

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('oficios.numero_oficio', 'like', "%{$search}%")
                    ->orWhere('oficios.asunto', 'like', "%{$search}%")
                    ->orWhere('oficios.remitente', 'like', "%{$search}%")
                    ->orWhere('areas.name', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%");
            });
        }

        // Filtro por Estatus de Turno
        if ($request->filled('estatus')) {
            $query->where('area_oficio.estatus', $request->estatus);
        }

        $turnos = $query->orderBy('oficios.created_at', 'desc')->paginate(15);

        // Adjuntar subdirecciones asignadas y su personal
        foreach ($turnos as $turno) {
            $turno->subareas = DB::table('subarea_oficio')
                ->leftJoin('subareas', 'subarea_oficio.subarea_id', '=', 'subareas.id')
                ->leftJoin('users', 'subarea_oficio.user_id', '=', 'users.id')
                ->where('subarea_oficio.area_oficio_id', $turno->pivote_id)
                ->select('subareas.name as subarea_name', 'users.name as user_name', 'subarea_oficio.estatus')
                ->get();
        }

        return view('oficios.seguimiento', compact('turnos'));
    }

    public function recibirTurno($pivote_id)
    {
        DB::table('area_oficio')
            ->where('id', $pivote_id)
            ->update([
                'estatus' => 'Recibido'
            ]);

        $pivot = DB::table('area_oficio')->where('id', $pivote_id)->first();
        if ($pivot) {
            \App\Models\OficioHistorial::registrar(
                $pivot->oficio_id,
                'Recibido',
                "La dirección confirmó de recibido el oficio.",
                $pivot->area_id
            );
        }

        return back()->with('success', 'Se ha confirmado la recepción del oficio correctamente.');
    }

    public function notificarTurno(Request $request, $pivote_id)
    {
        // Si viene un subarea_oficio_id, actualizamos ese registro
        if ($request->has('subarea_oficio_id')) {
            $subareaOficio = \App\Models\SubareaOficio::findOrFail($request->subarea_oficio_id);
            $subareaOficio->update(['estatus' => 'Notificado']);

            // Verificar si todas las subarea_oficio del area_oficio padre están al menos Notificadas
            $pendientes = \App\Models\SubareaOficio::where('area_oficio_id', $subareaOficio->area_oficio_id)
                ->where('estatus', 'Asignado')
                ->count();
            if ($pendientes === 0) {
                DB::table('area_oficio')->where('id', $subareaOficio->area_oficio_id)->update(['estatus' => 'Notificado']);
            }

            $pivot = DB::table('area_oficio')->where('id', $subareaOficio->area_oficio_id)->first();
            if ($pivot) {
                $subName = $subareaOficio->subarea ? $subareaOficio->subarea->name : 'personal directo';
                \App\Models\OficioHistorial::registrar(
                    $pivot->oficio_id,
                    'Notificado',
                    "El destinatario ({$subName}) confirmó de enterado / recibido.",
                    $pivot->area_id,
                    $subareaOficio->subarea_id
                );
            }

            return back()->with('success', 'Has confirmado de notificado para este turno correctamente.');
        }

        // Comportamiento original para áreas sin subdirecciones
        DB::table('area_oficio')
            ->where('id', $pivote_id)
            ->update([
                'estatus' => 'Notificado'
            ]);

        $pivot = DB::table('area_oficio')->where('id', $pivote_id)->first();
        if ($pivot) {
            \App\Models\OficioHistorial::registrar(
                $pivot->oficio_id,
                'Notificado',
                "El destinatario directo confirmó de enterado / recibido.",
                $pivot->area_id
            );
        }

        return back()->with('success', 'Has confirmado de notificado para este turno correctamente.');
    }

    public function atender(Request $request, $areaOficioId)
    {
        $user = Auth::user();

        // Si viene un subarea_oficio_id, usamos ese contexto
        $subareaOficio = null;
        if ($request->has('subarea_oficio_id')) {
            $subareaOficio = \App\Models\SubareaOficio::findOrFail($request->subarea_oficio_id);
        }

        // Buscamos el registro pivote
        $areaOficio = DB::table('area_oficio')->where('id', $areaOficioId)->first();
        if (!$areaOficio) {
            abort(404, 'Turno no encontrado.');
        }

        // Verificar permiso: admin, o asignado directamente, o tiene subarea_oficio asignada
        $hasPermission = ($user->role === 'admin');
        if (!$hasPermission && $areaOficio->user_id === $user->id) {
            $hasPermission = true;
        }
        if (!$hasPermission && $subareaOficio) {
            $hasPermission = ($subareaOficio->user_id === $user->id);
        }
        if (!$hasPermission) {
            // Verificar si tiene alguna subarea_oficio asignada en este area_oficio
            $hasPermission = \App\Models\SubareaOficio::where('area_oficio_id', $areaOficioId)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('subarea_id', $user->subarea_id);
                })->exists();
        }

        if (!$hasPermission) {
            abort(403, 'No tienes permiso para atender este turno.');
        }

        // Cargamos el oficio
        $oficio = Oficio::findOrFail($areaOficio->oficio_id);
        $areaOficio->oficio = $oficio;

        return view('oficios.atender', compact('areaOficio', 'subareaOficio'));
    }

    public function solventar(Request $request, $areaOficioId)
    {
        $request->validate([
            'tipo_respuesta' => 'required|in:Conocimiento,Solventacion',
            'mensaje' => 'required|string',
            'archivo_evidencia' => 'required_if:tipo_respuesta,Solventacion|nullable|file|mimes:pdf|max:5120',
            'subarea_oficio_id' => 'nullable|exists:subarea_oficio,id'
        ], [
            'archivo_evidencia.required_if' => 'Debe adjuntar obligatoriamente el archivo PDF de evidencia para una solventación o respuesta formal.'
        ]);

        // 2. Gestionamos el archivo si existe
        $path = null;
        if ($request->hasFile('archivo_evidencia')) {
            $path = $request->file('archivo_evidencia')->store('evidencias', 'public');
        }

        // 3. Guardamos en la base de datos
        \App\Models\OficioRespuesta::create([
            'area_oficio_id' => $areaOficioId,
            'subarea_oficio_id' => $request->input('subarea_oficio_id'),
            'user_id' => Auth::id(),
            'tipo_respuesta' => $request->tipo_respuesta,
            'mensaje' => $request->mensaje,
            'archivo_evidencia' => $path
        ]);

        // 4. Si tiene subarea_oficio_id, actualizar esa subdirección
        if ($request->filled('subarea_oficio_id')) {
            $subareaOficio = \App\Models\SubareaOficio::find($request->subarea_oficio_id);
            if ($subareaOficio) {
                $subareaOficio->update(['estatus' => 'Solventado']);

                // Verificar si todas las subarea_oficio del area_oficio padre están Solventadas
                $pendientes = \App\Models\SubareaOficio::where('area_oficio_id', $subareaOficio->area_oficio_id)
                    ->where('estatus', '!=', 'Solventado')
                    ->count();

                if ($pendientes === 0) {
                    DB::table('area_oficio')
                        ->where('id', $subareaOficio->area_oficio_id)
                        ->update(['estatus' => 'Solventado']);
                }
            }
        } else {
            // Comportamiento original para áreas sin subdirecciones
            DB::table('area_oficio')
                ->where('id', $areaOficioId)
                ->update(['estatus' => 'Solventado']);
        }

        // 5. Verificar si todas las áreas del oficio están solventadas
        $turno = DB::table('area_oficio')->where('id', $areaOficioId)->first();
        $isInternal = false;
        if ($turno) {
            $oficioId = $turno->oficio_id;
            $oficio = Oficio::find($oficioId);
            if ($oficio && $oficio->tipo_correspondencia === 'Interna') {
                $isInternal = true;
            }

            $subareaOficio = null;
            if ($request->filled('subarea_oficio_id')) {
                $subareaOficio = \App\Models\SubareaOficio::find($request->subarea_oficio_id);
            }

            $respDesc = ($request->tipo_respuesta === 'Solventacion' ? 'Respuesta/Solventación formal' : 'Conocimiento/Notificación de atención') . ": \"{$request->mensaje}\"";
            \App\Models\OficioHistorial::registrar(
                $oficioId,
                'Solventado',
                $respDesc,
                $turno->area_id,
                $subareaOficio ? $subareaOficio->subarea_id : null
            );

            $pendientes = DB::table('area_oficio')
                ->where('oficio_id', $oficioId)
                ->where('estatus', '!=', 'Solventado')
                ->where('estatus', '!=', 'Cancelado')
                ->count();

            if ($pendientes === 0) {
                DB::table('oficios')
                    ->where('id', $oficioId)
                    ->update(['estatus' => 'Solventado']);
            }
        }

        if ($isInternal) {
            return redirect()->route('principal')->with('success', '¡Respuesta registrada correctamente!');
        }

        return redirect()->route('oficios.gestion')->with('success', '¡Acción registrada correctamente!');
    }

    public function edit(Oficio $oficio)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'correspondencia', 'recepcionista'])) {
            abort(403, 'No tienes permiso para editar este oficio.');
        }

        return view('oficios.edit', compact('oficio'));
    }

    public function update(Request $request, Oficio $oficio)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'correspondencia', 'recepcionista'])) {
            abort(403, 'No tienes permiso para actualizar este oficio.');
        }

        $request->validate([
            'numero_oficio' => 'required|string|max:255',
            'remitente' => 'required|string|max:255',
            'municipio' => 'required|string|max:255',
            'asunto' => 'required|string',
            'fecha_recepcion' => 'required|date',
            'prioridad' => 'required|string',
            'numero_oficio_dependencia' => 'required|string|max:255',
            'fecha_limite' => 'nullable|date',
            'localidad' => 'required|string|max:255',
            'observaciones' => 'nullable|string',
            'archivo_pdf' => 'nullable|mimes:pdf|max:10240',
            'tipo_correspondencia' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('archivo_pdf')) {
            $path = $request->file('archivo_pdf')->store('oficios_pdf', 'public');
            $data['pdf_path'] = $path;
        }

        $oficio->update($data);

        return redirect()->route('oficios.show', $oficio)->with('success', 'Oficio actualizado correctamente.');
    }

    public function cancelar(Request $request, Oficio $oficio)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'correspondencia'])) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $request->validate([
            'motivo_cancelacion' => 'required|string',
        ]);

        $oficio->update([
            'estatus' => 'Cancelado',
            'motivo_cancelacion' => $request->motivo_cancelacion
        ]);

        \App\Models\OficioHistorial::registrar(
            $oficio->id,
            'Cancelado',
            "Se canceló el oficio completo. Motivo: \"{$request->motivo_cancelacion}\"."
        );

        // Actualizamos el estatus de sus áreas turnadas si existen a 'Cancelado'
        DB::table('area_oficio')
            ->where('oficio_id', $oficio->id)
            ->update(['estatus' => 'Cancelado']);

        return redirect()->route('oficios.index')->with('success', 'El oficio ha sido cancelado correctamente.');
    }

    public function internosIndex(Request $request)
    {
        $user = Auth::user();

        // Si no es admin/correspondencia y no tiene área, abortar
        $isAdminOrCorrespondencia = in_array($user->role, ['admin', 'correspondencia']);
        if (!$isAdminOrCorrespondencia && !$user->area_id) {
            abort(403, 'No tienes una Dirección/Área asignada para ver esta sección.');
        }

        $query = Oficio::where('tipo_correspondencia', 'Interna')->with('areaOrigen');

        $areaId = $user->area_id;
        $filtroTipo = $request->input('tipo', 'Todos'); // Todos, Enviados, Recibidos, Solventados
        $filtroEstatus = $request->input('estatus', 'Todos'); // Todos, Notificado, Asignado, En Proceso, Solventado, Cancelado

        if (!$isAdminOrCorrespondencia) {
            if ($filtroTipo === 'Enviados') {
                $query->whereRaw('1 = 0');
            } elseif ($filtroTipo === 'Recibidos') {
                $query->whereHas('areas', function ($q) use ($areaId, $user) {
                    $q->where('areas.id', $areaId);
                    
                    // Restricción para operativos y subdirectores
                    if (!in_array($user->role, ['jefe_area', 'secretaria_area'])) {
                        $q->where(function ($sub) use ($user) {
                            $sub->where('area_oficio.user_id', $user->id)
                                ->orWhereExists(function ($subQ) use ($user) {
                                    $subQ->select(DB::raw(1))
                                        ->from('subarea_oficio')
                                        ->whereColumn('subarea_oficio.area_oficio_id', 'area_oficio.id')
                                        ->where(function ($deepQ) use ($user) {
                                            if ($user->role === 'subdirector') {
                                                if ($user->subarea_id) {
                                                    $deepQ->where('subarea_oficio.subarea_id', $user->subarea_id);
                                                } else {
                                                    $deepQ->whereRaw('1 = 0');
                                                }
                                            } else {
                                                $deepQ->where('subarea_oficio.user_id', $user->id);
                                                if ($user->subarea_id) {
                                                    $deepQ->orWhere(function ($groupQ) use ($user) {
                                                        $groupQ->where('subarea_oficio.subarea_id', $user->subarea_id)
                                                            ->whereNull('subarea_oficio.user_id');
                                                    });
                                                }
                                            }
                                        });
                                })
                                ->orWhereExists(function ($subQ) use ($user) {
                                    if ($user->role === 'subdirector' && $user->subarea_id) {
                                        $subQ->select(DB::raw(1))
                                            ->from('users')
                                            ->whereColumn('users.id', 'area_oficio.user_id')
                                            ->where('users.subarea_id', $user->subarea_id);
                                    } else {
                                        $subQ->select(DB::raw(1))->from('users')->whereRaw('1 = 0');
                                    }
                                });
                        });
                    }
                });
            } elseif ($filtroTipo === 'Solventados') {
                $query->whereHas('areas', function ($q) use ($areaId, $user) {
                    $q->where('areas.id', $areaId)->where('area_oficio.estatus', 'Solventado');

                    // Restricción para operativos y subdirectores
                    if (!in_array($user->role, ['jefe_area', 'secretaria_area'])) {
                        $q->where(function ($sub) use ($user) {
                            $sub->where('area_oficio.user_id', $user->id)
                                ->orWhereExists(function ($subQ) use ($user) {
                                    $subQ->select(DB::raw(1))
                                        ->from('subarea_oficio')
                                        ->whereColumn('subarea_oficio.area_oficio_id', 'area_oficio.id')
                                        ->where(function ($deepQ) use ($user) {
                                            if ($user->role === 'subdirector') {
                                                if ($user->subarea_id) {
                                                    $deepQ->where('subarea_oficio.subarea_id', $user->subarea_id);
                                                } else {
                                                    $deepQ->whereRaw('1 = 0');
                                                }
                                            } else {
                                                $deepQ->where('subarea_oficio.user_id', $user->id);
                                                if ($user->subarea_id) {
                                                    $deepQ->orWhere(function ($groupQ) use ($user) {
                                                        $groupQ->where('subarea_oficio.subarea_id', $user->subarea_id)
                                                            ->whereNull('subarea_oficio.user_id');
                                                    });
                                                }
                                            }
                                        });
                                })
                                ->orWhereExists(function ($subQ) use ($user) {
                                    if ($user->role === 'subdirector' && $user->subarea_id) {
                                        $subQ->select(DB::raw(1))
                                            ->from('users')
                                            ->whereColumn('users.id', 'area_oficio.user_id')
                                            ->where('users.subarea_id', $user->subarea_id);
                                    } else {
                                        $subQ->select(DB::raw(1))->from('users')->whereRaw('1 = 0');
                                    }
                                });
                        });
                    }
                });
            } else { // Todos
                if (in_array($user->role, ['jefe_area', 'secretaria_area'])) {
                    $query->whereHas('areas', function ($q) use ($areaId) {
                        $q->where('areas.id', $areaId);
                    });
                } else {
                    $query->whereHas('areas', function ($q) use ($areaId, $user) {
                        $q->where('areas.id', $areaId);

                        $q->where(function ($sub) use ($user) {
                            $sub->where('area_oficio.user_id', $user->id)
                                ->orWhereExists(function ($subQ) use ($user) {
                                    $subQ->select(DB::raw(1))
                                        ->from('subarea_oficio')
                                        ->whereColumn('subarea_oficio.area_oficio_id', 'area_oficio.id')
                                        ->where(function ($deepQ) use ($user) {
                                            if ($user->role === 'subdirector') {
                                                if ($user->subarea_id) {
                                                    $deepQ->where('subarea_oficio.subarea_id', $user->subarea_id);
                                                } else {
                                                    $deepQ->whereRaw('1 = 0');
                                                }
                                            } else {
                                                $deepQ->where('subarea_oficio.user_id', $user->id);
                                                if ($user->subarea_id) {
                                                    $deepQ->orWhere(function ($groupQ) use ($user) {
                                                        $groupQ->where('subarea_oficio.subarea_id', $user->subarea_id)
                                                            ->whereNull('subarea_oficio.user_id');
                                                    });
                                                }
                                            }
                                        });
                                })
                                ->orWhereExists(function ($subQ) use ($user) {
                                    if ($user->role === 'subdirector' && $user->subarea_id) {
                                        $subQ->select(DB::raw(1))
                                            ->from('users')
                                            ->whereColumn('users.id', 'area_oficio.user_id')
                                            ->where('users.subarea_id', $user->subarea_id);
                                    } else {
                                        $subQ->select(DB::raw(1))->from('users')->whereRaw('1 = 0');
                                    }
                                });
                        });
                    });
                }
            }
        } else {
            // Admin y correspondencia ven todos y pueden filtrar
            if ($filtroTipo === 'Enviados') {
                if ($request->filled('area_origen_id')) {
                    $query->where('area_origen_id', $request->area_origen_id);
                }
            } elseif ($filtroTipo === 'Recibidos') {
                if ($request->filled('area_destino_id')) {
                    $query->whereHas('areas', function ($q) use ($request) {
                        $q->where('areas.id', $request->area_destino_id);
                    });
                }
            } elseif ($filtroTipo === 'Solventados') {
                $query->where('estatus', 'Solventado');
            }
        }

        // Búsqueda por número de oficio, remitente o asunto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_oficio', 'like', "%{$search}%")
                    ->orWhere('remitente', 'like', "%{$search}%")
                    ->orWhere('asunto', 'like', "%{$search}%");
            });
        }

        // Filtro por estatus de oficio
        if ($filtroEstatus !== 'Todos') {
            if ($isAdminOrCorrespondencia) {
                $query->where('estatus', $filtroEstatus);
            } else {
                $query->whereHas('areas', function ($q) use ($areaId, $filtroEstatus) {
                    $q->where('areas.id', $areaId)->where('area_oficio.estatus', $filtroEstatus);
                });
            }
        }

        $oficios = $query->orderBy('created_at', 'desc')->paginate(10);
        $areas = \App\Models\Area::all();

        return view('oficios.internos.index', compact('oficios', 'filtroTipo', 'filtroEstatus', 'areas'));
    }

    public function internosCreate()
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && !in_array($user->role, ['jefe_area', 'secretaria_area', 'correspondencia'])) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        if ($user->role !== 'admin' && !$user->area_id) {
            return back()->with('error', 'El usuario no tiene una Dirección asignada.');
        }

        $areas = \App\Models\Area::all();
        $currentYear = now()->year;

        // Precalculamos los folios y remitentes por defecto para cada área (cuando actúa como Capturadora/Receptora o como Emisora)
        $areasData = [];
        foreach ($areas as $area) {
            $maxPivotConsecutivo = DB::table('area_oficio')
                ->where('area_id', $area->id)
                ->where('anio', $currentYear)
                ->max('consecutivo');
            $nextConsecutivo = ($maxPivotConsecutivo ?? 0) + 1;
            $prefijo = !empty($area->prefijo) ? $area->prefijo : 'OIC';
            $folio = $prefijo . '-INT-' . str_pad($nextConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;

            $director = User::where('area_id', $area->id)->where('role', 'jefe_area')->first();
            $remitente = $director ? ($director->prof ? $director->prof . ' ' : '') . $director->name : '';

            $areasData[$area->id] = [
                'id' => $area->id,
                'name' => $area->name,
                'folio' => $folio,
                'remitente' => $remitente,
                'prefijo' => $prefijo
            ];
        }

        // Determinar el área capturadora/receptora (la que se queda con el consecutivo y el folio)
        $capturingAreaId = $user->role === 'admin' ? old('area_captura_id', 2) : $user->area_id; // por defecto Gestión Institucional si es admin
        $areaCaptura = \App\Models\Area::find($capturingAreaId);

        // Folio generado para el área capturadora
        $siguienteFolio = $areasData[$capturingAreaId]['folio'] ?? '';

        return view('oficios.internos.create', compact('areas', 'areaCaptura', 'siguienteFolio', 'areasData'));
    }

    public function internosStore(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && !in_array($user->role, ['jefe_area', 'secretaria_area', 'correspondencia'])) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        // Área receptora/capturadora (la que genera el folio)
        $capturingAreaId = $user->role === 'admin' ? $request->area_captura_id : $user->area_id;
        // Área emisora/origen (la que mandó el oficio)
        $areaOrigenId = $request->area_origen_id;

        if (!$capturingAreaId) {
            return back()->with('error', 'No se ha determinado la Dirección de Destino/Captura.')->withInput();
        }
        if (!$areaOrigenId) {
            return back()->with('error', 'No se ha determinado la Dirección de Origen/Emisora.')->withInput();
        }

        $request->validate([
            'area_origen_id' => 'required|exists:areas,id',
            'numero_origen' => 'required|string|max:50',
            'asunto' => 'required|string',
            'fecha_recepcion' => 'required|date',
            'prioridad' => 'required|string',
            'archivo_pdf' => 'required|file|mimes:pdf|max:10240',
            'observaciones' => 'nullable|string',
            'remitente' => 'required|string|max:255',
            'fecha_limite' => 'nullable|date',
        ]);

        $areaCaptura = \App\Models\Area::findOrFail($capturingAreaId);
        $areaOrigen = \App\Models\Area::findOrFail($areaOrigenId);

        // Generar el consecutivo y el folio interno definitivos para el área receptora/capturadora
        $currentYear = now()->year;
        $maxPivotConsecutivo = DB::table('area_oficio')
            ->where('area_id', $areaCaptura->id)
            ->where('anio', $currentYear)
            ->max('consecutivo');
        $nextConsecutivo = ($maxPivotConsecutivo ?? 0) + 1;
        $prefijo = !empty($areaCaptura->prefijo) ? $areaCaptura->prefijo : 'OIC';
        $folio = $prefijo . '-INT-' . str_pad($nextConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;

        // El folio completo del emisor/origen es directamente lo que escribió el usuario
        $folioOrigenCompleto = $request->numero_origen;

        // Extraer consecutivo y año de origen si es posible
        $consecutivoOrigen = null;
        $anioOrigen = $currentYear;
        if (preg_match('/-INT-(\d+)\/(\d+)/', $request->numero_origen, $matches)) {
            $consecutivoOrigen = (int) $matches[1];
            $anioOrigen = (int) $matches[2];
        } elseif (preg_match('/(\d+)\D+(\d+)/', $request->numero_origen, $matches)) {
            $consecutivoOrigen = (int) $matches[1];
            $anioOrigen = (int) $matches[2];
        } elseif (preg_match('/(\d+)/', $request->numero_origen, $matches)) {
            $consecutivoOrigen = (int) $request->numero_origen;
        }

        // Guardar el archivo PDF
        $pdfPath = null;
        if ($request->hasFile('archivo_pdf')) {
            $pdfPath = $request->file('archivo_pdf')->store('oficios_pdf', 'public');
        }

        // Crear el Oficio
        $oficio = Oficio::create([
            'numero_oficio' => $folio,
            'remitente' => $request->remitente,
            'municipio' => 'Pachuca de Soto',
            'localidad' => 'Pachuca de Soto',
            'asunto' => $request->asunto,
            'estatus' => 'Turnado', // Ya está recibido e ingresado
            'fecha_recepcion' => $request->fecha_recepcion,
            'tipo_correspondencia' => 'Interna',
            'prioridad' => $request->prioridad,
            'numero_oficio_dependencia' => $folioOrigenCompleto,
            'fecha_limite' => $request->fecha_limite,
            'observaciones' => $request->observaciones,
            'pdf_path' => $pdfPath,
            'area_origen_id' => $areaOrigenId,
            'consecutivo_origen' => $consecutivoOrigen,
            'anio_origen' => $anioOrigen,
        ]);

        // Registrar el turno directamente para el área receptora/capturadora
        DB::table('area_oficio')->insert([
            'oficio_id' => $oficio->id,
            'area_id' => $areaCaptura->id,
            'instruccion' => 'Correspondencia Interna Recibida',
            'estatus' => 'Notificado', // Queda recibido para que puedan asignarlo de inmediato
            'folio_interno' => $folio,
            'consecutivo' => $nextConsecutivo,
            'anio' => $currentYear,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \App\Models\OficioHistorial::registrar(
            $oficio->id,
            'Registrado',
            "Se registró el oficio interno: {$folio} (Origen: {$areaOrigen->name}, No. Oficio: {$folioOrigenCompleto})."
        );

        \App\Models\OficioHistorial::registrar(
            $oficio->id,
            'Turnado',
            "Se turnó automáticamente a la {$areaCaptura->name}.",
            $areaCaptura->id
        );

        return redirect()->route('oficios.internos.index')->with('success', 'Oficio interno registrado correctamente con el folio receptor de tu área: ' . $folio);
    }
}