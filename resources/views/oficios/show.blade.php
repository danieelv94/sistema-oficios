<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Oficio: {{ $oficio->numero_oficio }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-right">
            <a href="{{ route('oficios.generar', $oficio) }}"
                class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                Generar Oficio Imprimible
            </a>
        </div>
    </div>

    <div class="py-12 pt-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- INFORMACIÓN SIEMPRE VISIBLE --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">Información del Oficio</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <p><strong>No. Oficio Interno:</strong> {{ $oficio->numero_oficio }}</p>
                        <p><strong>Remitente:</strong> {{ $oficio->remitente }}</p>
                        <p><strong>Fecha Recepción:</strong> {{ $oficio->fecha_recepcion }}</p>
                        <p><strong>Estatus General:</strong> {{ $oficio->estatus }}</p>
                        <p class="col-span-2">
                            <strong>Asunto:</strong><br>
                            {{-- La clase break-words asegura que el texto no se salga del cuadro blanco --}}
                            <span class="block break-words w-full">
                                {!! nl2br(e($oficio->asunto)) !!}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- MODO OPERATIVO: SOLO VER MI INSTRUCCIÓN --}}
            @if($mode == 'operativo')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <h3 class="text-lg font-bold mb-2">Instrucción Recibida:</h3>
                    @foreach($turnosParaMostrar as $area)
                        <p class="text-xl italic">"{{ $area->pivot->instruccion }}"</p>
                    @endforeach
                </div>
            @endif

            {{-- MODO GESTIÓN O RECEPCIÓN: HERRAMIENTAS --}}
            @if($mode == 'recepcion' || $mode == 'gestion')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">Seguimiento de Turnos</h3>
                    @forelse($turnosParaMostrar as $area)
                        <div class="border rounded-lg p-4 mb-4" style="background-color: #f9fafb; position: relative;">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center" style="gap: 15px; margin-bottom: 8px;">
                                        <p class="font-bold text-xl" style="margin: 0; color: #1e40af;">{{ $area->name }}</p>

                                        @if(Auth::user()->role == 'admin')
                                            <form action="{{ route('oficios.eliminarTurno', $area->pivot->id) }}" method="POST"
                                                style="display: inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Eliminar turno?')"
                                                    style="background-color: #dc2626; color: white; padding: 4px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; border: none; cursor: pointer;">
                                                    Eliminar Turno
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">Instrucción: {{ $area->pivot->instruccion }}</p>
                                    <p class="text-sm text-gray-600">Persona Asignada:
                                        {{ \App\Models\User::find($area->pivot->user_id)->name ?? 'Sin asignar' }}
                                    </p>
                                </div>

                                {{-- Botón Asignar (Control de acceso actualizado) --}}
                                @if((Auth::user()->role == 'admin' || Auth::user()->role == 'jefe_area' || Auth::user()->role == 'secretaria_area' || Auth::user()->role == 'recepcionista') && Auth::user()->area_id == $area->id)
                                    <form action="{{ route('oficios.asignar', $oficio) }}" method="POST"
                                        class="flex items-center space-x-2">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="pivote_id" value="{{ $area->pivot->id }}">
                                        <select name="user_id" class="block w-full rounded-md shadow-sm border-gray-300 text-sm">
                                            <option value="">Asignar...</option>
                                            @foreach($personalPorArea[$area->id] as $persona)
                                                <option value="{{ $persona->id }}" {{ $area->pivot->user_id == $persona->id ? 'selected' : '' }}>{{ $persona->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                            class="px-3 py-2 bg-yellow-500 text-black rounded-md text-sm font-bold transition">Asignar</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">No hay turnos para gestionar.</p>
                    @endforelse
                </div>

                {{-- Solo Recepción: Turnar a áreas --}}
                @if($mode == 'recepcion')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                        x-data="{ areas: [{id: '', instruccion: ''}] }">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4">Turnar Oficio a Áreas</h3>
                        <form action="{{ route('oficios.turnar', $oficio) }}" method="POST">
                            @csrf @method('PUT')
                            <template x-for="(area, index) in areas" :key="index">
                                <div class="flex items-center space-x-4 mb-3 border-b pb-3">
                                    <div class="flex-1">
                                        <label class="block font-medium text-sm text-gray-700">Área</label>
                                        <select name="areas[]" class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
                                            required>
                                            <option value="">Selecciona un área</option>
                                            @foreach($areasDisponibles as $areaOption)
                                                <option value="{{ $areaOption->id }}">{{ $areaOption->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block font-medium text-sm text-gray-700">Instrucción</label>
                                        <input type="text" name="instrucciones[]"
                                            class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                    </div>
                                    <button type="button" @click="areas.splice(index, 1)"
                                        class="px-3 py-2 bg-red-500 text-white rounded-md mt-6 self-end"
                                        x-show="areas.length > 1">Quitar</button>
                                </div>
                            </template>
                            <div class="flex justify-between mt-4">
                                <button type="button" @click="areas.push({id: '', instruccion: ''})"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-md">+ Añadir Otra Área</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-md">Guardar
                                    Turnos</button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>