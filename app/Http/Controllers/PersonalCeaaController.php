<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Comision;
use App\Models\ComisionOdso;
use App\Models\ConfiguracionVacacion;
use App\Models\Nivel;
use App\Models\PersonalIncidencia;
use App\Models\SolicitudVacacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PersonalCeaaController extends Controller
{
    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (!$user || !$user->canAccessPersonalCeaa()) {
            abort(403, 'No tienes permiso para acceder al módulo de Personal CEAA.');
        }
    }

    /**
     * Listado general / Directorio de Personal CEAA con indicadores de Kardex.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $anioActual = (int) $request->input('anio', now()->year);
        $hoy = now()->toDateString();

        $query = User::withTrashed()
            ->with(['area', 'subarea', 'nivel'])
            ->withCount([
                'comisiones as comisiones_anio_count' => function ($q) use ($anioActual) {
                    $q->where(function ($sub) use ($anioActual) {
                        $sub->where('anio', $anioActual)
                            ->orWhereYear('created_at', $anioActual);
                    })->where('status', '!=', 'Cancelado');
                },
                'comisionesOdso as comisiones_odso_anio_count' => function ($q) use ($anioActual) {
                    $q->where(function ($sub) use ($anioActual) {
                        $sub->where('anio', $anioActual)
                            ->orWhereYear('created_at', $anioActual);
                    })->where('status', '!=', 'Cancelado');
                },
                'incapacidades as incapacidades_anio_count' => function ($q) use ($anioActual) {
                    $q->where(function ($sub) use ($anioActual) {
                        $sub->whereYear('fecha_inicio', $anioActual)
                            ->orWhereYear('fecha_fin', $anioActual);
                    });
                },
                'permisos as permisos_anio_count' => function ($q) use ($anioActual) {
                    $q->where(function ($sub) use ($anioActual) {
                        $sub->whereYear('fecha_inicio', $anioActual)
                            ->orWhereYear('fecha_fin', $anioActual);
                    });
                },
                'incapacidades as incapacidad_activa_hoy_count' => function ($q) use ($hoy) {
                    $q->where('estatus', 'Activo')
                      ->whereDate('fecha_inicio', '<=', $hoy)
                      ->whereDate('fecha_fin', '>=', $hoy);
                },
            ])
            ->withSum([
                'vacacionesKardex as dias_vacaciones_kardex_sum' => function ($q) use ($anioActual) {
                    $q->where(function ($sub) use ($anioActual) {
                        $sub->whereYear('fecha_inicio', $anioActual)
                            ->orWhereYear('fecha_fin', $anioActual);
                    });
                }
            ], 'dias');

        // Filtro por estatus
        $estado = $request->input('estado', 'activos');
        if ($estado === 'activos') {
            $query->whereNull('deleted_at');
        } elseif ($estado === 'inactivos') {
            $query->onlyTrashed();
        } elseif ($estado === 'incapacitados_hoy') {
            $query->whereHas('incapacidades', function ($q) use ($hoy) {
                $q->where('estatus', 'Activo')
                  ->whereDate('fecha_inicio', '<=', $hoy)
                  ->whereDate('fecha_fin', '>=', $hoy);
            });
        }

        // Filtro por Dirección / Área
        if ($request->filled('area_id') && $request->area_id !== 'todas') {
            $query->where('area_id', $request->area_id);
        }

        // Filtro por Nivel
        if ($request->filled('nivel_id') && $request->nivel_id !== 'todos') {
            $query->where('nivel_id', $request->nivel_id);
        }

        // Buscador general
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('no_empleado', 'like', "%{$search}%")
                  ->orWhere('cargo', 'like', "%{$search}%")
                  ->orWhere('prof', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('curp', 'like', "%{$search}%")
                  ->orWhere('rfc', 'like', "%{$search}%")
                  ->orWhereHas('area', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('prefijo', 'like', "%{$search}%");
                  });
            });
        }

        $usuarios = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        // Estadísticas globales para tarjetas superiores
        $totalActivos = User::count();
        $totalInactivos = User::onlyTrashed()->count();

        $totalIncapacitadosHoy = PersonalIncidencia::where('categoria', 'incapacidad')
            ->where('estatus', 'Activo')
            ->whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy)
            ->distinct('user_id')
            ->count('user_id');

        $totalIncapacidadesAnio = PersonalIncidencia::where('categoria', 'incapacidad')
            ->where(function ($q) use ($anioActual) {
                $q->whereYear('fecha_inicio', $anioActual)
                  ->orWhereYear('fecha_fin', $anioActual);
            })->count();

        $diasIncapacidadAnio = (int) PersonalIncidencia::where('categoria', 'incapacidad')
            ->where(function ($q) use ($anioActual) {
                $q->whereYear('fecha_inicio', $anioActual)
                  ->orWhereYear('fecha_fin', $anioActual);
            })->sum('dias');

        $totalPermisosAnio = PersonalIncidencia::where('categoria', 'incidencia')
            ->where(function ($q) use ($anioActual) {
                $q->whereYear('fecha_inicio', $anioActual)
                  ->orWhereYear('fecha_fin', $anioActual);
            })->count();

        $totalComisionesAnio = Comision::where(function ($q) use ($anioActual) {
                $q->where('anio', $anioActual)->orWhereYear('created_at', $anioActual);
            })->where('status', '!=', 'Cancelado')->count()
            + ComisionOdso::where(function ($q) use ($anioActual) {
                $q->where('anio', $anioActual)->orWhereYear('created_at', $anioActual);
            })->where('status', '!=', 'Cancelado')->count();

        $areas = Area::orderBy('name')->get();
        $niveles = Nivel::orderBy('nombre')->get();

        return view('personal_ceaa.index', compact(
            'usuarios',
            'areas',
            'niveles',
            'estado',
            'anioActual',
            'totalActivos',
            'totalInactivos',
            'totalIncapacitadosHoy',
            'totalIncapacidadesAnio',
            'diasIncapacidadAnio',
            'totalPermisosAnio',
            'totalComisionesAnio'
        ));
    }

    /**
     * Vista del Kardex Individual de una persona del CEAA.
     */
    public function show(Request $request, $userId)
    {
        $this->authorizeAccess();

        $empleado = User::withTrashed()
            ->with(['area', 'subarea', 'nivel'])
            ->findOrFail($userId);

        $anio = (int) $request->input('anio', now()->year);
        Carbon::setLocale('es');

        // 1. Comisiones Normales y ODSO del empleado en el año seleccionado
        $comisionesNormales = Comision::with(['vehiculo', 'proyecto', 'jefeArea'])
            ->where('user_id', $empleado->id)
            ->where(function ($q) use ($anio) {
                $q->where('anio', $anio)->orWhereYear('created_at', $anio);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $comisionesOdso = ComisionOdso::with(['vehiculo', 'proyecto', 'jefeArea'])
            ->where('user_id', $empleado->id)
            ->where(function ($q) use ($anio) {
                $q->where('anio', $anio)->orWhereYear('created_at', $anio);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Unificar comisiones para la tabla y calendario
        $comisionesUnificadas = collect();

        foreach ($comisionesNormales as $c) {
            $fechasDetectadas = self::extractCommissionDates($c->dias_comision, $c->created_at, $anio);
            $comisionesUnificadas->push((object) [
                'id' => $c->id,
                'es_odso' => $c->isOdso(),
                'tipo_etiqueta' => $c->isOdso() ? 'ODSO' : 'Normal',
                'oficio_numero' => $c->oficio_numero,
                'dias_comision' => $c->dias_comision,
                'fechas_detectadas' => $fechasDetectadas,
                'hora_inicio' => $c->hora_inicio,
                'hora_fin' => $c->hora_fin,
                'lugar' => $c->lugar,
                'actividad' => $c->actividad,
                'status' => $c->status ?? 'Autorizado',
                'vehiculo' => $c->vehiculo,
                'proyecto' => $c->proyecto,
                'created_at' => $c->created_at,
                'url_show' => route('comisiones.show', $c->id),
            ]);
        }

        foreach ($comisionesOdso as $co) {
            $fechasDetectadas = self::extractCommissionDates($co->dias_comision, $co->created_at, $anio);
            $comisionesUnificadas->push((object) [
                'id' => $co->id,
                'es_odso' => true,
                'tipo_etiqueta' => 'ODSO',
                'oficio_numero' => $co->oficio_numero,
                'dias_comision' => $co->dias_comision,
                'fechas_detectadas' => $fechasDetectadas,
                'hora_inicio' => $co->hora_inicio,
                'hora_fin' => $co->hora_fin,
                'lugar' => $co->lugar,
                'actividad' => $co->actividad,
                'status' => $co->status ?? 'Autorizado',
                'vehiculo' => $co->vehiculo,
                'proyecto' => $co->proyecto,
                'created_at' => $co->created_at,
                'url_show' => route('comisiones-odso.show', $co->id),
            ]);
        }

        $comisionesUnificadas = $comisionesUnificadas->sortByDesc('created_at')->values();

        // 2. Incapacidades, Permisos/Incidencias y Vacaciones por Oficio del empleado
        $incapacidades = PersonalIncidencia::with('registrador')
            ->where('user_id', $empleado->id)
            ->where('categoria', 'incapacidad')
            ->where(function ($q) use ($anio) {
                $q->whereYear('fecha_inicio', $anio)
                  ->orWhereYear('fecha_fin', $anio);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $permisos = PersonalIncidencia::with('registrador')
            ->where('user_id', $empleado->id)
            ->where('categoria', 'incidencia')
            ->where(function ($q) use ($anio) {
                $q->whereYear('fecha_inicio', $anio)
                  ->orWhereYear('fecha_fin', $anio);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $vacacionesKardex = PersonalIncidencia::with('registrador')
            ->where('user_id', $empleado->id)
            ->where('categoria', 'vacacion')
            ->where(function ($q) use ($anio) {
                $q->whereYear('fecha_inicio', $anio)
                  ->orWhereYear('fecha_fin', $anio);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        // 3. Vacaciones del empleado en el año (combinando capturas por oficio en Kardex + solicitudes en sistema)
        $configVacaciones = ConfiguracionVacacion::where('anio', $anio)->first();
        $solicitudesVacaciones = SolicitudVacacion::with('fechas')
            ->where('user_id', $empleado->id)
            ->whereHas('fechas', function ($q) use ($anio) {
                $q->whereYear('fecha', $anio);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $p1Inicio = optional(optional($configVacaciones)->periodo1_inicio)->format('Y-m-d');
        $p1Fin = optional(optional($configVacaciones)->periodo1_fin)->format('Y-m-d');
        $p2Inicio = optional(optional($configVacaciones)->periodo2_inicio)->format('Y-m-d');
        $p2Fin = optional(optional($configVacaciones)->periodo2_fin)->format('Y-m-d');

        $diasVacacionesAprobados = 0;
        $diasVacacionesPendientes = 0;
        $diasP1Usados = 0;
        $diasP2Usados = 0;

        // Contabilizar vacaciones registradas por oficio en Kardex
        foreach ($vacacionesKardex as $vac) {
            if ($vac->estatus === 'Cancelado') continue;
            $diasVacacionesAprobados += $vac->dias;
            if (stripos($vac->tipo, '1er') !== false || stripos($vac->tipo, 'Primer') !== false) {
                $diasP1Usados += $vac->dias;
            } elseif (stripos($vac->tipo, '2do') !== false || stripos($vac->tipo, 'Segundo') !== false) {
                $diasP2Usados += $vac->dias;
            }
        }

        // Contabilizar solicitudes del módulo web (si existen)
        foreach ($solicitudesVacaciones as $sol) {
            foreach ($sol->fechas as $fObj) {
                $fStr = $fObj->fecha->format('Y-m-d');
                if ((int) $fObj->fecha->format('Y') !== $anio) {
                    continue;
                }
                if ($sol->estatus === 'Aprobado') {
                    $diasVacacionesAprobados++;
                } elseif ($sol->estatus === 'Pendiente') {
                    $diasVacacionesPendientes++;
                }

                if (in_array($sol->estatus, ['Aprobado', 'Pendiente'])) {
                    if ($p1Inicio && $p1Fin && $fStr >= $p1Inicio && $fStr <= $p1Fin) {
                        $diasP1Usados++;
                    } elseif ($p2Inicio && $p2Fin && $fStr >= $p2Inicio && $fStr <= $p2Fin) {
                        $diasP2Usados++;
                    }
                }
            }
        }

        // 4. Construir mapa de eventos por día para el Calendario Anual del Kardex
        $calendarioEventos = [];
        $traslapes = [];

        // Mapear Incapacidades
        foreach ($incapacidades as $inc) {
            if ($inc->estatus === 'Cancelado') continue;
            $cursor = $inc->fecha_inicio->copy()->startOfDay();
            $fin = $inc->fecha_fin->copy()->startOfDay();
            while ($cursor->lte($fin)) {
                if ((int) $cursor->year === $anio) {
                    $dKey = $cursor->format('Y-m-d');
                    $calendarioEventos[$dKey][] = [
                        'tipo' => 'incapacidad',
                        'titulo' => 'Incapacidad: ' . $inc->tipo,
                        'detalle' => ($inc->folio ? "Folio: {$inc->folio} | " : '') . ($inc->observaciones ?? ''),
                    ];
                }
                $cursor->addDay();
            }
        }

        // Mapear Permisos / Incidencias
        foreach ($permisos as $perm) {
            if ($perm->estatus === 'Cancelado') continue;
            $cursor = $perm->fecha_inicio->copy()->startOfDay();
            $fin = $perm->fecha_fin->copy()->startOfDay();
            while ($cursor->lte($fin)) {
                if ((int) $cursor->year === $anio) {
                    $dKey = $cursor->format('Y-m-d');
                    $calendarioEventos[$dKey][] = [
                        'tipo' => 'permiso',
                        'titulo' => $perm->tipo,
                        'detalle' => ($perm->folio ? "Ref: {$perm->folio} | " : '') . ($perm->observaciones ?? ''),
                    ];
                }
                $cursor->addDay();
            }
        }

        // Mapear Vacaciones registradas por Oficio en Kardex
        foreach ($vacacionesKardex as $vac) {
            if ($vac->estatus === 'Cancelado') continue;
            $soloHabiles = ($vac->institucion_medica === 'Solo días hábiles');
            $cursor = $vac->fecha_inicio->copy()->startOfDay();
            $fin = $vac->fecha_fin->copy()->startOfDay();
            while ($cursor->lte($fin)) {
                if ((int) $cursor->year === $anio) {
                    if (!$soloHabiles || !$cursor->isWeekend()) {
                        $dKey = $cursor->format('Y-m-d');
                        $calendarioEventos[$dKey][] = [
                            'tipo' => 'vacacion',
                            'titulo' => 'Vacaciones: ' . $vac->tipo,
                            'detalle' => ($vac->folio ? "Oficio: {$vac->folio} " : '') . ($vac->observaciones ?? ''),
                        ];
                    }
                }
                $cursor->addDay();
            }
        }

        // Mapear Vacaciones Aprobadas desde Solicitudes del Sistema
        foreach ($solicitudesVacaciones as $sol) {
            if ($sol->estatus !== 'Aprobado') continue;
            foreach ($sol->fechas as $fObj) {
                if ((int) $fObj->fecha->year === $anio) {
                    $dKey = $fObj->fecha->format('Y-m-d');
                    $calendarioEventos[$dKey][] = [
                        'tipo' => 'vacacion',
                        'titulo' => 'Vacaciones Autorizadas',
                        'detalle' => $sol->observaciones ?? 'Período vacacional oficial',
                    ];
                }
            }
        }

        // Mapear Comisiones Activas
        foreach ($comisionesUnificadas as $com) {
            if ($com->status === 'Cancelado') continue;
            foreach ($com->fechas_detectadas as $dKey) {
                $calendarioEventos[$dKey][] = [
                    'tipo' => 'comision',
                    'titulo' => "Comisión {$com->oficio_numero}",
                    'detalle' => "{$com->lugar} ({$com->dias_comision})",
                ];
            }
        }

        // Detectar traslapes (ej. comisión el mismo día que incapacidad o vacación)
        foreach ($calendarioEventos as $fechaKey => $items) {
            $tiposEnDia = array_unique(array_column($items, 'tipo'));
            if (count($tiposEnDia) > 1) {
                $traslapes[$fechaKey] = $items;
            }
        }

        ksort($traslapes);

        $totalDiasIncapacidad = $incapacidades->sum('dias');
        $totalDiasPermisos = $permisos->sum('dias');
        $totalComisionesActivas = $comisionesUnificadas->where('status', '!=', 'Cancelado')->count();

        return view('personal_ceaa.show', compact(
            'empleado',
            'anio',
            'comisionesUnificadas',
            'totalComisionesActivas',
            'incapacidades',
            'totalDiasIncapacidad',
            'permisos',
            'totalDiasPermisos',
            'vacacionesKardex',
            'solicitudesVacaciones',
            'configVacaciones',
            'diasVacacionesAprobados',
            'diasVacacionesPendientes',
            'diasP1Usados',
            'diasP2Usados',
            'calendarioEventos',
            'traslapes'
        ));
    }

    /**
     * Guarda una nueva incapacidad, incidencia/permiso o vacaciones por oficio en el Kardex del trabajador.
     */
    public function storeIncidencia(Request $request, $userId)
    {
        $this->authorizeAccess();

        $empleado = User::withTrashed()->findOrFail($userId);

        $request->validate([
            'categoria' => 'required|in:incapacidad,incidencia,vacacion',
            'tipo' => 'required|string|max:100',
            'folio' => 'nullable|string|max:100',
            'institucion_medica' => 'nullable|string|max:100',
            'modo_vacacion' => 'nullable|in:rango,especificas',
            'fecha_inicio' => 'required_unless:modo_vacacion,especificas|nullable|date',
            'fecha_fin' => 'required_unless:modo_vacacion,especificas|nullable|date|after_or_equal:fecha_inicio',
            'fechas_especificas' => 'required_if:modo_vacacion,especificas|nullable|array|min:1',
            'fechas_especificas.*' => 'nullable|date',
            'observaciones' => 'nullable|string|max:1500',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de término debe ser igual o posterior a la fecha de inicio.',
        ]);

        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('incidencias_personal', 'public');
        }

        // Caso especial: Vacaciones por días específicos sueltos
        if ($request->categoria === 'vacacion' && $request->input('modo_vacacion') === 'especificas') {
            $fechasLimpias = collect($request->input('fechas_especificas', []))
                ->filter()
                ->map(fn($f) => Carbon::parse($f)->toDateString())
                ->unique()
                ->sort()
                ->values();

            if ($fechasLimpias->isEmpty()) {
                return back()->withErrors(['fechas_especificas' => 'Debes seleccionar al menos una fecha de vacaciones.']);
            }

            foreach ($fechasLimpias as $fDate) {
                PersonalIncidencia::create([
                    'user_id' => $empleado->id,
                    'registrado_por' => Auth::id(),
                    'categoria' => 'vacacion',
                    'tipo' => $request->tipo,
                    'folio' => $request->folio,
                    'institucion_medica' => 'Día específico',
                    'fecha_inicio' => $fDate,
                    'fecha_fin' => $fDate,
                    'dias' => 1,
                    'observaciones' => $request->observaciones,
                    'archivo_path' => $archivoPath,
                    'estatus' => 'Activo',
                ]);
            }

            $anioDestino = Carbon::parse($fechasLimpias->first())->year;
            $totalGrabados = $fechasLimpias->count();

            return redirect()
                ->route('personal-ceaa.show', ['user' => $empleado->id, 'anio' => $anioDestino])
                ->with('success', "Se registraron {$totalGrabados} " . ($totalGrabados === 1 ? 'día' : 'días') . " de vacaciones en el Kardex y calendario.");
        }

        $inicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fin = Carbon::parse($request->fecha_fin)->startOfDay();

        $institucion = $request->institucion_medica;
        $dias = (int) round($inicio->diffInDays($fin)) + 1;

        if ($request->categoria === 'vacacion') {
            $soloHabiles = $request->boolean('solo_dias_habiles', true);
            $institucion = $soloHabiles ? 'Solo días hábiles' : 'Días naturales';

            if ($soloHabiles) {
                $diasHabiles = 0;
                $cursor = $inicio->copy();
                while ($cursor->lte($fin)) {
                    if (!$cursor->isWeekend()) {
                        $diasHabiles++;
                    }
                    $cursor->addDay();
                }
                $dias = max(1, $diasHabiles);
            }
        }

        PersonalIncidencia::create([
            'user_id' => $empleado->id,
            'registrado_por' => Auth::id(),
            'categoria' => $request->categoria,
            'tipo' => $request->tipo,
            'folio' => $request->folio,
            'institucion_medica' => $institucion,
            'fecha_inicio' => $inicio->toDateString(),
            'fecha_fin' => $fin->toDateString(),
            'dias' => $dias,
            'observaciones' => $request->observaciones,
            'archivo_path' => $archivoPath,
            'estatus' => 'Activo',
        ]);

        $etiquetas = [
            'incapacidad' => 'Incapacidad médica',
            'incidencia' => 'Incidencia / Permiso',
            'vacacion' => 'Período de vacaciones',
        ];
        $etiqueta = $etiquetas[$request->categoria] ?? 'Registro';

        return redirect()
            ->route('personal-ceaa.show', ['user' => $empleado->id, 'anio' => $inicio->year])
            ->with('success', "{$etiqueta} registrado correctamente ({$dias} " . ($dias === 1 ? 'día' : 'días') . ").");
    }

    /**
     * Elimina una incapacidad o incidencia registrada.
     */
    public function destroyIncidencia(PersonalIncidencia $incidencia)
    {
        $this->authorizeAccess();

        $userId = $incidencia->user_id;
        if ($incidencia->archivo_path && Storage::disk('public')->exists($incidencia->archivo_path)) {
            // Verificar si ningún otro registro comparte el mismo archivo antes de borrarlo
            $otrosConMismoArchivo = PersonalIncidencia::where('id', '!=', $incidencia->id)
                ->where('archivo_path', $incidencia->archivo_path)
                ->exists();
            if (!$otrosConMismoArchivo) {
                Storage::disk('public')->delete($incidencia->archivo_path);
            }
        }

        $incidencia->delete();

        return redirect()
            ->route('personal-ceaa.show', $userId)
            ->with('success', 'Registro eliminado del Kardex correctamente.');
    }

    /**
     * Actualiza la ficha de expediente básico y datos de emergencia del trabajador.
     */
    public function updateExpediente(Request $request, $userId)
    {
        $this->authorizeAccess();

        $empleado = User::withTrashed()->findOrFail($userId);

        $request->validate([
            'no_empleado' => 'nullable|string|max:255|unique:users,no_empleado,' . $empleado->id,
            'cargo' => 'nullable|string|max:255',
            'fecha_alta' => 'nullable|date',
            'curp' => 'nullable|string|max:25',
            'rfc' => 'nullable|string|max:20',
            'nss' => 'nullable|string|max:30',
            'tipo_sangre' => 'nullable|string|max:15',
            'telefono' => 'nullable|string|max:30',
            'contacto_emergencia_nombre' => 'nullable|string|max:150',
            'contacto_emergencia_telefono' => 'nullable|string|max:30',
            'contacto_emergencia_parentesco' => 'nullable|string|max:60',
            'notas_rh' => 'nullable|string|max:2000',
        ]);

        $empleado->update([
            'no_empleado' => $request->no_empleado,
            'cargo' => $request->cargo,
            'fecha_alta' => $request->fecha_alta ?: null,
            'curp' => $request->curp ? strtoupper(trim($request->curp)) : null,
            'rfc' => $request->rfc ? strtoupper(trim($request->rfc)) : null,
            'nss' => $request->nss,
            'tipo_sangre' => $request->tipo_sangre,
            'telefono' => $request->telefono,
            'contacto_emergencia_nombre' => $request->contacto_emergencia_nombre,
            'contacto_emergencia_telefono' => $request->contacto_emergencia_telefono,
            'contacto_emergencia_parentesco' => $request->contacto_emergencia_parentesco,
            'notas_rh' => $request->notas_rh,
        ]);

        return redirect()
            ->route('personal-ceaa.show', $empleado->id)
            ->with('success', 'Ficha de expediente del trabajador actualizada correctamente.');
    }

    /**
     * Extrae fechas en formato Y-m-d a partir del texto libre `dias_comision`
     * (Ej: "28 y 29 de Septiembre", "del 10 al 12 de Octubre", "15 de Mayo de 2026")
     * usando como respaldo el mes/año de creación del oficio.
     */
    public static function extractCommissionDates(?string $textoDias, $createdAt, int $targetYear): array
    {
        $fallbackDate = $createdAt ? Carbon::parse($createdAt) : now();
        if (empty($textoDias)) {
            return (int) $fallbackDate->year === $targetYear ? [$fallbackDate->format('Y-m-d')] : [];
        }

        $mesesMap = [
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'setiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12,
        ];

        $lower = mb_strtolower($textoDias, 'UTF-8');

        // Detectar mes mencionado en el texto; si no hay, usar mes de creación
        $mesDetectado = (int) $fallbackDate->month;
        foreach ($mesesMap as $nombreMes => $numMes) {
            if (strpos($lower, $nombreMes) !== false) {
                $mesDetectado = $numMes;
                break;
            }
        }

        // Detectar año si viene explícito (ej. 2025, 2026), si no, usar el año de creación
        $anioDetectado = (int) $fallbackDate->year;
        if (preg_match('/\b(20\d{2})\b/', $lower, $yearMatch)) {
            $anioDetectado = (int) $yearMatch[1];
        }

        if ($anioDetectado !== $targetYear) {
            return [];
        }

        // Remover el año del texto antes de extraer números de día
        $sinAnio = preg_replace('/\b20\d{2}\b/', '', $lower);

        $diasExtraidos = [];

        // Caso rango: "del 10 al 14" o "10 al 14" o "10-14"
        if (preg_match_all('/\b(\d{1,2})\s*(?:al|a|-)\s*(\d{1,2})\b/', $sinAnio, $rangeMatches, PREG_SET_ORDER)) {
            foreach ($rangeMatches as $rm) {
                $d1 = (int) $rm[1];
                $d2 = (int) $rm[2];
                if ($d1 >= 1 && $d2 <= 31 && $d1 <= $d2) {
                    for ($d = $d1; $d <= $d2; $d++) {
                        $diasExtraidos[] = $d;
                    }
                }
            }
        }

        // Extraer todos los números de día individuales (1..31)
        if (preg_match_all('/\b([0-3]?\d)\b/', $sinAnio, $dayMatches)) {
            foreach ($dayMatches[1] as $dStr) {
                $d = (int) $dStr;
                if ($d >= 1 && $d <= 31) {
                    $diasExtraidos[] = $d;
                }
            }
        }

        $diasExtraidos = array_unique($diasExtraidos);
        sort($diasExtraidos);

        $fechasValidas = [];
        foreach ($diasExtraidos as $dia) {
            if (checkdate($mesDetectado, $dia, $anioDetectado)) {
                $fechasValidas[] = sprintf('%04d-%02d-%02d', $anioDetectado, $mesDetectado, $dia);
            }
        }

        if (empty($fechasValidas) && (int) $fallbackDate->year === $targetYear) {
            $fechasValidas[] = $fallbackDate->format('Y-m-d');
        }

        return $fechasValidas;
    }
}
