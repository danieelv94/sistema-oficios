<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OficioController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComisionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login');

Route::get('/tickets', function () {
    return view('tickets');
})->middleware(['auth'])->name('tickets');

Route::middleware('auth')->group(function () {
    // ... (ruta de profile)

    Route::get('/principal', [OficioController::class, 'index'])->name('principal');
    Route::get('/oficios/crear', [OficioController::class, 'create'])->name('oficios.create');
    Route::post('/oficios', [OficioController::class, 'store'])->name('oficios.store');
    Route::get('/oficios/{oficio}', [OficioController::class, 'show'])->name('oficios.show');
    Route::put('/oficios/{oficio}/turnar', [OficioController::class, 'turnar'])->name('oficios.turnar');
    Route::put('/oficios/{oficio}/asignar', [OficioController::class, 'asignar'])->name('oficios.asignar');
    Route::delete('/oficios/turno/{pivote_id}', [OficioController::class, 'eliminarTurno'])->name('oficios.eliminarTurno');

    // --- Rutas para Administración de Usuarios (Solo para Admins) ---
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UserController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');

    Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');

    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    Route::put('/usuarios/{user}/restore', [UserController::class, 'restore'])->name('usuarios.restore');
    Route::delete('/usuarios/{user}/force-delete', [UserController::class, 'forceDelete'])->name('usuarios.forceDelete');


    Route::delete('/oficios/{oficio}', [OficioController::class, 'destroy'])->name('oficios.destroy');

    Route::get('/oficios/{oficio}/generar', [OficioController::class, 'generarOficio'])->name('oficios.generar');


    // --- Rutas para el Sistema de Tickets de Soporte ---
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/crear', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show'); // Vista de detalle
    Route::get('/tickets/{ticket}/resolver', [TicketController::class, 'edit'])->name('tickets.edit'); // Formulario para resolver
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update'); // Lógica para resolver

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/comisiones/crear', [ComisionController::class, 'create'])->name('comisiones.create');
    Route::post('/comisiones', [ComisionController::class, 'store'])->name('comisiones.store');
    Route::get('/comisiones/{comision}', [ComisionController::class, 'show'])->name('comisiones.show');
    // ------------------------------------
    Route::get('/comisiones', [ComisionController::class, 'index'])->name('comisiones.index'); // <-- AÑADE ESTA LÍNEA
    Route::get('/comisiones/crear', [ComisionController::class, 'create'])->name('comisiones.create');
    Route::post('/comisiones', [ComisionController::class, 'store'])->name('comisiones.store');
    Route::get('/comisiones/{comision}', [ComisionController::class, 'show'])->name('comisiones.show');
    Route::patch('/comisiones/{comision}/cancelar', [ComisionController::class, 'cancelar'])->name('comisiones.cancelar');
});

require __DIR__ . '/auth.php';
