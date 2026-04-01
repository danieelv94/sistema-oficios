<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight italic uppercase">
            {{ __('Emitir Nueva Circular Oficial - CEAA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-8 border-[#932C43]">
                <div class="p-8 text-gray-900">

                    <form action="{{ route('avisos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Título del Aviso --}}
                            <div class="md:col-span-2">
                                <label for="titulo"
                                    class="block text-sm font-black text-gray-700 uppercase tracking-wider">Asunto /
                                    Título de la Circular</label>
                                <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#932C43] focus:border-[#932C43] placeholder-gray-400"
                                    placeholder="Ej: Suspensión de labores por día festivo">
                                @error('titulo') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Mensaje o Contenido --}}
                            <div class="md:col-span-2">
                                <label for="mensaje"
                                    class="block text-sm font-black text-gray-700 uppercase tracking-wider">Cuerpo del
                                    Mensaje</label>
                                <textarea name="mensaje" id="mensaje" rows="5" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#932C43] focus:border-[#932C43] placeholder-gray-400"
                                    placeholder="Escriba aquí las instrucciones o la información detallada...">{{ old('mensaje') }}</textarea>
                                @error('mensaje') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Selección de Área (Destinatarios) --}}
                            <div>
                                <label for="area_id"
                                    class="block text-sm font-black text-gray-700 uppercase tracking-wider">Destinatarios
                                    (Dirección)</label>
                                <select name="area_id" id="area_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#932C43] focus:border-[#932C43]">
                                    <option value="">Toda la CEAA (Circular Global)</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-gray-500 mt-1 italic">Deje en blanco para enviar a todo el
                                    personal.</p>
                            </div>

                            {{-- Prioridad --}}
                            <div>
                                <label for="prioridad"
                                    class="block text-sm font-black text-gray-700 uppercase tracking-wider">Prioridad
                                    del Aviso</label>
                                <select name="prioridad" id="prioridad" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-[#932C43] focus:border-[#932C43]">
                                    <option value="Normal" {{ old('prioridad') == 'Normal' ? 'selected' : '' }}>Normal
                                    </option>
                                    <option value="Urgente" {{ old('prioridad') == 'Urgente' ? 'selected' : '' }}>Urgente
                                        (Bloquea pantalla)</option>
                                </select>
                            </div>

                            {{-- Carga de Archivo Adjunto --}}
                            <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300">
                                <label for="archivo"
                                    class="block text-sm font-black text-[#932C43] uppercase tracking-wider mb-2">
                                    Adjuntar Documento Oficial (PDF o Imagen)
                                </label>
                                <input type="file" name="archivo" id="archivo" accept=".pdf, .jpg, .jpeg, .png" class="block w-full text-sm text-gray-500 
                                    file:mr-4 file:py-2 file:px-4 
                                    file:rounded-full file:border-0 
                                    file:text-sm file:font-bold 
                                    file:bg-red-50 file:text-[#932C43] 
                                    hover:file:bg-red-100 cursor-pointer">
                                <p class="mt-2 text-xs text-gray-500">Formatos permitidos: **PDF, JPG, PNG**. Tamaño
                                    máximo: **10MB**.</p>
                                @error('archivo') <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="mt-10 flex items-center justify-end gap-4 border-t pt-6">
                            <a href="{{ route('principal') }}"
                                class="text-sm font-bold text-gray-500 hover:text-gray-700 uppercase tracking-widest">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-[#932C43] border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-[#7a2437] active:bg-[#5a1a29] focus:outline-none focus:ring-2 focus:ring-[#932C43] focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Emitir Circular Oficial
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>