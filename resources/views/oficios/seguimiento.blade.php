<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Seguimiento General de Turnos
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Supervisión integral de correspondencia, áreas turnadas, responsables y estados de solventación</p>
            </div>
            <div>
                <a href="{{ route('oficios.reporteDiario') }}" 
                    class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg font-bold shadow-xs hover:shadow-md transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Reporte Diario
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filtros y Buscador --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5">
                <form action="{{ route('oficios.seguimiento') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Buscar oficio, asunto, remitente, área o personal..." 
                            class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa transition">
                    </div>

                    <div class="w-full sm:w-56">
                        <select name="estatus" onchange="this.form.submit()" 
                            class="w-full py-2 px-3 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa bg-white transition">
                            <option value="">-- Todos los Estados --</option>
                            <option value="Turnado" {{ request('estatus') == 'Turnado' ? 'selected' : '' }}>Turnado</option>
                            <option value="Recibido" {{ request('estatus') == 'Recibido' ? 'selected' : '' }}>Recibido</option>
                            <option value="Asignado" {{ request('estatus') == 'Asignado' ? 'selected' : '' }}>Asignado</option>
                            <option value="Solventado" {{ request('estatus') == 'Solventado' ? 'selected' : '' }}>Solventado</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Buscar
                        </button>
                        @if(request()->filled('search') || request()->filled('estatus'))
                            <a href="{{ route('oficios.seguimiento') }}" class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla de Datos --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Monitoreo General de Turnos</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $turnos->total() }} registros
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Página {{ $turnos->currentPage() }} de {{ $turnos->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">Número de Oficio</th>
                                <th class="py-3 px-4 text-left">Remitente / Asunto</th>
                                <th class="py-3 px-4 text-left">Dirección Turnada</th>
                                <th class="py-3 px-4 text-left">Personal Operativo</th>
                                <th class="py-3 px-4 text-center">Estatus del Turno</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($turnos as $turno)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    {{-- No. Oficio --}}
                                    <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                        <a href="{{ route('oficios.show', [$turno->oficio_id, 'mode' => 'gestion']) }}" class="hover:underline">
                                            {{ $turno->numero_oficio }}
                                        </a>
                                    </td>
                                    
                                    {{-- Remitente / Asunto --}}
                                    <td class="py-3.5 px-4 max-w-sm">
                                        <p class="font-bold text-slate-800 line-clamp-1" title="{{ $turno->remitente }}">{{ $turno->remitente }}</p>
                                        <p class="text-slate-500 italic text-[11px] mt-0.5 line-clamp-1" title="{{ $turno->asunto }}">{{ $turno->asunto }}</p>
                                    </td>
                                    
                                    {{-- Dirección Turnada --}}
                                    <td class="py-3.5 px-4 font-bold text-slate-800 uppercase">
                                        <div class="text-[11px] text-slate-700">
                                            🏢 {{ $turno->area_name }}
                                        </div>
                                    </td>
                                    
                                    {{-- Responsable Asignado --}}
                                    <td class="py-3.5 px-4 text-slate-700">
                                        @if(isset($turno->subareas) && $turno->subareas->isNotEmpty())
                                            <div class="flex flex-col gap-1.5">
                                                @foreach($turno->subareas as $sa)
                                                    <div class="flex flex-col border-b border-slate-100 pb-1.5 last:border-0 last:pb-0">
                                                        <span class="font-bold text-[10px] text-slate-600 uppercase">
                                                            {{ $sa->subarea_name ?? 'Director (Jefe de Área)' }}:
                                                        </span>
                                                        @if($sa->user_name)
                                                            <span class="font-semibold text-slate-800 text-[11px]">{{ $sa->user_name }}</span>
                                                        @else
                                                            <span class="text-slate-400 italic text-[10px]">Por asignar...</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($turno->operativo_name)
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                <span class="font-bold text-slate-800">{{ $turno->operativo_name }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic font-medium">Por asignar...</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Estatus Badge --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            {{ $turno->turno_estatus == 'Turnado' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                                            {{ $turno->turno_estatus == 'Recibido' ? 'bg-slate-100 text-slate-700 border border-slate-200' : '' }}
                                            {{ $turno->turno_estatus == 'Asignado' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                            {{ $turno->turno_estatus == 'Solventado' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                        ">
                                            {{ $turno->turno_estatus }}
                                        </span>
                                    </td>
                                    
                                    {{-- Acciones --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <a href="{{ route('oficios.show', [$turno->oficio_id, 'mode' => 'gestion']) }}"
                                            class="px-2.5 py-1 bg-slate-800 hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver Expediente
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No se encontraron turnos registrados o que coincidan con la búsqueda.</p>
                                            <p class="text-xs text-slate-400">Intenta modificando los criterios de filtrado.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $turnos->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
