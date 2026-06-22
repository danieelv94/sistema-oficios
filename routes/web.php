<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\OficioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComisionController;
use App\Http\Controllers\AvisoController;

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/principal', [App\Http\Controllers\OficioController::class, 'principal'])->name('principal');

    // Gestión de Correspondencia (la tabla)
    Route::get('/oficios', [App\Http\Controllers\OficioController::class, 'index'])->name('oficios.index');

    // Bandeja de Gestión de Turnos (Debe declararse antes del recurso para evitar conflicto con el comodín {oficio})
    Route::get('/oficios/gestion', [App\Http\Controllers\OficioController::class, 'gestion'])
        ->name('oficios.gestion');

    // Seguimiento General de Turnos (Debe declararse antes del recurso)
    Route::get('/oficios/seguimiento', [App\Http\Controllers\OficioController::class, 'seguimiento'])
        ->name('oficios.seguimiento');

    // Reporte Diario de Turnos (Debe declararse antes del recurso)
    Route::get('/oficios/reporte-diario', [App\Http\Controllers\OficioController::class, 'reporteDiario'])
        ->name('oficios.reporteDiario');

    // Reporte Diario de Entradas (Debe declararse antes del recurso)
    Route::get('/oficios/reporte-entradas', [App\Http\Controllers\OficioController::class, 'reporteEntradas'])
        ->name('oficios.reporteEntradas');

    // --- Módulo de Oficios ---
    Route::resource('oficios', OficioController::class)->except(['index']);
    Route::put('/oficios/{oficio}/turnar', [OficioController::class, 'turnar'])->name('oficios.turnar');
    Route::put('/oficios/{oficio}/asignar', [OficioController::class, 'asignar'])->name('oficios.asignar');
    Route::put('/oficios/{oficio}/cancelar', [OficioController::class, 'cancelar'])->name('oficios.cancelar');
    Route::put('/oficios/turno/{pivote_id}/cancelar', [OficioController::class, 'cancelarTurno'])->name('oficios.cancelarTurno');
    Route::get('/oficios/{oficio}/generar', [OficioController::class, 'generarOficio'])->name('oficios.generar');

    // --- Administración de Usuarios (Admins) ---
    Route::resource('usuarios', UserController::class)->parameters([
        'usuarios' => 'user'
    ]);
    Route::put('/usuarios/{user}/restore', [UserController::class, 'restore'])->name('usuarios.restore');
    Route::delete('/usuarios/{user}/force-delete', [UserController::class, 'forceDelete'])->name('usuarios.forceDelete');


    // --- Sistema de Tickets de Soporte ---
    Route::resource('tickets', TicketController::class);
    Route::get('/tickets/{ticket}/resolver', [TicketController::class, 'edit'])->name('tickets.edit');

    // --- Perfil de Usuario ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // --- Órdenes de Comisión ---
    Route::resource('comisiones', ComisionController::class)->parameters([
        'comisiones' => 'comision'
    ]);
    Route::patch('/comisiones/{comision}/cancelar', [ComisionController::class, 'cancelar'])->name('comisiones.cancelar');

    // --- Módulo de Avisos / Circulares ---
    Route::get('/avisos', [AvisoController::class, 'index'])->name('avisos.index');
    Route::get('/avisos/crear', [AvisoController::class, 'create'])->name('avisos.create');
    Route::post('/avisos', [AvisoController::class, 'store'])->name('avisos.store');
    Route::get('/avisos/pendientes', [AvisoController::class, 'pendientes'])->name('avisos.pendientes');
    Route::post('/avisos/{aviso}/leer', [AvisoController::class, 'leer'])->name('avisos.leer');
    Route::get('/avisos/{aviso}/seguimiento', [AvisoController::class, 'seguimiento'])->name('avisos.seguimiento');

    // Rutas de Gestión de Turnos (Solo para roles con capacidad de gestión)
    Route::middleware(['auth'])->group(function () {
        // Confirmar recepción del turno
        Route::put('/oficios/turno/{pivote_id}/recibir', [App\Http\Controllers\OficioController::class, 'recibirTurno'])
            ->name('oficios.recibirTurno');

        // Confirmar de notificado por el operativo
        Route::put('/oficios/turno/{pivote_id}/notificar', [App\Http\Controllers\OficioController::class, 'notificarTurno'])
            ->name('oficios.notificarTurno');

        // Formulario de Solventación / Atención
        Route::get('/oficios/turno/{areaOficioId}/atender', [App\Http\Controllers\OficioController::class, 'atender'])
            ->name('oficios.atender');

        // Guardar la respuesta del operativo
        Route::post('/oficios/turno/{areaOficioId}/solventar', [App\Http\Controllers\OficioController::class, 'solventar'])
            ->name('oficios.solventar');
    });

    // Esta ruta es para que el Admin/Correspondencia vea el oficio y lo turne
    Route::get('/oficios/{oficio}/turnar', [App\Http\Controllers\OficioController::class, 'vistaTurnado'])
        ->name('oficios.vistaTurnado');

    // --- NOTIFICACIONES WEB PUSH ---
    Route::post('/notifications/subscribe', function (Request $request) {
        $request->validate([
            'endpoint' => 'required',
            'keys.auth' => 'required',
            'keys.p256dh' => 'required'
        ]);

        // Guarda la suscripción en la tabla 'push_subscriptions'
        Auth::user()->updatePushSubscription(
            $request->endpoint,
            $request->keys['p256dh'],
            $request->keys['auth']
        );

        return response()->json(['success' => true]);
    })->name('notifications.subscribe');

});

require __DIR__ . '/auth.php';