<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Administración de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Listado de Usuarios</h3>
                        <a href="{{ route('usuarios.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Crear Nuevo Usuario</a>
                    </div>

                    <div class="mb-4">
                        <form action="{{ route('usuarios.index') }}" method="GET">
                            <div class="flex items-center">
                                <input type="text" name="search" placeholder="Buscar por nombre, email, no. empleado o área..." 
                                       class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ request('search') }}">
                                <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded-r-md hover:bg-gray-800">Buscar</button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Nombre</th>
                                    <th class="py-2 px-4 border-b">Título</th>
                                    <th class="py-2 px-4 border-b">Email</th>
                                    <th class="py-2 px-4 border-b">No. Empleado</th>
                                    <th class="py-2 px-4 border-b">Área</th>
                                    <th class="py-2 px-4 border-b">Rol</th>
                                    <th class="py-2 px-4 border-b">Nivel</th>
                                    <th class="py-2 px-4 border-b">Estado</th>
                                    <th class="py-2 px-4 border-b">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($usuarios as $usuario)
                                    <tr class="hover:bg-gray-100 {{ $usuario->trashed() ? 'bg-red-50 text-gray-500' : '' }}">
                                        <td class="py-2 px-4 border-b">{{ $usuario->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $usuario->prof ?? 'N/A' }}</td>
                                        <td class="py-2 px-4 border-b">{{ $usuario->email }}</td>
                                        <td class="py-2 px-4 border-b">{{ $usuario->no_empleado ?? 'N/A' }}</td>
                                        <td class="py-2 px-4 border-b">{{ $usuario->area->name ?? 'N/A' }}</td>
                                        <td class="py-2 px-4 border-b">{{ $usuario->role }}</td>
                                        <td class="py-2 px-4 border-b">{{ $usuario->nivel->nombre ?? 'N/A' }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if($usuario->trashed())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Deshabilitado
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Activo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <div class="flex space-x-2">
                                                @if($usuario->trashed())
                                                    <form action="{{ route('usuarios.restore', $usuario->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded-md text-xs">Habilitar</button>
                                                    </form>
                                                    <form action="{{ route('usuarios.forceDelete', $usuario->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres ELIMINAR PERMANENTEMENTE a este usuario? Esta acción no se puede deshacer.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1 bg-red-700 text-white rounded-md text-xs">Eliminar</button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('usuarios.edit', $usuario) }}" class="px-3 py-1 bg-blue-500 text-white rounded-md text-xs">Editar</a>
                                                    <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres deshabilitar a este usuario?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1 bg-yellow-500 text-white rounded-md text-xs">Deshabilitar</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">No hay usuarios registrados para esta búsqueda.</td>
                                        </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $usuarios->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>