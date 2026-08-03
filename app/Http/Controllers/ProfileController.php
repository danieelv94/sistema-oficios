<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Cargar las solicitudes de vacaciones del propio usuario con sus fechas
        $solicitudes = $user->solicitudesVacaciones()
            ->with('fechas')
            ->orderBy('created_at', 'desc')
            ->get();

        // Cargar solicitudes de vacaciones pendientes del personal
        $solicitudesPendientes = collect();
        if ($user->role === 'admin') {
            $solicitudesPendientes = \App\Models\SolicitudVacacion::with(['user.area', 'fechas'])
                ->where('estatus', 'Pendiente')
                ->orderBy('created_at', 'asc')
                ->get();
        } elseif ($user->role === 'jefe_area') {
            $solicitudesPendientes = \App\Models\SolicitudVacacion::with(['user.area', 'fechas'])
                ->where('estatus', 'Pendiente')
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('area_id', $user->area_id)
                          ->where('id', '!=', $user->id);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        // Cargar configuración de periodos vacacionales para el año actual
        $configuracion = \App\Models\ConfiguracionVacacion::where('anio', now()->year)->first();

        // Calcular días consumidos en cada período para este usuario
        $diasPeriodo1Usados = 0;
        $diasPeriodo2Usados = 0;

        if ($configuracion) {
            $userFechas = \App\Models\SolicitudVacacionFecha::whereHas('solicitud', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->whereIn('estatus', ['Pendiente', 'Aprobado']);
                })
                ->pluck('fecha')
                ->map(fn($d) => $d->format('Y-m-d'));

            $p1Inicio = $configuracion->periodo1_inicio ? $configuracion->periodo1_inicio->format('Y-m-d') : null;
            $p1Fin = $configuracion->periodo1_fin ? $configuracion->periodo1_fin->format('Y-m-d') : null;
            $p2Inicio = $configuracion->periodo2_inicio ? $configuracion->periodo2_inicio->format('Y-m-d') : null;
            $p2Fin = $configuracion->periodo2_fin ? $configuracion->periodo2_fin->format('Y-m-d') : null;

            foreach ($userFechas as $fecha) {
                if ($p1Inicio && $p1Fin && $fecha >= $p1Inicio && $fecha <= $p1Fin) {
                    $diasPeriodo1Usados++;
                } elseif ($p2Inicio && $p2Fin && $fecha >= $p2Inicio && $fecha <= $p2Fin) {
                    $diasPeriodo2Usados++;
                }
            }
        }

        return view('profile.edit', [
            'user' => $user,
            'solicitudes' => $solicitudes,
            'solicitudesPendientes' => $solicitudesPendientes,
            'configuracion' => $configuracion,
            'diasPeriodo1Usados' => $diasPeriodo1Usados,
            'diasPeriodo2Usados' => $diasPeriodo2Usados,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}