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
        $areas = Area::all();
        $niveles = Nivel::orderBy('nombre')->get();
        return view('usuarios.create', compact('areas', 'niveles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prof' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_empleado' => ['nullable', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,jefe_area,user,recepcionista,secretaria_area'],
            'cargo' => ['required', 'string', 'max:255'],
            'area_id' => ['required', 'exists:areas,id'],
            'nivel_id' => ['nullable', 'exists:nivels,id'],
        ]);
        User::create([
            'name' => $request->name,
            'prof' => $request->prof,
            'email' => $request->email,
            'no_empleado' => $request->no_empleado,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'area_id' => $request->area_id,
            'cargo' => $request->cargo,
            'nivel_id' => $request->nivel_id,
        ]);
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $areas = Area::all();
        $niveles = Nivel::orderBy('nombre')->get();
        return view('usuarios.edit', compact('user', 'areas', 'niveles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prof' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'no_empleado' => ['nullable', 'string', 'max:255', 'unique:users,no_empleado,' . $user->id],
            'role' => ['required', 'string', 'in:admin,jefe_area,user,recepcionista,secretaria_area'],
            'area_id' => ['required', 'exists:areas,id'],
            'cargo' => ['required', 'string', 'max:255'],
            'nivel_id' => ['nullable', 'exists:nivels,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);
        $data = $request->only('name', 'prof', 'email', 'role', 'area_id', 'no_empleado', 'cargo', 'nivel_id');
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