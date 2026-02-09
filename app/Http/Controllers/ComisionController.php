<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request; // <-- Importante que esta línea esté
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ComisionController extends Controller
{
    /**
     * Muestra una lista de las comisiones con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        // Iniciamos la consulta base
        $query = Comision::query();

        // Si el usuario no es admin, filtramos para que solo vea sus comisiones
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Si hay un término de búsqueda en la solicitud...
        if ($request->filled('search')) {
            $searchTerm = $request->search;

            // ...filtramos los resultados
            $query->where(function ($q) use ($searchTerm) {
                // Busca en el número de oficio, actividad o lugar
                $q->where('oficio_numero', 'like', "%{$searchTerm}%")
                    ->orWhere('actividad', 'like', "%{$searchTerm}%")
                    ->orWhere('lugar', 'like', "%{$searchTerm}%")
                    // También busca en la tabla relacionada 'users' por el nombre del solicitante
                    ->orWhereHas('user', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Ordenamos del más antiguo al más reciente (menor a mayor por fecha) y paginamos
        $comisiones = $query->with('user')->oldest()->paginate(15);

        return view('comisiones.index', compact('comisiones'));
    }

    /**
     * Muestra el formulario para crear una nueva comisión.
     */
    public function create()
    {
        $vehiculos = Vehiculo::orderBy('marca')->get();
        $proyectos = Proyecto::with('unidadesAdministrativas')->get();

        return view('comisiones.create', compact('vehiculos', 'proyectos'));
    }

    /**
     * Guarda una nueva comisión en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dias_comision' => 'required|string|max:255',
            'actividad' => 'required|string',
            'lugar' => 'required|string|max:255',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'unidad_administrativa_id' => 'nullable|exists:unidad_administrativas,id',
        ]);

        $user = Auth::user();
        $jefeArea = User::where('area_id', $user->area_id)->where('role', 'jefe_area')->first();
        if (!$jefeArea) {
            return back()->with('error', 'No se encontró un Jefe de Área asignado a tu departamento.');
        }

        $currentYear = now()->year;
        $ultimoConsecutivo = Comision::where('anio', $currentYear)->max('consecutivo');
        $siguienteConsecutivo = $ultimoConsecutivo ? $ultimoConsecutivo + 1 : 1;
        $numeroOficio = 'DGI-OC-' . str_pad($siguienteConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;

        $comision = Comision::create([
            'consecutivo' => $siguienteConsecutivo,
            'anio' => $currentYear,
            'oficio_numero' => $numeroOficio,
            'user_id' => $user->id,
            'jefe_area_id' => $jefeArea->id,
            'dias_comision' => $request->dias_comision,
            'actividad' => $request->actividad,
            'lugar' => $request->lugar,
            'vehiculo_id' => $request->vehiculo_id,
            'proyecto_id' => $request->proyecto_id,
            'unidad_administrativa_id' => $request->unidad_administrativa_id,
        ]);

        return redirect()->route('comisiones.show', $comision);
    }

    /**
     * Muestra el oficio de comisión generado.
     */
    public function show(Comision $comision)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $comision->user_id !== $user->id) {
            abort(403, 'No tienes permiso para ver esta comisión.');
        }
        if ($comision->status === 'Cancelado' && $user->role !== 'admin') {
            return redirect()->route('comisiones.index')->with('error', 'Este oficio de comisión ha sido cancelado y ya no se puede visualizar.');
        }

        Carbon::setLocale('es');
        $fechaFormateada = Carbon::parse($comision->created_at)->isoFormat('D [de] MMMM [de] YYYY');

        return view('comisiones.show', compact('comision', 'fechaFormateada'));
    }

    /**
     * Cancela un oficio de comisión.
     */
    public function cancelar(Comision $comision)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $comision->update(['status' => 'Cancelado']);

        return redirect()->route('comisiones.index')->with('success', 'El oficio de comisión ha sido cancelado.');
    }
}