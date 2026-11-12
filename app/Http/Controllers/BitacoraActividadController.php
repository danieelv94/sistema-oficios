<?php

namespace App\Http\Controllers;

use App\Models\BitacoraActividad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BitacoraActividadController extends Controller
{
    private function authorizeAccess(): void
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Acceso restringido exclusivamente al Administrador del sistema.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = BitacoraActividad::query();

        // Filtro por Módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        // Filtro por Acción
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        // Filtro por Usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por Fecha Desde / Hasta
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Búsqueda general
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', "%{$search}%")
                  ->orWhere('user_nombre', 'like', "%{$search}%")
                  ->orWhere('area_nombre', 'like', "%{$search}%")
                  ->orWhere('modulo', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $actividades = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();

        // KPIs de Auditoría
        $stats = [
            'total' => BitacoraActividad::count(),
            'hoy' => BitacoraActividad::whereDate('created_at', today())->count(),
            'creaciones' => BitacoraActividad::where('accion', 'Creación')->count(),
            'actualizaciones' => BitacoraActividad::whereIn('accion', ['Actualización', 'Cambio de Estatus'])->count(),
            'eliminaciones' => BitacoraActividad::where('accion', 'Eliminación')->count(),
            'accesos' => BitacoraActividad::whereIn('accion', ['Inicio de Sesión', 'Cierre de Sesión'])->count(),
        ];

        $modulos = BitacoraActividad::select('modulo')
            ->distinct()
            ->orderBy('modulo')
            ->pluck('modulo');

        $usuarios = User::orderBy('name')->get(['id', 'name', 'prof']);

        return view('bitacora_auditoria.index', compact(
            'actividades',
            'stats',
            'modulos',
            'usuarios'
        ));
    }
}

