<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Panel de Control de Oficios') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Centro de control, correspondencia y seguimiento operativo</p>
            </div>
            @if(in_array(Auth::user()->role, ['admin', 'recepcionista']))
                <a href="{{ route('oficios.create') }}" 
                   class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg font-bold shadow-xs hover:shadow-md transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Registrar Oficio
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stat KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Total Recibidos</p>
                        <p class="text-2xl font-black text-slate-800 mt-1">{{ $correspondenciaGeneral->total() ?? 0 }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Correspondencia general</p>
                    </div>
                    <div class="p-3 bg-guinda-ceaa/10 text-guinda-ceaa rounded-xl border border-guinda-ceaa/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Pendientes de Área</p>
                        <p class="text-2xl font-black text-slate-800 mt-1">{{ $gestionArea->total() ?? 0 }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">En bandeja de dirección</p>
                    </div>
                    <div class="p-3 bg-slate-100 text-slate-700 rounded-xl border border-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-dorado-ocre tracking-wider">Mis Tareas</p>
                        <p class="text-2xl font-black text-dorado-ocre mt-1">{{ $misTurnosAsignados->total() ?? 0 }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Turnos personales activos</p>
                    </div>
                    <div class="p-3 bg-amber-50 text-dorado-ocre rounded-xl border border-amber-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 1. CORRESPONDENCIA GENERAL (Admin y Recepción) --}}
            @if(in_array(Auth::user()->role, ['admin', 'recepcionista', 'correspondencia']))
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Entrada de Correspondencia (General)</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $correspondenciaGeneral->total() }} oficios
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">No. Oficio / PDF</th>
                                <th class="py-3 px-4 text-left">Remitente</th>
                                <th class="py-3 px-4 text-left">Asunto</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($correspondenciaGeneral as $oficio)
                            <tr class="hover:bg-slate-50/75 transition duration-150">
                                <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                    <div class="font-black text-sm">{{ $oficio->numero_oficio }}</div>
                                    @if($oficio->pdf_path)
                                        <span class="text-[10px] text-rose-600 font-bold uppercase tracking-wider">[PDF Cargado]</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $oficio->remitente }}</td>
                                <td class="py-3.5 px-4 text-slate-600 max-w-md truncate" title="{{ $oficio->asunto }}">{{ $oficio->asunto }}</td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('oficios.show', ['oficio' => $oficio, 'mode' => 'recepcion']) }}" 
                                            class="px-2.5 py-1 bg-slate-800 hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                            Turnar
                                        </a>
                                        @if(in_array(Auth::user()->role, ['admin', 'correspondencia', 'recepcionista']))
                                            <a href="{{ route('oficios.edit', $oficio) }}" 
                                                class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Editar
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-slate-400 italic">Sin correspondencia registrada.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $correspondenciaGeneral->appends(request()->query())->links() }}
                </div>
            </div>
            @endif

            {{-- 2. GESTIÓN DE DIRECCIÓN (Jefes y Secretarias) --}}
            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'jefe_area' || Auth::user()->role == 'secretaria_area')
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Gestión de la Dirección (Para Asignar)</h3>
                        <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-black rounded-full">
                            {{ $gestionArea->total() }} turnos
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">No. Oficio</th>
                                <th class="py-3 px-4 text-left">Instrucción Recibida</th>
                                <th class="py-3 px-4 text-center">Asignación</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($gestionArea as $oficio)
                            <tr class="hover:bg-slate-50/75 transition duration-150">
                                <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">{{ $oficio->numero_oficio }}</td>
                                <td class="py-3.5 px-4 text-slate-600 font-medium">
                                    {{ $oficio->areas->where('id', Auth::user()->area_id)->first()->pivot->instruccion ?? 'Sin instrucción' }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php 
                                        $userAsignadoId = $oficio->areas->where('id', Auth::user()->area_id)->first()->pivot->user_id;
                                        $userAsignado = \App\Models\User::find($userAsignadoId);
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $userAsignado ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $userAsignado ? $userAsignado->name : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <a href="{{ route('oficios.show', ['oficio' => $oficio, 'mode' => 'gestion']) }}" 
                                       class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Asignar Personal
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-slate-400 italic">No hay oficios turnados a su dirección.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- 3. MIS TURNOS ASIGNADOS (Personal Operativo) --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Mis Tareas Pendientes</h3>
                        <span class="px-2.5 py-0.5 bg-amber-50 text-dorado-ocre border border-amber-200 text-[11px] font-black rounded-full">
                            {{ $misTurnosAsignados->total() }} asignadas
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($misTurnosAsignados as $oficio)
                        <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 shadow-2xs hover:shadow-xs transition">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-black text-guinda-ceaa uppercase">{{ $oficio->numero_oficio }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase">{{ \Carbon\Carbon::parse($oficio->fecha_recepcion)->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-xs text-slate-800 font-bold mb-2 line-clamp-1" title="{{ $oficio->asunto }}">{{ $oficio->asunto }}</p>
                            <div class="bg-white p-2.5 rounded-lg border border-slate-200 text-[11px] italic text-slate-500 mb-3 line-clamp-2">
                                "{{ $oficio->areas->where('pivot.user_id', Auth::user()->id)->first()->pivot->instruccion ?? 'Sin instrucciones' }}"
                            </div>
                            <a href="{{ route('oficios.show', ['oficio' => $oficio, 'mode' => 'operativo']) }}" 
                               class="block text-center w-full py-1.5 bg-slate-800 hover:bg-guinda-ceaa text-white rounded-lg text-[10px] font-bold uppercase tracking-wider transition shadow-xs">
                                Ver Detalles / Atender
                            </a>
                        </div>
                        @empty
                        <div class="col-span-full py-8 text-center text-slate-400 italic">No tienes tareas asignadas actualmente.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>