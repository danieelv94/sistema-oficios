<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-xl text-slate-800 leading-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ __('Solicitar Oficio de Comisión ODSO') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Oficio de Comisión de Supervisión de Obra (DIH / DVPH)
                </p>
            </div>
            <a href="{{ route('comisiones-odso.index') }}"
                class="text-xs text-slate-600 hover:text-slate-900 font-semibold px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-50 transition">
                &larr; Volver a lista ODSO
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <div class="p-6 md:p-8 text-gray-900" x-data="comisionOdsoForm()">

                    {{-- Banner informativo del folio asignado --}}
                    <div class="mb-6 p-4 rounded-xl border border-amber-300 bg-amber-50/80 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-200/80 text-amber-900 flex items-center justify-center font-black">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-amber-900">
                                    Modalidad: Supervisión de Obra
                                </p>
                                <p class="text-xs text-amber-800">
                                    Este oficio se registrará en el libro exclusivo de comisiones ODSO.
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Siguiente Número</span>
                            <span class="font-mono font-black text-base text-amber-900 bg-white px-2.5 py-1 rounded-md border border-amber-300 shadow-2xs">
                                {{ $siguienteFolioOdso }}
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('comisiones-odso.store') }}" method="POST" @submit="isSubmitting = true">
                        @csrf
                        <div class="space-y-6">
                            <div class="border-t pt-6">
                                <p class="mb-4 text-sm text-slate-700">
                                    Por este conducto me permito comunicar a usted, que ha sido comisionado el (los) día (s):
                                    <input type="text" name="dias_comision" placeholder="Ej: 28 y 29 de Septiembre"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500" required>
                                </p>
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 border-l-4 border-amber-600 pl-4 bg-amber-50/40 p-3 rounded-r-lg">
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-600">Hora de Inicio</label>
                                        <input type="time" name="hora_inicio" min="07:00" max="21:00"
                                            class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-600">Hora de Término</label>
                                        <input type="time" name="hora_fin" min="07:00" max="23:00"
                                            class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500" required>
                                    </div>
                                </div>
                                <p class="mb-4 text-sm text-slate-700">
                                    para realizar la siguiente actividad de supervisión:
                                    <textarea name="actividad" rows="3"
                                        class="form-textarea rounded-md shadow-sm mt-1 block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500"
                                        placeholder="Describe detalladamente las acciones de supervisión de obra a realizar..." required></textarea>
                                </p>
                                <p class="text-sm text-slate-700">
                                    en el lugar o tramo:
                                    <input type="text" name="lugar"
                                        placeholder="Ej: Tramo del pozo y red de distribución en la comunidad de..."
                                        class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500" required>
                                </p>
                            </div>

                            <div class="border-t pt-6">
                                <label for="vehiculo_id" class="block font-medium text-sm text-gray-700">Vehículo Institucional (Opcional)</label>
                                <select name="vehiculo_id" id="vehiculo_id"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                                    <option value="">No se requiere vehículo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} - Placas: {{ $vehiculo->placa }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="border-t pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-red-600 text-xs font-semibold">Si no se viaticará dejar "Sin proyecto específico".</label>
                                    <label for="proyecto_id" class="block font-medium text-sm text-gray-700 mt-1">Proyecto (Opcional)</label>
                                    <select name="proyecto_id" id="proyecto_id" x-model="selectedProyecto"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                                        <option value="">Sin proyecto específico</option>
                                        @foreach($proyectos as $proyecto)
                                            <option value="{{ $proyecto->id }}">{{ $proyecto->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="unidades.length > 0" x-transition>
                                    <label class="text-red-600 text-xs font-semibold">Si no se viaticará no seleccionar unidad.</label>
                                    <label for="unidad_administrativa_id" class="block font-medium text-sm text-gray-700 mt-1">Unidad Administrativa</label>
                                    <select name="unidad_administrativa_id" id="unidad_administrativa_id"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-amber-500 focus:ring-amber-500"
                                        :disabled="!selectedProyecto">
                                        <option value="">Selecciona una unidad...</option>
                                        <template x-for="unidad in unidades" :key="unidad.id">
                                            <option :value="unidad.id" x-text="unidad.clave + ' ' + unidad.nombre"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t pt-6">
                            <a href="{{ route('comisiones-odso.index') }}"
                                class="text-sm text-gray-600 underline hover:text-gray-900">Cancelar</a>

                            <button type="submit"
                                class="ml-4 inline-flex items-center px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-sm shadow-md transition"
                                x-bind:disabled="isSubmitting"
                                x-bind:class="{ 'opacity-75 cursor-not-allowed': isSubmitting }">
                                <span x-show="!isSubmitting">Generar Oficio ODSO</span>
                                <span x-show="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Procesando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function comisionOdsoForm() {
            return {
                selectedProyecto: '',
                unidades: [],
                proyectos: @json($proyectos->keyBy('id')),
                isSubmitting: false,
                init() {
                    this.$watch('selectedProyecto', (newVal) => {
                        const unidadInput = document.getElementById('unidad_administrativa_id');
                        if (unidadInput) unidadInput.value = '';
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
