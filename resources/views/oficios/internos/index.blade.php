<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-widest">
                {{ __('Correspondencia Interna') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-blue-600">
                <div class="p-6 text-gray-900">

                    {{-- Encabezado e botón de creación --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex items-center">
                            <span class="p-2 bg-blue-50 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-black text-gray-700 uppercase italic">Control de Correspondencia
                                    Interna</h3>
                                <p class="text-xs text-gray-400 font-bold uppercase mt-0.5">Oficios emitidos y recibidos
                                    entre las Direcciones del Organismo</p>
                            </div>
                        </div>
                        @if(in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area', 'correspondencia']))
                            <a href="{{ route('oficios.internos.create') }}"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-bold shadow-md transition-all transform hover:scale-105 text-sm uppercase tracking-wider">
                                + Registrar Oficio Interno
                            </a>
                        @endif
                    </div>

                    {{-- Filtros y Buscador --}}
                    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                        {{-- Tabs de Filtro --}}
                        <div class="flex border-b border-gray-200">
                            <a href="{{ request()->fullUrlWithQuery(['tipo' => 'Todos']) }}"
                                class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition-all {{ $filtroTipo === 'Todos' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Todos
                            </a>
                            @if(in_array(Auth::user()->role, ['admin', 'correspondencia']))
                            <a href="{{ request()->fullUrlWithQuery(['tipo' => 'Enviados']) }}"
                                class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition-all {{ $filtroTipo === 'Enviados' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Enviados
                            </a>
                            @endif
                            <a href="{{ request()->fullUrlWithQuery(['tipo' => 'Recibidos']) }}"
                                class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition-all {{ $filtroTipo === 'Recibidos' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Recibidos
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['tipo' => 'Solventados']) }}"
                                class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition-all {{ $filtroTipo === 'Solventados' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Solventados
                            </a>
                        </div>

                        {{-- Formulario de búsqueda --}}
                        <form action="{{ route('oficios.internos.index') }}" method="GET"
                            class="flex gap-2 flex-1 md:max-w-xl">
                            <input type="hidden" name="tipo" value="{{ $filtroTipo }}">
                            <input type="text" name="search" placeholder="Buscar por número, remitente o asunto..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-600 focus:border-blue-600 focus:outline-none text-xs"
                                value="{{ request('search') }}">
                            
                            <select name="estatus" onchange="this.form.submit()"
                                class="text-xs rounded-md border-gray-300 focus:ring-blue-600 focus:border-blue-600 py-2">
                                <option value="Todos" {{ $filtroEstatus == 'Todos' ? 'selected' : '' }}>-- Todos los Estados --</option>
                                <option value="Notificado" {{ $filtroEstatus == 'Notificado' ? 'selected' : '' }}>Notificado</option>
                                <option value="Asignado" {{ $filtroEstatus == 'Asignado' ? 'selected' : '' }}>Asignado</option>
                                <option value="En Proceso" {{ $filtroEstatus == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="Solventado" {{ $filtroEstatus == 'Solventado' ? 'selected' : '' }}>Solventado</option>
                                <option value="Cancelado" {{ $filtroEstatus == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>

                            <button type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-black transition text-xs font-bold uppercase tracking-wider">Buscar</button>
                            @if(request('search') || request('estatus', 'Todos') !== 'Todos')
                                <a href="{{ route('oficios.internos.index', ['tipo' => $filtroTipo]) }}"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-xs font-bold uppercase tracking-wider flex items-center justify-center">Limpiar</a>
                            @endif
                        </form>
                    </div>

                    {{-- Mensaje de éxito --}}
                    @if(session('success'))
                        <div
                            class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-semibold rounded-r">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Tabla de Oficios Internos --}}
                    <div class="overflow-x-auto rounded-xl border border-gray-150 shadow-sm">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-150 text-gray-600 text-[10px] font-black uppercase tracking-wider">
                                    <th class="py-3 px-4 text-left">Número de Oficio</th>
                                    <th class="py-3 px-4 text-left">Origen</th>
                                    <th class="py-3 px-4 text-left">Destino (Turnado A)</th>
                                    <th class="py-3 px-4 text-left">Remitente</th>
                                    <th class="py-3 px-4 text-left">Asunto</th>
                                    <th class="py-3 px-4 text-left">Fecha Oficio</th>
                                    <th class="py-3 px-4 text-center">Acciones</th>
                                    <th class="py-3 px-4 text-center">Estatus</th>
                                    <th class="py-3 px-4 text-center">Oficio Original</th>
                                    <th class="py-3 px-4 text-center">PDF</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs">
                                @forelse($oficios as $oficio)
                                    @php
                                        $miArea = $oficio->areas->first(fn($a) => $a->id == Auth::user()->area_id);
                                        $pivot = $miArea ? $miArea->pivot : null;
                                        $subareaOficio = null;
                                        $hasSubareas = false;
                                        if ($pivot) {
                                            $hasSubareas = DB::table('subarea_oficio')->where('area_oficio_id', $pivot->id)->exists();
                                            // 1. Intentar buscar asignación directa al usuario
                                            $subareaOficio = \App\Models\SubareaOficio::where('area_oficio_id', $pivot->id)
                                                ->where('user_id', Auth::id())
                                                ->first();
                                            
                                            // 2. Si no hay asignación directa al usuario y pertenece a una subárea, buscar asignación grupal a la subárea (donde user_id es null)
                                            if (!$subareaOficio && Auth::user()->subarea_id) {
                                                $subareaOficio = \App\Models\SubareaOficio::where('area_oficio_id', $pivot->id)
                                                    ->where('subarea_id', Auth::user()->subarea_id)
                                                    ->whereNull('user_id')
                                                    ->first();
                                            }
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-3.5 px-4 font-black text-blue-600 whitespace-nowrap">
                                            {{ $oficio->numero_oficio }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-gray-700">
                                            {{ $oficio->areaOrigen->name ?? 'Externa' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-600 max-w-xs">
                                            <div class="flex flex-col gap-2">
                                                @foreach($oficio->areas as $destArea)
                                                    <div class="border-b border-gray-100 last:border-0 pb-1.5 last:pb-0">
                                                        <span
                                                            class="px-2 py-0.5 bg-gray-100 border border-gray-200 text-gray-700 text-[9px] font-bold rounded uppercase block w-fit">
                                                            {{ $destArea->name }}
                                                        </span>
                                                        
                                                        {{-- Cargar asignaciones de esta área --}}
                                                        @php
                                                            $assignments = DB::table('subarea_oficio')
                                                                ->leftJoin('subareas', 'subarea_oficio.subarea_id', '=', 'subareas.id')
                                                                ->leftJoin('users', 'subarea_oficio.user_id', '=', 'users.id')
                                                                ->where('subarea_oficio.area_oficio_id', $destArea->pivot->id)
                                                                ->select('subareas.name as subarea_name', 'users.name as user_name')
                                                                ->get();
                                                        @endphp
                                                        @if($assignments->isNotEmpty())
                                                            <div class="mt-1 pl-2 text-[10px] space-y-0.5 text-gray-500 font-medium">
                                                                @foreach($assignments as $assignment)
                                                                    <div class="flex items-center gap-1">
                                                                        <span class="text-gray-400">↳</span>
                                                                        @if($assignment->subarea_name && $assignment->user_name)
                                                                            <span>{{ $assignment->subarea_name }} ({{ $assignment->user_name }})</span>
                                                                        @elseif($assignment->subarea_name)
                                                                            <span>{{ $assignment->subarea_name }}</span>
                                                                        @elseif($assignment->user_name)
                                                                            <span>{{ $assignment->user_name }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="pl-2 text-[9px] text-gray-400 italic block mt-0.5">Pendiente de turnar en área</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-600 max-w-[180px] truncate"
                                            title="{{ $oficio->remitente }}">
                                            {{ $oficio->remitente }}
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate"
                                            title="{{ $oficio->asunto }}">
                                            {{ $oficio->asunto }}
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($oficio->fecha_recepcion)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center space-x-1.5 whitespace-nowrap">
                                            @if($pivot)
                                                @if(Auth::user()->role === 'jefe_area')
                                                    @if($pivot->estatus == 'Notificado' && $oficio->area_origen_id != Auth::user()->area_id)
                                                        <form action="{{ route('oficios.recibirTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                            @csrf @method('PUT')
                                                            <button type="submit"
                                                                class="bg-dorado-ocre hover:bg-guinda-ceaa text-white px-2.5 py-1 rounded-md font-black uppercase text-[9px] shadow-sm transition">
                                                                Confirmar Recibido
                                                            </button>
                                                        </form>
                                                    @elseif(in_array($pivot->estatus, ['Recibido', 'En Proceso', 'Asignado']) || ($pivot->estatus == 'Notificado' && $oficio->area_origen_id == Auth::user()->area_id))
                                                        <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'gestion']) }}"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded-md font-black uppercase text-[9px] shadow-sm transition">
                                                            Turnar
                                                        </a>
                                                    @endif
                                                @elseif(Auth::user()->role === 'subdirector')
                                                    @if($subareaOficio)
                                                        @if($subareaOficio->estatus == 'Asignado')
                                                            <form action="{{ route('oficios.notificarTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                                @csrf @method('PUT')
                                                                <input type="hidden" name="subarea_oficio_id" value="{{ $subareaOficio->id }}">
                                                                <button type="submit"
                                                                    class="bg-dorado-ocre hover:bg-guinda-ceaa text-white px-2.5 py-1 rounded-md font-black uppercase text-[9px] shadow-sm transition">
                                                                    Confirmar Recibido
                                                                </button>
                                                            </form>
                                                        @elseif($subareaOficio->estatus == 'Notificado')
                                                            <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'operativo']) }}"
                                                                class="bg-yellow-500 hover:bg-yellow-600 text-black px-2.5 py-1 rounded-md font-black uppercase text-[9px] shadow-sm transition">
                                                                Delegar
                                                            </a>
                                                            <a href="{{ route('oficios.atender', [$pivot->id, 'subarea_oficio_id' => $subareaOficio->id]) }}"
                                                                class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded-md font-black uppercase text-[9px] shadow-sm transition">
                                                                Atender
                                                            </a>
                                                        @endif
                                                    @endif
                                                @else
                                                    @if($subareaOficio)
                                                        @if($subareaOficio->estatus == 'Asignado')
                                                            <form action="{{ route('oficios.notificarTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                                @csrf @method('PUT')
                                                                <input type="hidden" name="subarea_oficio_id" value="{{ $subareaOficio->id }}">
                                                                <button type="submit"
                                                                    class="bg-dorado-ocre hover:bg-guinda-ceaa text-white px-2.5 py-1 rounded-md font-black uppercase text-[9px] shadow-sm transition">
                                                                    Confirmar Recibido
                                                                </button>
                                                            </form>
                                                        @elseif($subareaOficio->estatus == 'Notificado')
                                                            <a href="{{ route('oficios.atender', [$pivot->id, 'subarea_oficio_id' => $subareaOficio->id]) }}"
                                                                class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded-md font-black uppercase text-[9px] shadow-sm transition">
                                                                Atender
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                            
                                            <a href="{{ route('oficios.show', [$oficio, 'mode' => in_array(Auth::user()->role, ['admin', 'recepcionista', 'correspondencia', 'jefe_area', 'secretaria_area']) ? 'gestion' : 'operativo']) }}"
                                                class="px-2.5 py-1 bg-gray-800 hover:bg-gray-900 text-white rounded-md font-black uppercase text-[9px] transition shadow-sm">
                                                Expediente
                                            </a>
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'correspondencia' || Auth::user()->role === 'recepcionista')
                                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider
                                                                    {{ $oficio->estatus == 'Solventado' ? 'bg-green-100 text-green-700' : '' }}
                                                                    {{ $oficio->estatus == 'En Proceso' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                                    {{ $oficio->estatus == 'Turnado' ? 'bg-orange-100 text-orange-700' : '' }}
                                                                    {{ $oficio->estatus == 'Notificado' ? 'bg-blue-100 text-blue-700' : '' }}
                                                                    {{ $oficio->estatus == 'Cancelado' ? 'bg-red-100 text-red-700' : '' }}
                                                                ">
                                                    {{ $oficio->estatus }}
                                                </span>
                                            @else
                                                @php
                                                    $estatusTurno = $pivot ? $pivot->estatus : $oficio->estatus;
                                                @endphp
                                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider
                                                                    {{ $estatusTurno == 'Solventado' ? 'bg-green-100 text-green-700' : '' }}
                                                                    {{ $estatusTurno == 'Asignado' || $estatusTurno == 'En Proceso' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                                    {{ $estatusTurno == 'Notificado' || $estatusTurno == 'Turnado' ? 'bg-blue-100 text-blue-700' : '' }}
                                                                    {{ $estatusTurno == 'Cancelado' ? 'bg-red-100 text-red-700' : '' }}
                                                                ">
                                                    {{ $estatusTurno }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-bold text-gray-700 text-center whitespace-nowrap">
                                            {{ $oficio->numero_oficio_dependencia }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($oficio->pdf_path)
                                                <a href="{{ asset('storage/' . $oficio->pdf_path) }}" target="_blank"
                                                    class="inline-flex items-center justify-center p-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition shadow-sm"
                                                    title="Ver PDF del Oficio">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </a>
                                            @else
                                                <span class="text-gray-400 italic text-[10px]">Sin PDF</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-8 text-center text-gray-400 italic">
                                            No se encontraron oficios internos.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-4">
                        {{ $oficios->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>