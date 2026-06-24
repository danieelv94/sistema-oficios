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

        $query = Oficio::query();

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

        // Ordenamos por número de oficio de forma descendente (consecutivo)
        $oficios = $query->orderByRaw('CAST(numero_oficio AS UNSIGNED) DESC')->paginate(10);

        return view('oficios.index', compact('oficios'));
    }

    public function create()
    {
        $ultimoNumero = Oficio::withTrashed()->selectRaw('MAX(CAST(numero_oficio AS UNSIGNED)) as max_val')->value('max_val');
        $siguienteConsecutivo = ($ultimoNumero ?? 0) + 1;
        return view('oficios.create', compact('siguienteConsecutivo'));
    }

    public function store(Request $request)
    {
        $ultimoNumero = Oficio::withTrashed()->selectRaw('MAX(CAST(numero_oficio AS UNSIGNED)) as max_val')->value('max_val');
        $siguienteConsecutivo = ($ultimoNumero ?? 0) + 1;
        $request->merge(['numero_oficio' => (string)$siguienteConsecutivo]);

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

        if (!$request->has('tipo_correspondencia')) {
            $data['tipo_correspondencia'] = 'Externa';
        }

        if ($request->hasFile('archivo_pdf')) {
            $path = $request->file('archivo_pdf')->store('oficios_pdf', 'public');
            $data['pdf_path'] = $path;
        }

        Oficio::create($data);

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
            // El administrador, recepcionista, correspondencia y el jefe de gestión institucional ven todos los turnos del oficio
            $turnosParaMostrar = $oficio->areas;
        } elseif ($user->role == 'jefe_area' || $user->role == 'secretaria_area') {
            // El jefe o secretaria del área ve los turnos de su área
            $turnosParaMostrar = $oficio->areas->where('id', $user->area_id);
        } else {
            // El personal operativo solo ve los turnos asignados a él directamente
            $turnosParaMostrar = $oficio->areas->filter(function ($area) use ($user) {
                return $area->pivot->user_id == $user->id;
            });
        }

        $areasAsignadasIds = $oficio->areas->pluck('id')->toArray();
        $areasDisponibles = Area::whereNotIn('id', $areasAsignadasIds)->get();

        // Cargamos el personal para todas las áreas turnadas para evitar índices indefinidos
        $personalPorArea = [];
        foreach ($oficio->areas as $areaTurnada) {
            $personalPorArea[$areaTurnada->id] = User::where('area_id', $areaTurnada->id)->get();
        }

        $idsTurnos = $oficio->areas()->pluck('area_oficio.id');
        $respuestas = \App\Models\OficioRespuesta::whereIn('area_oficio_id', $idsTurnos)
            ->orderBy('created_at', 'desc')
            ->get();

        // Para retrocompatibilidad con la vista
        $mode = in_array($user->role, ['admin', 'recepcionista', 'correspondencia']) ? 'recepcion' : (in_array($user->role, ['jefe_area', 'secretaria_area']) ? 'gestion' : 'operativo');

        return view('oficios.show', compact('oficio', 'areasDisponibles', 'personalPorArea', 'turnosParaMostrar', 'mode', 'respuestas'));
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

                // Notificar a secretaria_area del área turnada
                $secretarias = User::where('area_id', $area_id)
                    ->where('role', 'secretaria_area')
                    ->get();

                foreach ($secretarias as $secretaria) {
                    $data = [
                        'oficio' => $oficio,
                        'instruccion' => $instruccion,
                        'usuario' => $secretaria,
                    ];

                    try {
                        Mail::send('emails.oficio_turnado', $data, function ($message) use ($secretaria, $oficio) {
                            $message->to($secretaria->email)
                                ->subject('[' . $oficio->prioridad . '] Nuevo Oficio Turnado - No. ' . $oficio->numero_oficio);
                        });
                    } catch (\Exception $e) {
                        Log::error("Error al enviar correo de oficio turnado a {$secretaria->email}: " . $e->getMessage());
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

        return back()->with('success', 'El turno ha sido cancelado correctamente.');
    }

    public function asignar(Request $request, Oficio $oficio)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pivote_id' => 'required|exists:area_oficio,id'
        ]);

        $areaOficio = DB::table('area_oficio')->where('id', $request->pivote_id)->first();
        if (!$areaOficio) {
            return back()->with('error', 'Registro de turno no encontrado.');
        }

        $updateData = [
            'user_id' => $request->user_id,
            'estatus' => 'Asignado'
        ];

        // Generamos el folio interno si no existe uno previo
        if (empty($areaOficio->folio_interno)) {
            $area = Area::find($areaOficio->area_id);
            if ($area) {
                $currentYear = now()->year;
                $ultimoConsecutivo = DB::table('area_oficio')
                    ->where('area_id', $area->id)
                    ->where('anio', $currentYear)
                    ->max('consecutivo');

                $siguienteConsecutivo = $ultimoConsecutivo ? $ultimoConsecutivo + 1 : 1;

                // Formato: PREFIJO-INT-CONSECUTIVO/ANIO
                $prefijo = !empty($area->prefijo) ? $area->prefijo : 'OIC';
                $folioInterno = $prefijo . '-INT-' . str_pad($siguienteConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;

                $updateData['folio_interno'] = $folioInterno;
                $updateData['consecutivo'] = $siguienteConsecutivo;
                $updateData['anio'] = $currentYear;
            }
        }

        DB::table('area_oficio')
            ->where('id', $request->pivote_id)
            ->update($updateData);

        $oficio->update(['estatus' => 'En Proceso']);

        // Notificar al usuario asignado por correo
        $assignedUser = User::find($request->user_id);
        if ($assignedUser) {
            $folioInterno = $updateData['folio_interno'] ?? $areaOficio->folio_interno;
            $data = [
                'usuario' => $assignedUser,
                'oficio' => $oficio,
                'folio_interno' => $folioInterno,
                'instruccion' => $areaOficio->instruccion,
            ];

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
        } elseif ($user->role != 'admin') {
            $turnosParaImprimir = $oficio->areas->filter(function ($area) use ($user) {
                return $area->id == $user->area_id;
            });
        }

        return view('oficios.generar', compact('oficio', 'turnosParaImprimir'));
    }
    public function gestion(Request $request)
    {
        $user = Auth::user();

        // Si es administrador o rol de gestión del área, ve todos los turnos del área.
        // Si es operativo, solo ve los turnos asignados a él directamente.
        $query = Oficio::whereHas('areas', function ($query) use ($user) {
            $query->where('area_id', $user->area_id);
            
            if (!in_array($user->role, ['admin', 'jefe_area', 'secretaria_area'])) {
                $query->where('area_oficio.user_id', $user->id);
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
        $oficios = Oficio::whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->orderByRaw('CAST(numero_oficio AS UNSIGNED) ASC')
            ->get();

        $directorGestion = \App\Models\User::where('area_id', 2)
            ->where('role', 'jefe_area')
            ->first();

        return view('oficios.reporte_entradas', compact('oficios', 'fechaInicio', 'fechaFin', 'directorGestion'));
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
            $query->where(function($q) use ($search) {
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

        return view('oficios.seguimiento', compact('turnos'));
    }

    public function recibirTurno($pivote_id)
    {
        DB::table('area_oficio')
            ->where('id', $pivote_id)
            ->update([
                'estatus' => 'Recibido'
            ]);

        return back()->with('success', 'Se ha confirmado la recepción del oficio correctamente.');
    }

    public function notificarTurno($pivote_id)
    {
        DB::table('area_oficio')
            ->where('id', $pivote_id)
            ->update([
                'estatus' => 'Notificado'
            ]);

        return back()->with('success', 'Has confirmado de notificado para este turno correctamente.');
    }

    public function atender($areaOficioId)
    {
        $user = Auth::user();
        
        // Buscamos el registro pivote
        $areaOficio = DB::table('area_oficio')->where('id', $areaOficioId)->first();
        if (!$areaOficio) {
            abort(404, 'Turno no encontrado.');
        }

        // Verificamos que pertenezca al usuario logueado o administrador
        if ($areaOficio->user_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'No tienes permiso para atender este turno.');
        }

        // Cargamos el oficio para mostrar la información en la vista
        $oficio = Oficio::findOrFail($areaOficio->oficio_id);

        // Agregamos una propiedad dinámica para facilitar el acceso en la vista
        $areaOficio->oficio = $oficio;

        return view('oficios.atender', compact('areaOficio'));
    }

    public function solventar(Request $request, $areaOficioId)
    {
        // 1. Validamos los datos
        $request->validate([
            'tipo_respuesta' => 'required|in:Conocimiento,Solventacion',
            'mensaje' => 'required|string',
            'archivo_evidencia' => 'nullable|file|mimes:pdf|max:5120'
        ]);

        // 2. Gestionamos el archivo si existe
        $path = null;
        if ($request->hasFile('archivo_evidencia')) {
            $path = $request->file('archivo_evidencia')->store('evidencias', 'public');
        }

        // 3. Guardamos en la base de datos
        \App\Models\OficioRespuesta::create([
            'area_oficio_id' => $areaOficioId, // Este es el ID de la tabla area_oficio
            'user_id' => Auth::id(),
            'tipo_respuesta' => $request->tipo_respuesta,
            'mensaje' => $request->mensaje,
            'archivo_evidencia' => $path
        ]);

        // 4. Actualizamos el estatus de la asignación a 'Solventado'
        DB::table('area_oficio')
            ->where('id', $areaOficioId)
            ->update(['estatus' => 'Solventado']);

        // 5. Opcional: Actualizar el estatus general del Oficio si todas las áreas asignadas ya solventaron
        $turno = DB::table('area_oficio')->where('id', $areaOficioId)->first();
        if ($turno) {
            $oficioId = $turno->oficio_id;
            $pendientes = DB::table('area_oficio')
                ->where('oficio_id', $oficioId)
                ->where('estatus', '!=', 'Solventado')
                ->count();

            if ($pendientes === 0) {
                DB::table('oficios')
                    ->where('id', $oficioId)
                    ->update(['estatus' => 'Solventado']);
            }
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

        // Actualizamos el estatus de sus áreas turnadas si existen a 'Cancelado'
        DB::table('area_oficio')
            ->where('oficio_id', $oficio->id)
            ->update(['estatus' => 'Cancelado']);

        return redirect()->route('oficios.index')->with('success', 'El oficio ha sido cancelado correctamente.');
    }
}