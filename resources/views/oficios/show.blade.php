<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Expediente de Oficio') }}: <span class="text-[#932C43]">{{ $oficio->numero_oficio }}</span>
            </h2>
            <div class="flex space-x-3">
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
                <a href="{{ route('oficios.generar', $oficio) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 shadow-md transition">
                    Imprimir Turno
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- INFORMACIÓN PRINCIPAL --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-[#932C43]">
                <div class="p-6">
                    <h3 class="text-sm font-black text-[#932C43] uppercase tracking-widest mb-4 border-b pb-2">
                        Información de Recepción</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Remitente</p>
                            <p class="font-bold text-gray-800 text-base">{{ $oficio->remitente }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">No. Oficio Dependencia</p>
                            <p class="font-bold text-gray-800">{{ $oficio->numero_oficio_dependencia }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Municipio / Localidad</p>
                            <p class="font-bold text-gray-800">{{ $oficio->municipio }}, {{ $oficio->localidad }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Fecha de Recepción</p>
                            <p class="font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($oficio->fecha_recepcion)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Prioridad</p>
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $oficio->prioridad == 'Urgente' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $oficio->prioridad }}
                            </span>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-xs font-bold text-gray-400 uppercase mb-1">Asunto</p>
                            <div
                                class="p-4 bg-gray-50 border border-gray-100 rounded italic text-gray-700 leading-relaxed break-words">
                                {!! nl2br(e($oficio->asunto)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($mode == 'operativo')
                {{-- MODO OPERATIVO --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-8 border-l-8 border-purple-600">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter">Instrucción para su Atención
                        </h3>
                    </div>
                    @foreach($turnosParaMostrar as $area)
                        <p class="text-2xl font-bold text-purple-900 leading-snug">"{{ $area->pivot->instruccion }}"</p>
                    @endforeach
                </div>
            @endif

            @if($mode == 'recepcion' || $mode == 'gestion')
                {{-- SEGUIMIENTO DE TURNOS --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-sm font-black text-[#932C43] uppercase tracking-widest mb-6 border-b pb-2">Seguimiento
                        de Turnos y Áreas</h3>
                    <div class="space-y-4">
                        @forelse($turnosParaMostrar as $area)
                            <div
                                class="bg-gray-50 border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition shadow-sm">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <p class="text-lg font-black text-blue-800 uppercase">{{ $area->name }}</p>
                                            @if(Auth::user()->role == 'admin')
                                                <form action="{{ route('oficios.eliminarTurno', $area->pivot->id) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" onclick="return confirm('¿Eliminar este turno?')"
                                                        class="text-[9px] bg-red-600 text-white px-2 py-0.5 rounded font-bold uppercase">Eliminar</button>
                                                </form>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-600"><span
                                                class="font-bold uppercase text-gray-400">Instrucción:</span>
                                            {{ $area->pivot->instruccion }}</p>
                                        <p class="text-xs text-gray-600 mt-1"><span
                                                class="font-bold uppercase text-gray-400">Responsable:</span> <span
                                                class="font-bold text-gray-800">{{ \App\Models\User::find($area->pivot->user_id)->name ?? 'PENDIENTE' }}</span>
                                        </p>
                                    </div>
                                    {{-- Formulario Asignación --}}
                                    @if((Auth::user()->role == 'admin' || Auth::user()->role == 'jefe_area' || Auth::user()->role == 'secretaria_area') && Auth::user()->area_id == $area->id)
                                        <form action="{{ route('oficios.asignar', $oficio) }}" method="POST"
                                            class="mt-4 md:mt-0 flex items-center space-x-2">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="pivote_id" value="{{ $area->pivot->id }}">
                                            <select name="user_id" class="text-xs rounded border-gray-300 focus:ring-[#932C43]"
                                                required>
                                                <option value="">Asignar a...</option>
                                                @foreach($personalPorArea[$area->id] as $persona)
                                                    <option value="{{ $persona->id }}" {{ $area->pivot->user_id == $persona->id ? 'selected' : '' }}>{{ $persona->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                class="bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1.5 rounded text-[10px] font-black uppercase">Asignar</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center py-6 text-gray-400 italic">No hay áreas turnadas actualmente.</p>
                        @endforelse
                    </div>
                </div>

                @if($mode == 'recepcion')
                    {{-- FORMULARIO DE TURNADO CON LISTA DESPLEGABLE --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-blue-500"
                        x-data="{ areas: [{id: '', instruccion: ''}] }">
                        <h3 class="text-sm font-black text-blue-800 uppercase tracking-widest mb-6 border-b pb-2">Turnar Oficio
                            a Nuevas Direcciones</h3>
                        <form action="{{ route('oficios.turnar', $oficio) }}" method="POST">
                            @csrf @method('PUT')
                            <template x-for="(area, index) in areas" :key="index">
                                <div
                                    class="flex flex-col md:flex-row items-end gap-4 mb-6 bg-gray-50 p-4 rounded-lg border-l-4 border-gray-300">
                                    <div class="w-full md:w-1/3">
                                        <label class="block font-bold text-[10px] text-gray-400 uppercase">Seleccionar
                                            Dirección</label>
                                        <select name="areas[]"
                                            class="block mt-1 w-full rounded border-gray-300 text-xs focus:ring-blue-500"
                                            required>
                                            <option value="">-- Seleccionar --</option>
                                            @foreach($areasDisponibles as $areaOption)
                                                <option value="{{ $areaOption->id }}">{{ $areaOption->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-full md:flex-1">
                                        <label class="block font-bold text-[10px] text-gray-400 uppercase">Instrucción de
                                            Turnado</label>
                                        {{-- CAMBIO A SELECT CON TUS OPCIONES --}}
                                        <select name="instrucciones[]"
                                            class="block mt-1 w-full rounded border-gray-300 text-xs focus:ring-blue-500"
                                            required>
                                            <option value="">-- Seleccione Instrucción --</option>
                                            <option value="Contestar con firma del Director">1. Contestar con firma del Director
                                            </option>
                                            <option value="Atender conforme a lo especificado">2. Atender conforme a lo
                                                especificado</option>
                                            <option value="Verificar antes de contestar oficio">3. Verificar antes de contestar
                                                oficio</option>
                                            <option value="Conocimiento y Efectos">4. Conocimiento y Efectos</option>
                                            <option value="Enviar a organismos Operadores">5. Enviar a organismos Operadores
                                            </option>
                                            <option value="Asistir e Informar">6. Asistir e Informar</option>
                                            <option value="Estudio y Opinión">7. Estudio y Opinión</option>
                                            <option value="Enviado de manera oficial">8. Enviado de manera oficial</option>
                                            <option value="Asesoría">9. Asesoría</option>
                                            <option value="Informar">10. Informar</option>
                                        </select>
                                    </div>
                                    <button type="button" @click="areas.splice(index, 1)"
                                        class="bg-red-500 text-white p-2 rounded hover:bg-red-700" x-show="areas.length > 1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            <div class="flex justify-between items-center mt-6">
                                <button type="button" @click="areas.push({id: '', instruccion: ''})"
                                    class="text-blue-600 font-bold text-xs uppercase hover:underline">+ Añadir Área</button>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-800 text-white px-8 py-2 rounded font-black uppercase text-xs shadow-lg tracking-widest transition-all transform hover:scale-105">Guardar
                                    Turnos</button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>