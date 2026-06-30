<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-widest">
            {{ __('Registrar Correspondencia Interna') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50" x-data="internosForm({
        areasData: {{ json_encode($areasData) }},
        selectedArea: '{{ old('area_captura_id', $areaCaptura->id ?? '') }}',
        selectedOrigenArea: '{{ old('area_origen_id', '') }}',
        oldRemitente: '{{ old('remitente', '') }}',
        isAdmin: {{ Auth::user()->role === 'admin' ? 'true' : 'false' }}
    })">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-blue-600">
                <div class="p-8 text-gray-900">

                    {{-- Encabezado del Formulario --}}
                    <div class="flex items-center mb-8 pb-4 border-b border-gray-150">
                        <span class="p-3 bg-blue-50 rounded-xl mr-4 text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-xl font-black text-gray-850 uppercase tracking-tight">Captura de Oficio Interno</h3>
                            <p class="text-xs text-gray-400 font-bold uppercase mt-0.5">Recepción de correspondencia interna de otras Direcciones</p>
                        </div>
                    </div>

                    <x-auth-validation-errors class="mb-6" :errors="$errors" />

                    <form action="{{ route('oficios.internos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            
                            {{-- Área Destinataria / Receptora (La que captura) --}}
                            <div>
                                <x-label for="area_captura_id" :value="__('Dirección Destinataria (Receptora / Captura)')" />
                                @if(Auth::user()->role === 'admin')
                                    <select name="area_captura_id" id="area_captura_id" x-model="selectedArea"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs">
                                        <option value="">-- Seleccione Dirección Receptora --</option>
                                        @foreach($areasData as $id => $a)
                                            <option value="{{ $id }}">{{ $a['name'] }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="area_captura_id" :value="selectedArea">
                                    <div class="block mt-1 w-full bg-gray-100 p-2.5 border border-gray-200 text-gray-650 rounded-md font-semibold text-xs leading-relaxed">
                                        {{ $areaCaptura->name ?? 'N/A' }}
                                    </div>
                                @endif
                            </div>

                            {{-- Número de Oficio / Folio Generado --}}
                            <div>
                                <x-label :value="__('Número de Oficio (Generado)')" />
                                <div class="flex items-center mt-1">
                                    <span class="inline-flex items-center px-3 py-2.5 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 font-bold text-xs uppercase">
                                        FOLIO
                                    </span>
                                    <div class="block w-full bg-blue-50/50 p-2.5 border border-gray-300 text-blue-800 rounded-r-md font-black text-sm tracking-wide text-center" x-text="generatedFolio">
                                        --
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Este consecutivo pertenece a tu Dirección Receptora</p>
                            </div>

                            {{-- Dirección de Origen (Emisora) --}}
                            <div>
                                <x-label for="area_origen_id" :value="__('Dirección de Origen (Emisora)')" />
                                <select name="area_origen_id" id="area_origen_id" x-model="selectedOrigenArea"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs" required>
                                    <option value="">-- Seleccione Área Emisora --</option>
                                    @foreach($areasData as $id => $a)
                                        <option value="{{ $id }}">{{ $a['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Remitente --}}
                            <div>
                                <x-label for="remitente" :value="__('Remitente (Director/Responsable)')" />
                                <x-input id="remitente" class="block mt-1 w-full text-xs" type="text" name="remitente" x-model="remitente" required />
                            </div>

                            {{-- Número de Oficio del Emisor (Origen) --}}
                            <div>
                                <x-label for="numero_origen" :value="__('Número de Oficio del Emisor (Origen)')" />
                                <x-input id="numero_origen" class="block mt-1 w-full text-xs" type="text" name="numero_origen" :value="old('numero_origen')" placeholder="Ej. DGI-INT-12/2026" required />
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Ingresa el número de oficio completo del emisor</p>
                            </div>

                            {{-- Prioridad --}}
                            <div>
                                <x-label for="prioridad" :value="__('Prioridad')" />
                                <select name="prioridad" id="prioridad"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs">
                                    <option value="Ordinaria" {{ old('prioridad') == 'Ordinaria' ? 'selected' : '' }}>Ordinaria</option>
                                    <option value="Urgente" {{ old('prioridad') == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                                </select>
                            </div>

                            {{-- Fecha del Oficio --}}
                            <div>
                                <x-label for="fecha_recepcion" :value="__('Fecha del Oficio')" />
                                <x-input id="fecha_recepcion" class="block mt-1 w-full text-xs" type="date" name="fecha_recepcion" :value="old('fecha_recepcion', date('Y-m-d'))" required />
                            </div>

                            {{-- Fecha Límite de Atención (Opcional) --}}
                            <div>
                                <x-label for="fecha_limite" :value="__('Fecha Límite (Opcional)')" />
                                <x-input id="fecha_limite" class="block mt-1 w-full text-xs" type="date" name="fecha_limite" :value="old('fecha_limite')" />
                            </div>

                            {{-- PDF del Oficio --}}
                            <div class="md:col-span-2">
                                <x-label for="archivo_pdf" :value="__('Subir PDF del Oficio')" />
                                <input type="file" name="archivo_pdf" id="archivo_pdf" accept=".pdf" required
                                    class="block mt-1 w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-300 rounded-md p-1" />
                                <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Solo se aceptan archivos PDF de hasta 10MB</p>
                            </div>

                            {{-- Asunto --}}
                            <div class="md:col-span-2">
                                <x-label for="asunto" :value="__('Asunto')" />
                                <textarea id="asunto" name="asunto" rows="4" required
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs"
                                    placeholder="Detalla el asunto del oficio interno recibido...">{{ old('asunto') }}</textarea>
                            </div>

                            {{-- Observaciones adicionales --}}
                            <div class="md:col-span-2">
                                <x-label for="observaciones" :value="__('Observaciones Adicionales (Opcional)')" />
                                <textarea id="observaciones" name="observaciones" rows="2"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs"
                                    placeholder="Comentarios o notas extras...">{{ old('observaciones') }}</textarea>
                            </div>

                        </div>

                        {{-- Botones de Acción --}}
                        <div class="flex items-center justify-end gap-3 mt-8 pt-4 border-t border-gray-150">
                            <a href="{{ route('oficios.internos.index') }}"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-750 text-xs font-bold uppercase tracking-wider hover:bg-gray-100 transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-bold uppercase tracking-wider shadow-md hover:shadow-lg transition">
                                Guardar Oficio Interno
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('internosForm', (config) => ({
                areasData: config.areasData,
                selectedArea: config.selectedArea || '',
                selectedOrigenArea: config.selectedOrigenArea || '',
                remitente: config.oldRemitente || '',
                isAdmin: config.isAdmin,

                get generatedFolio() {
                    if (this.selectedArea && this.areasData[this.selectedArea]) {
                        return this.areasData[this.selectedArea].folio;
                    }
                    return '--';
                },

                get origenPrefix() {
                    if (this.selectedOrigenArea && this.areasData[this.selectedOrigenArea]) {
                        return this.areasData[this.selectedOrigenArea].prefijo + '-INT-';
                    }
                    return '...-INT-';
                },

                init() {
                    // Cuando cambia la dirección de origen (emisora), actualizamos el remitente por defecto al titular de esa área
                    this.$watch('selectedOrigenArea', (newOrigenId) => {
                        if (newOrigenId && this.areasData[newOrigenId]) {
                            this.remitente = this.areasData[newOrigenId].remitente;
                        } else {
                            this.remitente = '';
                        }
                    });
                }
            }));
        });
    </script>
</x-app-layout>
