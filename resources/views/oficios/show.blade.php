<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Oficio: {{ $oficio->numero_oficio }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-right">
            <a href="{{ route('oficios.generar', $oficio) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                Generar Oficio Imprimible
            </a>
        </div>
    </div>

    <div class="py-12 pt-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">Información del Oficio</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <p><strong>No. Oficio Interno:</strong> {{ $oficio->numero_oficio }}</p>
                        <p><strong>No. Oficio Dependencia:</strong> {{ $oficio->numero_oficio_dependencia }}</p>
                        <p><strong>Remitente:</strong> {{ $oficio->remitente }}</p>
                        <p><strong>Tipo Correspondencia:</strong> {{ $oficio->tipo_correspondencia }}</p>
                        <p><strong>Municipio:</strong> {{ $oficio->municipio }}</p>
                        <p><strong>Localidad:</strong> {{ $oficio->localidad }}</p>
                        <p><strong>Fecha Recepción:</strong> {{ $oficio->fecha_recepcion }}</p>
                        <p><strong>Fecha Límite:</strong> {{ $oficio->fecha_limite ?? 'N/A' }}</p>
                        <p><strong>Prioridad:</strong> {{ $oficio->prioridad }}</p>
                        <p><strong>Estatus General:</strong> {{ $oficio->estatus }}</p>
                        <p class="col-span-2"><strong>Asunto:</strong><br>{{ $oficio->asunto }}</p>
                        <p class="col-span-2"><strong>Observaciones:</strong><br>{{ $oficio->observaciones ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold border-b pb-2 mb-4">Seguimiento de Turnos</h3>
                @forelse($oficio->areas as $area)
                    <div class="border rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-bold text-xl">{{ $area->name }}</p>
                                <p class="text-sm text-gray-600"><strong>Instrucción:</strong> {{ $area->pivot->instruccion }}</p>
                                <p class="text-sm text-gray-600"><strong>Persona Asignada:</strong> {{ \App\Models\User::find($area->pivot->user_id)->name ?? 'Sin asignar' }}</p>
                                <p class="text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $area->pivot->estatus }}</span></p>
                            </div>

                            @if(Auth::user()->role == 'jefe_area' && Auth::user()->area_id == $area->id)
                                <form action="{{ route('oficios.asignar', $oficio) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="pivote_id" value="{{ $area->pivot->id }}">
                                    <select name="user_id" class="block w-full rounded-md shadow-sm border-gray-300">
                                        @foreach($personalPorArea[$area->id] as $persona)
                                            <option value="{{ $persona->id }}" @selected($area->pivot->user_id == $persona->id)>{{ $persona->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-3 py-2 bg-yellow-500 text-white rounded-md text-sm">Asignar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Este oficio aún no ha sido turnado a ninguna área.</p>
                @endforelse
            </div>

            @if(Auth::user()->role == 'admin')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ areas: [{id: '', instruccion: ''}] }">
                <h3 class="text-lg font-bold border-b pb-2 mb-4">Turnar Oficio a Áreas</h3>
                <form action="{{ route('oficios.turnar', $oficio) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <template x-for="(area, index) in areas" :key="index">
                        <div class="flex items-center space-x-4 mb-3 border-b pb-3">
                            <div class="flex-1">
                                <label class="block font-medium text-sm text-gray-700">Área</label>
                                <select name="areas[]" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                    <option value="">Selecciona un área</option>
                                    @foreach($areasDisponibles as $areaOption)
                                        <option value="{{ $areaOption->id }}">{{ $areaOption->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block font-medium text-sm text-gray-700">Instrucción</label>
                                <input type="text" name="instrucciones[]" placeholder="Ej. Para su conocimiento" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                            </div>
                            <button type="button" @click="areas.splice(index, 1)" class="px-3 py-2 bg-red-500 text-white rounded-md mt-6 self-end" x-show="areas.length > 1">Quitar</button>
                        </div>
                    </template>
                    <div class="flex justify-between mt-4">
                        <button type="button" @click="areas.push({id: '', instruccion: ''})" class="px-4 py-2 bg-gray-600 text-white rounded-md">
                            + Añadir Otra Área
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-md">
                            Guardar Turnos
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>