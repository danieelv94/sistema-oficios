<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-widest">
            {{ __('Panel de Control') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-guinda-ceaa">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Recibidos</p>
                    <p class="text-3xl font-black text-guinda-ceaa">{{ $totalOficios ?? 0 }}</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-gris-oscuro">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pendientes de Área</p>
                    <p class="text-3xl font-black text-gris-oscuro">{{ $pendientesArea ?? 0 }}</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-dorado-ocre">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Mis Tareas</p>
                    <p class="text-3xl font-black text-dorado-ocre">{{ $misTareas ?? 0 }}</p>
                </div>
            </div>

            {{-- 2. Acciones Rápidas (Botones grandes adaptados por rol) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Nuevo Oficio (Solo Recepción y Admin) --}}
                @if(in_array(Auth::user()->role, ['admin', 'recepcionista', 'correspondencia']))
                    <a href="{{ route('oficios.create') }}"
                        class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition flex items-center gap-6 border border-gray-100">
                        <div
                            class="bg-guinda-ceaa/10 p-4 rounded-full text-guinda-ceaa group-hover:bg-guinda-ceaa group-hover:text-white transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase">Nuevo Oficio</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase">Captura de correspondencia externa</p>
                        </div>
                    </a>
                @endif

                {{-- Entrada de Correspondencia (Solo Correspondencia, Admin y Director de Gestión Institucional) --}}
                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'correspondencia' || (Auth::user()->role == 'jefe_area' && Auth::user()->area_id == 2))
                    <a href="{{ route('oficios.index') }}"
                        class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition flex items-center gap-6 border border-gray-100">
                        <div
                            class="bg-gris-claro/10 p-4 rounded-full text-gris-oscuro group-hover:bg-gris-oscuro group-hover:text-white transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase">Entrada de Correspondencia</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase">Turnar oficios recibidos</p>
                        </div>
                    </a>
                @endif

                {{-- Gestión de Turnos (Jefes, Secretarias y Operativos, o Admin; Ocultar para Recepcionista y
                Correspondencia) --}}
                @if((Auth::user()->area_id && !in_array(Auth::user()->role, ['recepcionista', 'correspondencia'])) || Auth::user()->role == 'admin')
                    <a href="{{ route('oficios.gestion') }}"
                        class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition flex items-center gap-6 border border-gray-100">
                        <div
                            class="bg-dorado-ocre/10 p-4 rounded-full text-dorado-ocre group-hover:bg-dorado-ocre group-hover:text-white transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase">Gestión de Turnos</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase">Ver, delegar y solventar correspondencia
                            </p>
                        </div>
                    </a>
                @endif

                {{-- Seguimiento General (Solo Admin, Correspondencia y Jefe de Gestión Institucional (area_id = 2))
                --}}
                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'correspondencia' || (Auth::user()->role == 'jefe_area' && Auth::user()->area_id == 2))
                    <a href="{{ route('oficios.seguimiento') }}"
                        class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition flex items-center gap-6 border border-gray-100">
                        <div
                            class="bg-green-600/10 p-4 rounded-full text-green-600 group-hover:bg-green-600 group-hover:text-white transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-800 uppercase">Seguimiento General</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase">Control de todos los turnos y áreas</p>
                        </div>
                    </a>
                @endif
            </div>


        </div>
    </div>
</x-app-layout>