<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Muestra una lista de todos los usuarios.
     */
    public function index()
    {
        $usuarios = User::withTrashed()->with('area')->orderBy('name', 'asc')->paginate(10);
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        $areas = Area::all(); // Obtenemos todas las áreas para el select del formulario
        return view('usuarios.create', compact('areas'));
    }

    /**
     * Guarda el nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,jefe_area,user'], // Valida que el rol sea uno de los permitidos
            'area_id' => ['required', 'exists:areas,id'], // Valida que el área exista
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'area_id' => $request->area_id,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function destroy(User $user)
    {
        // No puedes deshabilitarte a ti mismo
        if (auth()->id() == $user->id) {
            return back()->with('error', 'No puedes deshabilitarte a ti mismo.');
        }

        $user->delete(); // Esto ejecuta el borrado suave
        return redirect()->route('usuarios.index')->with('success', 'Usuario deshabilitado correctamente.');
    }

    public function restore($id)
    {
        // Usamos withTrashed() para encontrar al usuario deshabilitado
        User::withTrashed()->find($id)->restore();
        return redirect()->route('usuarios.index')->with('success', 'Usuario habilitado correctamente.');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->find($id);

        // No puedes borrarte a ti mismo
        if (auth()->id() == $user->id) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }
        
        $user->forceDelete(); // Esto ejecuta el borrado permanente
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado permanentemente.');
    }

    public function edit(User $user)
    {
        $areas = Area::all();
        return view('usuarios.edit', compact('user', 'areas'));
    }

    public function update(Request $request, User $user)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'role' => ['required', 'string', 'in:admin,jefe_area,user'],
        'area_id' => ['required', 'exists:areas,id'],
        'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // La contraseña es opcional
    ]);

    $data = $request->only('name', 'email', 'role', 'area_id');

    // Solo actualizamos la contraseña si el usuario escribió una nueva
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
}
}