<x-app-layout>
    {{-- HEADER CONDICIONAL: Solo se muestra para roles que gestionan o ven métricas --}}
    @if(Auth::user()->role != 'recepcionista')
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-widest">
                    {{ __('Panel de Control de Oficios') }}
                </h2>
                @if(Auth::user()->role == 'admin')
                    <a href="{{ route('oficios.create') }}" 
                       class="px-4 py-2 bg-[#932C43] text-white rounded-md hover:bg-[#722134] font-bold shadow-md transition-all transform hover:scale-105 text-sm uppercase">
                        + Registrar Oficio
                    </a>
                @endif
            </div>
        </x-slot>
    @endif

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ========================================================== --}}
            {{-- VISTA ÚNICA PARA RECEPCIÓN (PANTALLA LIMPIA)               --}}
            {{-- ========================================================== --}}
            @if(Auth::user()->role == 'recepcionista')
                <div class="flex flex-col items-center justify-center space-y-10 py-20">
                    {{-- Logo / Icono Institucional --}}
                    <div class="p-10 bg-white rounded-full shadow-2xl border-b-8 border-[#932C43] transition-transform hover:scale-110 duration-500">
                        <svg class="w-28 h-28 text-[#932C43]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>

                    <div class="text-center space-y-3">
                        <h1 class="text-5xl font-black text-gray-800 uppercase tracking-tighter">Módulo de Recepción</h1>
                        <p class="text-gray-400 font-bold uppercase text-sm tracking-[0.3em]">Comisión Estatal del Agua y Alcantarillado</p>
                        <div class="pt-4">
                            <span class="px-4 py-1 bg-gray-200 rounded-full text-[10px] font-black text-gray-600 uppercase tracking-widest">
                                {{ now()->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Botón Gigante de Acción --}}
                    <div class="pt-6">
                        <a href="{{ route('oficios.create') }}" 
                           class="group relative inline-flex items-center justify-center px-16 py-8 font-black text-white bg-[#932C43] rounded-2xl shadow-[0_20px_50px_rgba(147,44,67,0.3)] transition-all duration-300 hover:bg-[#722134] hover:scale-105 active:scale-95 overflow-hidden">
                            <span class="absolute inset-0 w-full h-full bg-gradient-to-br from-white/20 to-transparent"></span>
                            <svg class="w-10 h-10 mr-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-3xl uppercase tracking-widest">Registrar Nuevo Oficio</span>
                        </a>
                    </div>
                    <p class="text-[10px] text-gray-300 font-medium uppercase tracking-[0.5em] pt-20 italic">Sistema de Gestión Institucional CEAA</p>
                </div>

            {{-- ========================================================== --}}
            {{-- VISTA DASHBOARD (ADMIN, JEFES, OPERATIVOS)                --}}
            {{-- ========================================================== --}}
            @else
                
                {{-- MÉTRICAS RÁPIDAS --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-[#932C43] transition-all hover:shadow-md">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Recibidos</p>
                        <p class="text-3xl font-black text-[#932C43]">{{ method_exists($correspondenciaGeneral, 'total') ? $correspondenciaGeneral->total() : 0 }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-600 transition-all hover:shadow-md">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Pendientes de Área</p>
                        <p class="text-3xl font-black text-blue-600">{{ method_exists($gestionArea, 'total') ? $gestionArea->total() : 0 }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-600 transition-all hover:shadow-md">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Mis Tareas</p>
                        <p class="text-3xl font-black text-purple-600">{{ method_exists($misTurnosAsignados, 'total') ? $misTurnosAsignados->total() : 0 }}</p>
                    </div>
                </div>

                <div class="space-y-10">
                    {{-- TABLA: CORRESPONDENCIA GENERAL (Solo Admin) --}}
                    @if(Auth::user()->role == 'admin')
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-[#932C43]">
                            <div class="p-6">
                                <h3 class="text-sm font-black text-gray-700 uppercase italic mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#932C43]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    Entrada de Correspondencia (General)
                                </h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-xs text-left">
                                        <thead class="bg-gray-50 text-gray-500 uppercase font-black">
                                            <tr>
                                                <th class="px-4 py-3">Oficio / PDF</th>
                                                <th class="px-4 py-3">Remitente</th>
                                                <th class="px-4 py-3 text-center">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($correspondenciaGeneral as $oficio)
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-4 py-3">
                                                        <span class="font-bold text-gray-800">{{ $oficio->numero_oficio }}</span>
                                                        @if($oficio->pdf_path) <span class="ml-2 text-red-600 font-black">[PDF]</span> @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-500">{{ Str::limit($oficio->remitente, 40) }}</td>
                                                    <td class="px-4 py-3 text-center">
                                                        <a href="{{ route('oficios.show', ['oficio' => $oficio, 'mode' => 'recepcion']) }}" class="bg-gray-800 text-white px-3 py-1 rounded font-bold uppercase tracking-tighter">Turnar</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">{{ $correspondenciaGeneral->appends(['gen_page' => $correspondenciaGeneral->currentPage()])->links() }}</div>
                            </div>
                        </div>
                    @endif

                    {{-- TABLA: GESTIÓN DE ÁREA --}}
                    @if(Auth::user()->role == 'admin' || Auth::user()->role == 'jefe_area' || Auth::user()->role == 'secretaria_area')
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-blue-600">
                            <div class="p-6">
                                <h3 class="text-sm font-black text-gray-700 uppercase italic mb-4">Gestión de la Dirección (Asignación)</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-xs text-left">
                                        <thead class="bg-blue-50 text-blue-800 uppercase font-black">
                                            <tr>
                                                <th class="px-4 py-3">No. Oficio</th>
                                                <th class="px-4 py-3">Responsable</th>
                                                <th class="px-4 py-3 text-center">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($gestionArea as $oficio)
                                                <tr class="hover:bg-blue-50 transition">
                                                    <td class="px-4 py-3 font-bold">{{ $oficio->numero_oficio }}</td>
                                                    <td class="px-4 py-3">
                                                        @php $pivot = $oficio->areas->where('id', Auth::user()->area_id)->first()->pivot ?? null; @endphp
                                                        <span class="px-2 py-0.5 rounded {{ $pivot && $pivot->user_id ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700 font-black' }}">
                                                            {{ $pivot && $pivot->user_id ? \App\Models\User::find($pivot->user_id)->name : 'PENDIENTE' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <a href="{{ route('oficios.show', ['oficio' => $oficio, 'mode' => 'gestion']) }}" class="bg-blue-600 text-white px-3 py-1 rounded font-bold uppercase tracking-tighter">Asignar</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">{{ $gestionArea->appends(['gest_page' => $gestionArea->currentPage()])->links() }}</div>
                            </div>
                        </div>
                    @endif

                    {{-- CARDS: MIS TAREAS --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-purple-600">
                        <div class="p-6">
                            <h3 class="text-sm font-black text-gray-700 uppercase italic mb-6">Mis Tareas Asignadas</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @forelse($misTurnosAsignados as $oficio)
                                    <div class="bg-gray-50 border border-purple-100 p-4 rounded-xl shadow-sm hover:shadow-md transition">
                                        <div class="flex justify-between items-start mb-3">
                                            <span class="text-[10px] font-black text-purple-700 uppercase tracking-widest">{{ $oficio->numero_oficio }}</span>
                                            @if($oficio->pdf_path) <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 18V2h12v4h4v12H4zm14-10h-4V4.414L17.586 8H18z" /></svg> @endif
                                        </div>
                                        <p class="text-[11px] font-bold text-gray-800 mb-4 h-8 overflow-hidden line-clamp-2 uppercase">{{ $oficio->asunto }}</p>
                                        <a href="{{ route('oficios.show', ['oficio' => $oficio, 'mode' => 'operativo']) }}" class="block text-center py-2 bg-purple-600 text-white text-[10px] font-black uppercase rounded shadow-sm hover:bg-purple-800">Ver Detalles</a>
                                    </div>
                                @empty
                                    <div class="col-span-full py-10 text-center text-gray-400 italic text-xs uppercase tracking-widest">No tienes tareas asignadas.</div>
                                @endforelse
                            </div>
                            <div class="mt-6">{{ $misTurnosAsignados->appends(['task_page' => $misTurnosAsignados->currentPage()])->links() }}</div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>