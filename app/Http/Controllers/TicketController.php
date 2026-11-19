<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    /**
     * Muestra el dashboard de tickets con filtros por estatus (Por atender / Atendidos),
     * por usuario solicitante ("quién me mandó qué"), por área y búsqueda por texto.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $esAdmin = $user->role === 'admin';

        // Estatus seleccionado: 'todos', 'Pendiente' (Por atender), 'Concluido' (Atendidos)
        $estatusFiltro = $request->input('estatus', 'todos');
        $userIdFiltro = $request->input('user_id', '');
        $areaIdFiltro = $request->input('area_id', '');
        $search = trim($request->input('search', ''));

        // Base query según rol
        $baseQuery = Ticket::with(['user.area']);
        if (!$esAdmin) {
            $baseQuery->where('user_id', $user->id);
        }

        // Aplicar filtros comunes (usuario, área, búsqueda) para que impacten en las tablas y contadores
        $filteredQuery = clone $baseQuery;

        if ($esAdmin && !empty($userIdFiltro)) {
            $filteredQuery->where('user_id', $userIdFiltro);
        }

        if ($esAdmin && !empty($areaIdFiltro)) {
            $filteredQuery->whereHas('user', function ($q) use ($areaIdFiltro) {
                $q->withTrashed()->where('area_id', $areaIdFiltro);
            });
        }

        if (!empty($search)) {
            $filteredQuery->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('resolution_notes', 'like', "%{$search}%")
                  ->orWhere('id', ltrim($search, '#0'))
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->withTrashed()
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('cargo', 'like', "%{$search}%")
                        ->orWhereHas('area', function ($a) use ($search) {
                            $a->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }

        // Estadísticas globales (o filtradas por usuario/área/búsqueda)
        $stats = [
            'total' => (clone $filteredQuery)->count(),
            'pendientes' => (clone $filteredQuery)->where('status', 'Pendiente')->count(),
            'atendidos' => (clone $filteredQuery)->where('status', 'Concluido')->count(),
        ];

        // Tablas paginadas de "Por Atender" (Pendiente) y "Atendidos" (Concluido)
        $ticketsPendientes = (clone $filteredQuery)
            ->where('status', 'Pendiente')
            ->orderBy('created_at', $esAdmin ? 'asc' : 'desc')
            ->paginate(10, ['*'], 'page_pendientes')
            ->withQueryString();

        $ticketsConcluidos = (clone $filteredQuery)
            ->where('status', 'Concluido')
            ->orderBy('completed_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'page_concluidos')
            ->withQueryString();

        // Catálogos y resumen de "Quién me mandó qué" para el Admin
        $areas = collect();
        $remitentesResumen = collect();

        if ($esAdmin) {
            $areas = Area::orderBy('name')->get();

            // Usuarios que han enviado tickets con conteo de Por Atender y Atendidos
            $remitentesResumen = User::withTrashed()
                ->with('area')
                ->whereHas('tickets')
                ->withCount([
                    'tickets as total_tickets',
                    'tickets as pendientes_count' => function ($q) {
                        $q->where('status', 'Pendiente');
                    },
                    'tickets as atendidos_count' => function ($q) {
                        $q->where('status', 'Concluido');
                    },
                ])
                ->orderByDesc('pendientes_count')
                ->orderByDesc('total_tickets')
                ->orderBy('name')
                ->get();
        }

        return view('tickets.index', compact(
            'ticketsPendientes',
            'ticketsConcluidos',
            'stats',
            'estatusFiltro',
            'userIdFiltro',
            'areaIdFiltro',
            'search',
            'areas',
            'remitentesResumen'
        ));
    }

    /**
     * Muestra el formulario para que un usuario cree un nuevo ticket.
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Guarda el nuevo ticket en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
        ]);

        // NOTIFICACIÓN AL ADMIN (Tú)
        $data = ['ticket' => $ticket, 'usuario' => Auth::user()->name];
        try {
            Mail::send('emails.ticket_nuevo', $data, function ($message) use ($ticket) {
                $message->to('daniel.lopez@hidalgo.gob.mx')
                    ->subject('SOPORTE: Nuevo Ticket de ' . Auth::user()->name);
            });
        } catch (\Throwable $e) {
            // Evitar fallo si el servidor de correo no responde
        }

        return redirect()->route('tickets.index')->with('success', 'Tu solicitud de soporte ha sido enviada.');
    }

    /**
     * Muestra los detalles de un ticket específico.
     */
    public function show(Ticket $ticket)
    {
        // Valida que los usuarios solo puedan ver sus propios tickets, pero el admin puede ver todos.
        if (Auth::user()->role !== 'admin' && $ticket->user_id !== Auth::id()) {
            abort(403);
        }
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Muestra el formulario para que el admin resuelva un ticket.
     */
    public function edit(Ticket $ticket)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Actualiza el ticket a estado "Concluido" y guarda las notas/evidencia.
     */
    public function update(Request $request, Ticket $ticket)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'resolution_notes' => 'required|string',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('evidence')) {
            // Guarda la imagen en storage/app/public/evidence y guarda la ruta en la BD
            $path = $request->file('evidence')->store('evidence', 'public');
        }

        $ticket->update([
            'status' => 'Concluido',
            'resolution_notes' => $request->resolution_notes,
            'evidence_path' => $path,
            'completed_at' => now(),
        ]);

        // NOTIFICACIÓN AL USUARIO
        if ($ticket->user && $ticket->user->recibir_correos) {
            $data = ['ticket' => $ticket];
            try {
                Mail::send('emails.ticket_resuelto', $data, function ($message) use ($ticket) {
                    $message->to($ticket->user->email)
                        ->subject('Ticket Resuelto: ' . $ticket->subject);
                });
            } catch (\Throwable $e) {
                // Evitar fallo si el servidor de correo no responde
            }
        }

        return redirect()->route('tickets.index')->with('success', 'El ticket ha sido marcado como concluido.');
    }
}
