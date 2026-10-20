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
                    <svg class="w-6 h-6 text-amber-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ __('Comisiones de Supervisión de Obra (ODSO)') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Oficios de comisión de supervisión de obra emitidos (DIH y DVPH)
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
                <a href="{{ route('comisiones-odso.create') }}"
                    class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-bold shadow-xs hover:shadow-md transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Oficio ODSO
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Pestañas de Navegación entre Comisiones Normales y ODSO --}}
            <div class="flex items-center gap-2 border-b border-slate-200 no-print pb-1">
                <a href="{{ route('comisiones.index') }}"
                    class="px-4 py-2.5 rounded-t-xl text-xs font-bold transition flex items-center gap-2 border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-100/60">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Comisiones Normales (Institucionales)</span>
                </a>
                <a href="{{ route('comisiones-odso.index') }}"
                    class="px-4 py-2.5 rounded-t-xl text-xs font-bold transition flex items-center gap-2 border-b-2 border-amber-600 text-amber-900 bg-amber-50/70 shadow-xs">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Supervisión de Obra (ODSO)</span>
                    <span class="text-[10px] bg-amber-200/80 text-amber-900 px-1.5 py-0.2 rounded font-black">ODSO</span>
                </a>
            </div>

            {{-- Encabezado de Reporte para el Admin --}}
            @if(Auth::user()->role === 'admin')
                <div class="printable-header mb-6">
                    <div class="flex justify-between items-center border-b-2 border-amber-600 pb-4">
                        <img src="{{ asset('images/encabezado.png') }}" style="height: 60px;">
                        <div class="text-right">
                            <h1 class="text-xl font-bold uppercase text-amber-800">CEAA HIDALGO</h1>
                            <h2 class="text-lg font-bold">Reporte de Comisiones ODSO (Supervisión de Obra)</h2>
                            <p class="text-xs">Fecha: {{ now()->format('d/m/Y H:i') }} | Criterios: {{ request('search') ?? 'General' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Buscador --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5 no-print">
                <form action="{{ route('comisiones-odso.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" name="search"
                            placeholder="Buscar por No. oficio, comisionado, actividad o lugar..."
                            class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 transition"
                            value="{{ request('search') }}">
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit"
                            class="w-full sm:w-auto px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition shadow-xs">
                            Buscar
                        </button>

                        @if(request('search'))
                            <a href="{{ route('comisiones-odso.index') }}"
                                class="w-full sm:w-auto px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold text-center transition">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla de Oficios ODSO --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-amber-50/50 text-slate-700 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">No. Oficio ODSO</th>
                                <th class="py-3 px-4 text-left">Fecha Emisión</th>
                                <th class="py-3 px-4 text-left">Comisionado</th>
                                <th class="py-3 px-4 text-left">Días Comisión</th>
                                <th class="py-3 px-4 text-left">Actividad / Lugar</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-center no-print">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($comisiones as $comision)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    <td class="py-3.5 px-4 font-black text-amber-900 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('comisiones-odso.show', $comision) }}" class="hover:underline">
                                                {{ $comision->oficio_numero }}
                                            </a>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300">
                                                ODSO
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                        {{ $comision->created_at->format('d/m/Y') }}
                                    </td>

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

                                    <td class="py-3.5 px-4 font-bold text-amber-900 whitespace-nowrap">
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
                                            {{ $comision->status === 'Cancelado' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ $comision->status ?? 'Autorizado' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap no-print">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('comisiones-odso.show', $comision) }}"
                                                class="px-2.5 py-1 text-slate-600 hover:text-amber-800 hover:bg-slate-100 rounded text-xs font-semibold transition"
                                                title="Ver e Imprimir Oficio">
                                                Ver
                                            </a>
                                            @if(Auth::user()->role === 'admin')
                                                <a href="{{ route('comisiones-odso.edit', $comision) }}"
                                                    class="px-2.5 py-1 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded text-xs font-semibold transition"
                                                    title="Modificar oficio">
                                                    Editar
                                                </a>
                                                @if($comision->status !== 'Cancelado')
                                                    <form action="{{ route('comisiones-odso.cancelar', $comision) }}" method="POST"
                                                        onsubmit="return confirm('¿Seguro que deseas cancelar este oficio ODSO?');"
                                                        class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="px-2.5 py-1 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded text-xs font-semibold transition">
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <p class="font-bold text-sm text-slate-600">No se encontraron oficios de comisión ODSO</p>
                                            <p class="text-xs text-slate-400 mt-1">
                                                {{ request('search') ? 'Intenta con otros términos de búsqueda.' : 'Aún no se han generado oficios de supervisión de obra.' }}
                                            </p>
                                            <a href="{{ route('comisiones-odso.create') }}"
                                                class="mt-4 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition">
                                                Generar primer oficio ODSO
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($comisiones->hasPages())
                    <div class="p-4 border-t border-slate-100 no-print">
                        {{ $comisiones->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
