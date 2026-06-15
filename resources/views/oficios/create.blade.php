<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-tight">
            {{ __('Registrar Entrada de Correspondencia') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Alertas de Validación por si falta algún campo obligatorio --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 shadow-sm">
                    <p class="font-bold uppercase text-xs italic">Se detectaron errores en el formulario:</p>
                    <ul class="mt-2 list-disc list-inside text-xs font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-lg border-t-8 border-guinda-ceaa">
                <div class="p-8">

                    {{-- Encabezado del Formulario --}}
                    <div class="flex items-center mb-8 pb-4 border-b border-gray-100">
                        <div class="p-3 bg-red-50 rounded-full mr-4">
                            <svg class="w-8 h-8 text-guinda-ceaa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Nuevo Oficio</h3>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-tight">Recepción de
                                Correspondencia CEAA</p>
                        </div>
                    </div>

                    <form action="{{ route('oficios.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                            {{-- BLOQUE 1: IDENTIFICACIÓN --}}
                            <div class="space-y-4">
                                <h4
                                    class="text-[10px] font-black text-guinda-ceaa uppercase tracking-widest border-b border-red-50 mb-2">
                                    1. Identificación del Documento</h4>

                                <div>
                                    <label for="numero_oficio"
                                        class="block font-bold text-[10px] text-gray-400 uppercase tracking-wider">No.
                                        Turno</label>
                                    <input type="text" name="numero_oficio" id="numero_oficio"
                                        value="{{ $siguienteConsecutivo }}" readonly
                                        class="block mt-1 w-full rounded border-gray-300 text-sm bg-gray-100 text-gray-500 cursor-not-allowed focus:ring-0 focus:border-gray-300"
                                        placeholder="Generando consecutivo..." required>
                                </div>

                                <div>
                                    <label for="numero_oficio_dependencia"
                                        class="block font-bold text-[10px] text-gray-400 uppercase tracking-wider">No.
                                        Oficio de la Dependencia</label>
                                    <input type="text" name="numero_oficio_dependencia" id="numero_oficio_dependencia"
                                        value="{{ old('numero_oficio_dependencia') }}"
                                        class="block mt-1 w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa focus:border-guinda-ceaa"
                                        placeholder="DOP-2026-XYZ" required>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label for="prioridad"
                                            class="block font-bold text-[10px] text-gray-400 uppercase">Prioridad</label>
                                        <select name="prioridad" id="prioridad"
                                            class="block mt-1 w-full rounded border-gray-300 text-xs font-bold focus:ring-guinda-ceaa"
                                            required>
                                            <option value="Normal">NORMAL</option>
                                            <option value="Urgente">URGENTE</option>
                                            <option value="Baja">BAJA</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="tipo_correspondencia"
                                            class="block font-bold text-[10px] text-gray-400 uppercase">Tipo</label>
                                        <select name="tipo_correspondencia" id="tipo_correspondencia"
                                            class="block mt-1 w-full rounded border-gray-300 text-xs font-bold focus:ring-guinda-ceaa"
                                            required>
                                            <option value="Externa">EXTERNA</option>
                                            <option value="Interna">INTERNA</option>
                                            @if(in_array(Auth::user()->role, ['admin', 'correspondencia']))
                                                <option value="Correo Electronico">CORREO ELECTRONICO</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- BLOQUE 2: ORIGEN Y FECHAS --}}
                            <div class="space-y-4">
                                <h4
                                    class="text-[10px] font-black text-guinda-ceaa uppercase tracking-widest border-b border-red-50 mb-2">
                                    2. Origen y Plazos</h4>

                                <div>
                                    <label for="remitente"
                                        class="block font-bold text-[10px] text-gray-400 uppercase tracking-wider">Remitente
                                        / Dependencia</label>
                                    <input type="text" name="remitente" id="remitente" value="{{ old('remitente') }}"
                                        class="block mt-1 w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa focus:border-guinda-ceaa"
                                        required>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label for="municipio"
                                            class="block font-bold text-[10px] text-gray-400 uppercase tracking-wider">Municipio</label>
                                        <input type="text" name="municipio" id="municipio"
                                            value="{{ old('municipio') }}"
                                            class="block mt-1 w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa focus:border-guinda-ceaa"
                                            required>
                                    </div>
                                    <div>
                                        <label for="localidad"
                                            class="block font-bold text-[10px] text-gray-400 uppercase tracking-wider">Localidad</label>
                                        <input type="text" name="localidad" id="localidad"
                                            value="{{ old('localidad') }}"
                                            class="block mt-1 w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa focus:border-guinda-ceaa"
                                            required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label for="fecha_recepcion"
                                            class="block font-bold text-[10px] text-gray-400 uppercase tracking-wider">F.
                                            Recepción</label>
                                        <input type="date" name="fecha_recepcion" id="fecha_recepcion"
                                            value="{{ old('fecha_recepcion', date('Y-m-d')) }}"
                                            class="block mt-1 w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa"
                                            required>
                                    </div>
                                    <div>
                                        <label for="fecha_limite"
                                            class="block font-bold text-[10px] text-gray-400 uppercase tracking-wider">F.
                                            Límite</label>
                                        <input type="date" name="fecha_limite" id="fecha_limite"
                                            value="{{ old('fecha_limite') }}"
                                            class="block mt-1 w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa">
                                    </div>
                                </div>
                            </div>

                            {{-- BLOQUE 3: ARCHIVO DIGITAL (PDF) --}}
                            <div class="space-y-4">
                                <h4
                                    class="text-[10px] font-black text-guinda-ceaa uppercase tracking-widest border-b border-red-50 mb-2">
                                    3. Digitalización</h4>

                                <div class="mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-gray-300 border-dashed rounded-lg bg-gray-50 hover:bg-red-50 transition-colors cursor-pointer relative group"
                                    onclick="document.getElementById('archivo_pdf').click()">
                                    <div class="space-y-2 text-center">
                                        <svg id="pdf-icon"
                                            class="mx-auto h-12 w-12 text-gray-400 group-hover:text-guinda-ceaa transition-colors"
                                            stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="text-sm text-gray-600">
                                            <span
                                                class="font-black text-guinda-ceaa uppercase text-[10px] tracking-widest">Seleccionar
                                                Oficio PDF</span>
                                            <input id="archivo_pdf" name="archivo_pdf" type="file" class="hidden"
                                                accept=".pdf" required onchange="handleFileSelect(this)">
                                        </div>
                                        <p id="file-name-hint"
                                            class="text-[10px] text-gray-400 uppercase font-bold mt-2">Ningún archivo
                                            cargado</p>
                                    </div>
                                </div>
                                <p class="text-[9px] text-gray-400 italic text-center leading-tight">Asegúrese de que el
                                    escaneo sea legible antes de subirlo.</p>
                            </div>

                        </div>

                        {{-- SECCIÓN INFERIOR: ASUNTO --}}
                        <div class="mt-8 space-y-6">
                            <div>
                                <label for="asunto"
                                    class="block font-bold text-[10px] text-gray-400 uppercase tracking-widest">Asunto
                                    del Documento</label>
                                <textarea name="asunto" id="asunto" rows="3"
                                    class="block mt-1 w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa focus:border-guinda-ceaa"
                                    placeholder="Describa brevemente de qué trata el oficio..."
                                    required>{{ old('asunto') }}</textarea>
                            </div>

                            <div>
                                <label for="observaciones"
                                    class="block font-bold text-[10px] text-gray-400 uppercase tracking-widest">Observaciones
                                    Adicionales (Opcional)</label>
                                <textarea name="observaciones" id="observaciones" rows="2"
                                    class="block mt-1 w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa focus:border-guinda-ceaa bg-gray-50 italic">{{ old('observaciones') }}</textarea>
                            </div>
                        </div>

                        {{-- BOTONES DE ACCIÓN (AJUSTADOS) --}}
                        <div class="mt-10 flex justify-end items-center space-x-6 border-t pt-8">
                            <a href="{{ route('principal') }}"
                                class="text-xs font-black text-gray-400 uppercase hover:text-guinda-ceaa transition-colors tracking-widest">
                                Cancelar Registro
                            </a>
                            <button type="submit"
                                class="px-8 py-3 bg-guinda-ceaa text-white rounded shadow-lg hover:bg-guinda-ceaa-hover transition-all transform active:scale-95 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                <span class="text-xs font-black uppercase tracking-widest">Registrar y Turnar</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script de Interacción para el Archivo --}}
    <script>
        function handleFileSelect(input) {
            const hint = document.getElementById('file-name-hint');
            const icon = document.getElementById('pdf-icon');

            if (input.files && input.files[0]) {
                hint.innerText = "✓ " + input.files[0].name;
                hint.classList.remove('text-gray-400');
                hint.classList.add('text-green-600');
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-guinda-ceaa');
            } else {
                hint.innerText = "Ningún archivo cargado";
                hint.classList.remove('text-green-600');
                hint.classList.add('text-gray-400');
                icon.classList.add('text-gray-400');
                icon.classList.remove('text-guinda-ceaa');
            }
        }
    </script>
</x-app-layout>