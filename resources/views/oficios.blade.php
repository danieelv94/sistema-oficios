<x-app-layout>
    <x-slot name="header">
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 px-4 sm:px-0">
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-guinda-ceaa">
        <p class="text-xs font-bold text-gray-500 uppercase">Total Recibidos</p>
        <p class="text-2xl font-black text-guinda-ceaa">{{ $correspondenciaGeneral->total() ?? 0 }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-gris-oscuro">
        <p class="text-xs font-bold text-gray-500 uppercase">Pendientes de Área</p>
        <p class="text-2xl font-black text-gris-oscuro">{{ $gestionArea->total() ?? 0 }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-dorado-ocre">
        <p class="text-xs font-bold text-gray-500 uppercase">Mis Tareas</p>
        <p class="text-2xl font-black text-dorado-ocre">{{ $misTurnosAsignados->total() ?? 0 }}</p>
    </div>
</div>
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-widest">
                {{ __('Panel de Control de Oficios') }}
            </h2>
            @if(in_array(Auth::user()->role, ['admin', 'recepcionista']))
                <a href="{{ route('oficios.create') }}" 
                   class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-md font-bold shadow-md transition-all transform hover:scale-105 text-sm uppercase">
                    + Registrar Oficio
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- 1. CORRESPONDENCIA GENERAL (Admin y Recepción) --}}
            @if(in_array(Auth::user()->role, ['admin', 'recepcionista', 'correspondencia']))
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-guinda-ceaa">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <span class="p-2 bg-red-100 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-guinda-ceaa" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </span>
                        <h3 class="text-lg font-black text-gray-700 uppercase italic">Entrada de Correspondencia (General)</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-guinda-ceaa text-xs font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 border-b text-left">No. Oficio / PDF</th>
                                    <th class="py-3 px-4 border-b text-left">Remitente</th>
                                    <th class="py-3 px-4 border-b text-left">Asunto</th>
                                    <th class="py-3 px-4 border-b text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($correspondenciaGeneral as $oficio)
                                <tr class="hover:bg-red-50 transition">
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-sm text-gray-800">{{ $oficio->numero_oficio }}</div>
                                        @if($oficio->pdf_path)
                                            <span class="text-[10px] text-red-600 font-bold uppercase">[PDF Cargado]</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-xs text-gray-600 font-medium">{{ $oficio->remitente }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-500 italic">{{ Str::limit($oficio->asunto, 60) }}</td>
                                    <td class="py-3 px-4 text-center space-x-1">
                                         <a href="{{ route('oficios.show', ['oficio' => $oficio, 'mode' => 'recepcion']) }}" 
                                            class="px-3 py-1 bg-gray-800 text-white rounded text-[10px] font-bold hover:bg-black uppercase tracking-tighter">
                                             Turnar
                                         </a>
                                         @if(in_array(Auth::user()->role, ['admin', 'correspondencia', 'recepcionista']))
                                             <a href="{{ route('oficios.edit', $oficio) }}" 
                                                class="px-3 py-1 bg-blue-600 text-white rounded text-[10px] font-bold hover:bg-blue-700 uppercase tracking-tighter">
                                                 Editar
                                             </a>
                                         @endif
                                     </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-10 text-center text-gray-400 italic">Sin correspondencia registrada.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4 px-4">
    {{ $correspondenciaGeneral->appends(request()->query())->links() }}
</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 2. GESTIÓN DE DIRECCIÓN (Jefes y Secretarias) --}}
            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'jefe_area' || Auth::user()->role == 'secretaria_area')
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-gris-oscuro">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <span class="p-2 bg-gris-claro/20 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-gris-oscuro" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </span>
                        <h3 class="text-lg font-black text-gray-700 uppercase italic">Gestión de la Dirección (Para Asignar)</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border-collapse">
                            <thead>
                                <tr class="bg-arena-claro/20 text-guinda-ceaa text-xs font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 border-b text-left">No. Oficio</th>
                                    <th class="py-3 px-4 border-b text-left">Instrucción Recibida</th>
                                    <th class="py-3 px-4 border-b text-center">Asignación</th>
                                    <th class="py-3 px-4 border-b text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($gestionArea as $oficio)
                                <tr class="hover:bg-arena-claro/10 transition">
                                    <td class="py-3 px-4 text-sm font-bold text-guinda-ceaa">{{ $oficio->numero_oficio }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-600 italic">
                                        {{ $oficio->areas->where('id', Auth::user()->area_id)->first()->pivot->instruccion ?? 'Sin instrucción' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @php 
                                            $userAsignadoId = $oficio->areas->where('id', Auth::user()->area_id)->first()->pivot->user_id;
                                            $userAsignado = \App\Models\User::find($userAsignadoId);
                                        @endphp
                                        <span class="px-2 py-1 rounded text-[10px] font-bold {{ $userAsignado ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $userAsignado ? $userAsignado->name : 'PENDIENTE' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ route('oficios.show', ['oficio' => $oficio, 'mode' => 'gestion']) }}" 
                                           class="px-3 py-1 bg-blue-600 text-white rounded text-[10px] font-bold hover:bg-blue-700 uppercase tracking-tighter shadow-sm">
                                            Asignar Personal
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-10 text-center text-gray-400 italic">No hay oficios turnados a su dirección.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- 3. MIS TURNOS ASIGNADOS (Personal Operativo) --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-dorado-ocre">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <span class="p-2 bg-dorado-ocre/10 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-dorado-ocre" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <h3 class="text-lg font-black text-gray-700 uppercase italic">Mis Tareas Pendientes</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($misTurnosAsignados as $oficio)
                        <div class="bg-gray-50 p-4 rounded-lg border border-dorado-ocre/20 shadow-sm hover:shadow-md transition group">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-black text-dorado-ocre uppercase">{{ $oficio->numero_oficio }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase">{{ \Carbon\Carbon::parse($oficio->fecha_recepcion)->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-xs text-gray-800 font-bold mb-2 line-clamp-1">{{ $oficio->asunto }}</p>
                            <div class="bg-white p-2 rounded border border-dorado-ocre/10 text-[10px] italic text-gray-500 mb-4 h-12 overflow-hidden">
                                " {{ $oficio->areas->where('pivot.user_id', Auth::user()->id)->first()->pivot->instruccion ?? 'Sin instrucciones' }} "
                            </div>
                            <a href="{{ route('oficios.show', ['oficio' => $oficio, 'mode' => 'operativo']) }}" 
                               class="block text-center w-full py-2 bg-dorado-ocre text-white rounded text-[10px] font-bold uppercase tracking-widest group-hover:bg-guinda-ceaa transition shadow-sm">
                                Ver Detalles / PDF
                            </a>
                        </div>
                        @empty
                        <div class="col-span-full py-10 text-center text-gray-400 italic">No tienes tareas asignadas actualmente.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>