<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Solicitar Asistencia Técnica') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Nueva Solicitud</h3>
                    <p class="text-sm text-gray-600 mb-6">Por favor, describe tu problema o solicitud de la manera más detallada posible.</p>
                    
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form action="{{ route('tickets.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <x-label for="subject" value="Asunto o Título Breve" />
                                <x-input id="subject" class="block mt-1 w-full" type="text" name="subject" :value="old('subject')" required autofocus placeholder="Ej. Mi impresora no funciona" />
                            </div>

                            <div>
                                <x-label for="description" value="Descripción Detallada del Problema" />
                                <textarea name="description" id="description" rows="6" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('tickets.index') }}" class="text-gray-600 underline">Cancelar</a>
                            <x-button class="ml-4">
                                Enviar Solicitud
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>