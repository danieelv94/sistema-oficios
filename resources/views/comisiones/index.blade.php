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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight no-print">
            {{ __('Historial de Oficios de Comisión') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Encabezado de Reporte para el Admin --}}
            @if(Auth::user()->role === 'admin')
                <div class="printable-header mb-6">
                    <div class="flex justify-between items-center border-b-2 border-guinda-ceaa pb-4">
                        <img src="{{ asset('images/encabezado.png') }}" style="height: 60px;">
                        <div class="text-right">
                            <h1 class="text-xl font-bold uppercase text-guinda-ceaa">CEAA HIDALGO</h1>
                            <h2 class="text-lg font-bold">Reporte de Comisiones</h2>
                            <p class="text-xs">Fecha: {{ now()->format('d/m/Y H:i') }} | Criterios:
                                {{ request('search') ?? 'General' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6 no-print">
                        <h3 class="text-lg font-bold text-gray-700">
                            {{ Auth::user()->role == 'admin' ? 'Registro General Estatal' : 'Registro de Área' }}
                        </h3>
                        <div class="flex gap-2">
                            @if(Auth::user()->role === 'admin' && request('search'))
                                <button onclick="window.print()"
                                    class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-md font-bold shadow-sm transition text-xs uppercase">
                                    🖨️ Imprimir Reporte
                                </button>
                            @endif
                            <a href="{{ route('comisiones.create') }}"
                                class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-md font-bold shadow-sm transition">
                                + Solicitar Nueva Comisión
                            </a>
                        </div>
                    </div>

                    {{-- Buscador (Solo Admin y Secretaria) --}}
                    @if(in_array(Auth::user()->role, ['admin', 'secretaria_area']))
                        <div class="mb-6 no-print">
                            <form action="{{ route('comisiones.index') }}" method="GET" class="flex gap-2">
                                <input type="text" name="search"
                                    placeholder="Separa por comas: 15 de mayo, 15/05, Daniel..."
                                    class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-guinda-ceaa focus:border-guinda-ceaa"
                                    value="{{ request('search') }}">
                                <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-black transition">Buscar</button>
                                @if(request('search'))
                                    <a href="{{ route('comisiones.index') }}"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Limpiar</a>
                                @endif
                            </form>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-100">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b text-gray-600 text-xs font-bold uppercase tracking-wider">
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
                            <tbody class="divide-y divide-gray-100">
                                @forelse($comisiones as $comision)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-3 px-4 text-sm font-bold text-guinda-ceaa">
                                            {{ $comision->oficio_numero }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            {{ $comision->created_at->format('d/m/Y') }}</td>

                                        @if(in_array(Auth::user()->role, ['admin', 'secretaria_area']))
                                            <td class="py-3 px-4 text-sm">
                                                <div class="font-medium">
                                                    {{ $comision->user ? $comision->user->name : 'N/A' }}
                                                    @if($comision->user && $comision->user->trashed())
                                                        <span
                                                            class="text-[9px] bg-red-100 text-red-600 px-1 rounded ml-1 font-bold">INACTIVO</span>
                                                    @endif
                                                </div>
                                                <div class="text-[10px] text-gray-400">
                                                    {{ $comision->user->area->name ?? 'S/A' }}</div>
                                            </td>
                                        @endif

                                        <td class="py-3 px-4 text-sm font-bold text-guinda-ceaa">{{ $comision->dias_comision }}
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            <div class="font-bold">{{ $comision->lugar }}</div>
                                            <div class="text-xs italic text-gray-400">
                                                {{ Str::limit($comision->actividad, 45) }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span
                                                class="px-2 py-1 text-[10px] font-bold rounded-full {{ $comision->status === 'Cancelado' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} uppercase">
                                                {{ $comision->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center no-print">
                                            <div class="flex justify-center items-center space-x-2">
                                                <a href="{{ route('comisiones.show', $comision) }}"
                                                    class="p-1 text-gray-600 hover:text-guinda-ceaa" title="Ver PDF">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                @if(Auth::user()->role == 'admin' && $comision->status !== 'Cancelado')
                                                    <a href="{{ route('comisiones.edit', $comision) }}"
                                                        class="p-1 text-guinda-ceaa hover:text-guinda-ceaa-hover" title="Editar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('comisiones.cancelar', $comision) }}" method="POST"
                                                        onsubmit="return confirm('¿Cancelar?');" class="inline">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="p-1 text-yellow-600 hover:text-yellow-800"
                                                            title="Cancelar">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
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
                                        <td colspan="7" class="text-center py-10 text-gray-400 italic">No se encontraron
                                            registros.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 no-print">
                        {{ $comisiones->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>