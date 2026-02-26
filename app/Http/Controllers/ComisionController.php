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
        // Iniciamos la consulta cargando la relación del usuario para evitar múltiples consultas (Eager Loading)
        $query = Comision::with('user');

        // --- LÓGICA DE FILTRADO POR ROLES ---
        if ($user->role == 'jefe_area' || $user->role == 'secretaria_area' || $user->role == 'recepcionista') {
            // Los jefes y secretarias ven las comisiones de toda su área
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('area_id', $user->area_id);
            });
        } elseif ($user->role !== 'admin') {
            // El personal operativo (o cualquier otro rol no admin) solo ve sus propias comisiones
            $query->where('user_id', $user->id);
        }
        // El 'admin' no entra en estas condiciones, por lo que sigue viendo todo

        // --- LÓGICA DE BÚSQUEDA ---
        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('oficio_numero', 'like', "%{$searchTerm}%")
                    ->orWhere('actividad', 'like', "%{$searchTerm}%")
                    ->orWhere('lugar', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Ordenamos por fecha de creación (los más recientes primero) y paginamos
        $comisiones = $query->latest()->paginate(15);

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

        // 1. Obtenemos el área y su prefijo (asumiendo que tu tabla 'areas' tiene una columna 'prefix' o 'name')
        $area = $user->area; // Asegúrate de tener la relación 'area' en el modelo User
        $prefijoArea = $area ? $area->prefijo : 'SD'; // 'SD' como respaldo si no hay área

        $jefeArea = User::where('area_id', $user->area_id)->where('role', 'jefe_area')->first();

        if (!$jefeArea) {
            return back()->with('error', 'No se encontró un Jefe de Área asignado a tu departamento.');
        }

        $currentYear = now()->year;
        $ultimoConsecutivo = Comision::where('anio', $currentYear)
            ->whereHas('user', function ($q) use ($user) {
                $q->where('area_id', $user->area_id);
            })
            ->max('consecutivo');

        $siguienteConsecutivo = $ultimoConsecutivo ? $ultimoConsecutivo + 1 : 1;

        // 3. GENERACIÓN DEL NÚMERO DE OFICIO DINÁMICO
        $numeroOficio = $prefijoArea . '-OC-' . str_pad($siguienteConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;

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

        // --- VALIDACIÓN DE ACCESO MULTI-PERFIL ---
        // Permitimos el acceso si:
        // 1. Es Administrador.
        // 2. Es el dueño de la comisión.
        // 3. Es la secretaria del área correspondiente.
        // 4. Es la recepcionista (que también apoya al área).

        $esAutorizado = ($user->role === 'admin') ||
            ($comision->user_id === $user->id) ||
            ($user->role === 'secretaria_area' && $user->area_id === $comision->user->area_id) ||
            ($user->role === 'recepcionista');

        if (!$esAutorizado) {
            abort(403, 'No tienes permiso para ver esta comisión.');
        }

        // Restricción de visualización para documentos cancelados
        if ($comision->status === 'Cancelado' && $user->role !== 'admin') {
            return redirect()->route('comisiones.index')
                ->with('error', 'Este oficio de comisión ha sido cancelado y ya no se puede visualizar.');
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