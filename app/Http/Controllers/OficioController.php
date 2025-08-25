<?php

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

        if ($user->role == 'admin') {
            // El admin ve todos los oficios
            $oficios = Oficio::latest()->get();
        } else {
            // Los demás usuarios solo ven los oficios turnados a su área
            $oficios = Oficio::whereHas('areas', function ($query) use ($user) {
                $query->where('area_id', $user->area_id);
            })->latest()->get();
        }

        return view('dashboard', compact('oficios'));
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

    return redirect()->route('dashboard')->with('success', 'Oficio registrado correctamente.');
}

    /**
     * Muestra los detalles de un oficio específico.
     */
    // app/Http/Controllers/OficioController.php

/**
 * Muestra los detalles de un oficio específico.
 */
        // app/Http/Controllers/OficioController.php

/**
 * Muestra los detalles de un oficio específico.
 */
        public function show(Oficio $oficio)
        {
            $areasDisponibles = Area::all();
            $user = Auth::user();

            // Carga las áreas turnadas del oficio
            $oficio->load('areas');
            
            $turnosParaMostrar = $oficio->areas; // Por defecto, mostramos todos

            // --- LÓGICA DE FILTRADO ACTUALIZADA ---
            if ($user->role != 'admin') {
                $turnosParaMostrar = $oficio->areas->filter(function ($area) use ($user) {
                    return $area->id == $user->area_id;
                });
            }

            $personalPorArea = [];
            // Obtenemos el personal solo para las áreas que se van a mostrar
            foreach ($turnosParaMostrar as $areaTurnada) {
                $personalPorArea[$areaTurnada->id] = User::where('area_id', $areaTurnada->id)->get();
            }

            // Pasamos la variable $turnosParaMostrar a la vista
            return view('oficios.show', compact('oficio', 'areasDisponibles', 'personalPorArea', 'turnosParaMostrar'));
        }

    /**
     * Turna un oficio a una o más áreas.
     */
    public function turnar(Request $request, Oficio $oficio)
    {
        $request->validate([
            'areas' => 'required|array|min:1',
            'areas.*' => 'exists:areas,id',
            'instrucciones' => 'required|array|min:1',
            'instrucciones.*' => 'required|string',
        ]);

        // Usamos un array para construir los datos que vamos a adjuntar
        $turnos = [];
        foreach ($request->areas as $index => $area_id) {
            // Evitamos añadir turnos duplicados si se seleccionó la misma área varias veces
            if (!isset($turnos[$area_id])) {
                $turnos[$area_id] = [
                    'instruccion' => $request->instrucciones[$index],
                    'estatus' => 'Turnado'
                ];
            }
        }

        // El método sync es más seguro: añade los nuevos, mantiene los existentes y quita los que no están en la lista
        $oficio->areas()->sync($turnos);

        $oficio->update(['estatus' => 'Turnado']);

        return redirect()->route('oficios.show', $oficio)->with('success', 'Oficio turnado a múltiples áreas correctamente.');
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

        return redirect()->route('oficios.show', $oficio)->with('success', 'Oficio asignado correctamente.');
    }

    

        public function destroy(Oficio $oficio)
        {
            // Por seguridad, solo los admins pueden borrar oficios.
            if (auth()->user()->role !== 'admin') {
                return back()->with('error', 'No tienes permiso para realizar esta acción.');
            }

            $oficio->delete(); // Esto ejecuta el borrado suave

            return redirect()->route('dashboard')->with('success', 'Oficio eliminado correctamente.');
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