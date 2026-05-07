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
    public function index(Request $request)
    {
        $user = Auth::user();

        $correspondenciaGeneral = ($user->role == 'admin' || $user->role == 'recepcionista')
            ? Oficio::latest()->paginate(10, ['*'], 'gen_page')
            : collect();

        $gestionArea = ($user->role == 'admin' || $user->role == 'jefe_area' || $user->role == 'secretaria_area')
            ? Oficio::whereHas('areas', function ($q) use ($user) {
                $q->where('areas.id', $user->area_id);
            })->latest()->paginate(10, ['*'], 'gest_page')
            : collect();

        $misTurnosAsignados = Oficio::whereHas('areas', function ($q) use ($user) {
            $q->where('area_oficio.user_id', $user->id);
        })->latest()->paginate(9, ['*'], 'task_page');

        return view('principal', compact('correspondenciaGeneral', 'gestionArea', 'misTurnosAsignados'));
    }

    public function create()
    {
        return view('oficios.create');
    }

    public function store(Request $request)
    {
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


    public function show(Request $request, Oficio $oficio)
    {
        $user = Auth::user();
        $mode = $request->query('mode', 'operativo');
        $oficio->load('areas');

        if ($mode == 'recepcion' && ($user->role == 'admin' || $user->role == 'recepcionista')) {
            $turnosParaMostrar = $oficio->areas;
        } elseif ($mode == 'gestion' && ($user->role == 'admin' || $user->role == 'jefe_area' || $user->role == 'secretaria_area')) {
            $turnosParaMostrar = $oficio->areas->where('id', $user->area_id);
        } else {
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

        DB::table('area_oficio')->where('id', $pivote_id)->delete();

        return back()->with('success', 'Turno eliminado correctamente.');
    }

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
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $oficio->delete();

        return redirect()->route('principal')->with('success', 'Oficio eliminado correctamente.');
    }

    public function generarOficio(Oficio $oficio)
    {
        $user = Auth::user();
        $oficio->load('areas');

        $turnosParaImprimir = $oficio->areas;

        if ($user->role != 'admin') {
            $turnosParaImprimir = $oficio->areas->filter(function ($area) use ($user) {
                return $area->id == $user->area_id;
            });
        }

        return view('oficios.generar', compact('oficio', 'turnosParaImprimir'));
    }
}