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
                    x-data="{ turnos: [{area_id: '', instruccion: '', custom_instruccion: ''}] }">
                    @csrf @method('PUT')

                    <template x-for="(turno, index) in turnos" :key="index">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col gap-4 mb-4 relative">
                            <div class="flex flex-col md:flex-row items-end gap-4 w-full">
                                <div class="w-full md:flex-1">
                                    <label class="block font-bold text-[10px] text-gray-400 uppercase tracking-wider mb-1">Seleccionar Dirección</label>
                                    <select name="areas[]" class="block w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa focus:border-guinda-ceaa" required>
                                        <option value="">Seleccionar Dirección</option>
                                        @foreach($areasDisponibles as $area)
                                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-full md:flex-1">
                                    <label class="block font-bold text-[10px] text-gray-400 uppercase tracking-wider mb-1">Instrucción de Turnado</label>
                                    <input type="hidden" name="instrucciones[]" :value="turno.instruccion === 'Otro' ? turno.custom_instruccion : turno.instruccion">
                                    <select x-model="turno.instruccion" class="block w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa focus:border-guinda-ceaa" required>
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
                                        <option value="Otro">Otro (Especificar)</option>
                                    </select>
                                </div>
                                <button type="button" @click="turnos.splice(index, 1)"
                                    class="bg-red-50 hover:bg-red-100 text-red-600 p-2 rounded transition shadow-sm border border-red-200 self-stretch md:self-auto flex justify-center items-center" 
                                    x-show="turnos.length > 1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            
                            <div x-show="turno.instruccion === 'Otro'" class="w-full bg-white p-3 rounded border border-gray-150 shadow-inner" x-transition>
                                <label class="text-[10px] font-bold text-gray-500 uppercase block tracking-wider mb-1">Escribe la instrucción específica:</label>
                                <input type="text" x-model="turno.custom_instruccion" 
                                    placeholder="Escribe las indicaciones aquí..."
                                    class="block w-full rounded border-gray-300 text-sm focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="turnos.push({area_id: '', instruccion: '', custom_instruccion: ''})"
                        class="text-xs font-bold text-gris-oscuro underline uppercase hover:text-guinda-ceaa transition-colors">+ Añadir otra dirección</button>

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