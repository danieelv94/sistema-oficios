<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 uppercase tracking-widest">Seguimiento General de Turnos</h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-2xl border-t-4 border-green-600 transition-all">
                <div class="p-8">
                    
                    {{-- Encabezado e Instrucciones --}}
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-black text-gray-800 uppercase tracking-tight">Consola de Monitoreo Global</h3>
                                <a href="{{ route('oficios.reporteDiario') }}" 
                                    class="inline-flex items-center gap-1 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white text-[10px] font-black uppercase px-3 py-1 rounded-full shadow-sm hover:shadow transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Reporte Diario
                                </a>
                            </div>
                            <p class="text-xs text-gray-400 font-bold uppercase mt-1">Supervisión integral de correspondencia, áreas turnadas, responsables y estados de solventación</p>
                        </div>
                        
                        {{-- Filtros y Buscador Premium --}}
                        <form action="{{ route('oficios.seguimiento') }}" method="GET" class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    placeholder="Buscar oficio, asunto, remitente, área o personal..." 
                                    class="w-full sm:w-80 text-xs rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 pl-8 py-2">
                                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>

                            <select name="estatus" onchange="this.form.submit()" 
                                class="text-xs rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 py-2">
                                <option value="">-- Todos los Estados --</option>
                                <option value="Turnado" {{ request('estatus') == 'Turnado' ? 'selected' : '' }}>Turnado</option>
                                <option value="Recibido" {{ request('estatus') == 'Recibido' ? 'selected' : '' }}>Recibido</option>
                                <option value="Asignado" {{ request('estatus') == 'Asignado' ? 'selected' : '' }}>Asignado</option>
                                <option value="Solventado" {{ request('estatus') == 'Solventado' ? 'selected' : '' }}>Solventado</option>
                            </select>

                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-black uppercase px-4 py-2 rounded-lg transition shadow-sm hover:shadow">
                                Buscar
                            </button>
                            @if(request()->filled('search') || request()->filled('estatus'))
                                <a href="{{ route('oficios.seguimiento') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-black uppercase px-3 py-2 rounded-lg transition text-center flex items-center justify-center">
                                    Limpiar
                                </a>
                            @endif
                        </form>
                    </div>

                    {{-- Tabla de Datos --}}
                    <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                        <table class="min-w-full text-xs text-left">
                            <thead class="bg-green-50 text-green-800 uppercase font-black">
                                <tr>
                                    <th class="px-6 py-4 tracking-wider">Número de Oficio</th>
                                    <th class="px-6 py-4 tracking-wider">Remitente / Asunto</th>
                                    <th class="px-6 py-4 tracking-wider">Dirección Turnada</th>
                                    <th class="px-6 py-4 tracking-wider">Personal Operativo</th>
                                    <th class="px-6 py-4 tracking-wider text-center">Estatus del Turno</th>
                                    <th class="px-6 py-4 tracking-wider text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($turnos as $turno)
                                    <tr class="hover:bg-gray-50/80 transition duration-150">
                                        {{-- No. Oficio --}}
                                        <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                            {{ $turno->numero_oficio }}
                                        </td>
                                        
                                        {{-- Remitente / Asunto --}}
                                        <td class="px-6 py-4 max-w-sm">
                                            <p class="font-bold text-gray-800 line-clamp-1">{{ $turno->remitente }}</p>
                                            <p class="text-gray-400 italic text-[11px] mt-0.5 line-clamp-1">{{ $turno->asunto }}</p>
                                        </td>
                                        
                                        {{-- Dirección Turnada --}}
                                        <td class="px-6 py-4 font-black text-green-700 uppercase">
                                            {{ $turno->area_name }}
                                        </td>
                                        
                                        {{-- Responsable Asignado --}}
                                        <td class="px-6 py-4 text-gray-700">
                                            @if($turno->operativo_name)
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                    <span class="font-bold">{{ $turno->operativo_name }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic font-medium">Por asignar...</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Estatus Badge --}}
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                                {{ $turno->turno_estatus == 'Turnado' ? 'bg-orange-100 text-orange-700' : '' }}
                                                {{ $turno->turno_estatus == 'Recibido' ? 'bg-gris-claro/20 text-gris-oscuro' : '' }}
                                                {{ $turno->turno_estatus == 'Asignado' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                {{ $turno->turno_estatus == 'Solventado' ? 'bg-green-100 text-green-700' : '' }}
                                            ">
                                                {{ $turno->turno_estatus }}
                                            </span>
                                        </td>
                                        
                                        {{-- Acciones --}}
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('oficios.show', [$turno->oficio_id, 'mode' => 'gestion']) }}"
                                                class="inline-flex items-center gap-1 bg-slate-800 hover:bg-slate-900 text-white px-3 py-1.5 rounded-lg font-black uppercase text-[10px] shadow-sm transition">
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
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                            No se encontraron turnos registrados o que coincidan con la búsqueda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-6">
                        {{ $turnos->appends(request()->query())->links() }}
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
