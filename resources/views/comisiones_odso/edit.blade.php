<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Oficio ODSO:') }} {{ $comision->oficio_numero }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <div class="p-8 text-gray-900">

                    <div class="mb-6 flex justify-between items-center border-b pb-4">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-amber-900">Modificar Oficio ODSO</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300">
                                Supervisión de Obra
                            </span>
                        </div>
                        <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded">ID: {{ $comision->id }}</span>
                    </div>

                    <form action="{{ route('comisiones-odso.update', $comision) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Días de Comisión --}}
                            <div>
                                <label for="dias_comision" class="block text-sm font-bold text-gray-700 uppercase">Días
                                    de Comisión</label>
                                <input type="text" name="dias_comision" id="dias_comision"
                                    value="{{ old('dias_comision', $comision->dias_comision) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                                    required>
                            </div>

                            {{-- Hora --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase">Hora Inicio</label>
                                <input type="time" name="hora_inicio"
                                    value="{{ old('hora_inicio', $comision->hora_inicio) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase">Hora Término</label>
                                <input type="time" name="hora_fin" value="{{ old('hora_fin', $comision->hora_fin) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                            </div>

                            {{-- Lugar --}}
                            <div>
                                <label for="lugar" class="block text-sm font-bold text-gray-700 uppercase">Lugar de la
                                    Comisión</label>
                                <input type="text" name="lugar" id="lugar"
                                    value="{{ old('lugar', $comision->lugar) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                                    required>
                            </div>

                            {{-- Actividad (Ancho completo) --}}
                            <div class="md:col-span-2">
                                <label for="actividad" class="block text-sm font-bold text-gray-700 uppercase">Actividad
                                    a realizar</label>
                                <textarea name="actividad" id="actividad" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500"
                                    required>{{ old('actividad', $comision->actividad) }}</textarea>
                            </div>

                            {{-- Vehículo --}}
                            <div>
                                <label for="vehiculo_id"
                                    class="block text-sm font-bold text-gray-700 uppercase">Vehículo Asignado</label>
                                <select name="vehiculo_id" id="vehiculo_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Ninguno / Particular</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}" {{ $comision->vehiculo_id == $vehiculo->id ? 'selected' : '' }}>
                                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->placa }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Proyecto --}}
                            <div>
                                <label for="proyecto_id"
                                    class="block text-sm font-bold text-gray-700 uppercase">Proyecto / Recurso</label>
                                <select name="proyecto_id" id="proyecto_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Seleccionar Proyecto</option>
                                    @foreach($proyectos as $proyecto)
                                        <option value="{{ $proyecto->id }}" {{ $comision->proyecto_id == $proyecto->id ? 'selected' : '' }}>
                                            {{ $proyecto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-4">
                            <a href="{{ route('comisiones-odso.index') }}"
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition font-bold text-sm uppercase">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition font-bold text-sm uppercase shadow-md">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
