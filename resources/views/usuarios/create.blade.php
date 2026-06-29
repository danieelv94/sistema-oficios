<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="userForm({
        areas: {{ $areas->toJson() }},
        occupiedDirectors: {{ json_encode($occupiedDirectors) }},
        occupiedSubdirectors: {{ json_encode($occupiedSubdirectors) }},
        selectedArea: '{{ old('area_id', '') }}',
        selectedSubarea: '{{ old('subarea_id', '') }}',
        selectedRole: '{{ old('role', 'user') }}'
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form action="{{ route('usuarios.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="prof" :value="__('Título (Ej. C., Lic., Ing.)')" />
                                <x-input id="prof" class="block mt-1 w-full" type="text" name="prof" :value="old('prof', 'C.')" />
                            </div>
                            <div>
                                <x-label for="name" :value="__('Nombre')" />
                                <x-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="old('name')" required autofocus />
                            </div>
                            <div>
                                <x-label for="cargo" :value="__('Cargo')" />
                                <x-input id="cargo" class="block mt-1 w-full" type="text" name="cargo"
                                    :value="old('cargo')" />
                            </div>

                            <div>
                                <x-label for="email" :value="__('Email')" />
                                <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                                    :value="old('email')" required />
                            </div>

                            <div>
                                <x-label for="no_empleado" :value="__('Número de Empleado (Opcional)')" />
                                <x-input id="no_empleado" class="block mt-1 w-full" type="text" name="no_empleado"
                                    :value="old('no_empleado')" />
                            </div>

                            <div>
                                <x-label for="area_id" :value="__('Dirección (Área)')" />
                                <select name="area_id" id="area_id" x-model="selectedArea"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Selecciona una Dirección --</option>
                                    <template x-for="area in areas" :key="area.id">
                                        <option :value="area.id" x-text="area.name" :selected="area.id == selectedArea"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <x-label for="role" :value="__('Nivel de Usuario (Rol)')" />
                                <select name="role" id="role" x-model="selectedRole"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="user">Usuario Normal (Personal)</option>
                                    <option value="jefe_area" :disabled="isDirectorOccupied" x-text="isDirectorOccupied ? 'Jefe de Área (Director - OCUPADO)' : 'Jefe de Área (Director)'"></option>
                                    <option value="subdirector">Subdirector de Área</option>
                                    <option value="secretaria_area">Secretaria de Área</option>
                                    <option value="admin">Administrador</option>
                                    <option value="recepcionista">Recepcionista</option>
                                    <option value="correspondencia">Correspondencia</option>
                                </select>
                            </div>

                            <div x-show="subareas.length > 0">
                                <x-label for="subarea_id" :value="__('Subdirección')" />
                                <select name="subarea_id" id="subarea_id" x-model="selectedSubarea"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    :required="selectedRole === 'subdirector'">
                                    <option value="">-- Ninguna (Pertenece directamente al Director) --</option>
                                    <template x-for="sub in subareas" :key="sub.id">
                                        <option :value="sub.id" 
                                                :disabled="selectedRole === 'subdirector' && isSubdirectorOccupied(sub.id)"
                                                :selected="sub.id == selectedSubarea"
                                                x-text="selectedRole === 'subdirector' && isSubdirectorOccupied(sub.id) ? sub.name + ' (OCUPADO)' : sub.name">
                                        </option>
                                    </template>
                                </select>
                            </div>

                            <div x-show="selectedArea !== '' && subareas.length === 0" class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-500">
                                <span class="font-bold text-gray-700">Nota:</span> Esta dirección no cuenta con subdirecciones. El personal pertenecerá directamente al Director (Jefe de Área).
                            </div>

                            <div>
                                <x-label for="nivel_id" :value="__('Nivel / Puesto')" />
                                <select name="nivel_id" id="nivel_id"
                                    class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Selecciona un nivel...</option>
                                    @foreach($niveles as $nivel)
                                        <option value="{{ $nivel->id }}" {{ old('nivel_id') == $nivel->id ? 'selected' : '' }}>{{ $nivel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-label for="password" :value="__('Contraseña')" />
                                <x-input id="password" class="block mt-1 w-full" type="password" name="password"
                                    required />
                            </div>

                            <div>
                                <x-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                                <x-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                    name="password_confirmation" required />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('usuarios.index') }}" class="text-gray-600 underline text-sm mr-4">Cancelar</a>
                            <x-button>
                                {{ __('Crear Usuario') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userForm', (config) => ({
                areas: config.areas,
                occupiedDirectors: config.occupiedDirectors,
                occupiedSubdirectors: config.occupiedSubdirectors,
                selectedArea: config.selectedArea || '',
                selectedSubarea: config.selectedSubarea || '',
                selectedRole: config.selectedRole || 'user',

                get subareas() {
                    const area = this.areas.find(a => a.id == this.selectedArea);
                    return area ? area.subareas : [];
                },

                get isDirectorOccupied() {
                    return this.occupiedDirectors.includes(parseInt(this.selectedArea));
                },

                isSubdirectorOccupied(subareaId) {
                    return this.occupiedSubdirectors.includes(parseInt(subareaId));
                },

                init() {
                    this.$watch('selectedArea', value => {
                        this.selectedSubarea = '';
                        if (this.isDirectorOccupied && this.selectedRole === 'jefe_area') {
                            this.selectedRole = 'user';
                        }
                    });
                }
            }));
        });
    </script>
</x-app-layout>