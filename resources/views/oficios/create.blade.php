<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nuevo Oficio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('oficios.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="numero_oficio" class="block font-medium text-sm text-gray-700">Número de Oficio</label>
                                <input type="text" name="numero_oficio" id="numero_oficio" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                            </div>
                            <div>
                                <label for="fecha_recepcion" class="block font-medium text-sm text-gray-700">Fecha de Recepción</label>
                                <input type="date" name="fecha_recepcion" id="fecha_recepcion" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                            </div>
                            <div>
                                <label for="remitente" class="block font-medium text-sm text-gray-700">Remitente</label>
                                <input type="text" name="remitente" id="remitente" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                            </div>
                            <div>
                                <label for="municipio" class="block font-medium text-sm text-gray-700">Municipio</label>
                                <input type="text" name="municipio" id="municipio" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                            </div>
                            <div class="col-span-2">
                                <label for="asunto" class="block font-medium text-sm text-gray-700">Asunto</label>
                                <textarea name="asunto" id="asunto" rows="4" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required></textarea>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Guardar Oficio</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>