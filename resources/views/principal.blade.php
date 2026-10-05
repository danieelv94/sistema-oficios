<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ __('Panel de Control') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Bienvenido(a) a la plataforma institucional de gestión documental CEAA</p>
            </div>
            <div class="text-xs font-bold text-slate-500 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-guinda-ceaa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ now()->translatedFormat('l, d \\d\\e F \\d\\e Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/70 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. Tarjetas Resumen de Estadísticas (Estilo Homologado) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                {{-- Card 1: Total Recibidos --}}
                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 hover:border-slate-300 hover:shadow-md transition duration-200 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase text-slate-500 tracking-wider">Total Recibidos</p>
                        <p class="text-3xl font-black text-slate-800 mt-1">{{ number_format($totalOficios ?? 0) }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Oficios en el organismo</p>
                    </div>
                    <div class="p-3.5 bg-slate-100 text-slate-700 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>

                {{-- Card 2: Oficios Turnados (Las que te turnaron) --}}
                <a href="{{ route('oficios.gestion') }}"
                    class="group bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 hover:border-guinda-ceaa/50 hover:shadow-md transition duration-200 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase text-guinda-ceaa tracking-wider group-hover:underline">Oficios Turnados</p>
                        <p class="text-3xl font-black text-slate-800 mt-1">{{ number_format($misTareas ?? 0) }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Turnados a tu atención</p>
                    </div>
                    <div class="p-3.5 bg-guinda-ceaa/10 text-guinda-ceaa rounded-xl group-hover:bg-guinda-ceaa group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                        </svg>
                    </div>
                </a>

                {{-- Card 3: Mis Tareas Pendientes (Las que tienes pendientes) --}}
                <a href="{{ route('oficios.gestion', ['filtro' => 'pendientes']) }}"
                    class="group bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 hover:border-amber-500/60 hover:shadow-md transition duration-200 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase text-amber-600 tracking-wider group-hover:underline">Tareas Pendientes</p>
                        <p class="text-3xl font-black text-amber-700 mt-1">{{ number_format($misTareasPendientes ?? 0) }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Por atender o solventar</p>
                    </div>
                    <div class="p-3.5 bg-amber-50 text-amber-600 rounded-xl group-hover:bg-amber-500 group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>
            </div>

            {{-- 2. Lista de Tareas Pendientes Recientes (si tiene pendientes) --}}
            @if(isset($ultimasTareasPendientes) && $ultimasTareasPendientes->isNotEmpty())
                <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Mis Tareas Pendientes de Atención</h3>
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-black rounded-full">
                                {{ $misTareasPendientes }} pendientes
                            </span>
                        </div>
                        <a href="{{ route('oficios.gestion', ['filtro' => 'pendientes']) }}"
                            class="text-xs font-bold text-guinda-ceaa hover:text-guinda-ceaa-hover inline-flex items-center gap-1 transition">
                            <span>Ver todas en Gestión de Turnos</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                            <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 text-left">Número de Oficio</th>
                                    <th class="py-3 px-4 text-center">Tipo</th>
                                    <th class="py-3 px-4 text-left">Origen / Remitente</th>
                                    <th class="py-3 px-4 text-left">Asunto</th>
                                    <th class="py-3 px-4 text-left">Instrucción</th>
                                    <th class="py-3 px-4 text-center">Estatus</th>
                                    <th class="py-3 px-4 text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($ultimasTareasPendientes as $oficioPendiente)
                                    @php
                                        $areaPend = $oficioPendiente->areas->where('id', Auth::user()->area_id)->first() ?? $oficioPendiente->areas->first();
                                        $pivotPend = $areaPend ? $areaPend->pivot : null;
                                        $subareaOficioPend = null;
                                        if ($pivotPend) {
                                            $subQuery = \App\Models\SubareaOficio::where('area_oficio_id', $pivotPend->id);
                                            if (Auth::user()->role === 'subdirector' || (Auth::user()->role === 'admin' && Auth::user()->subarea_id !== null)) {
                                                $subQuery->where('subarea_id', Auth::user()->subarea_id);
                                            } else {
                                                $subQuery->where('user_id', Auth::id());
                                            }
                                            $subareaOficioPend = $subQuery->first();
                                        }
                                        $estatusP = $subareaOficioPend ? $subareaOficioPend->estatus : ($pivotPend ? $pivotPend->estatus : 'Pendiente');
                                        $isInterno = ($oficioPendiente->tipo_correspondencia === 'Interna');
                                    @endphp
                                    <tr class="hover:bg-slate-50/75 transition duration-150">
                                        <td class="py-3 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                            <a href="{{ route('oficios.show', [$oficioPendiente->id, 'mode' => in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']) ? 'gestion' : 'operativo']) }}" class="hover:underline">
                                                {{ $oficioPendiente->numero_oficio }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            @if($isInterno)
                                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                                    Interno
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                                    Externo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-slate-700 whitespace-nowrap">
                                            @if($isInterno)
                                                <span class="font-bold text-purple-800">{{ $oficioPendiente->areaOrigen->nombre ?? 'Dirección Interna' }}</span>
                                            @else
                                                <span class="font-semibold text-slate-700">{{ $oficioPendiente->remitente }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-slate-600 max-w-xs truncate" title="{{ $oficioPendiente->asunto }}">
                                            {{ $oficioPendiente->asunto }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-700 max-w-xs truncate">
                                            {{ $pivotPend->instruccion ?? 'Sin instrucción' }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                                {{ $estatusP == 'Turnado' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                                                {{ $estatusP == 'Recibido' ? 'bg-slate-100 text-slate-700 border border-slate-200' : '' }}
                                                {{ $estatusP == 'Asignado' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                                {{ $estatusP == 'Notificado' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                                {{ $estatusP == 'Solventado' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                            ">
                                                {{ $estatusP }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <a href="{{ route('oficios.show', [$oficioPendiente->id, 'mode' => in_array(Auth::user()->role, ['admin', 'jefe_area', 'secretaria_area']) ? 'gestion' : 'operativo']) }}"
                                                class="px-2.5 py-1 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded text-[10px] font-bold uppercase tracking-wider transition inline-flex items-center gap-1">
                                                <span>Atender / Ver</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- 2. Acciones Rápidas (Módulos principales del sistema) --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-wider">Acceso a Módulos Operativos</h3>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                    {{-- Nuevo Oficio (Solo Recepción y Admin) --}}
                    @if(in_array(Auth::user()->role, ['admin', 'recepcionista', 'correspondencia']))
                        <a href="{{ route('oficios.create') }}"
                            class="group bg-white p-6 rounded-xl shadow-xs hover:shadow-lg transition-all duration-200 flex items-start gap-4 border border-slate-200/80 hover:border-guinda-ceaa/50 transform hover:-translate-y-0.5">
                            <div
                                class="bg-guinda-ceaa/10 p-3.5 rounded-xl text-guinda-ceaa group-hover:bg-guinda-ceaa group-hover:text-white transition duration-200 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight group-hover:text-guinda-ceaa transition">Nuevo Oficio</h3>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-guinda-ceaa group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Captura de correspondencia externa y entrada de documentos</p>
                            </div>
                        </a>
                    @endif

                    {{-- Entrada de Correspondencia (Solo Correspondencia, Admin y Director de Gestión Institucional) --}}
                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'correspondencia' || (Auth::user()->role == 'jefe_area' && Auth::user()->area_id == 2))
                        <a href="{{ route('oficios.index') }}"
                            class="group bg-white p-6 rounded-xl shadow-xs hover:shadow-lg transition-all duration-200 flex items-start gap-4 border border-slate-200/80 hover:border-slate-800/50 transform hover:-translate-y-0.5">
                            <div
                                class="bg-slate-100 p-3.5 rounded-xl text-slate-700 group-hover:bg-slate-800 group-hover:text-white transition duration-200 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight group-hover:text-slate-900 transition">Entrada de Correspondencia</h3>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-700 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Turnar oficios recibidos a las direcciones correspondientes</p>
                            </div>
                        </a>
                    @endif

                    {{-- Gestión de Turnos (Jefes, Secretarias y Operativos, o Admin) --}}
                    @if((Auth::user()->area_id && !in_array(Auth::user()->role, ['recepcionista', 'correspondencia'])) || Auth::user()->role == 'admin')
                        <a href="{{ route('oficios.gestion') }}"
                            class="group bg-white p-6 rounded-xl shadow-xs hover:shadow-lg transition-all duration-200 flex items-start gap-4 border border-slate-200/80 hover:border-dorado-ocre/50 transform hover:-translate-y-0.5">
                            <div
                                class="bg-dorado-ocre/10 p-3.5 rounded-xl text-dorado-ocre group-hover:bg-dorado-ocre group-hover:text-white transition duration-200 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight group-hover:text-dorado-ocre transition">Gestión de Turnos</h3>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-dorado-ocre group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Ver, delegar y solventar correspondencia de tu área</p>
                            </div>
                        </a>
                    @endif

                    {{-- Seguimiento General --}}
                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'correspondencia' || Auth::user()->role == 'dg' || Auth::user()->id == 326 || (Auth::user()->role == 'jefe_area' && Auth::user()->area_id == 2))
                        <a href="{{ route('oficios.seguimiento') }}"
                            class="group bg-white p-6 rounded-xl shadow-xs hover:shadow-lg transition-all duration-200 flex items-start gap-4 border border-slate-200/80 hover:border-emerald-500/50 transform hover:-translate-y-0.5">
                            <div
                                class="bg-emerald-50 p-3.5 rounded-xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition duration-200 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight group-hover:text-emerald-700 transition">Seguimiento General</h3>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Supervisión integral de todos los turnos y áreas</p>
                            </div>
                        </a>
                    @endif

                    {{-- Oficios Internos --}}
                    @if(Auth::user()->area_id || Auth::user()->role == 'admin')
                        <a href="{{ route('oficios.internos.index') }}"
                            class="group bg-white p-6 rounded-xl shadow-xs hover:shadow-lg transition-all duration-200 flex items-start gap-4 border border-slate-200/80 hover:border-blue-500/50 transform hover:-translate-y-0.5">
                            <div
                                class="bg-blue-50 p-3.5 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition duration-200 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight group-hover:text-blue-700 transition">Oficios Internos</h3>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Correspondencia oficial entre direcciones del organismo</p>
                            </div>
                        </a>
                    @endif

                    {{-- Oficios de Comisión --}}
                    <a href="{{ route('comisiones.index') }}"
                        class="group bg-white p-6 rounded-xl shadow-xs hover:shadow-lg transition-all duration-200 flex items-start gap-4 border border-slate-200/80 hover:border-purple-500/50 transform hover:-translate-y-0.5">
                        <div
                            class="bg-purple-50 p-3.5 rounded-xl text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition duration-200 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight group-hover:text-purple-700 transition">Oficios de Comisión</h3>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-purple-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">Solicitud, control y expedientes de comisiones oficiales</p>
                        </div>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>