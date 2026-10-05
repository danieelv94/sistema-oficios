<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('Control de Correspondencia Interna') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Oficios emitidos y recibidos entre las Direcciones del Organismo</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(in_array(Auth::user()->role, ['admin', 'correspondencia', 'jefe_area', 'secretaria_area']))
                    <a href="{{ route('oficios.reporteInternos') }}"
                        class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg font-bold shadow-xs transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Reporte de Folios
                    </a>
                @endif
                @if(in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area', 'correspondencia']))
                    <a href="{{ route('oficios.internos.create') }}"
                        class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg font-bold shadow-xs hover:shadow-md transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Registrar Oficio Interno
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filtros y Buscador --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    {{-- Tabs de Filtro --}}
                    <div class="flex flex-wrap items-center gap-1 bg-slate-100/80 p-1 rounded-lg border border-slate-200/60">
                        <a href="{{ request()->fullUrlWithQuery(['tipo' => 'Todos']) }}"
                            class="px-3.5 py-1.5 rounded-md font-bold text-xs uppercase tracking-wider transition-all {{ $filtroTipo === 'Todos' ? 'bg-white text-guinda-ceaa shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Todos
                        </a>
                        @if(in_array(Auth::user()->role, ['admin', 'correspondencia']))
                        <a href="{{ request()->fullUrlWithQuery(['tipo' => 'Enviados']) }}"
                            class="px-3.5 py-1.5 rounded-md font-bold text-xs uppercase tracking-wider transition-all {{ $filtroTipo === 'Enviados' ? 'bg-white text-guinda-ceaa shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Enviados
                        </a>
                        @endif
                        <a href="{{ request()->fullUrlWithQuery(['tipo' => 'Recibidos']) }}"
                            class="px-3.5 py-1.5 rounded-md font-bold text-xs uppercase tracking-wider transition-all {{ $filtroTipo === 'Recibidos' ? 'bg-white text-guinda-ceaa shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Recibidos
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['tipo' => 'Solventados']) }}"
                            class="px-3.5 py-1.5 rounded-md font-bold text-xs uppercase tracking-wider transition-all {{ $filtroTipo === 'Solventados' ? 'bg-white text-guinda-ceaa shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Solventados
                        </a>
                    </div>

                    {{-- Formulario de búsqueda --}}
                    <form action="{{ route('oficios.internos.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2 w-full lg:w-auto flex-1 lg:max-w-2xl justify-end">
                        <input type="hidden" name="tipo" value="{{ $filtroTipo }}">
                        <div class="relative flex-1 w-full">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" placeholder="Buscar por número, remitente o asunto..."
                                class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa transition"
                                value="{{ request('search') }}">
                        </div>
                        
                        <div class="w-full sm:w-48">
                            <select name="estatus" onchange="this.form.submit()"
                                class="w-full py-2 px-3 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa bg-white transition">
                                <option value="Todos" {{ $filtroEstatus == 'Todos' ? 'selected' : '' }}>-- Todos los Estados --</option>
                                <option value="Notificado" {{ $filtroEstatus == 'Notificado' ? 'selected' : '' }}>Notificado</option>
                                <option value="Asignado" {{ $filtroEstatus == 'Asignado' ? 'selected' : '' }}>Asignado</option>
                                <option value="En Proceso" {{ $filtroEstatus == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="Solventado" {{ $filtroEstatus == 'Solventado' ? 'selected' : '' }}>Solventado</option>
                                <option value="Cancelado" {{ $filtroEstatus == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Buscar
                            </button>
                            @if(request('search') || request('estatus', 'Todos') !== 'Todos')
                                <a href="{{ route('oficios.internos.index', ['tipo' => $filtroTipo]) }}"
                                    class="w-full sm:w-auto px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs font-semibold rounded-r-lg shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Tabla de Oficios Internos --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Registros de Oficios Internos</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $oficios->total() }} registros
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Página {{ $oficios->currentPage() }} de {{ $oficios->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">Número de Oficio</th>
                                <th class="py-3 px-4 text-left">Origen</th>
                                <th class="py-3 px-4 text-left">Destino (Turnado A)</th>
                                <th class="py-3 px-4 text-left">Remitente</th>
                                <th class="py-3 px-4 text-left">Asunto</th>
                                <th class="py-3 px-4 text-left">Fecha Oficio</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                                <th class="py-3 px-4 text-center">Estatus</th>
                                <th class="py-3 px-4 text-center">Oficio Dependencia</th>
                                <th class="py-3 px-4 text-center">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
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
                                        
                                        // 2. Si no hay asignación directa al usuario y pertenece a una subárea, buscar asignación grupal a la subárea (donde user_id es null) - Solo aplicable a subdirectores
                                        if (!$subareaOficio && Auth::user()->subarea_id && in_array(Auth::user()->role, ['subdirector', 'admin'])) {
                                            $subareaOficio = \App\Models\SubareaOficio::where('area_oficio_id', $pivot->id)
                                                ->where('subarea_id', Auth::user()->subarea_id)
                                                ->whereNull('user_id')
                                                ->first();
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                        <a href="{{ route('oficios.show', [$oficio, 'mode' => in_array(Auth::user()->role, ['admin', 'recepcionista', 'correspondencia', 'jefe_area', 'secretaria_area']) ? 'gestion' : 'operativo']) }}" class="hover:underline">
                                            {{ $oficio->numero_oficio }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-800">
                                        {{ $oficio->areaOrigen->name ?? 'Externa' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 max-w-xs">
                                        <div class="flex flex-col gap-1.5">
                                            @foreach($oficio->areas as $destArea)
                                                <div class="border-b border-slate-100 last:border-0 pb-1.5 last:pb-0">
                                                    <span class="px-2 py-0.5 bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-bold rounded uppercase inline-block">
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
                                                        <div class="mt-1 pl-2 text-[10px] space-y-0.5 text-slate-500 font-medium">
                                                            @foreach($assignments as $assignment)
                                                                <div class="flex items-center gap-1">
                                                                    <span class="text-slate-400">↳</span>
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
                                                        <span class="pl-2 text-[9px] text-slate-400 italic block mt-0.5">Pendiente de turnar</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 max-w-[180px] truncate" title="{{ $oficio->remitente }}">
                                        {{ $oficio->remitente }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate" title="{{ $oficio->asunto }}">
                                        {{ $oficio->asunto }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($oficio->fecha_recepcion)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            @if($pivot)
                                                @if(Auth::user()->role === 'jefe_area')
                                                    @if($pivot->estatus == 'Notificado' && $oficio->area_origen_id != Auth::user()->area_id)
                                                        <form action="{{ route('oficios.recibirTurno', $pivot->id) }}" method="POST" class="inline-block">
                                                            @csrf @method('PUT')
                                                            <button type="submit"
                                                                class="px-2.5 py-1 bg-dorado-ocre hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                Confirmar Recibido
                                                            </button>
                                                        </form>
                                                    @elseif(in_array($pivot->estatus, ['Recibido', 'En Proceso', 'Asignado']) || ($pivot->estatus == 'Notificado' && $oficio->area_origen_id == Auth::user()->area_id))
                                                        <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'gestion']) }}"
                                                            class="px-2.5 py-1 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                            </svg>
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
                                                                    class="px-2.5 py-1 bg-dorado-ocre hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    Confirmar Recibido
                                                                </button>
                                                            </form>
                                                        @elseif($subareaOficio->estatus == 'Notificado')
                                                            <a href="{{ route('oficios.show', [$oficio->id, 'mode' => 'operativo']) }}"
                                                                class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                </svg>
                                                                Delegar
                                                            </a>
                                                            <a href="{{ route('oficios.atender', [$pivot->id, 'subarea_oficio_id' => $subareaOficio->id]) }}"
                                                                class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
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
                                                                    class="px-2.5 py-1 bg-dorado-ocre hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                    Confirmar Recibido
                                                                </button>
                                                            </form>
                                                        @elseif($subareaOficio->estatus == 'Notificado')
                                                            <a href="{{ route('oficios.atender', [$pivot->id, 'subarea_oficio_id' => $subareaOficio->id]) }}"
                                                                class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                                Atender
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                            
                                            <a href="{{ route('oficios.show', [$oficio, 'mode' => in_array(Auth::user()->role, ['admin', 'recepcionista', 'correspondencia', 'jefe_area', 'secretaria_area']) ? 'gestion' : 'operativo']) }}"
                                                class="px-2.5 py-1 bg-slate-800 hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Expediente
                                            </a>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'correspondencia' || Auth::user()->role === 'recepcionista')
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ $oficio->estatus == 'Solventado' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                                {{ $oficio->estatus == 'En Proceso' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                                {{ $oficio->estatus == 'Turnado' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                                                {{ $oficio->estatus == 'Notificado' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                                {{ $oficio->estatus == 'Cancelado' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                            ">
                                                {{ $oficio->estatus }}
                                            </span>
                                        @else
                                            @php
                                                $estatusTurno = $pivot ? $pivot->estatus : $oficio->estatus;
                                            @endphp
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ $estatusTurno == 'Solventado' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                                {{ $estatusTurno == 'Asignado' || $estatusTurno == 'En Proceso' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                                {{ $estatusTurno == 'Notificado' || $estatusTurno == 'Turnado' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                                {{ $estatusTurno == 'Cancelado' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                            ">
                                                {{ $estatusTurno }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-700 text-center whitespace-nowrap">
                                        {{ $oficio->numero_oficio_dependencia ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($oficio->pdf_path)
                                            <a href="{{ asset('storage/' . $oficio->pdf_path) }}" target="_blank"
                                                class="inline-flex items-center justify-center p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-md transition shadow-2xs border border-rose-200"
                                                title="Ver PDF del Oficio">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic text-[10px]">Sin PDF</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No se encontraron oficios internos registrados.</p>
                                            <p class="text-xs text-slate-400">Intenta modificando los filtros de búsqueda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $oficios->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>