<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                    </svg>
                    Gestión de Turnos - Área
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Bandeja interna de asignaciones, confirmaciones y respuestas de correspondencia del área</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Buscador y Filtros en Bandeja de Gestión --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5">
                <form action="{{ route('oficios.gestion') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Buscar por oficio, asunto, remitente, instrucción o personal..." 
                            class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa transition">
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Buscar
                        </button>
                        @if(request()->filled('search'))
                            <a href="{{ route('oficios.gestion') }}" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla de Gestión de Turnos --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Correspondencia Asignada al Área</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $oficiosTurnados->total() }} turnos
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Página {{ $oficiosTurnados->currentPage() }} de {{ $oficiosTurnados->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">Número de Oficio</th>
                                <th class="py-3 px-4 text-left">Asunto</th>
                                <th class="py-3 px-4 text-left">Instrucción</th>
                                <th class="py-3 px-4 text-center">Estatus</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($oficiosTurnados as $oficio)
                                @php
                                    $areaTurnada = $oficio->areas->where('id', Auth::user()->area_id)->first();
                                    $pivot = $areaTurnada ? $areaTurnada->pivot : null;
                                    $hasSubareas = $areaTurnada ? \App\Models\Subarea::where('area_id', $areaTurnada->id)->exists() : false;
                                    $isSubareaAssigned = false;
                                    $subareaOficio = null;

                                    if ($pivot) {
                                        $isSubareaAssigned = \App\Models\SubareaOficio::where('area_oficio_id', $pivot->id)->exists();
                                        $subareaOficioQuery = \App\Models\SubareaOficio::where('area_oficio_id', $pivot->id);
                                        if (Auth::user()->role === 'subdirector' || (Auth::user()->role === 'admin' && Auth::user()->subarea_id !== null)) {
                                            $subareaOficioQuery->where('subarea_id', Auth::user()->subarea_id);
                                        } else {
                                            $subareaOficioQuery->where('user_id', Auth::id());
                                        }
                                        $subareaOficio = $subareaOficioQuery->first();
                                    }
                                @endphp
                                @if($pivot)
                                    <tr class="hover:bg-slate-50/75 transition duration-150">
                                        <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                            <a href="{{ route('oficios.show', [$oficio->id, 'mode' => in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']) ? 'gestion' : 'operativo']) }}" 
                                               class="hover:underline">
                                                {{ $oficio->numero_oficio }}
                                            </a>
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate" title="{{ $oficio->asunto }}">
                                            {{ $oficio->asunto }}
                                        </td>
                                        <td class="py-3.5 px-4 font-medium text-slate-700">
                                            {{ $pivot->instruccion ?: 'Sin instrucción especificada' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @php
                                                $estatusMostrar = $subareaOficio ? $subareaOficio->estatus : $pivot->estatus;
                                            @endphp
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ $estatusMostrar == 'Turnado' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                                                {{ $estatusMostrar == 'Recibido' ? 'bg-slate-100 text-slate-700 border border-slate-200' : '' }}
                                                {{ $estatusMostrar == 'Asignado' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                                {{ $estatusMostrar == 'Notificado' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                                {{ $estatusMostrar == 'Solventado' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                            ">
                                                {{ $estatusMostrar }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-1.5">
                                                {{-- Botón Asignar/Ver: Solo para directores y secretarias del área --}}
                                                @if(in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']))
                                                    {{-- Botón Confirmar Recibido --}}
                                                    @if($pivot->estatus == 'Turnado')
                                                        <form action="{{ route('oficios.recibirTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                            @csrf @method('PUT')
                                                            <button type="submit"
                                                                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                Confirmar Recibido
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($pivot->estatus !== 'Turnado')
                                                        @if(($hasSubareas && !$isSubareaAssigned) || (!$hasSubareas && !$pivot->user_id))
                                                            <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'gestion']) }}"
                                                                class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                </svg>
                                                                Asignar
                                                            </a>
                                                        @else
                                                            <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'gestion']) }}"
                                                                class="px-2.5 py-1 bg-slate-700 hover:bg-slate-800 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                Ver Seguimiento
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif

                                                @if($subareaOficio)
                                                    {{-- Acciones basadas en subarea_oficio --}}
                                                    @if($subareaOficio->estatus == 'Asignado' && (is_null($subareaOficio->user_id) || $subareaOficio->user_id == Auth::id()))
                                                        <form action="{{ route('oficios.notificarTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                            @csrf @method('PUT')
                                                            <input type="hidden" name="subarea_oficio_id" value="{{ $subareaOficio->id }}">
                                                            <button type="submit"
                                                                class="px-2.5 py-1 bg-dorado-ocre hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                Confirmar Notificado
                                                            </button>
                                                        </form>
                                                    @elseif($subareaOficio->estatus == 'Notificado')
                                                        @if(!$subareaOficio->user_id || $subareaOficio->user_id == Auth::id())
                                                            <a href="{{ route('oficios.atender', [$pivot->id, 'subarea_oficio_id' => $subareaOficio->id]) }}"
                                                                class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Atender
                                                            </a>
                                                        @endif
                                                        @if(Auth::user()->role === 'subdirector' && !$subareaOficio->user_id)
                                                            <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'operativo']) }}"
                                                                class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                </svg>
                                                                Delegar a Personal
                                                            </a>
                                                        @endif
                                                    @elseif($subareaOficio->estatus == 'Solventado')
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Atendido
                                                        </span>
                                                    @endif
                                                @else
                                                    {{-- Acciones basadas en area_oficio original --}}
                                                    @if($pivot->user_id == Auth::id())
                                                        @if($pivot->estatus == 'Asignado')
                                                            <form action="{{ route('oficios.notificarTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                                @csrf @method('PUT')
                                                                <button type="submit"
                                                                    class="px-2.5 py-1 bg-dorado-ocre hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    Confirmar Notificado
                                                                </button>
                                                            </form>
                                                        @elseif($pivot->estatus == 'Notificado')
                                                            <a href="{{ route('oficios.atender', $pivot->id) }}"
                                                                class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Atender
                                                            </a>
                                                        @elseif($pivot->estatus == 'Solventado')
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                Atendido
                                                            </span>
                                                        @endif
                                                    @elseif(!$pivot->user_id && !in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']))
                                                        <span class="text-slate-400 italic text-[10px]">Sin asignar</span>
                                                    @endif

                                                    {{-- Formulario de delegación para Subdirector --}}
                                                    @if(Auth::user()->role === 'subdirector' && $pivot->user_id == Auth::id() && !in_array($pivot->estatus, ['Solventado', 'Cancelado', 'Asignado']))
                                                        <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'operativo']) }}"
                                                            class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            Delegar a Personal
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No hay correspondencia asignada o turnada a esta área actualmente.</p>
                                            <p class="text-xs text-slate-400">Los nuevos oficios turnados por la Oficialía de Partes se mostrarán aquí.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $oficiosTurnados->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>