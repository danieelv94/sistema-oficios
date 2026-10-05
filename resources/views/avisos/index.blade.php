<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    {{ __('Historial de Circulares Emitidas') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Gestión y control de comunicados, circulares y lecturas institucionales</p>
            </div>
            @if(in_array(Auth::user()->role, ['admin', 'secretaria_area', 'jefe_area']))
                <a href="{{ route('avisos.create') }}"
                    class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg font-bold shadow-xs hover:shadow-md transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Circular
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Registro de Circulares</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $avisos->total() }} circulares
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Página {{ $avisos->currentPage() }} de {{ $avisos->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">Circular</th>
                                <th class="py-3 px-4 text-center">Prioridad</th>
                                <th class="py-3 px-4 text-center">Lecturas / Cumplimiento</th>
                                <th class="py-3 px-4 text-center">Fecha Emisión</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($avisos as $aviso)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    <td class="py-3.5 px-4 max-w-sm">
                                        <div class="font-bold text-slate-800 text-sm">{{ $aviso->titulo }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            Emitido por: <strong class="font-semibold text-slate-700">{{ $aviso->autor ? $aviso->autor->name : 'Usuario Deshabilitado' }}</strong>
                                            ({{ $aviso->autor && $aviso->autor->area ? $aviso->autor->area->name : 'Área N/D' }})
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-full uppercase
                                            {{ $aviso->prioridad == 'Urgente' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ $aviso->prioridad }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap min-w-[160px]">
                                        <div class="text-xs font-bold text-slate-700">
                                            {{ $aviso->leidos }} / {{ $aviso->total }}
                                            @php $porcentaje = $aviso->total > 0 ? ($aviso->leidos / $aviso->total) * 100 : 0; @endphp
                                            <span class="text-[10px] text-slate-400 font-normal">({{ round($porcentaje) }}%)</span>
                                        </div>
                                        <div class="w-full max-w-xs mx-auto bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300"
                                                style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap text-slate-500">
                                        {{ $aviso->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <a href="{{ route('avisos.seguimiento', $aviso) }}"
                                            class="px-2.5 py-1 bg-slate-800 hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Seguimiento
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No hay circulares registradas.</p>
                                            <p class="text-xs text-slate-400">Las nuevas circulares emitidas se listarán aquí.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $avisos->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>