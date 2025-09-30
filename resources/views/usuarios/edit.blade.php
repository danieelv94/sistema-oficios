<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Usuario') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form action="{{ route('usuarios.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="name" :value="__('Nombre')" />
                                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            </div>

                            <div>
                                <x-label for="email" :value="__('Email')" />
                                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            </div>
                            
                            <div>
                                <x-label for="no_empleado" :value="__('Número de Empleado (Opcional)')" />
                                <x-input id="no_empleado" class="block mt-1 w-full" type="text" name="no_empleado" :value="old('no_empleado', $user->no_empleado)" />
                            </div>

                            <div>
                                <x-label for="area_id" :value="__('Área')" />
                                <select name="area_id" id="area_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id', $user->area_id) == $area->id ? 'selected' : '' }}>
                                            {{ $area->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-label for="role" :value="__('Nivel de Usuario')" />
                                <select name="role" id="role" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Usuario Normal</option>
                                    <option value="jefe_area" {{ old('role', $user->role) == 'jefe_area' ? 'selected' : '' }}>Jefe de Área</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>
                            </div>
                            
                            <div>
                                <x-label for="nivel_id" :value="__('Nivel / Puesto')" />
                                <select name="nivel_id" id="nivel_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                    <option value="">Selecciona un nivel...</option>
                                    @foreach($niveles as $nivel)
                                        <option value="{{ $nivel->id }}" {{ old('nivel_id', $user->nivel_id) == $nivel->id ? 'selected' : '' }}>
                                            {{ $nivel->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-2">
                                <p class="text-sm text-gray-600">Deja los campos de contraseña en blanco si no deseas cambiarla.</p>
                            </div>
                            <div>
                                <x-label for="password" :value="__('Nueva Contraseña')" />
                                <x-input id="password" class="block mt-1 w-full" type="password" name="password" />
                            </div>
                            <div>
                                <x-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" />
                                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('usuarios.index') }}" class="text-gray-600 underline">Cancelar</a>
                            <x-button class="ml-4">
                                {{ __('Actualizar Usuario') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>