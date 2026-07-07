<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ComisionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Comision::with([
            'user' => function ($q) {
                $q->withTrashed();
            },
            'user.area'
        ]);

        // --- SEGURIDAD POR ROLES ---
        if (in_array($user->role, ['jefe_area', 'secretaria_area', 'recepcionista'])) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->withTrashed()->where('area_id', $user->area_id);
            });
        } elseif ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // --- LÓGICA DE BÚSQUEDA MÚLTIPLE (Admin y Secretarias) ---
        if (in_array($user->role, ['admin', 'secretaria_area']) && $request->filled('search')) {
            $terms = array_map('trim', explode(',', $request->search));

            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere(function ($sub) use ($term) {
                        $sub->where('oficio_numero', 'like', "%{$term}%")
                            ->orWhere('actividad', 'like', "%{$term}%")
                            ->orWhere('lugar', 'like', "%{$term}%")
                            ->orWhere('dias_comision', 'like', "%{$term}%")
                            ->orWhereHas('user', function ($u) use ($term) {
                                $u->withTrashed()->where('name', 'like', "%{$term}%");
                            });
                    });
                }
            });
        }

        $perPage = ($user->role === 'admin' && $request->filled('search')) ? 100 : 15;
        $comisiones = $query->latest()->paginate($perPage);

        return view('comisiones.index', compact('comisiones'));
    }

    // Los métodos create, store, show, edit, update y cancelar se mantienen igual que en tu versión funcional...
    public function create()
    {
        $vehiculos = Vehiculo::orderBy('marca')->get();
        $proyectos = Proyecto::with('unidadesAdministrativas')->get();
        return view('comisiones.create', compact('vehiculos', 'proyectos'));
    }

    public function store(Request $request)
    {
        $request->validate(['dias_comision' => 'required', 'actividad' => 'required', 'lugar' => 'required', 'hora_inicio' => 'required', 'hora_fin' => 'required']);
        $user = Auth::user();
        $area = $user->area;
        $jefeArea = User::where('area_id', $user->area_id)->where('role', 'jefe_area')->first();
        if (!$jefeArea)
            return back()->with('error', 'No se encontró Jefe de Área.');
        $currentYear = now()->year;
        $ultimoConsecutivo = Comision::where('anio', $currentYear)->whereHas('user', fn($q) => $q->where('area_id', $user->area_id))->max('consecutivo');
        $siguienteConsecutivo = $ultimoConsecutivo ? $ultimoConsecutivo + 1 : 1;
        $numeroOficio = ($area ? $area->prefijo : 'SD') . '-OC-' . str_pad($siguienteConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;
        $comision = Comision::create(['consecutivo' => $siguienteConsecutivo, 'anio' => $currentYear, 'oficio_numero' => $numeroOficio, 'user_id' => $user->id, 'jefe_area_id' => $jefeArea->id, 'dias_comision' => $request->dias_comision, 'hora_inicio' => $request->hora_inicio, 'hora_fin' => $request->hora_fin, 'actividad' => $request->actividad, 'lugar' => $request->lugar, 'vehiculo_id' => $request->vehiculo_id, 'proyecto_id' => $request->proyecto_id]);
        return redirect()->route('comisiones.show', $comision);
    }

    public function show(Comision $comision)
    {
        $comision->load(['user' => fn($q) => $q->withTrashed(), 'user.area']);
        Carbon::setLocale('es');
        $fechaFormateada = Carbon::parse($comision->created_at)->isoFormat('D [de] MMMM [de] YYYY');
        return view('comisiones.show', compact('comision', 'fechaFormateada'));
    }

    public function edit(Comision $comision)
    {
        if (Auth::user()->role !== 'admin')
            abort(403);
        $vehiculos = Vehiculo::all();
        $proyectos = Proyecto::all();
        return view('comisiones.edit', compact('comision', 'vehiculos', 'proyectos'));
    }

    public function update(Request $request, Comision $comision)
    {
        if (Auth::user()->role !== 'admin')
            abort(403);
        $comision->update($request->all());
        return redirect()->route('comisiones.index')->with('success', 'Comisión actualizada.');
    }

    public function cancelar(Comision $comision)
    {
        if (Auth::user()->role !== 'admin')
            abort(403);
        $comision->update(['status' => 'Cancelado']);
        return redirect()->route('comisiones.index')->with('success', 'Oficio cancelado.');
    }

    public function recursosHumanosIndex(Request $request)
    {
        $user = Auth::user();
        $isRH = $user->subarea && ($user->subarea->prefijo === 'SRH' || strpos(strtolower($user->subarea->name), 'recursos humanos') !== false);
        if ($user->role !== 'admin' && !$isRH) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $filtro = $request->input('filtro', 'Todos'); // Todos, Pendientes, Entregados
        $query = Comision::with([
            'user' => function ($q) {
                $q->withTrashed();
            },
            'user.area'
        ]);

        if ($filtro === 'Pendientes') {
            $query->where('entregado_acuse', false);
        } elseif ($filtro === 'Entregados') {
            $query->where('entregado_acuse', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('oficio_numero', 'like', "%{$search}%")
                  ->orWhere('actividad', 'like', "%{$search}%")
                  ->orWhere('lugar', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->withTrashed()->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $comisiones = $query->latest()->paginate(15);

        return view('comisiones.recursos_humanos', compact('comisiones', 'filtro'));
    }

    public function toggleAcuse(Request $request, Comision $comision)
    {
        $user = Auth::user();
        $isRH = $user->subarea && ($user->subarea->prefijo === 'SRH' || strpos(strtolower($user->subarea->name), 'recursos humanos') !== false);
        if ($user->role !== 'admin' && !$isRH) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            abort(403);
        }

        // Si ya está entregado y no es administrador, impedir desactivarlo
        if ($comision->entregado_acuse && $user->role !== 'admin') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Solo el Administrador puede desactivar un acuse ya entregado.'], 403);
            }
            abort(403, 'Solo el Administrador puede desactivar un acuse ya entregado.');
        }

        $comision->update([
            'entregado_acuse' => !$comision->entregado_acuse
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'entregado' => $comision->entregado_acuse,
                'message' => $comision->entregado_acuse ? 'Acuse marcado como entregado.' : 'Acuse marcado como pendiente.'
            ]);
        }

        return back()->with('success', 'Estatus del acuse actualizado.');
    }
}