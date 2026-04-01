<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Oficios de Comisión') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700">
                            {{ Auth::user()->role == 'admin' ? 'Registro General Estatal' : 'Registro de Área' }}
                        </h3>
                        <a href="{{ route('comisiones.create') }}"
                            class="px-4 py-2 bg-[#932C43] text-white rounded-md hover:bg-[#722134] font-bold shadow-sm transition">
                            + Solicitar Nueva Comisión
                        </a>
                    </div>

                    {{-- Buscador (Solo Admin) --}}
                    @if(Auth::user()->role == 'admin')
                        <div class="mb-6">
                            <form action="{{ route('comisiones.index') }}" method="GET" class="flex gap-2">
                                <input type="text" name="search" placeholder="No. Oficio, nombre del comisionado o lugar..."
                                    class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md focus:ring-[#932C43] focus:border-[#932C43]"
                                    value="{{ request('search') }}">
                                <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-black transition">Buscar</button>
                                @if(request('search'))
                                    <a href="{{ route('comisiones.index') }}"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Limpiar</a>
                                @endif
                            </form>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-100">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b text-gray-600 text-xs font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 text-left">No. Oficio</th>
                                    <th class="py-3 px-4 text-left">Fecha Emisión</th>
                                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'secretaria_area')
                                        <th class="py-3 px-4 text-left">Comisionado</th>
                                    @endif
                                    <th class="py-3 px-4 text-left">Actividad / Lugar</th>
                                    <th class="py-3 px-4 text-center">Estado</th>
                                    <th class="py-3 px-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($comisiones as $comision)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-3 px-4 text-sm font-bold text-[#932C43]">
                                            {{ $comision->oficio_numero }}
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            {{ $comision->created_at->format('d/m/Y') }}
                                        </td>

                                        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'secretaria_area')
                                            <td class="py-3 px-4 text-sm">
                                                <div class="font-medium">
                                                    {{-- BLINDAJE: Muestra el nombre aunque esté deshabilitado (con withTrashed)
                                                    --}}
                                                    {{ $comision->user ? $comision->user->name : 'Usuario no disponible' }}

                                                    @if($comision->user && method_exists($comision->user, 'trashed') && $comision->user->trashed())
                                                        <span
                                                            class="text-[9px] bg-red-100 text-red-600 px-1 rounded ml-1 font-bold">INACTIVO</span>
                                                    @endif
                                                </div>
                                                <div class="text-[10px] text-gray-400">
                                                    {{ ($comision->user && $comision->user->area) ? $comision->user->area->name : 'Área no registrada' }}
                                                </div>
                                            </td>
                                        @endif

                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            <div class="font-bold">{{ $comision->lugar }}</div>
                                            <div class="text-xs italic text-gray-400">
                                                {{ Str::limit($comision->actividad, 45) }}
                                            </div>
                                        </td>

                                        <td class="py-3 px-4 text-center">
                                            @if($comision->status === 'Cancelado')
                                                <span
                                                    class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700 uppercase">Cancelado</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">Activo</span>
                                            @endif
                                        </td>

                                        <td class="py-3 px-4 text-center">
                                            <div class="flex justify-center items-center space-x-2">
                                                {{-- Botón Ver PDF --}}
                                                <a href="{{ route('comisiones.show', $comision) }}"
                                                    class="p-1 text-gray-600 hover:text-[#932C43] transition"
                                                    title="Ver PDF">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                @if(Auth::user()->role == 'admin' && $comision->status !== 'Cancelado')
                                                    {{-- Botón Editar --}}
                                                    <a href="{{ route('comisiones.edit', $comision) }}"
                                                        class="p-1 text-blue-600 hover:text-blue-800 transition" title="Editar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>

                                                    {{-- Botón Cancelar --}}
                                                    <form action="{{ route('comisiones.cancelar', $comision) }}" method="POST"
                                                        onsubmit="return confirm('¿Confirmas la cancelación de este oficio?');"
                                                        class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="p-1 text-yellow-600 hover:text-yellow-800 transition"
                                                            title="Cancelar Oficio">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10 text-gray-400 italic">No se encontraron
                                            oficios de comisión.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $comisiones->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>