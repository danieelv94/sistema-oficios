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
                @if(in_array(Auth::user()->role, ['admin', 'correspondencia', 'recepcionista']))
                    <a href="{{ route('oficios.edit', $oficio) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 shadow-md transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar Captura
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
                                {{ \Carbon\Carbon::parse($oficio->fecha_recepcion)->format('d/m/Y') }}
                            </p>
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
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <p class="text-2xl font-bold text-purple-900 leading-snug">"{{ $area->pivot->instruccion }}"</p>
                            
                            {{-- Botón Confirmar Notificado para Operativos --}}
                            @if($area->pivot->estatus == 'Asignado')
                                <form action="{{ route('oficios.notificarTurno', $area->pivot->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                        class="bg-purple-600 hover:bg-purple-800 text-white px-5 py-2 rounded-lg text-xs font-black uppercase shadow-md transition transform hover:scale-102">
                                        Confirmar Notificado
                                    </button>
                                </form>
                            @elseif($area->pivot->estatus == 'Notificado')
                                <div class="flex items-center gap-3">
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-black uppercase flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Notificado en el Sistema
                                    </span>
                                    <a href="{{ route('oficios.atender', $area->pivot->id) }}"
                                        class="bg-blue-600 hover:bg-blue-800 text-white px-5 py-2 rounded-lg text-xs font-black uppercase shadow-md transition transform hover:scale-102">
                                        Atender
                                    </a>
                                </div>
                            @elseif($area->pivot->estatus == 'Solventado')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-black uppercase flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Atendido / Solventado
                                </span>
                            @endif
                        </div>
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
                                        <div class="flex items-center space-x-3 mb-2 flex-wrap gap-y-1">
                                            <p class="text-lg font-black text-blue-800 uppercase">{{ $area->name }}</p>
                                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider
                                                {{ $area->pivot->estatus == 'Turnado' ? 'bg-orange-100 text-orange-700' : '' }}
                                                {{ $area->pivot->estatus == 'Recibido' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $area->pivot->estatus == 'Asignado' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                {{ $area->pivot->estatus == 'Notificado' ? 'bg-purple-100 text-purple-700' : '' }}
                                                {{ $area->pivot->estatus == 'Solventado' ? 'bg-green-100 text-green-700' : '' }}
                                            ">
                                                {{ $area->pivot->estatus }}
                                            </span>
                                            @if(Auth::user()->role == 'admin')
                                                <form action="{{ route('oficios.eliminarTurno', $area->pivot->id) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" onclick="return confirm('¿Eliminar este turno?')"
                                                        class="text-[9px] bg-red-600 text-white px-2 py-0.5 rounded font-bold uppercase hover:bg-red-800 transition">Eliminar</button>
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
                                        @if($area->pivot->user_id)
                                            <div class="mt-1">
                                                <a href="{{ route('oficios.generar', $oficio) }}" target="_blank"
                                                    class="inline-flex items-center text-[10px] font-bold text-blue-600 hover:underline bg-blue-50 px-2.5 py-1 rounded-md">
                                                    <svg class="w-3.5 h-3.5 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Imprimir Volante de Turno (Papel)
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col md:flex-row items-end md:items-center gap-3 mt-4 md:mt-0">
                                        {{-- Botón Confirmar Recibido --}}
                                        @if($area->pivot->estatus == 'Turnado' && (Auth::user()->role == 'admin' || (in_array(Auth::user()->role, ['jefe_area', 'secretaria_area']) && Auth::user()->area_id == $area->id)))
                                            <form action="{{ route('oficios.recibirTurno', $area->pivot->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <button type="submit"
                                                    class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded text-[10px] font-black uppercase shadow-sm transition">
                                                    Confirmar Recibido
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Formulario Asignación --}}
                                        @if((Auth::user()->role == 'admin' || (in_array(Auth::user()->role, ['jefe_area', 'secretaria_area']) && Auth::user()->area_id == $area->id)) && $area->pivot->estatus !== 'Turnado')
                                            <form action="{{ route('oficios.asignar', $oficio) }}" method="POST"
                                                class="flex items-center space-x-2 flex-wrap gap-y-1"
                                                x-data="{ isEditing: {{ $area->pivot->user_id ? 'false' : 'true' }} }">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="pivote_id" value="{{ $area->pivot->id }}">
                                                <select name="user_id" 
                                                    class="text-xs rounded border-gray-300 focus:ring-[#932C43] disabled:bg-gray-100 disabled:text-gray-500"
                                                    :disabled="!isEditing"
                                                    required>
                                                    <option value="">Asignar a...</option>
                                                    @foreach($personalPorArea[$area->id] as $persona)
                                                        <option value="{{ $persona->id }}" {{ $area->pivot->user_id == $persona->id ? 'selected' : '' }}>{{ $persona->name }}</option>
                                                    @endforeach
                                                </select>
                                                
                                                {{-- Botón Asignar (solo visible cuando está editando) --}}
                                                <button type="submit" x-show="isEditing"
                                                    class="bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1.5 rounded text-[10px] font-black uppercase transition shadow-sm">
                                                    Asignar
                                                </button>
                                                
                                                {{-- Botón Cancelar (solo visible cuando está editando y ya tiene un usuario asignado) --}}
                                                @if($area->pivot->user_id)
                                                    <button type="button" x-show="isEditing" @click="isEditing = false"
                                                        class="bg-gray-400 hover:bg-gray-500 text-white px-2 py-1.5 rounded text-[10px] font-black uppercase transition shadow-sm">
                                                        Cancelar
                                                    </button>
                                                @endif
                                                
                                                {{-- Botón Reasignar (solo visible cuando no está editando) --}}
                                                <button type="button" x-show="!isEditing" @click="isEditing = true"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-[10px] font-black uppercase transition shadow-sm">
                                                    Reasignar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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

    <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-gray-800">
        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-6 border-b pb-2">
            Historial de Atención y Solventaciones
        </h3>

        <div class="space-y-6">
            @forelse($oficio->respuestas()->latest()->get() as $respuesta)
                <div class="flex gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center font-black text-blue-700">
                            {{ substr($respuesta->user->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between">
                            <p class="text-xs font-black uppercase text-gray-800">{{ $respuesta->user->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $respuesta->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span
                            class="inline-block px-2 py-0.5 mt-1 text-[9px] font-black uppercase {{ $respuesta->tipo_respuesta == 'Solventacion' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                            {{ $respuesta->tipo_respuesta }}
                        </span>
                        <p class="mt-2 text-sm text-gray-700 italic">"{{ $respuesta->mensaje }}"</p>

                        @if($respuesta->archivo_evidencia)
                            <a href="{{ asset('storage/' . $respuesta->archivo_evidencia) }}" target="_blank"
                                class="mt-3 inline-flex items-center text-[10px] font-bold text-red-600 hover:underline">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Ver archivo de evidencia
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 italic text-xs">Aún no hay acciones registradas en este oficio.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>