<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Transparencia PNT: Procedimientos de Adjudicación') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Control y reporte de contratos, adquisiciones y licitaciones públicas</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pnt.export') }}" 
                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase text-xs tracking-wider rounded-lg transition shadow-xs hover:shadow-md flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    Exportar a Excel
                </a>
                
                @if(Auth::user()->canEditPntSection(1))
                    <a href="{{ route('pnt.create') }}" 
                       class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white font-bold uppercase text-xs tracking-wider rounded-lg transition shadow-xs hover:shadow-md flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Registro
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Mensajes de Estatus --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs font-semibold rounded-r-lg shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-xs font-semibold rounded-r-lg shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Tabla Principal --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Procedimientos Registrados</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ count($procedimientos) }} registros
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">Expediente / Folio</th>
                                <th class="py-3 px-4 text-center">Ejercicio</th>
                                <th class="py-3 px-4 text-left">Procedimiento</th>
                                <th class="py-3 px-4 text-left">Proveedor Ganador</th>
                                <th class="py-3 px-4 text-right">Monto Max.</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($procedimientos as $p)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                        {{ $p->numero_expediente }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                        {{ $p->ejercicio }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="bg-slate-100 text-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-slate-200 uppercase inline-block">
                                            {{ $p->tipo_procedimiento }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-800 max-w-xs truncate" title="{{ $p->proveedor_ganador_nombre }}">
                                        {{ $p->proveedor_ganador_nombre ?: 'Pendiente de capturar...' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-black text-slate-800 whitespace-nowrap">
                                        {{ $p->monto_contrato_max ? '$' . number_format($p->monto_contrato_max, 2) : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('pnt.edit', $p) }}" 
                                               class="px-2.5 py-1 bg-sky-600 hover:bg-sky-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Editar / Ver
                                            </a>
                                            
                                            @if(Auth::user()->role === 'admin')
                                                <form action="{{ route('pnt.destroy', $p) }}" method="POST" 
                                                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro de transparencia y todas sus subtablas?');" 
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="px-2.5 py-1 bg-rose-700 hover:bg-rose-800 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No se han registrado procedimientos de adjudicación para la PNT en este ejercicio.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
