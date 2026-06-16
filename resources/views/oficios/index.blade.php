<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 uppercase tracking-widest">
            {{ __('Entrada de Correspondencia') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50" x-data="{ showCancelModal: false, cancelOficioId: null, cancelOficioNumero: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-black uppercase text-green-800">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-guinda-ceaa transition-all">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Correspondencia Recibida</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase mt-1">Buzón central de oficios y documentos pendientes de asignación</p>
                        </div>
                        @if(in_array(Auth::user()->role, ['admin', 'recepcionista']))
                            <a href="{{ route('oficios.create') }}"
                                class="bg-guinda-ceaa text-white px-5 py-2.5 rounded-lg text-xs font-black uppercase hover:bg-guinda-ceaa-hover shadow-md hover:shadow-lg transition duration-200 transform hover:scale-102 flex items-center gap-2">
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
                                        <td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
                                            <a href="{{ route('oficios.vistaTurnado', $oficio->id) }}"
                                                class="inline-block bg-guinda-ceaa text-white px-4 py-2 rounded-lg font-black uppercase text-[10px] tracking-wider hover:bg-guinda-ceaa-hover transition duration-150 hover:shadow-sm">
                                                Turnar
                                            </a>
                                            @if(in_array(Auth::user()->role, ['admin', 'correspondencia', 'recepcionista']))
                                                <a href="{{ route('oficios.edit', $oficio->id) }}"
                                                    class="inline-block bg-gris-oscuro text-white px-4 py-2 rounded-lg font-black uppercase text-[10px] tracking-wider hover:bg-guinda-ceaa transition duration-150 hover:shadow-sm">
                                                    Editar
                                                </a>
                                            @endif
                                            @if(in_array(Auth::user()->role, ['admin', 'correspondencia']))
                                                <button type="button" @click="showCancelModal = true; cancelOficioId = {{ $oficio->id }}; cancelOficioNumero = '{{ $oficio->numero_oficio }}'"
                                                    class="inline-block bg-red-600 text-white px-4 py-2 rounded-lg font-black uppercase text-[10px] tracking-wider hover:bg-red-700 transition duration-150 hover:shadow-sm">
                                                    Cancelar
                                                </button>
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

    {{-- MODAL DE CANCELACIÓN --}}
    <div x-show="showCancelModal" 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4" 
         style="display: none;" 
         x-init="$el.style.display = 'flex'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 backdrop-blur-sm transition-opacity" @click="showCancelModal = false"></div>

        <div class="relative w-full max-w-lg mx-auto z-[100000] transform transition-all shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="bg-white rounded-xl overflow-hidden border-t-8 border-red-600">
                <div class="p-5 border-b border-gray-100 flex items-center">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black uppercase tracking-tight text-gray-800">
                        Cancelar Oficio
                    </h3>
                </div>

                <form :action="'/oficios/' + cancelOficioId + '/cancelar'" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <p class="text-sm font-bold text-gray-700 mb-3 uppercase">
                            ¿Está seguro que desea cancelar el oficio: <span class="text-red-600 font-black" x-text="cancelOficioNumero"></span>?
                        </p>
                        <div>
                            <label class="block font-bold text-[10px] text-gray-400 uppercase mb-2">Explicación del Motivo de Cancelación</label>
                            <textarea name="motivo_cancelacion" required rows="4" 
                                placeholder="Escriba aquí el motivo detallado de la cancelación..."
                                class="w-full text-xs rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 p-3"
                                x-init="$watch('showCancelModal', value => { if(!value) $el.value = '' })"></textarea>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                        <button type="button" @click="showCancelModal = false"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-xs font-black uppercase transition">
                            Cerrar
                        </button>
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-xs font-black uppercase transition shadow-md hover:shadow-lg">
                            Confirmar Cancelación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
