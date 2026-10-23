<?php

namespace App\Http\Controllers;

use App\Models\ComisionOdso;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComisionOdsoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = ComisionOdso::with([
            'user' => function ($q) {
                $q->withTrashed();
            },
            'user.area'
        ]);

        // --- SEGURIDAD POR ROLES ---
        if (in_array($user->role, ['jefe_area', 'secretaria_area', 'recepcionista'])) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('user', function ($sub) use ($user) {
                    $sub->withTrashed()->where('area_id', $user->area_id);
                })->orWhere('jefe_area_id', $user->id);
            });
        } elseif ($user->role !== 'admin' && !$user->isRecursosHumanos() && !$user->canViewAllComisiones()) {
            $query->where('user_id', $user->id);
        }

        // --- BÚSQUEDA MÚLTIPLE ---
        if ($request->filled('search')) {
            $terms = array_map('trim', explode(',', $request->search));

            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    if (empty($term)) continue;
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

        return view('comisiones_odso.index', compact('comisiones'));
    }

    public function create()
    {
        $user = Auth::user();
        $area = $user->area;

        if (!$user->canAccessOdso()) {
            return redirect()->route('comisiones.create')
                ->with('error', 'Los oficios ODSO de supervisión de obra están reservados para las áreas técnicas autorizadas (DIH, DVPH, DCA y DAyF).');
        }

        $vehiculos = Vehiculo::orderBy('marca')->get();
        $proyectos = Proyecto::with('unidadesAdministrativas')->get();

        $currentYear = now()->year;
        $ultimoConsecutivo = ComisionOdso::where('anio', $currentYear)->max('consecutivo');
        $siguienteConsecutivo = $ultimoConsecutivo ? $ultimoConsecutivo + 1 : 1;
        $siguienteFolioOdso = 'ODSO-OC-' . str_pad($siguienteConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;

        // Titulares que pueden autorizar supervisión de obra
        $jefesAutorizadores = User::with('area')
            ->where('role', 'jefe_area')
            ->whereIn('area_id', [8, 7, 9, 4, 1]) // DIH, DVPH, DCA, DAyF, DG
            ->orderByRaw("FIELD(area_id, 8, 7, 9, 4, 1)")
            ->get();

        // Titular por defecto: si es DVPH -> 258 (Orlando), en otro caso -> 292 (Germán)
        $defaultJefeId = ($area && $area->prefijo === 'DVPH') ? 258 : 292;

        return view('comisiones_odso.create', compact(
            'vehiculos',
            'proyectos',
            'siguienteFolioOdso',
            'area',
            'jefesAutorizadores',
            'defaultJefeId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dias_comision' => 'required',
            'actividad' => 'required',
            'lugar' => 'required',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'jefe_area_id' => 'nullable|exists:users,id',
        ]);

        $user = Auth::user();
        if (!$user->canAccessOdso()) {
            return redirect()->route('comisiones.create')
                ->with('error', 'No tiene permisos para generar oficios ODSO.');
        }

        $area = $user->area;

        // Determinar quién autoriza la comisión
        $jefeArea = null;
        if ($request->filled('jefe_area_id')) {
            $jefeArea = User::find($request->jefe_area_id);
        }

        if (!$jefeArea) {
            if ($area && $area->prefijo === 'DVPH') {
                $jefeArea = User::find(258) ?? User::where('area_id', $user->area_id)->where('role', 'jefe_area')->first();
            } else {
                $jefeArea = User::find(292) ?? User::where('area_id', $user->area_id)->where('role', 'jefe_area')->first();
            }
        }

        if (!$jefeArea) {
            return back()->with('error', 'No se encontró Titular que autorice la comisión.');
        }

        $currentYear = now()->year;

        $comision = DB::transaction(function () use ($user, $jefeArea, $currentYear, $request) {
            $ultimoConsecutivo = ComisionOdso::where('anio', $currentYear)
                ->lockForUpdate()
                ->max('consecutivo');
            $siguienteConsecutivo = $ultimoConsecutivo ? $ultimoConsecutivo + 1 : 1;
            $numeroOficio = 'ODSO-OC-' . str_pad($siguienteConsecutivo, 2, '0', STR_PAD_LEFT) . '/' . $currentYear;

            return ComisionOdso::create([
                'consecutivo' => $siguienteConsecutivo,
                'anio' => $currentYear,
                'oficio_numero' => $numeroOficio,
                'user_id' => $user->id,
                'jefe_area_id' => $jefeArea->id,
                'dias_comision' => $request->dias_comision,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'actividad' => $request->actividad,
                'lugar' => $request->lugar,
                'vehiculo_id' => $request->vehiculo_id,
                'proyecto_id' => $request->proyecto_id,
                'unidad_administrativa_id' => $request->unidad_administrativa_id,
            ]);
        });

        return redirect()->route('comisiones-odso.show', $comision);
    }

    public function show(ComisionOdso $comision_odso)
    {
        $comision_odso->load(['user' => fn($q) => $q->withTrashed(), 'user.area', 'jefeArea', 'vehiculo', 'proyecto']);
        Carbon::setLocale('es');
        $fechaFormateada = Carbon::parse($comision_odso->created_at)->isoFormat('D [de] MMMM [de] YYYY');
        $comision = $comision_odso;
        return view('comisiones_odso.show', compact('comision', 'fechaFormateada'));
    }

    public function edit(ComisionOdso $comision_odso)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        $comision = $comision_odso;
        $vehiculos = Vehiculo::all();
        $proyectos = Proyecto::all();
        $jefesAutorizadores = User::with('area')
            ->where('role', 'jefe_area')
            ->orderBy('name')
            ->get();
        return view('comisiones_odso.edit', compact('comision', 'vehiculos', 'proyectos', 'jefesAutorizadores'));
    }

    public function update(Request $request, ComisionOdso $comision_odso)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        $comision_odso->update($request->all());
        return redirect()->route('comisiones-odso.index')->with('success', 'Oficio ODSO actualizado.');
    }

    public function cancelar(ComisionOdso $comision_odso)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        $comision_odso->update(['status' => 'Cancelado']);
        return redirect()->route('comisiones-odso.index')->with('success', 'Oficio ODSO cancelado.');
    }

    public function toggleAcuse(Request $request, ComisionOdso $comision_odso)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && !$user->isRecursosHumanos()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            abort(403);
        }

        if ($comision_odso->entregado_acuse && $user->role !== 'admin') {
            if ($request->ajax()) {
                return response()->json(['error' => 'Solo el Administrador puede desactivar un acuse ya entregado.'], 403);
            }
            abort(403, 'Solo el Administrador puede desactivar un acuse ya entregado.');
        }

        $comision_odso->update([
            'entregado_acuse' => !$comision_odso->entregado_acuse
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'entregado' => $comision_odso->entregado_acuse,
                'message' => $comision_odso->entregado_acuse ? 'Acuse marcado como entregado.' : 'Acuse marcado como pendiente.'
            ]);
        }

        return back()->with('success', 'Estatus del acuse actualizado.');
    }
}
