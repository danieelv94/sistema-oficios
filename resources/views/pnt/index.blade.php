<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-lg text-slate-800 leading-tight uppercase tracking-wider">
                Transparencia PNT: Procedimientos de Adjudicación
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('pnt.export') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-black uppercase text-[10px] tracking-widest px-4 py-2.5 rounded-lg transition shadow-md hover:shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    Exportar a Excel
                </a>
                
                @if(Auth::user()->canEditPntSection(1))
                    <a href="{{ route('pnt.create') }}" 
                       class="bg-guinda-ceaa hover:bg-guinda-ceaa/90 text-white font-black uppercase text-[10px] tracking-widest px-4 py-2.5 rounded-lg transition shadow-md hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Registro
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Mensajes de Estatus --}}
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm text-xs font-semibold text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm text-xs font-semibold text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Tabla Principal --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-guinda-ceaa">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-250 text-xs">
                        <thead class="bg-gray-50 font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Expediente / Folio</th>
                                <th class="px-4 py-3 text-center">Ejercicio</th>
                                <th class="px-4 py-3 text-left">Procedimiento</th>
                                <th class="px-4 py-3 text-left">Proveedor Ganador</th>
                                <th class="px-4 py-3 text-right">Monto Max.</th>
                                <th class="px-4 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 text-slate-700">
                            @forelse($procedimientos as $p)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-4 py-3 font-black text-slate-800">
                                        {{ $p->numero_expediente }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-600">
                                        {{ $p->ejercicio }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="bg-slate-100 text-slate-800 text-[10px] font-bold px-2 py-0.5 rounded border border-slate-200 uppercase">
                                            {{ $p->tipo_procedimiento }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-semibold">
                                        {{ $p->proveedor_ganador_nombre ?: 'Pendiente de capturar...' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800">
                                        {{ $p->monto_contrato_max ? '$' . number_format($p->monto_contrato_max, 2) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('pnt.edit', $p) }}" 
                                               class="text-blue-650 hover:text-blue-800 font-black uppercase text-[10px] tracking-wider">
                                                Editar / Ver
                                            </a>
                                            
                                            @if(Auth::user()->role === 'admin')
                                                <form action="{{ route('pnt.destroy', $p) }}" method="POST" 
                                                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro de transparencia y todas sus subtablas?');" 
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-650 hover:text-red-900 font-black uppercase text-[10px] tracking-wider cursor-pointer">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 italic">
                                        No se han registrado procedimientos de adjudicación para la PNT en este ejercicio.
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
