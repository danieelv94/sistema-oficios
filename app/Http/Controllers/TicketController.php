<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Muestra el dashboard de tickets, variando según el rol del usuario.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            // El admin ve todos los tickets, separados por estado y ordenados por antigüedad
            $ticketsPendientes = Ticket::where('status', 'Pendiente')->with('user.area')->oldest()->get();
            $ticketsConcluidos = Ticket::where('status', 'Concluido')->with('user.area')->latest()->get();
        } else {
            // Los demás usuarios solo ven sus propios tickets
            $ticketsPendientes = Ticket::where('user_id', $user->id)->where('status', 'Pendiente')->latest()->get();
            $ticketsConcluidos = Ticket::where('user_id', $user->id)->where('status', 'Concluido')->latest()->get();
        }

        return view('tickets.index', compact('ticketsPendientes', 'ticketsConcluidos'));
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

        Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
        ]);

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
            'evidence' => 'nullable|image|max:2048', // Opcional, imagen, máx 2MB
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

        return redirect()->route('tickets.index')->with('success', 'El ticket ha sido marcado como concluido.');
    }
}