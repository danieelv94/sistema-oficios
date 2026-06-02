<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-widest">
                {{ __('Administración de Usuarios') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-[#932C43]">
                <div class="p-6 text-gray-900">
                    
                    {{-- Encabezado e botón de creación --}}
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center">
                            <span class="p-2 bg-red-100 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-[#932C43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <h3 class="text-lg font-black text-gray-700 uppercase italic">Listado de Usuarios</h3>
                        </div>
                        <a href="{{ route('usuarios.create') }}"
                            class="px-4 py-2 bg-[#932C43] text-white rounded-md hover:bg-[#722134] font-bold shadow-md transition-all transform hover:scale-105 text-sm uppercase">
                            + Crear Nuevo Usuario
                        </a>
                    </div>

                    {{-- Buscador estilizado --}}
                    <div class="mb-6">
                        <form action="{{ route('usuarios.index') }}" method="GET" class="flex gap-2">
                            <input type="text" name="search"
                                placeholder="Buscar por nombre, email, no. empleado o área..."
                                class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-[#932C43] focus:border-[#932C43] focus:outline-none"
                                value="{{ request('search') }}">
                            <button type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-black transition text-sm font-bold uppercase tracking-wider">Buscar</button>
                            @if(request('search'))
                                <a href="{{ route('usuarios.index') }}"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-bold uppercase tracking-wider flex items-center justify-center">Limpiar</a>
                            @endif
                        </form>
                    </div>

                    {{-- Tabla estilizada --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-100">
                            <thead>
                                <tr class="bg-gray-50 border-b text-gray-600 text-xs font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 text-left">Nombre</th>
                                    <th class="py-3 px-4 text-left">Título</th>
                                    <th class="py-3 px-4 text-left">Cargo</th>
                                    <th class="py-3 px-4 text-left">Email</th>
                                    <th class="py-3 px-4 text-left">No. Empleado</th>
                                    <th class="py-3 px-4 text-left">Área</th>
                                    <th class="py-3 px-4 text-left">Rol</th>
                                    <th class="py-3 px-4 text-left">Nivel</th>
                                    <th class="py-3 px-4 text-center">Estado</th>
                                    <th class="py-3 px-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($usuarios as $usuario)
                                    <tr class="hover:bg-gray-50 transition {{ $usuario->trashed() ? 'bg-red-50 text-gray-500' : '' }}">
                                        <td class="py-3 px-4 text-sm font-bold text-[#932C43]">{{ $usuario->name }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600">{{ $usuario->prof ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600">{{ $usuario->cargo ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600">{{ $usuario->email }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600 font-medium">{{ $usuario->no_empleado ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600">{{ $usuario->area->name ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-sm">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700 uppercase">
                                                {{ $usuario->role }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">{{ $usuario->nivel->nombre ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($usuario->trashed())
                                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700 uppercase">
                                                    Deshabilitado
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">
                                                    Activo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex justify-center items-center space-x-2">
                                                @if($usuario->trashed())
                                                    <form action="{{ route('usuarios.restore', $usuario->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit"
                                                            class="px-3 py-1 bg-green-600 text-white rounded text-[10px] font-bold hover:bg-green-700 uppercase tracking-tighter shadow-sm transition">Habilitar</button>
                                                    </form>
                                                    <form action="{{ route('usuarios.forceDelete', $usuario->id) }}"
                                                        method="POST"
                                                        class="inline"
                                                        onsubmit="return confirm('¿Estás seguro de que quieres ELIMINAR PERMANENTEMENTE a este usuario? Esta acción no se puede deshacer.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-3 py-1 bg-red-700 text-white rounded text-[10px] font-bold hover:bg-red-800 uppercase tracking-tighter shadow-sm transition">Eliminar</button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('usuarios.edit', $usuario) }}"
                                                        class="px-3 py-1 bg-blue-600 text-white rounded text-[10px] font-bold hover:bg-blue-800 uppercase tracking-tighter shadow-sm transition">Editar</a>
                                                    <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                                        class="inline"
                                                        onsubmit="return confirm('¿Estás seguro de que quieres deshabilitar a este usuario?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-3 py-1 bg-yellow-600 text-white rounded text-[10px] font-bold hover:bg-yellow-700 uppercase tracking-tighter shadow-sm transition">Deshabilitar</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-10 text-gray-400 italic">No hay usuarios registrados para esta búsqueda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $usuarios->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>