<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-purple-600">
                <h3 class="text-lg font-black text-gray-800 uppercase mb-6">Atender Oficio:
                    {{ $areaOficio->oficio->numero_oficio }}</h3>

                <form action="{{ route('oficios.solventar', $areaOficio->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-400 uppercase">Tipo de Respuesta</label>
                        <select name="tipo_respuesta" class="w-full rounded border-gray-300 text-sm">
                            <option value="Conocimiento">Solo Conocimiento</option>
                            <option value="Solventacion">Solventación / Respuesta Formal</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-400 uppercase">Mensaje de Acción</label>
                        <textarea name="mensaje" class="w-full rounded border-gray-300 text-sm" rows="4"
                            required></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Adjuntar Evidencia (PDF)</label>
                        <div class="mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-gray-300 border-dashed rounded-lg bg-gray-50 hover:bg-purple-50 transition-colors cursor-pointer relative group"
                            onclick="document.getElementById('archivo_evidencia').click()">
                            <div class="space-y-2 text-center">
                                <svg id="pdf-icon"
                                    class="mx-auto h-12 w-12 text-gray-400 group-hover:text-purple-600 transition-colors"
                                    stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="text-sm text-gray-600">
                                    <span
                                        class="font-black text-purple-600 uppercase text-[10px] tracking-widest">Seleccionar
                                        PDF de Evidencia</span>
                                    <input id="archivo_evidencia" name="archivo_evidencia" type="file" class="hidden"
                                        accept=".pdf" onchange="handleFileSelect(this)">
                                </div>
                                <p id="file-name-hint"
                                    class="text-[10px] text-gray-400 uppercase font-bold mt-2">Ningún archivo cargado</p>
                            </div>
                        </div>
                        <p class="text-[9px] text-gray-400 italic text-center leading-tight mt-2.5">Suba un archivo PDF legible (máximo 5MB). Opcional para solventación o conocimiento.</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-purple-600 text-white font-black uppercase py-3 rounded shadow-md hover:bg-purple-800 transition transform active:scale-95">
                        Enviar Respuesta
                    </button>
                </form>
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
                icon.classList.add('text-purple-600');
            } else {
                hint.innerText = "Ningún archivo cargado";
                hint.classList.remove('text-green-600');
                hint.classList.add('text-gray-400');
                icon.classList.add('text-gray-400');
                icon.classList.remove('text-purple-600');
            }
        }
    </script>
</x-app-layout>