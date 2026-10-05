<x-app-layout>
    <style>
        @media print {
            .no-print,
            nav,
            button,
            form,
            .pagination,
            footer {
                display: none !important;
            }

            .printable-header {
                display: block !important;
            }

            body {
                background: white !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #ccc !important;
                padding: 6px 8px !important;
                font-size: 8.5pt !important;
                color: black !important;
            }

            th {
                background-color: #f3f4f6 !important;
            }
        }

        .printable-header {
            display: none;
        }
    </style>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    {{ __('Registro General de Oficios de Comisión') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Consulta global de todas las comisiones registradas en el organismo</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg font-bold shadow-xs transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir Reporte
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Encabezado impreso --}}
            <div class="printable-header mb-6">
                <div class="flex justify-between items-center border-b-2 border-guinda-ceaa pb-4">
                    <img src="{{ asset('images/encabezado.png') }}" style="height: 60px;">
                    <div class="text-right">
                        <h1 class="text-xl font-bold uppercase text-guinda-ceaa">COMISIÓN ESTATAL DEL AGUA Y ALCANTARILLADO</h1>
                        <h2 class="text-md font-bold">Reporte General de Oficios de Comisión</h2>
                        <p class="text-xs text-slate-600">Fecha de emisión: {{ now()->format('d/m/Y H:i') }} | Criterios:
                            {{ request('search') ? 'Búsqueda: "' . request('search') . '" ' : '' }}{{ request('area_id') && request('area_id') !== 'todas' ? '| Área ID: ' . request('area_id') . ' ' : '' }}{{ request('status') && request('status') !== 'todos' ? '| Estatus: ' . request('status') . ' ' : '' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tarjetas Resumen de Estadísticas --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 no-print">
                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Total Comisiones</p>
                        <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalComisiones) }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Registros históricos</p>
                    </div>
                    <div class="p-3 bg-purple-50 text-purple-700 rounded-xl border border-purple-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-emerald-700 tracking-wider">Autorizadas / Activas</p>
                        <p class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($totalAutorizadas) }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">En regla / vigentes</p>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-rose-700 tracking-wider">Canceladas</p>
                        <p class="text-2xl font-black text-rose-700 mt-1">{{ number_format($totalCanceladas) }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Oficios invalidados</p>
                    </div>
                    <div class="p-3 bg-rose-50 text-rose-700 rounded-xl border border-rose-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-sky-700 tracking-wider">Acuses Entregados</p>
                        <p class="text-2xl font-black text-sky-700 mt-1">{{ number_format($totalAcusesEntregados) }}</p>
                        <p class="text-[10px] text-amber-600 mt-0.5 font-bold">
                            {{ number_format($totalAcusesPendientes) }} pendientes
                        </p>
                    </div>
                    <div class="p-3 bg-sky-50 text-sky-700 rounded-xl border border-sky-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Bloque de Filtros Avanzados --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5 no-print">
                <form action="{{ route('comisiones.general') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                        {{-- Búsqueda de Texto --}}
                        <div class="lg:col-span-2">
                            <label for="search" class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                Búsqueda General
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" id="search"
                                    placeholder="No. oficio, comisionado, lugar, actividad (separa con comas)..."
                                    class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa transition"
                                    value="{{ request('search') }}">
                            </div>
                        </div>

                        {{-- Filtro por Área / Dirección --}}
                        <div>
                            <label for="area_id" class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                Área / Dirección
                            </label>
                            <select name="area_id" id="area_id"
                                class="w-full py-2 px-3 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa bg-white transition">
                                <option value="todas">-- Todas las Áreas --</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro por Estatus --}}
                        <div>
                            <label for="status" class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                Estatus del Oficio
                            </label>
                            <select name="status" id="status"
                                class="w-full py-2 px-3 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa bg-white transition">
                                <option value="todos" {{ request('status') === 'todos' || !request('status') ? 'selected' : '' }}>-- Todos los Estatus --</option>
                                <option value="Autorizado" {{ request('status') === 'Autorizado' ? 'selected' : '' }}>
                                    Autorizado / Activo</option>
                                <option value="Cancelado" {{ request('status') === 'Cancelado' ? 'selected' : '' }}>
                                    Cancelado</option>
                            </select>
                        </div>

                    </div>

                    {{-- Segunda Fila de Filtros Secundarios --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 pt-3 border-t border-slate-100">
                        {{-- Filtro por Acuse RH --}}
                        <div>
                            <label for="acuse" class="block text-[11px] font-bold uppercase text-slate-500 mb-1">
                                Entrega de Acuse (RH)
                            </label>
                            <select name="acuse" id="acuse"
                                class="w-full py-1.5 px-3 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa bg-white transition">
                                <option value="todos" {{ request('acuse') === 'todos' || !request('acuse') ? 'selected' : '' }}>-- Todos --</option>
                                <option value="Entregados" {{ request('acuse') === 'Entregados' ? 'selected' : '' }}>
                                    Entregado</option>
                                <option value="Pendientes" {{ request('acuse') === 'Pendientes' ? 'selected' : '' }}>
                                    Pendiente</option>
                            </select>
                        </div>

                        {{-- Filtro por Año --}}
                        <div>
                            <label for="anio" class="block text-[11px] font-bold uppercase text-slate-500 mb-1">
                                Año
                            </label>
                            <select name="anio" id="anio"
                                class="w-full py-1.5 px-3 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa bg-white transition">
                                <option value="todos" {{ request('anio') === 'todos' || !request('anio') ? 'selected' : '' }}>-- Todos los Años --</option>
                                @foreach($aniosDisponibles as $anioItem)
                                    <option value="{{ $anioItem }}" {{ request('anio') == $anioItem ? 'selected' : '' }}>
                                        {{ $anioItem }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="sm:col-span-1 lg:col-span-2 flex items-end justify-end gap-2 pt-1">
                            <button type="submit"
                                class="px-5 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Aplicar Filtros
                            </button>

                            @if(request()->anyFilled(['search', 'area_id', 'status', 'acuse', 'anio', 'fecha_desde', 'fecha_hasta']))
                                <a href="{{ route('comisiones.general') }}"
                                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- Mensajes de estado --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs font-semibold rounded-r-lg shadow-xs flex items-center gap-2 no-print">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Tabla de Resultados --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60 no-print">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Resultados de Oficios de Comisión</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $comisiones->total() }} registros
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Página {{ $comisiones->currentPage() }} de {{ $comisiones->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">No. Oficio</th>
                                <th class="py-3 px-4 text-left">Emisión</th>
                                <th class="py-3 px-4 text-left">Comisionado / Área</th>
                                <th class="py-3 px-4 text-left">Días y Horario</th>
                                <th class="py-3 px-4 text-left">Lugar y Actividad</th>
                                <th class="py-3 px-4 text-center">Estatus</th>
                                <th class="py-3 px-4 text-center">Acuse RH</th>
                                <th class="py-3 px-4 text-center no-print">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($comisiones as $comision)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    {{-- No. Oficio --}}
                                    <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                        <a href="{{ route('comisiones.show', $comision) }}" class="hover:underline"
                                            title="Ver detalle del oficio">
                                            {{ $comision->oficio_numero }}
                                        </a>
                                    </td>

                                    {{-- Fecha Emisión --}}
                                    <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $comision->created_at ? $comision->created_at->format('d/m/Y') : 'S/F' }}
                                    </td>

                                    {{-- Comisionado y Área --}}
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="font-bold text-slate-800">
                                            @if($comision->user)
                                                {{ $comision->user->prof ? $comision->user->prof . ' ' : '' }}{{ $comision->user->name }}
                                                @if($comision->user->trashed())
                                                    <span class="text-[9px] bg-rose-100 text-rose-600 px-1 rounded ml-1 font-bold">INACTIVO</span>
                                                @endif
                                            @else
                                                <span class="text-slate-400 italic">No asignado</span>
                                            @endif
                                        </div>
                                        <div class="text-[10.5px] text-slate-500 font-medium truncate mt-0.5"
                                            title="{{ $comision->user->area->name ?? 'Sin Dirección' }}">
                                            🏢 {{ $comision->user->area->name ?? 'Sin Dirección' }}
                                        </div>
                                        @if($comision->user && $comision->user->subarea)
                                            <div class="text-[9.5px] text-slate-400 italic truncate"
                                                title="{{ $comision->user->subarea->name }}">
                                                ↳ {{ $comision->user->subarea->name }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Días y Horario --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-guinda-ceaa">{{ $comision->dias_comision }}</div>
                                        @if($comision->hora_inicio && $comision->hora_fin)
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                🕒 {{ substr($comision->hora_inicio, 0, 5) }} a
                                                {{ substr($comision->hora_fin, 0, 5) }} hrs
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Lugar y Actividad --}}
                                    <td class="py-3.5 px-4 max-w-xs text-slate-600">
                                        <div class="font-bold text-slate-800 truncate" title="{{ $comision->lugar }}">
                                            📍 {{ $comision->lugar }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 italic line-clamp-2 mt-0.5"
                                            title="{{ $comision->actividad }}">
                                            "{{ $comision->actividad }}"
                                        </div>
                                    </td>

                                    {{-- Estatus --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-full uppercase
                                            {{ $comision->status === 'Cancelado' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                            {{ $comision->status ?: 'Autorizado' }}
                                        </span>
                                    </td>

                                    {{-- Acuse RH --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($comision->entregado_acuse)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200 uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Entregado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="py-3.5 px-4 text-center no-print whitespace-nowrap">
                                        <div class="flex justify-center items-center gap-1.5">
                                            <a href="{{ route('comisiones.show', $comision) }}"
                                                class="px-2.5 py-1 bg-slate-800 hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1"
                                                title="Ver Oficio / Expediente">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Ver
                                            </a>

                                            @if(Auth::user()->role === 'admin' && $comision->status !== 'Cancelado')
                                                <a href="{{ route('comisiones.edit', $comision) }}"
                                                    class="p-1 text-slate-500 hover:text-guinda-ceaa rounded transition"
                                                    title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('comisiones.cancelar', $comision) }}" method="POST"
                                                    onsubmit="return confirm('¿Seguro que deseas cancelar este oficio de comisión?');"
                                                    class="inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                        class="p-1 text-slate-400 hover:text-rose-600 rounded transition"
                                                        title="Cancelar Oficio">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No se encontraron oficios de comisión con los criterios seleccionados.</p>
                                            <p class="text-xs text-slate-400">Prueba ajustando los filtros o el texto de búsqueda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/60 no-print">
                    {{ $comisiones->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>