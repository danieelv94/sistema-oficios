<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 uppercase tracking-widest">
            {{ __('Entrada de Correspondencia') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-[#932C43] transition-all">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Correspondencia Recibida</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase mt-1">Buzón central de oficios y documentos pendientes de asignación</p>
                        </div>
                        @if(in_array(Auth::user()->role, ['admin', 'recepcionista']))
                            <a href="{{ route('oficios.create') }}"
                                class="bg-[#932C43] text-white px-5 py-2.5 rounded-lg text-xs font-black uppercase hover:bg-[#722134] shadow-md hover:shadow-lg transition duration-200 transform hover:scale-102 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                </svg>
                                Nuevo Oficio
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full text-xs text-left">
                            <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase font-black">
                                <tr>
                                    <th class="px-6 py-4 tracking-wider">Número de Oficio</th>
                                    <th class="px-6 py-4 tracking-wider">Remitente</th>
                                    <th class="px-6 py-4 tracking-wider">Asunto</th>
                                    <th class="px-6 py-4 tracking-wider text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($oficios as $oficio)
                                    <tr class="hover:bg-gray-50/80 transition duration-150">
                                        <td class="px-6 py-4 font-bold text-gray-900">{{ $oficio->numero_oficio }}</td>
                                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $oficio->remitente }}</td>
                                        <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $oficio->asunto }}</td>
                                        <td class="px-6 py-4 text-center space-x-2">
                                            <a href="{{ route('oficios.vistaTurnado', $oficio->id) }}"
                                                class="inline-block bg-[#932C43] text-white px-4 py-2 rounded-lg font-black uppercase text-[10px] tracking-wider hover:bg-[#722134] transition duration-150 hover:shadow-sm">
                                                Turnar
                                            </a>
                                            @if(in_array(Auth::user()->role, ['admin', 'correspondencia', 'recepcionista']))
                                                <a href="{{ route('oficios.edit', $oficio->id) }}"
                                                    class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg font-black uppercase text-[10px] tracking-wider hover:bg-blue-700 transition duration-150 hover:shadow-sm">
                                                    Editar
                                                </a>
                                            @endif
                                        </td>
                                     </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                            No se encontraron oficios registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $oficios->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
