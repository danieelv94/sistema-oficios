<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\OficioFirmaDg;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OficioFirmaDgController extends Controller
{
    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (!$user || !$user->canAccessFirmaDg()) {
            abort(403, 'Acceso exclusivo para personal de Dirección General.');
        }
    }

    /**
     * Vista principal de la Bitácora de Oficios para Firma del Director General.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = OficioFirmaDg::with(['area', 'entregadoPor.area', 'recogidoPor.area', 'registrador', 'entregadorDg', 'cancelador']);

        // Filtro por Estatus: Por defecto los "Cancelados" NO aparecen en la lista general ni en Total Registrados,
        // solo cuando se selecciona explícitamente el apartado/pestaña "Cancelado".
        $estatus = $request->input('estatus', 'Todos');
        if ($estatus === 'Cancelado') {
            $query->where('estatus', 'Cancelado');
        } else {
            $query->where('estatus', '!=', 'Cancelado');
            if ($estatus !== 'Todos' && !empty($estatus)) {
                $query->where('estatus', $estatus);
            }
        }

        // Filtro por Dirección / Área
        if ($request->filled('area_id') && $request->area_id !== 'todas') {
            $query->where('area_id', $request->area_id);
        }

        // Filtro por Prioridad
        if ($request->filled('prioridad') && $request->prioridad !== 'Todas') {
            $query->where('prioridad', $request->prioridad);
        }

        // Filtro por Fecha de Recepción
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_recepcion', $request->fecha);
        }

        // Buscador general
        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchNumero = ltrim($search, '#0');
            $query->where(function ($q) use ($search, $searchNumero) {
                $q->where('numero_oficio', 'like', "%{$search}%")
                  ->orWhere('consecutivo', $search)
                  ->orWhere('asunto', 'like', "%{$search}%")
                  ->orWhere('destinatario', 'like', "%{$search}%")
                  ->orWhere('entregado_por_nombre', 'like', "%{$search}%")
                  ->orWhere('recogido_por_nombre', 'like', "%{$search}%")
                  ->orWhere('observaciones', 'like', "%{$search}%")
                  ->orWhereHas('entregadoPor', function ($u) use ($search) {
                      $u->withTrashed()->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('recogidoPor', function ($u) use ($search) {
                      $u->withTrashed()->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('area', function ($a) use ($search) {
                      $a->where('name', 'like', "%{$search}%");
                  });
                if ($searchNumero !== '' && is_numeric($searchNumero)) {
                    $q->orWhere('consecutivo', (int) $searchNumero);
                }
            });
        }

        $registros = $query->orderBy('consecutivo', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Indicadores KPI (Total Registrados excluye los Cancelados)
        $stats = [
            'total' => OficioFirmaDg::where('estatus', '!=', 'Cancelado')->count(),
            'en_firma' => OficioFirmaDg::where('estatus', 'En Firma')->count(),
            'firmados' => OficioFirmaDg::where('estatus', 'Firmado')->count(),
            'entregados' => OficioFirmaDg::where('estatus', 'Entregado')->count(),
            'devueltos' => OficioFirmaDg::where('estatus', 'Devuelto')->count(),
            'cancelados' => OficioFirmaDg::where('estatus', 'Cancelado')->count(),
        ];

        $areas = Area::orderBy('name')->get();
        $personal = User::with('area')
            ->orderBy('name')
            ->get(['id', 'name', 'prof', 'cargo', 'area_id']);

        return view('firma_dg.index', compact(
            'registros',
            'areas',
            'personal',
            'estatus',
            'stats'
        ));
    }

    /**
     * Registra un nuevo oficio dejado en Dirección General para firma del Titular.
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'numero_oficio' => 'nullable|string|max:120',
            'tipo_documento' => 'required|string|max:60',
            'entregado_por_user_id' => 'nullable|exists:users,id',
            'area_id' => 'required|exists:areas,id',
            'destinatario' => 'nullable|string|max:255',
            'asunto' => 'required|string|max:2000',
            'anexos' => 'nullable|string|max:255',
            'prioridad' => 'required|in:Normal,Urgente',
            'fecha_recepcion' => 'required|date',
            'observaciones' => 'nullable|string|max:1500',
        ], [
            'area_id.required' => 'Debes seleccionar el Área / Dirección que entrega el oficio.',
            'asunto.required' => 'El asunto del oficio es obligatorio.',
        ]);

        $nombreQuienDejo = trim($request->entregado_por_nombre ?? '') ?: null;
        if ($request->filled('entregado_por_user_id')) {
            $u = User::withTrashed()->find($request->entregado_por_user_id);
            if ($u) {
                $nombreQuienDejo = trim(($u->prof ? $u->prof . ' ' : '') . $u->name);
            }
        }

        $siguienteConsecutivo = ((int) OficioFirmaDg::max('consecutivo')) + 1;

        OficioFirmaDg::create([
            'consecutivo' => $siguienteConsecutivo,
            'numero_oficio' => $request->numero_oficio,
            'tipo_documento' => $request->tipo_documento,
            'area_id' => $request->area_id,
            'entregado_por_user_id' => $request->entregado_por_user_id ?: null,
            'entregado_por_nombre' => $nombreQuienDejo,
            'destinatario' => $request->destinatario,
            'asunto' => $request->asunto,
            'anexos' => $request->anexos,
            'prioridad' => $request->prioridad,
            'fecha_recepcion' => Carbon::parse($request->fecha_recepcion),
            'estatus' => 'En Firma',
            'observaciones' => $request->observaciones,
            'registrado_por' => Auth::id(),
        ]);

        return redirect()
            ->route('firma-dg.index')
            ->with('success', 'Oficio registrado en la bitácora de Firma de Dirección General.');
    }

    /**
     * Cambia rápidamente el estatus del documento (Ej: Marcar como "Firmado" listo para recoger, o "En Firma").
     */
    public function cambiarEstatus(Request $request, OficioFirmaDg $registro)
    {
        $this->authorizeAccess();

        $request->validate([
            'estatus' => 'required|in:En Firma,Firmado,Devuelto',
            'observaciones' => 'nullable|string|max:1500',
        ]);

        $datos = [
            'estatus' => $request->estatus,
        ];

        if ($request->estatus === 'Firmado' && !$registro->fecha_firma) {
            $datos['fecha_firma'] = now();
        }

        if ($request->filled('observaciones')) {
            $datos['observaciones'] = $request->observaciones;
        }

        $registro->update($datos);
        $registro->load(['area', 'entregadoPor']);

        $correosEnviados = 0;
        if ($request->estatus === 'Firmado') {
            $destinatarios = collect();

            // 1. La persona que dejó el documento en Dirección General
            if ($registro->entregado_por_user_id) {
                $personaDejo = User::find($registro->entregado_por_user_id);
                if ($personaDejo) {
                    $destinatarios->push($personaDejo);
                }
            }

            // 2. El Jefe de Área (Director) del área correspondiente
            if ($registro->area_id) {
                $directoresArea = User::where('area_id', $registro->area_id)
                    ->where('role', 'jefe_area')
                    ->get();
                foreach ($directoresArea as $dir) {
                    $destinatarios->push($dir);
                }
            }

            // Evitar duplicados (por si quien lo dejó fue el mismo Director de Área)
            $destinatarios = $destinatarios->unique('id');

            $numConsecutivo = $registro->consecutivo ?? $registro->id;
            $folioLabel = $registro->numero_oficio ?: ('Folio #' . $numConsecutivo);

            foreach ($destinatarios as $usuarioDestino) {
                if (!$usuarioDestino->recibir_correos || empty($usuarioDestino->email)) {
                    continue;
                }

                try {
                    Mail::send('emails.oficio_firma_dg_listo', [
                        'usuario' => $usuarioDestino,
                        'registro' => $registro,
                    ], function ($message) use ($usuarioDestino, $registro, $folioLabel) {
                        $message->to($usuarioDestino->email)
                            ->subject('[' . $registro->prioridad . '] ✍️ Documento Firmado Listo para Recoger - ' . $folioLabel);
                    });
                    $correosEnviados++;
                } catch (\Exception $e) {
                    Log::error("Error al enviar correo de Firma DG a {$usuarioDestino->email}: " . $e->getMessage());
                }
            }
        }

        $mensaje = $request->estatus === 'Firmado'
            ? 'Documento marcado como FIRMADO (Listo para recoger).' . ($correosEnviados > 0 ? " Se notificó por correo electrónico ({$correosEnviados})." : '')
            : "Estatus actualizado a {$request->estatus}.";

        return back()->with('success', $mensaje);
    }

    /**
     * Registra quién recogió el oficio firmado (o devuelto) y en qué fecha/hora.
     */
    public function registrarEntrega(Request $request, OficioFirmaDg $registro)
    {
        $this->authorizeAccess();

        $request->validate([
            'recogido_por_user_id' => 'nullable|exists:users,id',
            'recogido_por_nombre' => 'nullable|string|max:180',
            'recogido_por_nombre_manual' => 'nullable|string|max:180',
            'fecha_entrega' => 'required|date',
            'estatus' => 'nullable|in:Entregado,Devuelto',
            'estatus_entrega' => 'nullable|in:Entregado,Devuelto',
            'observaciones' => 'nullable|string|max:1500',
        ]);

        $nombreManual = trim($request->recogido_por_nombre ?? $request->recogido_por_nombre_manual ?? '');

        if (empty($request->recogido_por_user_id) && empty($nombreManual)) {
            return back()->withErrors(['recogido_por_user_id' => 'Debes seleccionar qué servidor público recogió el oficio o escribir su nombre.']);
        }

        $nombreRecogio = $nombreManual;
        if ($request->filled('recogido_por_user_id')) {
            $u = User::withTrashed()->find($request->recogido_por_user_id);
            if ($u) {
                $nombreRecogio = trim(($u->prof ? $u->prof . ' ' : '') . $u->name);
            }
        }

        $estatusSalida = $request->estatus ?? $request->estatus_entrega ?? 'Entregado';

        $registro->update([
            'estatus' => $estatusSalida,
            'fecha_firma' => $registro->fecha_firma ?? now(),
            'recogido_por_user_id' => $request->recogido_por_user_id ?: null,
            'recogido_por_nombre' => $nombreRecogio,
            'fecha_entrega' => Carbon::parse($request->fecha_entrega),
            'observaciones' => $request->filled('observaciones') ? $request->observaciones : $registro->observaciones,
            'entregado_en_dg_por' => Auth::id(),
        ]);

        return back()->with('success', "Salida registrada correctamente: recogido por {$nombreRecogio}.");
    }

    /**
     * Cancela un registro de la bitácora (no se elimina físicamente, pasa al apartado de Cancelados).
     */
    public function cancelar(Request $request, OficioFirmaDg $registro)
    {
        $this->authorizeAccess();

        $request->validate([
            'motivo_cancelacion' => 'required|string|max:1500',
        ], [
            'motivo_cancelacion.required' => 'Debes indicar el motivo de la cancelación del documento.',
        ]);

        $registro->update([
            'estatus' => 'Cancelado',
            'motivo_cancelacion' => trim($request->motivo_cancelacion),
            'fecha_cancelacion' => now(),
            'cancelado_por' => Auth::id(),
        ]);

        return back()->with('success', 'El documento ha sido cancelado y movido al apartado de Cancelados.');
    }

    /**
     * Vista para Usuarios / Directores de Área: Seguimiento de oficios dejados para Firma del Director General.
     * - Si es un usuario normal: SOLO ve los oficios que él mismo entregó (o recogió).
     * - Si es Jefe de Área (Director, role === 'jefe_area'): ve todos los oficios de su Área/Dirección y los suyos propios.
     */
    public function misOficios(Request $request)
    {
        $user = Auth::user();
        $esDirectorArea = in_array($user->role, ['jefe_area', 'admin']) || $user->canAccessFirmaDg();

        $baseQuery = OficioFirmaDg::with(['area', 'entregadoPor.area', 'recogidoPor.area', 'registrador', 'entregadorDg'])
            ->where('estatus', '!=', 'Cancelado')
            ->where(function ($q) use ($user, $esDirectorArea) {
                $q->where('entregado_por_user_id', $user->id)
                  ->orWhere('recogido_por_user_id', $user->id);

                // Solo el Jefe de Área (Director) puede ver todos los oficios de su Dirección
                if ($esDirectorArea && $user->area_id) {
                    $q->orWhere('area_id', $user->area_id);
                }
            });

        // Contadores de los oficios visibles para este usuario
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'en_firma' => (clone $baseQuery)->where('estatus', 'En Firma')->count(),
            'firmados' => (clone $baseQuery)->where('estatus', 'Firmado')->count(),
            'entregados' => (clone $baseQuery)->where('estatus', 'Entregado')->count(),
            'devueltos' => (clone $baseQuery)->where('estatus', 'Devuelto')->count(),
        ];

        $query = clone $baseQuery;

        // Filtro por estatus
        $estatus = $request->input('estatus', '');
        if (!empty($estatus) && $estatus !== 'Todos') {
            $query->where('estatus', $estatus);
        }

        // Filtro solo "Dejados por mí" vs "Toda mi Dirección" (aplicable a Jefe de Área)
        $alcance = $esDirectorArea ? $request->input('alcance', 'area') : 'mios';
        if ($alcance === 'mios') {
            $query->where(function ($q) use ($user) {
                $q->where('entregado_por_user_id', $user->id)
                  ->orWhere('recogido_por_user_id', $user->id);
            });
        }

        // Búsqueda por texto
        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchNumero = ltrim($search, '#0');
            $query->where(function ($q) use ($search, $searchNumero) {
                $q->where('numero_oficio', 'like', "%{$search}%")
                  ->orWhere('consecutivo', $search)
                  ->orWhere('asunto', 'like', "%{$search}%")
                  ->orWhere('destinatario', 'like', "%{$search}%")
                  ->orWhere('observaciones', 'like', "%{$search}%");
                if ($searchNumero !== '' && is_numeric($searchNumero)) {
                    $q->orWhere('consecutivo', (int) $searchNumero);
                }
            });
        }

        $registros = $query->orderByRaw("FIELD(estatus, 'Firmado', 'Devuelto', 'En Firma', 'Entregado')")
            ->orderBy('consecutivo', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('firma_dg.mis_oficios', compact('registros', 'stats', 'estatus', 'alcance', 'esDirectorArea'));
    }
}

