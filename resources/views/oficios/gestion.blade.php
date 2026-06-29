<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 uppercase tracking-widest">Gestión de Turnos - Área</h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-2xl border-t-4 border-gris-oscuro transition-all">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Bandeja de Dirección y Seguimiento</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase mt-1">Gestión interna de asignaciones, confirmaciones y respuestas de correspondencia del área</p>
                        </div>
                        
                        {{-- Buscador en Bandeja de Gestión --}}
                        <form action="{{ route('oficios.gestion') }}" method="GET" class="w-full md:w-auto flex gap-2">
                            <div class="relative w-full sm:w-72">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    placeholder="Buscar por oficio, asunto, remitente, instrucción o personal..." 
                                    class="w-full text-xs rounded-lg border-gray-300 focus:ring-gris-oscuro focus:border-gris-oscuro pl-8 py-2">
                                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <button type="submit" class="bg-gris-oscuro hover:bg-guinda-ceaa text-white text-xs font-black uppercase px-4 py-2 rounded-lg transition shadow-sm">
                                Buscar
                            </button>
                            @if(request()->filled('search'))
                                <a href="{{ route('oficios.gestion') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-black uppercase px-3 py-2 rounded-lg transition text-center flex items-center justify-center">
                                    Limpiar
                                </a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full text-xs text-left">
                            <thead class="bg-arena-claro/20 text-guinda-ceaa uppercase font-black">
                                <tr>
                                    <th class="px-6 py-4 tracking-wider">Número de Oficio</th>
                                    <th class="px-6 py-4 tracking-wider">Asunto</th>
                                    <th class="px-6 py-4 tracking-wider">Instrucción</th>
                                    <th class="px-6 py-4 tracking-wider text-center">Estatus</th>
                                    <th class="px-6 py-4 tracking-wider text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($oficiosTurnados as $oficio)
                                    @php
                                        $areaTurnada = $oficio->areas->where('id', Auth::user()->area_id)->first();
                                        $pivot = $areaTurnada ? $areaTurnada->pivot : null;
                                        $hasSubareas = $areaTurnada ? \App\Models\Subarea::where('area_id', $areaTurnada->id)->exists() : false;
                                        $isSubareaAssigned = false;
                                        $subareaOficio = null;

                                        if ($pivot && $hasSubareas) {
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
                                        <tr class="hover:bg-gray-50/80 transition duration-150">
                                            <td class="px-6 py-4 font-bold text-gray-900">
                                                <a href="{{ route('oficios.show', [$oficio->id, 'mode' => in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']) ? 'gestion' : 'operativo']) }}" 
                                                   class="text-gris-oscuro hover:underline font-bold">
                                                    {{ $oficio->numero_oficio }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $oficio->asunto }}</td>
                                            <td class="px-6 py-4 font-medium text-gray-700">{{ $pivot->instruccion }}</td>
                                            <td class="px-6 py-4 text-center">
                                                @php
                                                    $estatusMostrar = ($hasSubareas && $subareaOficio) ? $subareaOficio->estatus : $pivot->estatus;
                                                @endphp
                                                <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                                    {{ $estatusMostrar == 'Turnado' ? 'bg-orange-100 text-orange-700' : '' }}
                                                    {{ $estatusMostrar == 'Recibido' ? 'bg-gris-claro/20 text-gris-oscuro' : '' }}
                                                    {{ $estatusMostrar == 'Asignado' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                    {{ $estatusMostrar == 'Notificado' ? 'bg-dorado-ocre/10 text-dorado-ocre' : '' }}
                                                    {{ $estatusMostrar == 'Solventado' ? 'bg-green-100 text-green-700' : '' }}
                                                ">
                                                    {{ $estatusMostrar }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    {{-- Botón Asignar/Ver: Solo para directores y secretarias del área --}}
                                                    @if(in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']))
                                                        {{-- Botón Confirmar Recibido --}}
                                                        @if($pivot->estatus == 'Turnado')
                                                            <form action="{{ route('oficios.recibirTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                                @csrf @method('PUT')
                                                                <button type="submit"
                                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm hover:shadow-md transition">
                                                                    Confirmar Recibido
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if($pivot->estatus !== 'Turnado')
                                                            @if(($hasSubareas && !$isSubareaAssigned) || (!$hasSubareas && !$pivot->user_id))
                                                                <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'gestion']) }}"
                                                                    class="inline-block bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm hover:shadow-md transition">
                                                                    Asignar
                                                                </a>
                                                            @else
                                                                <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'gestion']) }}"
                                                                    class="inline-block bg-slate-500 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm hover:shadow-md transition">
                                                                    Ver Seguimiento
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif

                                                    @if($hasSubareas)
                                                        {{-- Acciones basadas en subarea_oficio --}}
                                                        @if($subareaOficio)
                                                            @if($subareaOficio->estatus == 'Asignado')
                                                                <form action="{{ route('oficios.notificarTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                                    @csrf @method('PUT')
                                                                    <input type="hidden" name="subarea_oficio_id" value="{{ $subareaOficio->id }}">
                                                                    <button type="submit"
                                                                        class="bg-dorado-ocre hover:bg-guinda-ceaa text-white px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm hover:shadow-md transition">
                                                                        Confirmar Notificado
                                                                    </button>
                                                                </form>
                                                            @elseif($subareaOficio->estatus == 'Notificado')
                                                                @if(!$subareaOficio->user_id || $subareaOficio->user_id == Auth::id())
                                                                    <a href="{{ route('oficios.atender', [$pivot->id, 'subarea_oficio_id' => $subareaOficio->id]) }}"
                                                                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm hover:shadow-md transition">
                                                                        Atender
                                                                    </a>
                                                                @endif
                                                                @if(Auth::user()->role === 'subdirector' && !$subareaOficio->user_id)
                                                                    <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'operativo']) }}"
                                                                        class="inline-block bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm hover:shadow-md transition">
                                                                        Delegar a Personal
                                                                    </a>
                                                                @endif
                                                            @elseif($subareaOficio->estatus == 'Solventado')
                                                                <span class="text-green-600 font-black uppercase text-[10px] flex items-center gap-1 bg-green-50 px-2 py-0.5 rounded-full">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    Atendido
                                                                </span>
                                                            @endif
                                                        @else
                                                            @if(!in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']))
                                                                <span class="text-gray-400 italic text-[10px]">Sin asignar</span>
                                                            @endif
                                                        @endif
                                                    @else
                                                        {{-- Acciones basadas en area_oficio original --}}
                                                        @if($pivot->user_id == Auth::id())
                                                            @if($pivot->estatus == 'Asignado')
                                                                <form action="{{ route('oficios.notificarTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                                    @csrf @method('PUT')
                                                                    <button type="submit"
                                                                        class="bg-dorado-ocre hover:bg-guinda-ceaa text-white px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm hover:shadow-md transition">
                                                                        Confirmar Notificado
                                                                    </button>
                                                                </form>
                                                            @elseif($pivot->estatus == 'Notificado')
                                                                <a href="{{ route('oficios.atender', $pivot->id) }}"
                                                                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm hover:shadow-md transition">
                                                                    Atender
                                                                </a>
                                                            @elseif($pivot->estatus == 'Solventado')
                                                                <span class="text-green-600 font-black uppercase text-[10px] flex items-center gap-1 bg-green-50 px-2 py-0.5 rounded-full">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    Atendido
                                                                </span>
                                                            @endif
                                                        @elseif(!$pivot->user_id && !in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']))
                                                            <span class="text-gray-400 italic text-[10px]">Sin asignar</span>
                                                        @endif

                                                        {{-- Formulario de delegación para Subdirector --}}
                                                        @if(Auth::user()->role === 'subdirector' && $pivot->user_id == Auth::id() && !in_array($pivot->estatus, ['Solventado', 'Cancelado', 'Asignado']))
                                                            <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'operativo']) }}"
                                                                class="inline-block bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm hover:shadow-md transition">
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
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                            No hay correspondencia asignada o turnada a esta área actualmente.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $oficiosTurnados->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>