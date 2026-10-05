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
                padding: 8px !important;
                font-size: 9pt !important;
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
                    {{ __('Historial de Oficios de Comisión') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    {{ Auth::user()->role == 'admin' ? 'Registro General Estatal de comisiones emitidas' : 'Historial de comisiones correspondientes a su área' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(Auth::user()->role === 'admin' && request('search'))
                    <button onclick="window.print()"
                        class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg font-bold shadow-xs transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Imprimir
                    </button>
                @endif
                <a href="{{ route('comisiones.create') }}"
                    class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg font-bold shadow-xs hover:shadow-md transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Solicitar Nueva Comisión
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Encabezado de Reporte para el Admin --}}
            @if(Auth::user()->role === 'admin')
                <div class="printable-header mb-6">
                    <div class="flex justify-between items-center border-b-2 border-guinda-ceaa pb-4">
                        <img src="{{ asset('images/encabezado.png') }}" style="height: 60px;">
                        <div class="text-right">
                            <h1 class="text-xl font-bold uppercase text-guinda-ceaa">CEAA HIDALGO</h1>
                            <h2 class="text-lg font-bold">Reporte de Comisiones</h2>
                            <p class="text-xs">Fecha: {{ now()->format('d/m/Y H:i') }} | Criterios: {{ request('search') ?? 'General' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Buscador (Solo Admin y Secretaria) --}}
            @if(in_array(Auth::user()->role, ['admin', 'secretaria_area']))
                <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5 no-print">
                    <form action="{{ route('comisiones.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                        <div class="relative flex-1 w-full">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search"
                                placeholder="Buscar por oficio, fecha o comisionado (separa por comas: 15 de mayo, Daniel...)"
                                class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa transition"
                                value="{{ request('search') }}">
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="submit"
                                class="w-full sm:w-auto px-5 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Buscar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('comisiones.index') }}"
                                    class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            @endif

            {{-- Tabla de Resultados --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60 no-print">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                            {{ Auth::user()->role == 'admin' ? 'Registro General Estatal' : 'Registro de Comisiones del Área' }}
                        </h3>
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
                                <th class="py-3 px-4 text-left">Fecha Emisión</th>
                                @if(in_array(Auth::user()->role, ['admin', 'secretaria_area']))
                                    <th class="py-3 px-4 text-left">Comisionado</th>
                                @endif
                                <th class="py-3 px-4 text-left">Días Comisión</th>
                                <th class="py-3 px-4 text-left">Actividad / Lugar</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-center no-print">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($comisiones as $comision)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                        <a href="{{ route('comisiones.show', $comision) }}" class="hover:underline">
                                            {{ $comision->oficio_numero }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $comision->created_at->format('d/m/Y') }}
                                    </td>

                                    @if(in_array(Auth::user()->role, ['admin', 'secretaria_area']))
                                        <td class="py-3.5 px-4 max-w-xs">
                                            <div class="font-bold text-slate-800">
                                                {{ $comision->user ? $comision->user->name : 'N/A' }}
                                                @if($comision->user && $comision->user->trashed())
                                                    <span class="text-[9px] bg-rose-100 text-rose-600 px-1 rounded ml-1 font-bold">INACTIVO</span>
                                                @endif
                                            </div>
                                            <div class="text-[10.5px] text-slate-500 font-medium truncate mt-0.5">
                                                🏢 {{ $comision->user->area->name ?? 'S/A' }}
                                            </div>
                                        </td>
                                    @endif

                                    <td class="py-3.5 px-4 font-bold text-guinda-ceaa whitespace-nowrap">
                                        {{ $comision->dias_comision }}
                                    </td>
                                    <td class="py-3.5 px-4 max-w-xs text-slate-600">
                                        <div class="font-bold text-slate-800 truncate" title="{{ $comision->lugar }}">📍 {{ $comision->lugar }}</div>
                                        <div class="text-xs italic text-slate-500 line-clamp-1 mt-0.5" title="{{ $comision->actividad }}">
                                            "{{ Str::limit($comision->actividad, 55) }}"
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold rounded-full uppercase
                                            {{ $comision->status === 'Cancelado' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                            {{ $comision->status ?: 'Autorizado' }}
                                        </span>
                                    </td>
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
                                            @if(Auth::user()->role == 'admin' && $comision->status !== 'Cancelado')
                                                <a href="{{ route('comisiones.edit', $comision) }}"
                                                    class="p-1 text-slate-500 hover:text-guinda-ceaa rounded transition" title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('comisiones.cancelar', $comision) }}" method="POST"
                                                    onsubmit="return confirm('¿Seguro que deseas cancelar esta comisión?');" class="inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded transition"
                                                        title="Cancelar">
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
                                    <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No se encontraron comisiones registradas.</p>
                                            <p class="text-xs text-slate-400">Las nuevas comisiones solicitadas se mostrarán aquí.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100 bg-slate-50/60 no-print">
                    {{ $comisiones->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>