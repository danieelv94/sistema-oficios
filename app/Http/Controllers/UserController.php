<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request; // <-- Importante que esta línea esté
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Muestra una lista de todos los usuarios, con opción de búsqueda.
     */
    public function index(Request $request)
    {
        // Iniciamos la consulta base, incluyendo usuarios deshabilitados y sus áreas
        $query = User::withTrashed()->with('area');

        // Si hay un término de búsqueda en la solicitud...
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            
            // ...filtramos los resultados
            $query->where(function ($q) use ($searchTerm) {
                // Busca en el nombre, email y número de empleado del usuario
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('no_empleado', 'like', "%{$searchTerm}%")
                  // También busca en la tabla relacionada 'areas' por el nombre del área
                  ->orWhereHas('area', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Ordenamos por nombre y paginamos los resultados
        $usuarios = $query->orderBy('name', 'asc')->paginate(10);
        
        // Devolvemos la vista con los usuarios (filtrados o no)
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
            'no_empleado' => ['nullable', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,jefe_area,user'],
            'area_id' => ['required', 'exists:areas,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_empleado' => $request->no_empleado,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'area_id' => $request->area_id,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $user)
    {
        $areas = Area::all();
        return view('usuarios.edit', compact('user', 'areas'));
    }

    /**
     * Actualiza un usuario en la base de datos.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'no_empleado' => ['nullable', 'string', 'max:255', 'unique:users,no_empleado,' . $user->id],
            'role' => ['required', 'string', 'in:admin,jefe_area,user'],
            'area_id' => ['required', 'exists:areas,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = $request->only('name', 'email', 'role', 'area_id', 'no_empleado');

        // Solo actualizamos la contraseña si el usuario escribió una nueva
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }
    
    /**
     * Deshabilita un usuario (Soft Delete).
     */
    public function destroy(User $user)
    {
        // No puedes deshabilitarte a ti mismo
        if (auth()->id() == $user->id) {
            return back()->with('error', 'No puedes deshabilitarte a ti mismo.');
        }

        $user->delete(); // Esto ejecuta el borrado suave
        return redirect()->route('usuarios.index')->with('success', 'Usuario deshabilitado correctamente.');
    }

    /**
     * Restaura un usuario deshabilitado.
     */
    public function restore($id)
    {
        // Usamos withTrashed() para encontrar al usuario deshabilitado
        User::withTrashed()->find($id)->restore();
        return redirect()->route('usuarios.index')->with('success', 'Usuario habilitado correctamente.');
    }

    /**
     * Borra permanentemente un usuario de la base de datos.
     */
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
}