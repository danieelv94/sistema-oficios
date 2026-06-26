<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-xl border-t-4 border-guinda-ceaa">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-black uppercase">Turnar Oficio: {{ $oficio->numero_oficio }}</h2>
                    @if($oficio->pdf_path)
                        <a href="{{ asset('storage/' . $oficio->pdf_path) }}" target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-red-700 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-red-800 shadow-md transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Ver PDF
                        </a>
                    @endif
                </div>

                <form action="{{ route('oficios.turnar', $oficio) }}" method="POST"
                    x-data="{ turnos: [{area_id: '', instruccion: ''}] }">
                    @csrf @method('PUT')

                    <template x-for="(turno, index) in turnos" :key="index">
                        <div class="flex gap-4 mb-4">
                            <select name="areas[]" class="w-1/2 rounded border-gray-300 text-sm" required>
                                <option value="">Seleccionar Dirección</option>
                                @foreach($areasDisponibles as $area)
                                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                            <select name="instrucciones[]" class="w-1/2 rounded border-gray-300 text-sm" required>
                                <option value="">-- Seleccione Instrucción --</option>
                                <option value="Contestar con firma del Director">1. Contestar con firma del Director</option>
                                <option value="Atender conforme a lo especificado">2. Atender conforme a lo especificado</option>
                                <option value="Verificar antes de contestar oficio">3. Verificar antes de contestar oficio</option>
                                <option value="Conocimiento y Efectos">4. Conocimiento y Efectos</option>
                                <option value="Enviar a organismos Operadores">5. Enviar a organismos Operadores</option>
                                <option value="Asistir e Informar">6. Asistir e Informar</option>
                                <option value="Estudio y Opinion">7. Estudio y Opinion</option>
                                <option value="Enviado de manera oficial">8. Enviado de manera oficial</option>
                                <option value="Asesoria">9. Asesoria</option>
                                <option value="Informar">10. Informar</option>
                            </select>
                        </div>
                    </template>

                    <button type="button" @click="turnos.push({area_id: '', instruccion: ''})"
                        class="text-xs font-bold text-gris-oscuro underline uppercase">+ Añadir otra dirección</button>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                            class="bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white px-6 py-2 rounded font-black uppercase transition shadow-md">Confirmar
                            Turnado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>