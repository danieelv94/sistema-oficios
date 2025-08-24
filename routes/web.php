<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OficioController;
use App\Http\Controllers\UserController;


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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // ... (ruta de profile)

    Route::get('/dashboard', [OficioController::class, 'index'])->name('dashboard');
    Route::get('/oficios/crear', [OficioController::class, 'create'])->name('oficios.create');
    Route::post('/oficios', [OficioController::class, 'store'])->name('oficios.store');
    Route::get('/oficios/{oficio}', [OficioController::class, 'show'])->name('oficios.show');
    Route::put('/oficios/{oficio}/turnar', [OficioController::class, 'turnar'])->name('oficios.turnar');
    Route::put('/oficios/{oficio}/asignar', [OficioController::class, 'asignar'])->name('oficios.asignar');
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
});

require __DIR__.'/auth.php';
