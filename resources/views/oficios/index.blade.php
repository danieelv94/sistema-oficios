<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    {{ __('Entrada de Correspondencia') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Buzón central y registro de oficios externos recibidos</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'correspondencia' || (Auth::user()->role == 'jefe_area' && Auth::user()->area_id == 2))
                    <a href="{{ route('oficios.reporteEntradas') }}"
                        class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm hover:shadow transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Reporte Diario
                    </a>
                @endif
                @if(in_array(Auth::user()->role, ['admin', 'recepcionista', 'correspondencia']))
                    <a href="{{ route('oficios.create') }}"
                        class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm hover:shadow transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Oficio
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/70 min-h-screen" x-data="{ showCancelModal: false, cancelOficioId: null, cancelOficioNumero: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs font-bold rounded-r-xl shadow-xs flex items-center justify-between no-print">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Bloque de Filtros y Buscador --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5 no-print">
                <form action="{{ route('oficios.index') }}" method="GET" class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        <div class="sm:col-span-2 lg:col-span-2">
                            <label for="search" class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                Búsqueda Rápida
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" id="search"
                                    placeholder="No. oficio, remitente, asunto..."
                                    class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-guinda-ceaa/20 focus:border-guinda-ceaa transition bg-white"
                                    value="{{ request('search') }}">
                            </div>
                        </div>

                        <div>
                            <label for="estatus" class="block text-xs font-bold uppercase text-slate-600 mb-1">
                                Estatus del Oficio
                            </label>
                            <select name="estatus" id="estatus" onchange="this.form.submit()"
                                class="w-full py-2 px-3 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-guinda-ceaa/20 focus:border-guinda-ceaa bg-white transition">
                                <option value="Todos" {{ request('estatus', 'Pendiente') == 'Todos' ? 'selected' : '' }}>-- Todos los Estados --</option>
                                <option value="Pendiente" {{ request('estatus', 'Pendiente') == 'Pendiente' ? 'selected' : '' }}>Pendiente de Turnar</option>
                                <option value="Turnado" {{ request('estatus') == 'Turnado' ? 'selected' : '' }}>Turnado</option>
                                <option value="En Proceso" {{ request('estatus') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="Atendido" {{ request('estatus') == 'Atendido' ? 'selected' : '' }}>Atendido</option>
                                <option value="Solventado" {{ request('estatus') == 'Solventado' ? 'selected' : '' }}>Solventado</option>
                                <option value="Cancelado" {{ request('estatus') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>

                        <div class="flex items-end justify-end gap-2">
                            <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Buscar
                            </button>
                            @if(request()->filled('search') || request('estatus', 'Pendiente') !== 'Pendiente')
                                <a href="{{ route('oficios.index') }}"
                                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabla de Resultados Homologada --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-700 uppercase tracking-wide">
                            Resultados de Correspondencia
                        </h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $oficios->total() }} registros
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 font-medium">
                        Página {{ $oficios->currentPage() }} de {{ $oficios->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100">
                        <thead>
                            <tr class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider border-b border-slate-200/60">
                                <th class="py-3.5 px-4 text-left">Número de Oficio</th>
                                <th class="py-3.5 px-4 text-left">Remitente</th>
                                <th class="py-3.5 px-4 text-left">Asunto</th>
                                <th class="py-3.5 px-4 text-center">Estatus</th>
                                <th class="py-3.5 px-4 text-center no-print">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($oficios as $oficio)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    {{-- Número de Oficio --}}
                                    <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                        <a href="{{ route('oficios.show', $oficio->id) }}" class="hover:underline inline-flex items-center gap-1" title="Ver detalles del oficio">
                                            {{ $oficio->numero_oficio }}
                                        </a>
                                        @if($oficio->fecha_recepcion)
                                            <div class="text-[10px] text-slate-400 font-normal mt-0.5">
                                                F. Rec: {{ \Carbon\Carbon::parse($oficio->fecha_recepcion)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Remitente --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800">{{ $oficio->remitente }}</div>
                                        @if($oficio->numero_oficio_dependencia)
                                            <div class="text-[10.5px] text-slate-400 font-medium truncate mt-0.5">
                                                Ref: {{ $oficio->numero_oficio_dependencia }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Asunto --}}
                                    <td class="py-3.5 px-4 max-w-sm">
                                        <div class="text-slate-600 font-medium line-clamp-2" title="{{ $oficio->asunto }}">
                                            {{ $oficio->asunto }}
                                        </div>
                                    </td>

                                    {{-- Estatus --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @php
                                            $st = $oficio->estatus ?: 'Pendiente';
                                        @endphp
                                        @if($st === 'Cancelado')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase">
                                                Cancelado
                                            </span>
                                        @elseif($st === 'Solventado' || $st === 'Atendido')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                                {{ $st }}
                                            </span>
                                        @elseif($st === 'Turnado' || $st === 'En Proceso')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                                {{ $st }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                                {{ $st }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="py-3.5 px-4 text-center no-print whitespace-nowrap">
                                        <div class="flex justify-center items-center gap-1.5">
                                            @if($oficio->pdf_path)
                                                <a href="{{ asset('storage/' . $oficio->pdf_path) }}" target="_blank"
                                                    class="px-2.5 py-1 bg-rose-700 hover:bg-rose-800 text-white rounded text-[10px] font-bold uppercase tracking-wider transition shadow-xs inline-flex items-center gap-1"
                                                    title="Ver PDF original">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                    PDF
                                                </a>
                                            @endif

                                            @if(empty($oficio->estatus) || $oficio->estatus == 'Recibido' || $oficio->estatus == 'Pendiente')
                                                <a href="{{ route('oficios.vistaTurnado', $oficio->id) }}"
                                                    class="px-2.5 py-1 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded text-[10px] font-bold uppercase tracking-wider transition shadow-xs inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                    Turnar
                                                </a>
                                            @else
                                                <a href="{{ route('oficios.show', $oficio->id) }}"
                                                    class="px-2.5 py-1 bg-slate-800 hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider transition shadow-xs inline-flex items-center gap-1"
                                                    title="Ver Expediente">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver
                                                </a>
                                            @endif

                                            @if(in_array(Auth::user()->role, ['admin', 'correspondencia', 'recepcionista']))
                                                <a href="{{ route('oficios.edit', $oficio->id) }}"
                                                    class="p-1 text-slate-500 hover:text-guinda-ceaa hover:bg-slate-100 rounded transition"
                                                    title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            @endif

                                            @if($oficio->estatus !== 'Cancelado' && in_array(Auth::user()->role, ['admin', 'correspondencia']))
                                                <button type="button" @click="showCancelModal = true; cancelOficioId = {{ $oficio->id }}; cancelOficioNumero = '{{ $oficio->numero_oficio }}'"
                                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition"
                                                    title="Cancelar Oficio">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No se encontraron oficios registrados con los criterios seleccionados.</p>
                                            <p class="text-xs text-slate-400">Prueba ajustando los filtros o el texto de búsqueda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/50 no-print">
                    {{ $oficios->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        {{-- MODAL DE CANCELACIÓN --}}
        <div x-show="showCancelModal" 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4" 
             style="display: none;" 
             x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-xs transition-opacity" @click="showCancelModal = false"></div>

            <div class="relative w-full max-w-lg mx-auto z-[100000] transform transition-all shadow-2xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white rounded-2xl overflow-hidden border-t-8 border-rose-600 shadow-2xl">
                    <div class="p-5 border-b border-slate-100 flex items-center">
                        <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black uppercase tracking-tight text-slate-800">
                                Cancelar Oficio
                            </h3>
                            <p class="text-xs text-slate-500">Esta acción invalidará el oficio en el sistema</p>
                        </div>
                    </div>

                    <form :action="'/oficios/' + cancelOficioId + '/cancelar'" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="p-6 space-y-3">
                            <p class="text-xs font-bold text-slate-700 uppercase">
                                ¿Estás seguro que deseas cancelar el oficio: <span class="text-rose-600 font-black" x-text="cancelOficioNumero"></span>?
                            </p>
                            <div>
                                <label class="block font-bold text-[10px] text-slate-500 uppercase mb-1">Motivo Detallado de Cancelación</label>
                                <textarea name="motivo_cancelacion" required rows="4" 
                                    placeholder="Escribe aquí el motivo institucional de la cancelación..."
                                    class="w-full text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 p-3"
                                    x-init="$watch('showCancelModal', value => { if(!value) $el.value = '' })"></textarea>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                            <button type="button" @click="showCancelModal = false"
                                class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition">
                                Cerrar
                            </button>
                            <button type="submit"
                                class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm hover:shadow">
                                Confirmar Cancelación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
