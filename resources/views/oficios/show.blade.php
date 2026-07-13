<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-tight">
                {{ __('Expediente de Oficio') }}: <span class="text-guinda-ceaa">{{ $oficio->numero_oficio }}</span>
            </h2>
            <div class="flex space-x-3">
                {{-- Botón para regresar a la Bandeja de Dirección y Seguimiento --}}
                @if(in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']))
                    <a href="{{ route('oficios.gestion') }}"
                        class="inline-flex items-center px-4 py-2 bg-gris-oscuro hover:bg-guinda-ceaa border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest shadow-md transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                        </svg>
                        Bandeja de Dirección y Seguimiento
                    </a>
                @endif
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

    <div class="py-12 bg-gray-50" x-data="{ showCancelTurnModal: false, cancelTurnPivoteId: null, cancelAreaName: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- BANNER DE CANCELACIÓN --}}
            @if($oficio->estatus == 'Cancelado')
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm flex items-start gap-3 border border-red-100">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-red-800 uppercase tracking-widest">Oficio Cancelado</h3>
                        <p class="mt-1 text-xs text-red-700 font-bold uppercase text-[10px]">Motivo de Cancelación:</p>
                        <p class="mt-0.5 text-sm text-red-700 italic">"{{ $oficio->motivo_cancelacion ?? 'No se especificó motivo.' }}"</p>
                    </div>
                </div>
            @endif

            {{-- INFORMACIÓN PRINCIPAL --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-guinda-ceaa">
                <div class="p-6 text-gray-900 space-y-6">
                    <h3 class="text-sm font-black text-guinda-ceaa uppercase tracking-widest mb-4 border-b pb-2">
                        Información de Recepción</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Remitente</p>
                            <p class="font-bold text-gray-800 text-base">{{ $oficio->remitente }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">No. Oficio Dependencia/Area</p>
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
                                class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $oficio->prioridad == 'Urgente' ? 'bg-red-100 text-red-700' : 'bg-gris-claro/20 text-gris-oscuro' }}">
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

            @php
                $turnosOperativos = $turnosParaMostrar->filter(function($area) {
                    $user = Auth::user();
                    if (!in_array($user->role, ['admin', 'correspondencia', 'recepcionista'])) {
                        return $area->id == $user->area_id;
                    }
                    return \App\Models\SubareaOficio::where('area_oficio_id', $area->pivot->id)
                        ->where('user_id', $user->id)
                        ->exists();
                });

                $hasMyAssignments = false;
                foreach ($turnosOperativos as $t) {
                    $checkQuery = \App\Models\SubareaOficio::where('area_oficio_id', $t->pivot->id);
                    if (Auth::user()->role === 'subdirector' || (Auth::user()->role === 'admin' && Auth::user()->subarea_id !== null)) {
                        $checkQuery->where('subarea_id', Auth::user()->subarea_id);
                    } else {
                        $checkQuery->where('user_id', Auth::id());
                    }
                    if ($checkQuery->exists()) {
                        $hasMyAssignments = true;
                        break;
                    }
                }
            @endphp

            @if($mode == 'operativo' || $hasMyAssignments)
                {{-- MODO OPERATIVO --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-8 border-l-8 border-dorado-ocre">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-dorado-ocre mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter">Instrucción para su Atención
                        </h3>
                    </div>
                    @foreach($turnosOperativos as $area)
                        @php
                            $mySubareaOficiosQuery = \App\Models\SubareaOficio::where('area_oficio_id', $area->pivot->id);
                            if (Auth::user()->role === 'subdirector' || (Auth::user()->role === 'admin' && Auth::user()->subarea_id !== null)) {
                                $mySubareaOficiosQuery->where('subarea_id', Auth::user()->subarea_id);
                            } else {
                                $mySubareaOficiosQuery->where('user_id', Auth::id());
                            }
                            $mySubareaOficios = $mySubareaOficiosQuery->get();
                        @endphp

                        @if($mySubareaOficios->isNotEmpty())
                            @foreach($mySubareaOficios as $mySubareaOficio)
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-dorado-ocre uppercase tracking-wider mb-1">{{ $mySubareaOficio->subarea ? $mySubareaOficio->subarea->name : 'Director (Jefe de Área)' }}</p>
                                        <p class="text-2xl font-bold text-gray-800 leading-snug">"{{ $mySubareaOficio->instruccion ?? $area->pivot->instruccion }}"</p>
                                    </div>

                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap mt-2 sm:mt-0">
                                        {{-- Botón Confirmar Notificado para Operativos --}}
                                        @if($mySubareaOficio->estatus == 'Asignado')
                                            <form action="{{ route('oficios.notificarTurno', $area->pivot->id) }}" method="POST" class="inline-block">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="subarea_oficio_id" value="{{ $mySubareaOficio->id }}">
                                                <button type="submit"
                                                    class="bg-dorado-ocre hover:bg-guinda-ceaa text-white px-5 py-2 rounded-lg text-xs font-black uppercase shadow-md transition transform hover:scale-102">
                                                    Confirmar Notificado
                                                </button>
                                            </form>
                                        @elseif($mySubareaOficio->estatus == 'Notificado')
                                            <div class="flex items-center gap-3 flex-wrap">
                                                <span
                                                    class="bg-dorado-ocre/10 text-dorado-ocre px-3 py-1 rounded-full text-xs font-black uppercase flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Notificado en el Sistema
                                                </span>
                                                <a href="{{ route('oficios.atender', [$area->pivot->id, 'subarea_oficio_id' => $mySubareaOficio->id]) }}"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-black uppercase shadow-md transition transform hover:scale-102">
                                                    Atender
                                                </a>
                                                @if($area->pivot->folio_interno)
                                                    <a href="{{ route('oficios.generar', [$oficio->id, 'area_id' => $area->id, 'subarea_oficio_id' => $mySubareaOficio->id]) }}"
                                                        target="_blank"
                                                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-black uppercase shadow-md transition transform hover:scale-102 flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                        </svg>
                                                        Imprimir
                                                    </a>
                                                @endif
                                            </div>
                                        @elseif($mySubareaOficio->estatus == 'Solventado')
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-black uppercase flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Atendido / Solventado
                                                </span>
                                                @if($area->pivot->folio_interno)
                                                    <a href="{{ route('oficios.generar', [$oficio->id, 'area_id' => $area->id, 'subarea_oficio_id' => $mySubareaOficio->id]) }}"
                                                        target="_blank"
                                                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-black uppercase shadow-md transition transform hover:scale-102 flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                        </svg>
                                                        Imprimir
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Formulario de delegación para el Subdirector dentro de su subdirección --}}
                                        @if((Auth::user()->role === 'subdirector' || (Auth::user()->role === 'admin' && Auth::user()->subarea_id !== null)) && $mySubareaOficio->estatus !== 'Solventado' && $area->pivot->estatus !== 'Cancelado')
                                            <form action="{{ route('oficios.asignarSubarea', $mySubareaOficio->id) }}" method="POST" class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-sm">
                                                @csrf @method('PUT')
                                                <select name="user_id" class="text-xs rounded border-gray-300 focus:ring-gris-oscuro py-1.5 disabled:bg-gray-150 disabled:text-gray-500" required {{ $mySubareaOficio->user_id ? 'disabled' : '' }}>
                                                    <option value="">Delegar a personal...</option>
                                                    @foreach($personalPorSubarea[$mySubareaOficio->id] ?? [] as $persona)
                                                        <option value="{{ $persona->id }}" {{ $mySubareaOficio->user_id == $persona->id ? 'selected' : '' }}>
                                                            {{ $persona->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if(!$mySubareaOficio->user_id)
                                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-xs font-black uppercase shadow-sm transition">
                                                        Asignar
                                                    </button>
                                                @else
                                                    <span class="text-green-700 font-bold text-[10px] uppercase bg-green-50 px-2 py-1.5 rounded-lg border border-green-200">Asignado</span>
                                                @endif
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <p class="text-2xl font-bold text-gray-800 leading-snug">"{{ $area->pivot->instruccion }}"</p>

                                @php
                                    $isSubdirectorCheck = (
                                        Auth::user()->role === 'subdirector' || 
                                        (Auth::user()->role === 'admin' && Auth::user()->subarea_id !== null && $area->pivot->user_id == Auth::user()->id)
                                    );
                                @endphp

                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 flex-wrap mt-2 sm:mt-0">
                                    {{-- Botón Confirmar Notificado para Operativos --}}
                                    @if($area->pivot->estatus == 'Asignado')
                                        <form action="{{ route('oficios.notificarTurno', $area->pivot->id) }}" method="POST" class="inline-block">
                                            @csrf @method('PUT')
                                            <button type="submit"
                                                class="bg-dorado-ocre hover:bg-guinda-ceaa text-white px-5 py-2 rounded-lg text-xs font-black uppercase shadow-md transition transform hover:scale-102">
                                                Confirmar Notificado
                                            </button>
                                        </form>
                                    @elseif($area->pivot->estatus == 'Notificado')
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="bg-dorado-ocre/10 text-dorado-ocre px-3 py-1 rounded-full text-xs font-black uppercase flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Notificado en el Sistema
                                            </span>
                                            <a href="{{ route('oficios.atender', $area->pivot->id) }}"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-black uppercase shadow-md transition transform hover:scale-102">
                                                Atender
                                            </a>
                                        </div>
                                    @elseif($area->pivot->estatus == 'Solventado')
                                        <span
                                            class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-black uppercase flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Atendido / Solventado
                                        </span>
                                    @endif

                                    {{-- Formulario de delegación para el Subdirector --}}
                                    @if($isSubdirectorCheck && $area->pivot->estatus !== 'Solventado' && $area->pivot->estatus !== 'Cancelado')
                                        <form action="{{ route('oficios.asignar', $oficio) }}" method="POST" class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border border-gray-200 shadow-sm">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="pivote_id" value="{{ $area->pivot->id }}">
                                            <select name="user_id" class="text-xs rounded border-gray-300 focus:ring-gris-oscuro py-1.5 disabled:bg-gray-150 disabled:text-gray-500" required {{ $area->pivot->user_id ? 'disabled' : '' }}>
                                                <option value="">Asignar a personal...</option>
                                                @foreach($personalPorArea[$area->id] ?? [] as $persona)
                                                    <option value="{{ $persona->id }}" {{ $area->pivot->user_id == $persona->id ? 'selected' : '' }}>
                                                        {{ $persona->name }}
                                                        @if($persona->subarea)
                                                            - {{ $persona->subarea->name }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if(!$area->pivot->user_id)
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-xs font-black uppercase shadow-sm transition">
                                                    Asignar
                                                </button>
                                            @else
                                                <span class="text-green-700 font-bold text-[10px] uppercase bg-green-50 px-2 py-1.5 rounded-lg border border-green-200">Asignado</span>
                                            @endif
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($mode == 'recepcion' || $mode == 'gestion')
                {{-- SEGUIMIENTO DE TURNOS --}}
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-sm font-black text-gris-oscuro uppercase tracking-widest mb-6 border-b pb-2">Seguimiento de Turnos y Áreas</h3>
                    <div class="space-y-4">
                        @forelse($turnosParaMostrar as $area)
                            @php
                                $hasSubareas = \App\Models\Subarea::where('area_id', $area->id)->exists();
                                $subareasDisponibles = $subareasDisponiblesPorArea[$area->pivot->id] ?? collect();
                                $subareaAssignments = $subareaOficiosPorArea[$area->pivot->id] ?? collect();
                            @endphp

                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 hover:border-gris-claro/30 transition shadow-sm">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2 flex-wrap gap-y-1">
                                            <p class="text-lg font-black text-gris-oscuro uppercase">{{ $area->name }}</p>
                                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider
                                                {{ $area->pivot->estatus == 'Turnado' ? 'bg-orange-100 text-orange-700' : '' }}
                                                {{ $area->pivot->estatus == 'Recibido' ? 'bg-gris-claro/20 text-gris-oscuro' : '' }}
                                                {{ $area->pivot->estatus == 'Asignado' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                {{ $area->pivot->estatus == 'Notificado' ? 'bg-dorado-ocre/10 text-dorado-ocre' : '' }}
                                                {{ $area->pivot->estatus == 'Solventado' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $area->pivot->estatus == 'Cancelado' ? 'bg-red-100 text-red-700' : '' }}
                                            ">
                                                {{ $area->pivot->estatus }}
                                            </span>
                                            @if(in_array(Auth::user()->role, ['admin', 'correspondencia']) && $area->pivot->estatus !== 'Cancelado')
                                                <button type="button" 
                                                    @click="showCancelTurnModal = true; cancelTurnPivoteId = {{ $area->pivot->id }}; cancelAreaName = '{{ $area->name }}'"
                                                    class="text-[9px] bg-red-600 hover:bg-red-700 text-white px-2 py-0.5 rounded font-bold uppercase transition">
                                                    Cancelar
                                                </button>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-600"><span class="font-bold uppercase text-gray-400">Instrucción:</span> {{ $area->pivot->instruccion }}</p>
                                        
                                        @if($area->pivot->estatus == 'Cancelado')
                                            <p class="text-xs text-red-700 mt-1 font-medium bg-red-50 p-2 rounded border border-red-100 max-w-xl">
                                                <span class="font-bold uppercase text-[10px]">Motivo de Cancelación:</span> "{{ $area->pivot->motivo_cancelacion ?? 'No se especificó motivo.' }}"
                                            </p>
                                        @endif
                                        
                                        @if($area->pivot->folio_interno)
                                            <p class="text-xs text-gray-600 mt-1"><span class="font-bold uppercase text-gray-400">Folio Interno:</span> <span class="font-bold text-gris-oscuro">{{ $area->pivot->folio_interno }}</span></p>
                                        @endif

                                        @if($subareaAssignments->isNotEmpty())
                                            {{-- Renderizar asignaciones de subdirección o personal directo en modo gestion --}}
                                            <div class="mt-4 space-y-2 pl-4 border-l-2 border-gray-200">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Destinatarios Asignados:</p>
                                                @foreach($subareaAssignments as $subareaOficio)
                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white p-2 rounded border border-gray-150 text-xs shadow-sm gap-2">
                                                        <div>
                                                            <span class="font-bold text-gray-800">
                                                                @if($subareaOficio->subarea)
                                                                    {{ $subareaOficio->subarea->name }}
                                                                @elseif($subareaOficio->user && $subareaOficio->user->role === 'jefe_area')
                                                                    Director (Jefe de Área)
                                                                @else
                                                                    Atención Directa: {{ $subareaOficio->user->name ?? 'N/A' }}
                                                                @endif
                                                            </span>
                                                            @if($subareaOficio->subarea)
                                                                @if($subareaOficio->user)
                                                                    <span class="text-gray-500 font-medium"> (Responsable: {{ $subareaOficio->user->name }})</span>
                                                                @else
                                                                    <span class="text-gray-400 italic"> (Sin delegar)</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                                                {{ $subareaOficio->estatus == 'Asignado' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                                {{ $subareaOficio->estatus == 'Notificado' ? 'bg-orange-100 text-orange-700' : '' }}
                                                                {{ $subareaOficio->estatus == 'Solventado' ? 'bg-green-100 text-green-700' : '' }}
                                                            ">
                                                                {{ $subareaOficio->estatus }}
                                                            </span>
                                                            @if($area->pivot->folio_interno)
                                                                <a href="{{ route('oficios.generar', [$oficio->id, 'area_id' => $area->id, 'subarea_oficio_id' => $subareaOficio->id]) }}"
                                                                    target="_blank"
                                                                    title="Imprimir Turno Destinatario"
                                                                    class="text-gray-400 hover:text-gris-oscuro transition">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                                    </svg>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-600 mt-1"><span class="font-bold uppercase text-gray-400">Responsable:</span> <span class="font-bold text-gray-800">{{ \App\Models\User::find($area->pivot->user_id)->name ?? 'PENDIENTE' }}</span></p>
                                            @if($area->pivot->user_id)
                                                <div class="mt-1">
                                                    <a href="{{ route('oficios.generar', [$oficio->id, 'area_id' => $area->id]) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center text-[10px] font-bold text-gris-oscuro hover:underline bg-gris-claro/10 px-2.5 py-1 rounded-md">
                                                        <svg class="w-3.5 h-3.5 mr-1 text-gris-oscuro" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                        </svg>
                                                        Imprimir Turno
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="flex flex-col md:flex-row items-end md:items-center gap-3 mt-4 md:mt-0">
                                        {{-- Botón Confirmar Recibido --}}
                                        @if($area->pivot->estatus == 'Turnado' && (Auth::user()->role == 'admin' || (in_array(Auth::user()->role, ['jefe_area', 'secretaria_area']) && Auth::user()->area_id == $area->id)))
                                            <form action="{{ route('oficios.recibirTurno', $area->pivot->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-[10px] font-black uppercase shadow-sm transition">
                                                    Confirmar Recibido
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Formulario para asignar a subdirecciones, Director o Personal Directo --}}
                                        @if((Auth::user()->role == 'admin' || (in_array(Auth::user()->role, ['jefe_area', 'secretaria_area']) && Auth::user()->area_id == $area->id)) && $area->pivot->estatus !== 'Turnado' && $area->pivot->estatus !== 'Cancelado')
                                            @php
                                                $isDirectorAssigned = $subareaAssignments->contains(fn($sa) => is_null($sa->subarea_id) && $sa->user && $sa->user->role === 'jefe_area');
                                                $directosDisponibles = $personalDirectoDisponiblesPorArea[$area->pivot->id] ?? collect();
                                            @endphp
                                            @if(count($subareasDisponibles) > 0 || !$isDirectorAssigned || count($directosDisponibles) > 0)
                                                <form action="{{ route('oficios.asignar', $oficio) }}" method="POST" class="flex flex-col gap-3 bg-white p-4 rounded-xl border border-gray-200 shadow-md w-full max-w-sm">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="pivote_id" value="{{ $area->pivot->id }}">
                                                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-wider border-b pb-1">Asignar Destinatarios:</p>
                                                    <div class="flex flex-col gap-2 max-h-48 overflow-y-auto pr-1">
                                                        @if(!$isDirectorAssigned)
                                                            <div class="p-2.5 rounded-lg border border-gray-150 bg-white shadow-sm flex flex-col gap-2">
                                                                <label class="flex items-center cursor-pointer">
                                                                    <input type="checkbox" name="subarea_ids[]" value="director" class="rounded border-gray-300 text-dorado-ocre focus:ring-dorado-ocre mr-2.5 h-3.5 w-3.5">
                                                                    <div class="text-left">
                                                                        <p class="text-[11px] font-black text-gray-800">Director (Jefe de Área)</p>
                                                                        <p class="text-[9px] text-gray-400">Atención directa por parte del titular</p>
                                                                    </div>
                                                                </label>
                                                                <div>
                                                                    <label class="text-[9px] font-bold text-gray-400 uppercase block">Instrucción específica:</label>
                                                                    <select name="instruccion_director"
                                                                        class="block w-full mt-0.5 rounded border-gray-300 text-[10px] py-1 px-2 focus:ring-dorado-ocre focus:border-dorado-ocre">
                                                                        <option value="">-- Usar instrucción del Oficio --</option>
                                                                        <option value="Contestar con firma del Director" {{ $area->pivot->instruccion == 'Contestar con firma del Director' ? 'selected' : '' }}>1. Contestar con firma del Director</option>
                                                                        <option value="Atender conforme a lo especificado" {{ $area->pivot->instruccion == 'Atender conforme a lo especificado' ? 'selected' : '' }}>2. Atender conforme a lo especificado</option>
                                                                        <option value="Verificar antes de contestar oficio" {{ $area->pivot->instruccion == 'Verificar antes de contestar oficio' ? 'selected' : '' }}>3. Verificar antes de contestar oficio</option>
                                                                        <option value="Conocimiento y Efectos" {{ $area->pivot->instruccion == 'Conocimiento y Efectos' ? 'selected' : '' }}>4. Conocimiento y Efectos</option>
                                                                        <option value="Enviar a organismos Operadores" {{ $area->pivot->instruccion == 'Enviar a organismos Operadores' ? 'selected' : '' }}>5. Enviar a organismos Operadores</option>
                                                                        <option value="Asistir e Informar" {{ $area->pivot->instruccion == 'Asistir e Informar' ? 'selected' : '' }}>6. Asistir e Informar</option>
                                                                        <option value="Estudio y Opinion" {{ $area->pivot->instruccion == 'Estudio y Opinion' ? 'selected' : '' }}>7. Estudio y Opinion</option>
                                                                        <option value="Enviado de manera oficial" {{ $area->pivot->instruccion == 'Enviado de manera oficial' ? 'selected' : '' }}>8. Enviado de manera oficial</option>
                                                                        <option value="Asesoria" {{ $area->pivot->instruccion == 'Asesoria' ? 'selected' : '' }}>9. Asesoria</option>
                                                                        <option value="Informar" {{ $area->pivot->instruccion == 'Informar' ? 'selected' : '' }}>10. Informar</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @foreach($subareasDisponibles as $subarea)
                                                            <div class="p-2.5 rounded-lg border border-gray-150 bg-white shadow-sm flex flex-col gap-2">
                                                                <label class="flex items-center cursor-pointer">
                                                                    <input type="checkbox" name="subarea_ids[]" value="{{ $subarea->id }}" class="rounded border-gray-300 text-dorado-ocre focus:ring-dorado-ocre mr-2.5 h-3.5 w-3.5">
                                                                    <div class="text-left">
                                                                        <p class="text-[11px] font-black text-gray-800">{{ $subarea->name }}</p>
                                                                        <p class="text-[9px] text-gray-400">Delegar trabajo a esta subdirección</p>
                                                                    </div>
                                                                </label>
                                                                <div>
                                                                    <label class="text-[9px] font-bold text-gray-400 uppercase block">Instrucción específica:</label>
                                                                    <select name="instruccion_{{ $subarea->id }}"
                                                                        class="block w-full mt-0.5 rounded border-gray-300 text-[10px] py-1 px-2 focus:ring-dorado-ocre focus:border-dorado-ocre">
                                                                        <option value="">-- Usar instrucción del Oficio --</option>
                                                                        <option value="Contestar con firma del Director" {{ $area->pivot->instruccion == 'Contestar con firma del Director' ? 'selected' : '' }}>1. Contestar con firma del Director</option>
                                                                        <option value="Atender conforme a lo especificado" {{ $area->pivot->instruccion == 'Atender conforme a lo especificado' ? 'selected' : '' }}>2. Atender conforme a lo especificado</option>
                                                                        <option value="Verificar antes de contestar oficio" {{ $area->pivot->instruccion == 'Verificar antes de contestar oficio' ? 'selected' : '' }}>3. Verificar antes de contestar oficio</option>
                                                                        <option value="Conocimiento y Efectos" {{ $area->pivot->instruccion == 'Conocimiento y Efectos' ? 'selected' : '' }}>4. Conocimiento y Efectos</option>
                                                                        <option value="Enviar a organismos Operadores" {{ $area->pivot->instruccion == 'Enviar a organismos Operadores' ? 'selected' : '' }}>5. Enviar a organismos Operadores</option>
                                                                        <option value="Asistir e Informar" {{ $area->pivot->instruccion == 'Asistir e Informar' ? 'selected' : '' }}>6. Asistir e Informar</option>
                                                                        <option value="Estudio y Opinion" {{ $area->pivot->instruccion == 'Estudio y Opinion' ? 'selected' : '' }}>7. Estudio y Opinion</option>
                                                                        <option value="Enviado de manera oficial" {{ $area->pivot->instruccion == 'Enviado de manera oficial' ? 'selected' : '' }}>8. Enviado de manera oficial</option>
                                                                        <option value="Asesoria" {{ $area->pivot->instruccion == 'Asesoria' ? 'selected' : '' }}>9. Asesoria</option>
                                                                        <option value="Informar" {{ $area->pivot->instruccion == 'Informar' ? 'selected' : '' }}>10. Informar</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        @foreach($directosDisponibles as $directo)
                                                            <div class="p-2.5 rounded-lg border border-gray-150 bg-white shadow-sm flex flex-col gap-2">
                                                                <label class="flex items-center cursor-pointer">
                                                                    <input type="checkbox" name="subarea_ids[]" value="user_{{ $directo->id }}" class="rounded border-gray-300 text-dorado-ocre focus:ring-dorado-ocre mr-2.5 h-3.5 w-3.5">
                                                                    <div class="text-left">
                                                                        <p class="text-[11px] font-black text-gray-800">{{ $directo->name }} ({{ $directo->role === 'secretaria_area' ? 'Secretaría de Área' : 'Operativo Directo' }})</p>
                                                                        <p class="text-[9px] text-gray-400">Personal adscrito directamente a Dirección</p>
                                                                    </div>
                                                                </label>
                                                                <div>
                                                                    <label class="text-[9px] font-bold text-gray-400 uppercase block">Instrucción específica:</label>
                                                                    <select name="instruccion_user_{{ $directo->id }}"
                                                                        class="block w-full mt-0.5 rounded border-gray-300 text-[10px] py-1 px-2 focus:ring-dorado-ocre focus:border-dorado-ocre">
                                                                        <option value="">-- Usar instrucción del Oficio --</option>
                                                                        <option value="Contestar con firma del Director" {{ $area->pivot->instruccion == 'Contestar con firma del Director' ? 'selected' : '' }}>1. Contestar con firma del Director</option>
                                                                        <option value="Atender conforme a lo especificado" {{ $area->pivot->instruccion == 'Atender conforme a lo especificado' ? 'selected' : '' }}>2. Atender conforme a lo especificado</option>
                                                                        <option value="Verificar antes de contestar oficio" {{ $area->pivot->instruccion == 'Verificar antes de contestar oficio' ? 'selected' : '' }}>3. Verificar antes de contestar oficio</option>
                                                                        <option value="Conocimiento y Efectos" {{ $area->pivot->instruccion == 'Conocimiento y Efectos' ? 'selected' : '' }}>4. Conocimiento y Efectos</option>
                                                                        <option value="Enviar a organismos Operadores" {{ $area->pivot->instruccion == 'Enviar a organismos Operadores' ? 'selected' : '' }}>5. Enviar a organismos Operadores</option>
                                                                        <option value="Asistir e Informar" {{ $area->pivot->instruccion == 'Asistir e Informar' ? 'selected' : '' }}>6. Asistir e Informar</option>
                                                                        <option value="Estudio y Opinion" {{ $area->pivot->instruccion == 'Estudio y Opinion' ? 'selected' : '' }}>7. Estudio y Opinion</option>
                                                                        <option value="Enviado de manera oficial" {{ $area->pivot->instruccion == 'Enviado de manera oficial' ? 'selected' : '' }}>8. Enviado de manera oficial</option>
                                                                        <option value="Asesoria" {{ $area->pivot->instruccion == 'Asesoria' ? 'selected' : '' }}>9. Asesoria</option>
                                                                        <option value="Informar" {{ $area->pivot->instruccion == 'Informar' ? 'selected' : '' }}>10. Informar</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="submit" class="w-full bg-gris-oscuro hover:bg-guinda-ceaa text-white py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition shadow-sm hover:shadow-md">
                                                        Confirmar Asignación
                                                    </button>
                                                </form>
                                            @endif
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
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-gris-oscuro"
                        x-data="{ areas: [{id: '', instruccion: ''}] }">
                        <h3 class="text-sm font-black text-gris-oscuro uppercase tracking-widest mb-6 border-b pb-2">Turnar Oficio
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
                                            class="block mt-1 w-full rounded border-gray-300 text-xs focus:ring-gris-oscuro focus:border-gris-oscuro"
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
                                            class="block mt-1 w-full rounded border-gray-300 text-xs focus:ring-gris-oscuro focus:border-gris-oscuro"
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
                                    class="text-gris-oscuro font-bold text-xs uppercase hover:underline">+ Añadir Área</button>
                                <button type="submit"
                                    class="bg-gris-oscuro hover:bg-guinda-ceaa text-white px-8 py-2 rounded font-black uppercase text-xs shadow-lg tracking-widest transition-all transform hover:scale-105">Guardar
                                    Turnos</button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif

        </div>

        {{-- MODAL DE CANCELACIÓN DE TURNO --}}
        <div x-show="showCancelTurnModal" 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4" 
             style="display: none;" 
             x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-gray-900 bg-opacity-80 backdrop-blur-sm transition-opacity" @click="showCancelTurnModal = false"></div>

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
                            Cancelar Turnado de Área
                        </h3>
                    </div>

                    <form :action="'/oficios/turno/' + cancelTurnPivoteId + '/cancelar'" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="p-6">
                            <p class="text-sm font-bold text-gray-700 mb-3 uppercase">
                                ¿Está seguro que desea cancelar el turnado al área: <span class="text-red-600 font-black" x-text="cancelAreaName"></span>?
                            </p>
                            <div>
                                <label class="block font-bold text-[10px] text-gray-400 uppercase mb-2">Explicación del Motivo de Cancelación</label>
                                <textarea name="motivo_cancelacion" required rows="4" 
                                    placeholder="Escriba aquí el motivo detallado de la cancelación de este turno..."
                                    class="w-full text-xs rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 p-3"
                                    x-init="$watch('showCancelTurnModal', value => { if(!value) $el.value = '' })"></textarea>
                            </div>
                        </div>

                        <div class="p-5 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                            <button type="button" @click="showCancelTurnModal = false"
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

    <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-gray-800">
        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-6 border-b pb-2">
            Historial de Atención y Solventaciones
        </h3>

        <div class="space-y-6">
            @forelse($oficio->respuestas()->latest()->get() as $respuesta)
                <div class="flex gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 rounded-full bg-gris-claro/20 flex items-center justify-center font-black text-gris-oscuro">
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
                        @if($respuesta->subareaOficio)
                            <span class="inline-block px-2 py-0.5 mt-1 text-[9px] font-black uppercase bg-blue-100 text-blue-700">
                                {{ $respuesta->subareaOficio->subarea ? $respuesta->subareaOficio->subarea->name : 'Director (Jefe de Área)' }}
                            </span>
                        @endif
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