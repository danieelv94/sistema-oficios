<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ __('Administración de Usuarios') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Gestión de cuentas de usuario, roles de acceso, niveles y estatus institucional</p>
            </div>
            <div>
                <a href="{{ route('usuarios.create') }}"
                    class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg font-bold shadow-xs hover:shadow-md transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Crear Nuevo Usuario
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Buscador estilizado --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5">
                <form action="{{ route('usuarios.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search"
                            placeholder="Buscar por nombre, correo, no. de empleado o dirección..."
                            class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa transition"
                            value="{{ request('search') }}">
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit"
                            class="w-full sm:w-auto px-5 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Buscar
                        </button>
                        @if(request('search'))
                            <a href="{{ route('usuarios.index') }}"
                                class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla estilizada --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Directorio de Usuarios</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $usuarios->total() }} usuarios
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Página {{ $usuarios->currentPage() }} de {{ $usuarios->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">Nombre</th>
                                <th class="py-3 px-4 text-left">Título / Cargo</th>
                                <th class="py-3 px-4 text-left">Email</th>
                                <th class="py-3 px-4 text-left">No. Empleado</th>
                                <th class="py-3 px-4 text-left">Área</th>
                                <th class="py-3 px-4 text-left">Rol</th>
                                <th class="py-3 px-4 text-left">Nivel</th>
                                <th class="py-3 px-4 text-left">F. Alta</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($usuarios as $usuario)
                                <tr class="hover:bg-slate-50/75 transition duration-150 {{ $usuario->trashed() ? 'bg-rose-50/30' : '' }}">
                                    <td class="py-3.5 px-4 font-bold text-slate-800 whitespace-nowrap">
                                        <span class="text-guinda-ceaa font-black">{{ $usuario->name }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 max-w-xs">
                                        <div class="font-semibold text-slate-700">{{ $usuario->cargo ?? 'S/C' }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium">{{ $usuario->prof ?? 'S/T' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 font-mono text-[11px]">
                                        {{ $usuario->email }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-700 font-medium whitespace-nowrap">
                                        #{{ $usuario->no_empleado ?? 'S/N' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 max-w-xs">
                                        <div class="font-medium text-slate-800 truncate" title="{{ $usuario->area->name ?? 'N/A' }}">
                                            🏢 {{ $usuario->area->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 uppercase border border-slate-200">
                                            {{ $usuario->role }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap font-medium">
                                        {{ $usuario->nivel->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($usuario->fecha_alta)
                                            <span class="text-slate-700 font-medium">{{ $usuario->fecha_alta->format('d/m/Y') }}</span>
                                        @else
                                            <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200 text-[10px] uppercase">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($usuario->trashed())
                                            <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200 uppercase">
                                                Deshabilitado
                                            </span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                                Activo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="flex justify-center items-center gap-1.5">
                                            @if($usuario->trashed())
                                                <form action="{{ route('usuarios.restore', $usuario->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition">
                                                        Habilitar
                                                    </button>
                                                </form>
                                                <form action="{{ route('usuarios.forceDelete', $usuario->id) }}"
                                                    method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('¿Estás seguro de que deseas ELIMINAR PERMANENTEMENTE a este usuario? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-2.5 py-1 bg-rose-700 hover:bg-rose-800 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                                    class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Editar
                                                </a>
                                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('¿Estás seguro de que deseas deshabilitar a este usuario?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition">
                                                        Deshabilitar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No hay usuarios registrados para esta búsqueda.</p>
                                            <p class="text-xs text-slate-400">Intenta ajustando los términos de búsqueda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $usuarios->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>