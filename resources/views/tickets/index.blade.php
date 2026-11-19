<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ __('Mesa de Ayuda y Soporte Técnico') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Atención a incidentes, soporte informático y seguimiento de turnos por atender y atendidos
                </p>
            </div>
            <div>
                <a href="{{ route('tickets.create') }}"
                    class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg font-bold shadow-xs hover:shadow-md transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Levantar Ticket de Soporte
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $esAdmin = Auth::user()->role === 'admin';
        $usuarioFiltradoObj = ($esAdmin && !empty($userIdFiltro))
            ? $remitentesResumen->firstWhere('id', (int) $userIdFiltro)
            : null;
    @endphp

    <div class="py-6 bg-slate-50/50 min-h-screen" x-data="{ verResumenRemitentes: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- BANNER DE NOTIFICACIONES PUSH --}}
            <div x-data="pushSubscription()" x-init="checkSubscription()" x-show="!isSubscribed && showBanner"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="bg-gradient-to-r from-guinda-ceaa to-guinda-ceaa-hover rounded-xl shadow-md p-6 text-white relative overflow-hidden"
                style="display: none;">

                <div class="absolute right-[-20px] top-[-20px] opacity-10">
                    <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 22a2 2 0 002-2H10a2 2 0 002 2zm6-6V11a6 6 0 00-9.33-5.05A3.003 3.003 0 005 11v5l-2 2v1h18v-1l-2-2z" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-white/20 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-base font-black uppercase tracking-tight">¿Deseas recibir alertas de soporte al instante?</h4>
                            <p class="text-xs text-white/90 font-medium mt-0.5">
                                Activa las notificaciones en tu navegador para saber de inmediato cuando tu ticket sea resuelto o existan comunicados.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 shrink-0">
                        <button @click="showBanner = false"
                            class="text-xs font-bold uppercase opacity-75 hover:opacity-100 transition tracking-wider underline">Omitir</button>
                        <button @click="toggleSubscription"
                            class="bg-white text-guinda-ceaa px-5 py-2 rounded-lg shadow-sm font-black text-xs uppercase tracking-wider hover:bg-slate-100 transition-all transform active:scale-95">
                            Activar Alertas
                        </button>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-2.5 text-xs font-bold shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- TARJETAS / PESTAÑAS RÁPIDAS DE FILTRO: TODOS | POR ATENDER | ATENDIDOS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Todos --}}
                <a href="{{ route('tickets.index', array_merge(request()->except(['page_pendientes', 'page_concluidos']), ['estatus' => 'todos'])) }}"
                    class="bg-white rounded-2xl p-4 border {{ $estatusFiltro === 'todos' ? 'border-slate-500 ring-2 ring-slate-200' : 'border-slate-200/80' }} shadow-xs hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Turnos / Tickets</p>
                        <p class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Ver por atender y atendidos</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </a>

                {{-- Por Atender (Pendientes) --}}
                <a href="{{ route('tickets.index', array_merge(request()->except(['page_pendientes', 'page_concluidos']), ['estatus' => 'Pendiente'])) }}"
                    class="bg-white rounded-2xl p-4 border {{ $estatusFiltro === 'Pendiente' ? 'border-amber-500 ring-2 ring-amber-100 bg-amber-50/20' : 'border-slate-200/80' }} shadow-xs hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600">⏳ Turnos Por Atender</p>
                        <p class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pendientes'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Solicitudes pendientes de solución</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>

                {{-- Atendidos (Concluidos) --}}
                <a href="{{ route('tickets.index', array_merge(request()->except(['page_pendientes', 'page_concluidos']), ['estatus' => 'Concluido'])) }}"
                    class="bg-white rounded-2xl p-4 border {{ $estatusFiltro === 'Concluido' ? 'border-emerald-500 ring-2 ring-emerald-100 bg-emerald-50/20' : 'border-slate-200/80' }} shadow-xs hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600">✅ Turnos Atendidos</p>
                        <p class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['atendidos'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Tickets resueltos y concluidos</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>
            </div>

            {{-- BARRA DE FILTROS Y "¿QUIÉN ME MANDÓ QUÉ?" --}}
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 space-y-4">
                <form method="GET" action="{{ route('tickets.index') }}" class="grid grid-cols-1 sm:grid-cols-2 {{ $esAdmin ? 'lg:grid-cols-6' : 'lg:grid-cols-4' }} gap-3 items-end">
                    {{-- Buscador libre --}}
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">
                            Buscar qué mandaron (Asunto, descripción, folio o persona)
                        </label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Ej. impresora, internet, oficio, nombre..."
                                class="w-full h-[38px] pl-9 pr-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    @if($esAdmin)
                        {{-- Filtro por Quién lo mandó (Usuario) --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">
                                ¿Quién lo mandó? (Usuario)
                            </label>
                            <select name="user_id" class="w-full h-[38px] px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                                <option value="">Todos los usuarios ({{ $remitentesResumen->count() }})</option>
                                @foreach($remitentesResumen as $rem)
                                    <option value="{{ $rem->id }}" @selected((string)$userIdFiltro === (string)$rem->id)>
                                        {{ $rem->name }} ({{ $rem->pendientes_count }} por atender / {{ $rem->atendidos_count }} atendidos)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro por Área / Dirección --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">
                                Área / Dirección
                            </label>
                            <select name="area_id" class="w-full h-[38px] px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                                <option value="">Todas las áreas</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" @selected((string)$areaIdFiltro === (string)$area->id)>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Filtro por Estatus (Por Atender / Atendidos) --}}
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">
                            Turnos a mostrar
                        </label>
                        <select name="estatus" class="w-full h-[38px] px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa font-semibold">
                            <option value="todos" @selected($estatusFiltro === 'todos')>📋 Todos (Por atender y Atendidos)</option>
                            <option value="Pendiente" @selected($estatusFiltro === 'Pendiente')>⏳ Solo Por Atender</option>
                            <option value="Concluido" @selected($estatusFiltro === 'Concluido')>✅ Solo Atendidos</option>
                        </select>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="flex-1 h-[38px] px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'user_id', 'area_id']) || $estatusFiltro !== 'todos')
                            <a href="{{ route('tickets.index') }}"
                                class="h-[38px] px-3 inline-flex items-center bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition"
                                title="Limpiar filtros">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Botón para desplegar el directorio rápido de "¿Quién me ha mandado tickets?" --}}
                @if($esAdmin && $remitentesResumen->count() > 0)
                    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2 text-xs text-slate-600">
                            @if($usuarioFiltradoObj)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-guinda-ceaa/10 text-guinda-ceaa font-bold">
                                    👤 Viendo tickets enviados por: {{ $usuarioFiltradoObj->name }}
                                    ({{ $usuarioFiltradoObj->area ? $usuarioFiltradoObj->area->name : 'Sin Área' }})
                                </span>
                            @else
                                <span class="text-slate-500">
                                    Selecciona a una persona para ver exactamente qué tickets te ha enviado (por atender y atendidos).
                                </span>
                            @endif
                        </div>

                        <button type="button" @click="verResumenRemitentes = !verResumenRemitentes"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-guinda-ceaa hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-text="verResumenRemitentes ? 'Ocultar resumen por usuario' : 'Ver resumen por usuario (Quién mandó qué)'"></span>
                        </button>
                    </div>

                    {{-- Panel desplegable: Tarjetas de usuarios que han enviado tickets --}}
                    <div x-show="verResumenRemitentes" x-transition
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 pt-2 max-h-72 overflow-y-auto pr-1">
                        @foreach($remitentesResumen as $rem)
                            <a href="{{ route('tickets.index', ['user_id' => $rem->id, 'estatus' => $estatusFiltro]) }}"
                                class="p-3 rounded-xl border transition flex items-center justify-between gap-2 {{ (string)$userIdFiltro === (string)$rem->id ? 'border-guinda-ceaa bg-guinda-ceaa/5 ring-1 ring-guinda-ceaa' : 'border-slate-200 hover:border-slate-300 bg-slate-50/60 hover:bg-white' }}">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $rem->name }}</p>
                                    <p class="text-[10.5px] text-slate-500 truncate">
                                        🏢 {{ $rem->area ? $rem->area->name : 'Sin Área' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($rem->pendientes_count > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold" title="Turnos por atender">
                                            ⏳ {{ $rem->pendientes_count }}
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold" title="Turnos atendidos">
                                        ✅ {{ $rem->atendidos_count }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ========================================================= --}}
            {{-- SECCIÓN 1: TURNOS POR ATENDER (PENDIENTES)                --}}
            {{-- ========================================================= --}}
            @if(in_array($estatusFiltro, ['todos', 'Pendiente']))
                <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-amber-50/40">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">
                                Turnos Por Atender (Solicitudes en Proceso)
                            </h3>
                            <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 text-[11px] font-black rounded-full">
                                {{ $ticketsPendientes->total() }} por atender
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 font-medium">
                            Página {{ $ticketsPendientes->currentPage() }} de {{ $ticketsPendientes->lastPage() }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                            <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 text-left">Turno / Fecha Envío</th>
                                    @if($esAdmin)
                                        <th class="py-3 px-4 text-left">¿Quién lo Mandó? (Usuario / Área)</th>
                                    @endif
                                    <th class="py-3 px-4 text-left">¿Qué Mandó? (Asunto y Detalle del Problema)</th>
                                    <th class="py-3 px-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($ticketsPendientes as $ticket)
                                    <tr class="hover:bg-slate-50/75 transition duration-150 align-top">
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-block px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-extrabold text-[10px] mb-1">
                                                Turno #{{ $ticket->id }}
                                            </span>
                                            <div class="text-slate-700 font-bold">
                                                {{ $ticket->created_at->format('d/m/Y') }}
                                            </div>
                                            <div class="text-[11px] text-slate-500">
                                                🕒 {{ $ticket->created_at->format('H:i') }} hrs
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                {{ $ticket->created_at->diffForHumans() }}
                                            </div>
                                        </td>

                                        @if($esAdmin)
                                            <td class="py-3.5 px-4 max-w-xs">
                                                @if($ticket->user)
                                                    <a href="{{ route('tickets.index', ['user_id' => $ticket->user_id, 'estatus' => $estatusFiltro]) }}"
                                                        class="font-bold text-slate-800 hover:text-guinda-ceaa hover:underline block"
                                                        title="Ver todos los tickets enviados por {{ $ticket->user->name }}">
                                                        👤 {{ trim(($ticket->user->prof ? $ticket->user->prof . ' ' : '') . $ticket->user->name) }}
                                                    </a>
                                                    <div class="text-[11px] text-guinda-ceaa font-semibold mt-0.5">
                                                        🏢 {{ $ticket->user->area ? $ticket->user->area->name : 'Sin Área' }}
                                                    </div>
                                                    @if($ticket->user->cargo)
                                                        <div class="text-[10.5px] text-slate-400 mt-0.5">
                                                            {{ $ticket->user->cargo }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-slate-400 italic">Usuario Inactivo</span>
                                                @endif
                                            </td>
                                        @endif

                                        <td class="py-3.5 px-4 max-w-xl">
                                            <div class="font-extrabold text-slate-800 text-xs">
                                                {{ $ticket->subject }}
                                            </div>
                                            @if($ticket->description)
                                                <div class="mt-1 text-xs text-slate-600 bg-slate-50 border border-slate-200/70 rounded-lg p-2.5 whitespace-pre-line leading-relaxed">
                                                    {{ $ticket->description }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <div class="inline-flex flex-col sm:flex-row items-center gap-1.5">
                                                @if($esAdmin)
                                                    <a href="{{ route('tickets.edit', $ticket) }}"
                                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Atender / Resolver
                                                    </a>
                                                @endif
                                                <a href="{{ route('tickets.show', $ticket) }}"
                                                    class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold uppercase tracking-wider transition inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Detalle
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-slate-400 italic">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-sm font-semibold text-slate-600">No hay turnos pendientes por atender con estos criterios.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($ticketsPendientes->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                            {{ $ticketsPendientes->links() }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- ========================================================= --}}
            {{-- SECCIÓN 2: TURNOS ATENDIDOS (HISTORIAL DE SOLUCIONES)     --}}
            {{-- ========================================================= --}}
            @if(in_array($estatusFiltro, ['todos', 'Concluido']))
                <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-emerald-50/40">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">
                                Turnos Atendidos (Historial de Soluciones)
                            </h3>
                            <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 text-[11px] font-black rounded-full">
                                {{ $ticketsConcluidos->total() }} atendidos
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 font-medium">
                            Página {{ $ticketsConcluidos->currentPage() }} de {{ $ticketsConcluidos->lastPage() }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                            <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 text-left">Turno / Fechas</th>
                                    @if($esAdmin)
                                        <th class="py-3 px-4 text-left">¿Quién lo Mandó? (Solicitante / Área)</th>
                                    @endif
                                    <th class="py-3 px-4 text-left">¿Qué Mandó y Cómo se Atendió?</th>
                                    <th class="py-3 px-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($ticketsConcluidos as $ticket)
                                    <tr class="hover:bg-slate-50/75 transition duration-150 align-top">
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-block px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold text-[10px] mb-1">
                                                ✅ Turno #{{ $ticket->id }}
                                            </span>
                                            <div class="text-[11px] text-slate-500">
                                                <span class="font-semibold">Enviado:</span> {{ $ticket->created_at->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="text-[11px] text-emerald-700 font-bold mt-0.5">
                                                <span>Atendido:</span> {{ $ticket->completed_at ? \Carbon\Carbon::parse($ticket->completed_at)->format('d/m/Y H:i') : 'N/A' }}
                                            </div>
                                        </td>

                                        @if($esAdmin)
                                            <td class="py-3.5 px-4 max-w-xs">
                                                @if($ticket->user)
                                                    <a href="{{ route('tickets.index', ['user_id' => $ticket->user_id, 'estatus' => $estatusFiltro]) }}"
                                                        class="font-bold text-slate-800 hover:text-guinda-ceaa hover:underline block"
                                                        title="Ver todos los tickets enviados por {{ $ticket->user->name }}">
                                                        👤 {{ trim(($ticket->user->prof ? $ticket->user->prof . ' ' : '') . $ticket->user->name) }}
                                                    </a>
                                                    <div class="text-[11px] text-guinda-ceaa font-semibold mt-0.5">
                                                        🏢 {{ $ticket->user->area ? $ticket->user->area->name : 'Sin Área' }}
                                                    </div>
                                                    @if($ticket->user->cargo)
                                                        <div class="text-[10.5px] text-slate-400 mt-0.5">
                                                            {{ $ticket->user->cargo }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-slate-400 italic">Ex-empleado</span>
                                                @endif
                                            </td>
                                        @endif

                                        <td class="py-3.5 px-4 max-w-xl space-y-1.5">
                                            <div class="font-extrabold text-slate-800">
                                                {{ $ticket->subject }}
                                            </div>
                                            @if($ticket->description)
                                                <div class="text-xs text-slate-600 bg-slate-50 border border-slate-200/70 rounded-lg p-2 whitespace-pre-line">
                                                    <span class="font-bold text-slate-700">Solicitud:</span> {{ $ticket->description }}
                                                </div>
                                            @endif
                                            @if($ticket->resolution_notes)
                                                <div class="text-xs text-emerald-900 bg-emerald-50/70 border border-emerald-200 rounded-lg p-2 whitespace-pre-line">
                                                    <span class="font-bold text-emerald-800">Solución:</span> {{ $ticket->resolution_notes }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                class="px-3 py-1.5 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Revisar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-slate-400 italic">
                                            No se encontraron turnos atendidos con estos criterios.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($ticketsConcluidos->hasPages())
                        <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                            {{ $ticketsConcluidos->links() }}
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    <script>
        function pushSubscription() {
            return {
                isSubscribed: false,
                showBanner: true,
                vapidPublicKey: "{{ env('VAPID_PUBLIC_KEY') }}",

                checkSubscription() {
                    if (!('Notification' in window)) {
                        this.showBanner = false;
                        return;
                    }
                    if (Notification.permission === 'granted') {
                        this.isSubscribed = true;
                    } else if (Notification.permission === 'denied') {
                        this.showBanner = false;
                    }
                },

                async toggleSubscription() {
                    const permission = await Notification.requestPermission();
                    if (permission === 'granted') {
                        this.isSubscribed = true;
                        this.subscribeUser();
                    } else if (permission === 'denied') {
                        this.showBanner = false;
                        alert('Has rechazado las notificaciones. Si cambias de opinión, ajusta los permisos de tu navegador.');
                    }
                },

                async subscribeUser() {
                    try {
                        const registration = await navigator.serviceWorker.ready;
                        const subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
                        });

                        const response = await fetch("{{ route('notifications.subscribe') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(subscription)
                        });

                        const text = await response.text();

                        try {
                            const jsonStart = text.indexOf('{');
                            const cleanJson = text.substring(jsonStart);
                            const data = JSON.parse(cleanJson);

                            if (data.success) {
                                alert('¡Suscripción guardada en la base de datos de la CEAA!');
                                this.isSubscribed = true;
                                this.showBanner = false;
                            }
                        } catch (e) {
                            console.error("Error al parsear respuesta limpia:", text);
                        }

                    } catch (error) {
                        console.error('Error al suscribirse:', error);
                    }
                },

                urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) {
                        outputArray[i] = rawData.charCodeAt(i);
                    }
                    return outputArray;
                }
            }
        }
    </script>
</x-app-layout>
