<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Oficios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- SECCIÓN 1: RECEPCIÓN (ADMIN / RECEPCIONISTA) --}}
            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'recepcionista')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Entrada de Correspondencia</h3>
                            <a href="{{ route('oficios.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md">
                                Registrar Nuevo Oficio
                            </a>
                        </div>
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">No. Oficio</th>
                                    <th class="py-2 px-4 border-b">Asunto</th>
                                    <th class="py-2 px-4 border-b">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($correspondenciaGeneral as $oficio)
                                    <tr class="hover:bg-gray-100">
                                        <td class="py-2 px-4 border-b text-center">{{ $oficio->numero_oficio }}</td>
                                        <td class="py-2 px-4 border-b text-center"><span class="block break-words w-full">
                                                {!! nl2br(e($oficio->asunto)) !!}
                                            </span></td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <a href="{{ route('oficios.show', ['oficio' => $oficio->id, 'mode' => 'recepcion']) }}"
                                                class="px-3 py-1 bg-green-500 text-white rounded-md text-xs whitespace-nowrap">
                                                Turnar a Área
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- SECCIÓN 2: GESTIÓN DE ÁREA (ADMIN / JEFE / SECRETARIA ÁREA) --}}
            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'jefe_area' || Auth::user()->role == 'secretaria_area')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">Gestión de Turnos de la Dirección</h3>
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">No. Oficio</th>
                                    <th class="py-2 px-4 border-b">Estatus</th>
                                    <th class="py-2 px-4 border-b">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gestionArea as $oficio)
                                    <tr class="hover:bg-gray-100">
                                        <td class="py-2 px-4 border-b text-center">{{ $oficio->numero_oficio }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $oficio->estatus }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <a href="{{ route('oficios.show', ['oficio' => $oficio->id, 'mode' => 'gestion']) }}"
                                                class="px-3 py-1 bg-green-500 text-white rounded-md text-xs whitespace-nowrap">
                                                Asignar Personal
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- SECCIÓN 3: MIS TURNOS (TODOS) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Mis Turnos Pendientes</h3>
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">No. Oficio</th>
                                <th class="py-2 px-4 border-b">Instrucción</th>
                                <th class="py-2 px-4 border-b">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($misTurnosAsignados as $oficio)
                                <tr class="hover:bg-gray-100">
                                    <td class="py-2 px-4 border-b text-center">{{ $oficio->numero_oficio }}</td>
                                    <td class="py-2 px-4 border-b italic text-sm text-center">
                                        {{ $oficio->areas->where('id', Auth::user()->area_id)->first()->pivot->instruccion ?? 'N/A' }}
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('oficios.show', ['oficio' => $oficio->id, 'mode' => 'operativo']) }}"
                                            class="px-3 py-1 bg-green-500 text-white rounded-md text-xs whitespace-nowrap">
                                            Ver y Responder
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">No tienes turnos asignados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>