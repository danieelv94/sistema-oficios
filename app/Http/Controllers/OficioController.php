<?php
//antes dashborad.blade.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oficio;
use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OficioController extends Controller
{
    /**
     * Muestra la lista de oficios.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Cargas globales para Admin/Recepcionista (Entrada de correspondencia)
        $correspondenciaGeneral = ($user->role == 'admin' || $user->role == 'recepcionista')
            ? Oficio::latest()->get()
            : collect();

        // 2. Cargas para Secretarias/Jefes (Gestión de su Dirección)
        $gestionArea = ($user->role == 'admin' || $user->role == 'jefe_area' || $user->role == 'secretaria_area')
            ? Oficio::whereHas('areas', function ($q) use ($user) {
                $q->where('areas.id', $user->area_id);
            })->latest()->get()
            : collect();

        // 3. Cargas para el Responsable (Tareas personales para completar el turno)
        $misTurnosAsignados = Oficio::whereHas('areas', function ($q) use ($user) {
            $q->where('area_oficio.user_id', $user->id);
        })->latest()->get();

        return view('principal', compact('correspondenciaGeneral', 'gestionArea', 'misTurnosAsignados'));
    }

    /**
     * Muestra el formulario para crear un nuevo oficio.
     */
    public function create()
    {
        return view('oficios.create');
    }

    /**
     * Guarda el nuevo oficio en la base de datos.
     */
    // app/Http/Controllers/OficioController.php -> dentro del método store()

    public function store(Request $request)
    {
        $request->validate([
            'numero_oficio' => 'required|string|max:255',
            'remitente' => 'required|string|max:255',
            'municipio' => 'required|string|max:255',
            'asunto' => 'required|string',
            'fecha_recepcion' => 'required|date',
            // --- NUEVAS REGLAS DE VALIDACIÓN ---
            'tipo_correspondencia' => 'required|string',
            'prioridad' => 'required|string',
            'numero_oficio_dependencia' => 'required|string|max:255',
            'fecha_limite' => 'nullable|date', // Opcional
            'localidad' => 'required|string|max:255',
            'observaciones' => 'nullable|string', // Opcional
        ]);

        Oficio::create($request->all());

        return redirect()->route('principal')->with('success', 'Oficio registrado correctamente.');
    }


    public function show(Request $request, Oficio $oficio)
    {
        $user = Auth::user();
        $mode = $request->query('mode', 'operativo'); // Por defecto 'operativo'
        $oficio->load('areas');

        // Determinamos qué turnos mostrar según el clic que diste en el Dashboard
        if ($mode == 'recepcion' && ($user->role == 'admin' || $user->role == 'recepcionista')) {
            $turnosParaMostrar = $oficio->areas; // Ver todo para gestionar
        } elseif ($mode == 'gestion' && ($user->role == 'admin' || $user->role == 'jefe_area' || $user->role == 'secretaria_area')) {
            $turnosParaMostrar = $oficio->areas->where('id', $user->area_id); // Solo los de mi área para asignar
        } else {
            // Modo operativo: Solo el turno que tengo asignado yo
            $turnosParaMostrar = $oficio->areas->filter(function ($area) use ($user) {
                return $area->pivot->user_id == $user->id;
            });
        }

        $areasAsignadasIds = $oficio->areas->pluck('id')->toArray();
        $areasDisponibles = Area::whereNotIn('id', $areasAsignadasIds)->get();

        $personalPorArea = [];
        foreach ($turnosParaMostrar as $areaTurnada) {
            $personalPorArea[$areaTurnada->id] = User::where('area_id', $areaTurnada->id)->get();
        }

        return view('oficios.show', compact('oficio', 'areasDisponibles', 'personalPorArea', 'turnosParaMostrar', 'mode'));
    }

    public function turnar(Request $request, Oficio $oficio)
    {
        $request->validate([
            'areas' => 'required|array',
            'instrucciones' => 'required|array',
        ]);

        foreach ($request->areas as $index => $area_id) {
            if ($area_id) {
                // Usamos attach para añadir sin borrar lo anterior
                $oficio->areas()->attach($area_id, [
                    'instruccion' => $request->instrucciones[$index],
                    'estatus' => 'Turnado'
                ]);
            }
        }

        $oficio->update(['estatus' => 'Turnado']);
        return redirect()->route('oficios.show', $oficio)->with('success', 'Nuevas áreas añadidas correctamente.');
    }

    public function eliminarTurno($pivote_id)
    {
        if (Auth::user()->role !== 'admin') {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        // Eliminamos directamente de la tabla pivote
        DB::table('area_oficio')->where('id', $pivote_id)->delete();

        return back()->with('success', 'Turno eliminado correctamente.');
    }

    /**
     * Asigna un turno específico a una persona.
     */
    public function asignar(Request $request, Oficio $oficio)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pivote_id' => 'required|exists:area_oficio,id'
        ]);

        DB::table('area_oficio')
            ->where('id', $request->pivote_id)
            ->update([
                'user_id' => $request->user_id,
                'estatus' => 'Asignado'
            ]);

        $oficio->update(['estatus' => 'En Proceso']);

        return redirect()->route('oficios.show', $oficio)->with('success', 'Oficio asignado y estatus actualizado.');
    }



    public function destroy(Oficio $oficio)
    {
        // Por seguridad, solo los admins pueden borrar oficios.
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $oficio->delete(); // Esto ejecuta el borrado suave

        return redirect()->route('principal')->with('success', 'Oficio eliminado correctamente.');
    }

    // app/Http/Controllers/OficioController.php

    /**
     * Muestra una vista previa del oficio listo para imprimir.
     */
    public function generarOficio(Oficio $oficio)
    {
        $user = Auth::user();

        // Cargamos las relaciones para tener acceso a las áreas
        $oficio->load('areas');

        // --- INICIO DE LA LÓGICA DE FILTRADO ---
        $turnosParaImprimir = $oficio->areas; // Por defecto, mostramos todos

        if ($user->role != 'admin') {
            // Si NO es un administrador, filtramos la colección para mostrar
            // SOLO el turno que corresponde al área del usuario.
            $turnosParaImprimir = $oficio->areas->filter(function ($area) use ($user) {
                return $area->id == $user->area_id;
            });
        }
        // --- FIN DE LA LÓGICA ---

        // Pasamos la variable filtrada a la vista
        return view('oficios.generar', compact('oficio', 'turnosParaImprimir'));
    }
}