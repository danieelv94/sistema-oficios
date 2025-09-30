<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Solicitar Oficio de Comisión') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900" x-data="comisionForm()">
                    <h3 class="text-lg font-bold mb-4">Nueva Solicitud de Comisión</h3>
                    <form action="{{ route('comisiones.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div class="border-t pt-6">
                                <p class="mb-4">
                                    Por este conducto me permito comunicar a usted, que ha sido comisionado el (los) día (s):
                                    <input type="text" name="dias_comision" placeholder="Ej: 28 y 29 de Septiembre" class="form-input rounded-md shadow-sm mt-1 block w-full" required>
                                </p>
                                <p class="mb-4">
                                    para realizar la siguiente actividad:
                                    <textarea name="actividad" rows="3" class="form-textarea rounded-md shadow-sm mt-1 block w-full" placeholder="Describe la actividad a realizar..." required></textarea>
                                </p>
                                <p>
                                    en:
                                    <input type="text" name="lugar" placeholder="Ej: las oficinas de CONAGUA en la Ciudad de México" class="form-input rounded-md shadow-sm mt-1 block w-full" required>
                                </p>
                            </div>

                            <div class="border-t pt-6">
                                <label for="vehiculo_id" class="block font-medium text-sm text-gray-700">Vehículo Institucional (Opcional)</label>
                                <select name="vehiculo_id" id="vehiculo_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">No se requiere vehículo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}">
                                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }} - Placas: {{ $vehiculo->placa }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="border-t pt-6">
                                <label for="proyecto_id" class="block font-medium text-sm text-gray-700">Proyecto (Opcional)</label>
                                <select name="proyecto_id" id="proyecto_id" x-model="selectedProyecto" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Sin proyecto específico</option>
                                    @foreach($proyectos as $proyecto)
                                        <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                                    @endforeach
                                </select>

                                <div class="mt-4" x-show="unidades.length > 0" x-transition>
                                    <h4 class="font-semibold text-sm text-gray-800">Unidad(es) Administrativa(s) asociadas:</h4>
                                    <ul class="list-disc list-inside text-sm text-gray-600 mt-2 space-y-1">
                                        <template x-for="unidad in unidades" :key="unidad.id">
                                            <li x-text="unidad.clave + ' ' + unidad.nombre"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t pt-6">
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 underline hover:text-gray-900">Cancelar</a>
                            <x-button class="ml-4">
                                Generar Oficio
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function comisionForm() {
            return {
                selectedProyecto: '',
                unidades: [],
                proyectos: @json($proyectos->keyBy('id')),
                init() {
                    this.$watch('selectedProyecto', (newVal) => {
                        if (newVal && this.proyectos[newVal]) {
                            this.unidades = this.proyectos[newVal].unidades_administrativas;
                        } else {
                            this.unidades = [];
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>