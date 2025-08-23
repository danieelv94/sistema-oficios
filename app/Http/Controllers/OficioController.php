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
    public function store(Request $request)
    {
        $request->validate([
            'numero_oficio' => 'required|string|max:255',
            'remitente' => 'required|string|max:255',
            'municipio' => 'required|string|max:255',
            'asunto' => 'required|string',
            'fecha_recepcion' => 'required|date',
        ]);

        Oficio::create($request->all());

        return redirect()->route('dashboard')->with('success', 'Oficio registrado correctamente.');
    }

    /**
     * Muestra los detalles de un oficio específico.
     */
    public function show(Oficio $oficio)
    {
        $areasDisponibles = Area::all(); // Renombrado para evitar confusión
        $oficio->load('areas'); // Carga las áreas ya asignadas al oficio

        $personalPorArea = [];
        // Obtenemos el personal solo para las áreas que ya han sido turnadas
        foreach ($oficio->areas as $areaTurnada) {
            $personalPorArea[$areaTurnada->id] = User::where('area_id', $areaTurnada->id)->get();
        }

        return view('oficios.show', compact('oficio', 'areasDisponibles', 'personalPorArea'));
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
}