<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Area;
use App\Models\Nivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withTrashed()->with('area', 'nivel');
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('no_empleado', 'like', "%{$searchTerm}%")
                    ->orWhere('prof', 'like', "%{$searchTerm}%")
                    ->orWhere('role', 'like', "%{$searchTerm}%")
                    ->orWhere('cargo', 'like', "%{$searchTerm}%")
                    ->orWhereHas('area', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }
        $usuarios = $query->orderBy('name', 'asc')->paginate(10);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $areas = Area::with('subareas')->get();
        $niveles = Nivel::orderBy('nombre')->get();
        $occupiedDirectors = User::where('role', 'jefe_area')->pluck('area_id')->toArray();
        $occupiedSubdirectors = User::whereIn('role', ['subdirector', 'admin'])->whereNotNull('subarea_id')->pluck('subarea_id')->toArray();
        return view('usuarios.create', compact('areas', 'niveles', 'occupiedDirectors', 'occupiedSubdirectors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prof' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_empleado' => ['nullable', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,jefe_area,subdirector,user,recepcionista,secretaria_area,correspondencia'],
            'cargo' => ['nullable', 'string', 'max:255'],
            'area_id' => ['required', 'exists:areas,id'],
            'subarea_id' => ['nullable', 'exists:subareas,id'],
            'nivel_id' => ['nullable', 'exists:nivels,id'],
            'recibir_correos' => ['nullable', 'boolean'],
        ]);

        if ($request->role === 'jefe_area') {
            $exists = User::where('role', 'jefe_area')->where('area_id', $request->area_id)->exists();
            if ($exists) {
                return back()->withErrors(['role' => 'Esta Dirección ya cuenta con un Director asignado.'])->withInput();
            }
        }

        if ($request->role === 'subdirector' || ($request->role === 'admin' && !empty($request->subarea_id))) {
            if ($request->role === 'subdirector' && empty($request->subarea_id)) {
                return back()->withErrors(['subarea_id' => 'Debe seleccionar una Subdirección para el rol de Subdirector.'])->withInput();
            }
            $exists = User::whereIn('role', ['subdirector', 'admin'])->where('subarea_id', $request->subarea_id)->exists();
            if ($exists) {
                return back()->withErrors(['role' => 'Esta Subdirección ya cuenta con un Subdirector o Administrador asignado.'])->withInput();
            }
        }

        User::create([
            'name' => $request->name,
            'prof' => $request->prof,
            'email' => $request->email,
            'no_empleado' => $request->no_empleado,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'area_id' => $request->area_id,
            'subarea_id' => $request->subarea_id,
            'cargo' => $request->cargo,
            'nivel_id' => $request->nivel_id,
            'recibir_correos' => $request->has('recibir_correos'),
        ]);
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $areas = Area::with('subareas')->get();
        $niveles = Nivel::orderBy('nombre')->get();
        $occupiedDirectors = User::where('role', 'jefe_area')->where('id', '!=', $user->id)->pluck('area_id')->toArray();
        $occupiedSubdirectors = User::whereIn('role', ['subdirector', 'admin'])->where('id', '!=', $user->id)->whereNotNull('subarea_id')->pluck('subarea_id')->toArray();
        return view('usuarios.edit', compact('user', 'areas', 'niveles', 'occupiedDirectors', 'occupiedSubdirectors'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prof' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'no_empleado' => ['nullable', 'string', 'max:255', 'unique:users,no_empleado,' . $user->id],
            'role' => ['required', 'string', 'in:admin,jefe_area,subdirector,user,recepcionista,secretaria_area,correspondencia'],
            'area_id' => ['required', 'exists:areas,id'],
            'subarea_id' => ['nullable', 'exists:subareas,id'],
            'cargo' => ['nullable', 'string', 'max:255'],
            'nivel_id' => ['nullable', 'exists:nivels,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'recibir_correos' => ['nullable', 'boolean'],
        ]);

        if ($request->role === 'jefe_area') {
            $exists = User::where('role', 'jefe_area')->where('area_id', $request->area_id)->where('id', '!=', $user->id)->exists();
            if ($exists) {
                return back()->withErrors(['role' => 'Esta Dirección ya cuenta con un Director asignado.'])->withInput();
            }
        }

        if ($request->role === 'subdirector' || ($request->role === 'admin' && !empty($request->subarea_id))) {
            if ($request->role === 'subdirector' && empty($request->subarea_id)) {
                return back()->withErrors(['subarea_id' => 'Debe seleccionar una Subdirección para el rol de Subdirector.'])->withInput();
            }
            $exists = User::whereIn('role', ['subdirector', 'admin'])->where('subarea_id', $request->subarea_id)->where('id', '!=', $user->id)->exists();
            if ($exists) {
                return back()->withErrors(['role' => 'Esta Subdirección ya cuenta con un Subdirector o Administrador asignado.'])->withInput();
            }
        }

        $data = $request->only('name', 'prof', 'email', 'role', 'area_id', 'subarea_id', 'no_empleado', 'cargo', 'nivel_id');
        $data['recibir_correos'] = $request->has('recibir_correos');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() == $user->id) {
            return back()->with('error', 'No puedes deshabilitarte a ti mismo.');
        }
        $user->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario deshabilitado correctamente.');
    }

    public function restore($id)
    {
        User::withTrashed()->find($id)->restore();
        return redirect()->route('usuarios.index')->with('success', 'Usuario habilitado correctamente.');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->find($id);
        if (auth()->id() == $user->id) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }
        $user->forceDelete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado permanentemente.');
    }
}