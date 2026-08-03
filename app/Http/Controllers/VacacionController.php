<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudVacacion;
use App\Models\SolicitudVacacionFecha;
use Illuminate\Support\Facades\Auth;

class VacacionController extends Controller
{
    /**
     * Store a new vacation request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fechas' => 'required|array|min:1',
            'fechas.*' => 'required|date_format:Y-m-d',
            'observaciones' => 'nullable|string|max:500',
        ]);

        // Verificamos que el usuario tenga derecho a vacaciones (fecha_alta >= 1 año)
        $user = Auth::user();
        if (!$user->fecha_alta) {
            return back()->with('error', 'Tu fecha de ingreso no está registrada.');
        }

        $fechaAltaCarbon = \Carbon\Carbon::parse($user->fecha_alta);
        if ($fechaAltaCarbon->diffInYears(now()) < 1) {
            return back()->with('error', 'No cumples con el año mínimo de antigüedad requerido para solicitar vacaciones.');
        }

        // Cargar configuración de periodos vacacionales para el año actual
        $config = \App\Models\ConfiguracionVacacion::where('anio', now()->year)->first();
        if (!$config) {
            return back()->with('error', 'Los períodos vacacionales para el año ' . now()->year . ' aún no han sido configurados por el administrador.');
        }

        $p1Inicio = $config->periodo1_inicio ? $config->periodo1_inicio->format('Y-m-d') : null;
        $p1Fin = $config->periodo1_fin ? $config->periodo1_fin->format('Y-m-d') : null;
        $p2Inicio = $config->periodo2_inicio ? $config->periodo2_inicio->format('Y-m-d') : null;
        $p2Fin = $config->periodo2_fin ? $config->periodo2_fin->format('Y-m-d') : null;

        // Calcular días acumulados (Aprobados o Pendientes) para este usuario en cada período
        $userFechas = \App\Models\SolicitudVacacionFecha::whereHas('solicitud', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereIn('estatus', ['Pendiente', 'Aprobado']);
            })
            ->pluck('fecha')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        $diasPeriodo1Usados = 0;
        $diasPeriodo2Usados = 0;
        foreach ($userFechas as $f) {
            if ($p1Inicio && $p1Fin && $f >= $p1Inicio && $f <= $p1Fin) {
                $diasPeriodo1Usados++;
            } elseif ($p2Inicio && $p2Fin && $f >= $p2Inicio && $f <= $p2Fin) {
                $diasPeriodo2Usados++;
            }
        }

        // Validar anticipación de al menos 3 días hábiles
        $earliestAllowed = self::getEarliestAllowedDate(now());
        foreach ($request->input('fechas') as $fecha) {
            if ($fecha < $earliestAllowed) {
                return back()->with('error', 'Las fechas solicitadas deben ser con al menos 3 días hábiles de anticipación (' . \Carbon\Carbon::parse($earliestAllowed)->format('d/m/Y') . ' o posterior).');
            }
        }

        // Validar cada fecha de la nueva solicitud contra los periodos y límites
        $nuevasFechasP1 = 0;
        $nuevasFechasP2 = 0;
        foreach ($request->input('fechas') as $fecha) {
            $perteneceP1 = ($p1Inicio && $p1Fin && $fecha >= $p1Inicio && $fecha <= $p1Fin);
            $perteneceP2 = ($p2Inicio && $p2Fin && $fecha >= $p2Inicio && $fecha <= $p2Fin);

            if (!$perteneceP1 && !$perteneceP2) {
                return back()->with('error', 'La fecha ' . \Carbon\Carbon::parse($fecha)->format('d/m/Y') . ' está fuera de los períodos vacacionales autorizados para este año.');
            }

            if ($perteneceP1) {
                $nuevasFechasP1++;
            } else {
                $nuevasFechasP2++;
            }
        }

        if (($diasPeriodo1Usados + $nuevasFechasP1) > 5) {
            return back()->with('error', 'No puedes solicitar más de 5 días de vacaciones para el Período 1 (ya tienes ' . $diasPeriodo1Usados . ' días registrados o pendientes y estás intentando solicitar ' . $nuevasFechasP1 . ' adicionales).');
        }

        if (($diasPeriodo2Usados + $nuevasFechasP2) > 5) {
            return back()->with('error', 'No puedes solicitar más de 5 días de vacaciones para el Período 2 (ya tienes ' . $diasPeriodo2Usados . ' días registrados o pendientes y estás intentando solicitar ' . $nuevasFechasP2 . ' adicionales).');
        }

        // Crear la solicitud
        $solicitud = SolicitudVacacion::create([
            'user_id' => $user->id,
            'estatus' => 'Pendiente',
            'observaciones' => $request->input('observaciones'),
        ]);

        // Crear las fechas asociadas
        foreach ($request->input('fechas') as $fecha) {
            SolicitudVacacionFecha::create([
                'solicitud_vacacion_id' => $solicitud->id,
                'fecha' => $fecha,
            ]);
        }

        return back()->with('success', 'Tu solicitud de vacaciones ha sido enviada correctamente.');
    }

    /**
     * Cancel/delete a pending vacation request.
     */
    public function destroy(SolicitudVacacion $solicitud)
    {
        // Solo el propio usuario puede cancelar su solicitud y solo si está Pendiente
        if ($solicitud->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        if ($solicitud->estatus !== 'Pendiente') {
            return back()->with('error', 'No puedes cancelar una solicitud que ya ha sido procesada.');
        }

        $solicitud->delete();

        return back()->with('success', 'Solicitud de vacaciones cancelada correctamente.');
    }

    /**
     * Admin processes (approve/reject) a request.
     */
    public function procesar(Request $request, SolicitudVacacion $solicitud)
    {
        $user = Auth::user();

        // El admin puede procesar cualquier solicitud.
        // El jefe de área puede procesar solicitudes del personal de su propia área (excluyéndose a sí mismo).
        $isJefeAreaAutorizado = $user->role === 'jefe_area' && 
                                $solicitud->user->area_id === $user->area_id && 
                                $solicitud->user_id !== $user->id;

        if ($user->role !== 'admin' && !$isJefeAreaAutorizado) {
            abort(403, 'Acción no autorizada.');
        }

        $request->validate([
            'accion' => 'required|in:aprobar,rechazar',
        ]);

        $accion = $request->input('accion');
        $estatus = $accion === 'aprobar' ? 'Aprobado' : 'Rechazado';

        $solicitud->update([
            'estatus' => $estatus,
        ]);

        return back()->with('success', 'La solicitud ha sido ' . ($accion === 'aprobar' ? 'aprobada' : 'rechazada') . ' correctamente.');
    }

    /**
     * Calcula la fecha más cercana permitida (3 días hábiles después de la fecha de inicio).
     */
    public static function getEarliestAllowedDate(\Carbon\Carbon $startDate)
    {
        $date = $startDate->copy();
        $businessDaysAdded = 0;
        while ($businessDaysAdded < 3) {
            $date->addDay();
            if (!$date->isWeekend()) {
                $businessDaysAdded++;
            }
        }
        return $date->toDateString();
    }

    /**
     * Guarda o actualiza la configuración de periodos vacacionales.
     */
    public function guardarConfiguracion(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acción no autorizada.');
        }

        $request->validate([
            'periodo1_inicio' => 'required|date',
            'periodo1_fin' => 'required|date|after_or_equal:periodo1_inicio',
            'periodo2_inicio' => 'required|date',
            'periodo2_fin' => 'required|date|after_or_equal:periodo2_inicio',
        ]);

        \App\Models\ConfiguracionVacacion::updateOrCreate(
            ['anio' => now()->year],
            [
                'periodo1_inicio' => $request->input('periodo1_inicio'),
                'periodo1_fin' => $request->input('periodo1_fin'),
                'periodo2_inicio' => $request->input('periodo2_inicio'),
                'periodo2_fin' => $request->input('periodo2_fin'),
            ]
        );

        return back()->with('success', 'La configuración de períodos vacacionales para el año ' . now()->year . ' se ha guardado correctamente.');
    }
}
