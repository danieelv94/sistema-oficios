<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Solicitar Oficio de Comisión') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900" x-data="comisionForm()">
                    <h3 class="text-lg font-bold mb-4">Nueva Solicitud de Comisión</h3>

                    <form action="{{ route('comisiones.store') }}" method="POST" @submit="isSubmitting = true">
                        @csrf
                        <div class="space-y-6">
                            <div class="border-t pt-6">
                                <p class="mb-4">
                                    Por este conducto me permito comunicar a usted, que ha sido comisionado el (los) día
                                    (s):
                                    <input type="text" name="dias_comision" placeholder="Ej: 28 y 29 de Septiembre"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full" required>
                                </p>
                                <p class="mb-4">
                                    para realizar la siguiente actividad:
                                    <textarea name="actividad" rows="3"
                                        class="form-textarea rounded-md shadow-sm mt-1 block w-full"
                                        placeholder="Describe la actividad a realizar..." required></textarea>
                                </p>
                                <p>
                                    en:
                                    <input type="text" name="lugar"
                                        placeholder="Ej: las oficinas de CONAGUA en la Ciudad de México"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full" required>
                                </p>
                            </div>

                            <div class="border-t pt-6">
                                <label for="vehiculo_id" class="block font-medium text-sm text-gray-700">Vehículo
                                    Institucional (Opcional)</label>
                                <select name="vehiculo_id" id="vehiculo_id"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                    <option value="">No se requiere vehículo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} -
                                            Placas: {{ $vehiculo->placa }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="border-t pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="proyecto_id" class="block font-medium text-sm text-gray-700">Proyecto
                                        (Opcional)</label>
                                    <select name="proyecto_id" id="proyecto_id" x-model="selectedProyecto"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                        <option value="">Sin proyecto específico</option>
                                        @foreach($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="unidades.length > 0" x-transition>
                                    <label for="unidad_administrativa_id"
                                        class="block font-medium text-sm text-gray-700">Unidad Administrativa</label>
                                    <select name="unidad_administrativa_id" id="unidad_administrativa_id"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
                                        :disabled="!selectedProyecto">
                                        <option value="">Selecciona una unidad...</option>
                                        <template x-for="unidad in unidades" :key="unidad.id">
                                            <option :value="unidad.id" x-text="unidad.clave + ' ' + unidad.nombre">
                                            </option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t pt-6">
                            <a href="{{ route('principal') }}"
                                class="text-sm text-gray-600 underline hover:text-gray-900">Cancelar</a>

                            <x-button class="ml-4" x-bind:disabled="isSubmitting"
                                x-bind:class="{ 'opacity-75 cursor-not-allowed': isSubmitting }">
                                <span x-show="!isSubmitting">Generar Oficio</span>
                                <span x-show="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Procesando...
                                </span>
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
                isSubmitting: false,
                init() {
                    this.$watch('selectedProyecto', (newVal) => {
                        document.getElementById('unidad_administrativa_id').value = '';
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