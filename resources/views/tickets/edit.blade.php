<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Resolver Ticket de Soporte') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                <a href="{{ route('tickets.index') }}"
                    class="text-sm font-bold text-gray-500 hover:text-[#932C43] flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Listado
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Columna de Información --}}
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-[#932C43]">
                        <h4 class="text-xs font-black text-gray-400 uppercase mb-4">Datos del Reporte</h4>
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="text-xs font-bold text-[#932C43] uppercase">Solicitante</p>
                                <p>{{ $ticket->user ? $ticket->user->name : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#932C43] uppercase">Asunto</p>
                                <p class="font-bold">{{ $ticket->subject }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formulario --}}
                <div class="md:col-span-2">
                    <div class="bg-white shadow-2xl sm:rounded-lg border-t-8 border-[#932C43]">
                        <div class="p-8">
                            <form method="POST" action="{{ route('tickets.update', $ticket) }}"
                                enctype="multipart/form-data" class="space-y-6">
                                @csrf
                                @method('PUT')

                                {{-- Notas de Solución --}}

                                <div class="md:col-span-2">
                                    <div class="bg-white overflow-hidden shadow-2xl sm:rounded-lg">
                                        <div class="p-8">
                                            <div class="mb-8 border-b border-gray-100 pb-4">
                                                <h3 class="text-xl font-black text-gray-800 uppercase italic">
                                                    Descripción del Problema</h3>
                                                <p
                                                    class="mt-4 text-gray-600 bg-gray-50 p-4 rounded-md border-l-4 border-gray-200 italic">
                                                    "{{ $ticket->description }}"
                                                </p>
                                            </div>
                                            <div>
                                                <x-label for="resolution_notes"
                                                    class="text-[#932C43] font-bold uppercase text-xs"
                                                    value="Descripción de la Solución" />
                                                <textarea name="resolution_notes" id="resolution_notes" rows="5"
                                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-[#932C43] focus:ring-[#932C43]"
                                                    required>{{ old('resolution_notes') }}</textarea>
                                                <x-input-error :messages="$errors->get('resolution_notes')"
                                                    class="mt-2" />
                                            </div>

                                            {{-- Campo de Evidencia --}}
                                            <div
                                                class="bg-gray-50 p-4 rounded-lg border-2 border-dashed border-gray-200">
                                                <x-label for="evidence"
                                                    class="text-[#932C43] font-bold uppercase text-xs mb-2"
                                                    value="Evidencia Fotográfica" />
                                                <input type="file" name="evidence" id="evidence"
                                                    accept="image/jpeg, image/png, image/jpg"
                                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-[#932C43] file:text-white hover:file:bg-[#722134]" />
                                                <p class="mt-2 text-[10px] text-gray-400 italic">Si la imagen es muy
                                                    grande y marca
                                                    error, intenta comprimirla antes de subirla.</p>
                                            </div>

                                            <div class="flex items-center justify-end pt-4">
                                                <button type="submit"
                                                    class="px-10 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 font-black uppercase tracking-widest shadow-lg transition-all transform hover:-translate-y-1">
                                                    Cerrar Ticket
                                                </button>
                                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>