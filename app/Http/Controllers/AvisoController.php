<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AvisoController extends Controller
{
    /**
     * Guarda una nueva circular y la asigna a los usuarios correspondientes.
     */
    public function index()
    {
        $user = auth()->user();

        // 1. Verificamos roles autorizados
        if (!in_array($user->role, ['admin', 'secretaria_area', 'jefe_area'])) {
            abort(403);
        }

        $query = Aviso::with('autor', 'area')
            ->withCount([
                'usuarios as leidos' => function ($query) {
                    $query->whereNotNull('leido_at');
                }
            ])
            ->withCount('usuarios as total');

        // --- LÓGICA DE PRIVACIDAD POR ÁREA ---
        // Si NO es admin, solo ve los avisos generados por su propia área
        if ($user->role !== 'admin') {
            // Filtramos por el area_id del usuario que inició sesión
            $query->whereHas('autor', function ($q) use ($user) {
                $q->where('area_id', $user->area_id);
            });
        }

        $avisos = $query->latest()->paginate(10);

        return view('avisos.index', compact('avisos'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'prioridad' => 'required|in:Normal,Urgente',
            'area_id' => 'nullable|exists:areas,id',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Máximo 10MB
        ]);

        $data = [
            'user_id' => Auth::id(),
            'titulo' => $request->titulo,
            'mensaje' => $request->mensaje,
            'prioridad' => $request->prioridad,
            'area_id' => $request->area_id
        ];

        // Lógica para subir el archivo
        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('avisos', 'public');
            $data['archivo'] = $path;
        }

        $aviso = Aviso::create($data);

        // Determinar destinatarios (tu lógica actual)
        $query = User::query();
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }
        $usuariosIds = $query->pluck('id');
        $aviso->usuarios()->attach($usuariosIds);

        return redirect()->route('avisos.index')->with('success', 'Circular emitida con éxito.');
    }

    /**
     * Muestra la pantalla de avisos que el usuario no ha leído.
     */
    public function pendientes()
    {
        $user = Auth::user();

        // Avisos sin leer (Muro principal)
        $avisos = $user->avisos()
            ->wherePivot('leido_at', null)
            ->latest()
            ->get();

        // Avisos ya leídos (Historial personal)
        $leidos = $user->avisos()
            ->wherePivotNotNull('leido_at')
            ->latest()
            ->take(10) // Mostramos los últimos 10
            ->get();

        return view('avisos.pendientes', compact('avisos', 'leidos'));
    }

    /**
     * Marca un aviso como "Enterado".
     */
    public function leer(Aviso $aviso)
    {
        $user = Auth::user();
        $user->avisos()->updateExistingPivot($aviso->id, [
            'leido_at' => now()
        ]);

        return back()->with('success', 'Has confirmado la recepción de la circular: ' . $aviso->titulo);
    }

    public function seguimiento(Aviso $aviso)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'secretaria_area', 'jefe_area'])) {
            abort(403);
        }

        // --- PRIVACIDAD: El usuario debe ser admin O pertenecer al área que creó el aviso ---
        // Accedemos al área del autor del aviso para comparar
        if ($user->role !== 'admin' && $user->area_id !== $aviso->autor->area_id) {
            abort(403, 'No tienes permiso para ver métricas de otras áreas.');
        }

        $usuarios = $aviso->usuarios()->with('area')->get();

        $total = $usuarios->count();
        $leidos = $usuarios->whereNotNull('pivot.leido_at')->count();
        $pendientes = $total - $leidos;
        $porcentaje = $total > 0 ? round(($leidos / $total) * 100) : 0;

        return view('avisos.seguimiento', compact('aviso', 'usuarios', 'total', 'leidos', 'pendientes', 'porcentaje'));
    }

    public function create()
    {
        // Solo permitimos el acceso a Admin y Secretarias
        if (!in_array(auth()->user()->role, ['admin', 'secretaria_area', 'jefe_area'])) {
            abort(403);
        }

        // Usamos 'name' que es el nombre real de la columna en tu SQL
        $areas = \App\Models\Area::orderBy('name')->get();

        return view('avisos.create', compact('areas'));
    }
}