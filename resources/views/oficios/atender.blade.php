<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- DETALLES DEL OFICIO ORIGINAL --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-guinda-ceaa">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-4 gap-4">
                            <div>
                                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Detalles del Turno Asignado</h3>
                                <p class="text-xs text-gray-400 font-bold uppercase mt-1">Información de procedencia y contenido del oficio</p>
                            </div>
                            @if($areaOficio->oficio->pdf_path)
                                <a href="{{ asset('storage/' . $areaOficio->oficio->pdf_path) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-md font-bold text-xs uppercase tracking-widest shadow-md transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Ver PDF Original
                                </a>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm mb-6">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">No. Turno / Oficio</p>
                                <p class="font-bold text-gray-800 text-base">{{ $areaOficio->oficio->numero_oficio }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Remitente / Dependencia</p>
                                <p class="font-bold text-gray-800">{{ $areaOficio->oficio->remitente }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">No. Oficio Dependencia</p>
                                <p class="font-bold text-guinda-ceaa font-mono">{{ $areaOficio->oficio->numero_oficio_dependencia }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Prioridad</p>
                                <span class="inline-block mt-1 px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                    {{ $areaOficio->oficio->prioridad == 'Urgente' ? 'bg-red-100 text-red-700' : 'bg-gris-claro/20 text-gris-oscuro' }}
                                ">
                                    {{ $areaOficio->oficio->prioridad }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Municipio / Localidad</p>
                                <p class="font-bold text-gray-800">{{ $areaOficio->oficio->municipio }}, {{ $areaOficio->oficio->localidad }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Fecha Recepción / Límite</p>
                                <p class="font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($areaOficio->oficio->fecha_recepcion)->format('d/m/Y') }}
                                    @if($areaOficio->oficio->fecha_limite)
                                        <span class="text-red-600 font-bold"> (Límite: {{ \Carbon\Carbon::parse($areaOficio->oficio->fecha_limite)->format('d/m/Y') }})</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="border-t pt-4 mb-6">
                            <p class="text-xs font-bold text-dorado-ocre uppercase mb-1">Instrucción de Dirección</p>
                            <div class="p-4 bg-arena-claro/10 border border-dorado-ocre/20 rounded-lg">
                                <p class="text-xl font-black text-gray-800 leading-snug">"{{ $areaOficio->instruccion }}"</p>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-xs font-bold text-gray-400 uppercase mb-1">Asunto</p>
                            <div class="p-4 bg-gray-50 border border-gray-100 rounded text-gray-700 leading-relaxed italic whitespace-pre-line text-xs font-semibold">
                                {{ $areaOficio->oficio->asunto }}
                            </div>
                        </div>

                        @if($areaOficio->oficio->observaciones)
                            <div class="mt-4 border-t pt-4">
                                <p class="text-xs font-bold text-gray-400 uppercase mb-1">Observaciones</p>
                                <p class="text-xs text-gray-600 italic bg-yellow-50/50 p-3 border border-yellow-100 rounded">{{ $areaOficio->oficio->observaciones }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- FORMULARIO DE RESPUESTA --}}
                <div class="lg:col-span-1">
                    <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-dorado-ocre sticky top-6">
                        <h3 class="text-lg font-black text-gray-800 uppercase mb-6">Registrar Respuesta</h3>

                        <form action="{{ route('oficios.solventar', $areaOficio->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Tipo de Respuesta</label>
                                <select name="tipo_respuesta" class="w-full rounded border-gray-300 text-sm focus:ring-dorado-ocre focus:border-dorado-ocre">
                                    <option value="Conocimiento">Solo Conocimiento</option>
                                    <option value="Solventacion">Solventación / Respuesta Formal</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Mensaje de Acción</label>
                                <textarea name="mensaje" class="w-full rounded border-gray-300 text-sm focus:ring-dorado-ocre focus:border-dorado-ocre" rows="4"
                                    placeholder="Detalle las acciones realizadas o los comentarios referentes al turno..." required></textarea>
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Adjuntar Evidencia (PDF)</label>
                                <div class="mt-1 flex justify-center px-4 pt-8 pb-8 border-2 border-gray-300 border-dashed rounded-lg bg-gray-50 hover:bg-arena-claro/20 transition-colors cursor-pointer relative group"
                                    onclick="document.getElementById('archivo_evidencia').click()">
                                    <div class="space-y-2 text-center">
                                        <svg id="pdf-icon"
                                            class="mx-auto h-10 w-10 text-gray-400 group-hover:text-dorado-ocre transition-colors"
                                            stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="text-sm text-gray-600">
                                            <span class="font-black text-dorado-ocre uppercase text-[10px] tracking-widest">Seleccionar PDF</span>
                                            <input id="archivo_evidencia" name="archivo_evidencia" type="file" class="hidden"
                                                accept=".pdf" onchange="handleFileSelect(this)">
                                        </div>
                                        <p id="file-name-hint" class="text-[9px] text-gray-400 uppercase font-bold mt-1">Ningún archivo cargado</p>
                                    </div>
                                </div>
                                <p class="text-[8px] text-gray-400 italic text-center leading-tight mt-2">Opcional para solventación o conocimiento. Máximo 5MB.</p>
                            </div>

                            <div class="flex flex-col gap-3">
                                <button type="submit"
                                    class="w-full bg-dorado-ocre text-white font-black uppercase py-3 rounded shadow-md hover:bg-guinda-ceaa transition transform active:scale-95 text-xs tracking-wider">
                                    Enviar Respuesta
                                </button>
                                <a href="{{ route('oficios.gestion') }}"
                                    class="w-full text-center text-xs font-black text-gray-400 uppercase hover:text-red-600 py-2 transition-colors">
                                    Regresar a la Bandeja
                                </a>
                            </div>
                        </form>
                    </div>
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
                icon.classList.add('text-dorado-ocre');
            } else {
                hint.innerText = "Ningún archivo cargado";
                hint.classList.remove('text-green-600');
                hint.classList.add('text-gray-400');
                icon.classList.add('text-gray-400');
                icon.classList.remove('text-dorado-ocre');
            }
        }
    </script>
</x-app-layout>