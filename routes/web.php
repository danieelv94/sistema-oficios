<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OficioController;

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
});

require __DIR__.'/auth.php';
