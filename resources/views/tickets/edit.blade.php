<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Resolver Solicitud de Soporte
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="border-b pb-4 mb-6">
                        <h3 class="text-lg font-bold">Solicitud: <span class="text-gray-700">{{ $ticket->subject }}</span></h3>
                        <div class="text-sm text-gray-500 mt-2">
                            <p><strong>Solicitante:</strong> {{ $ticket->user->name }}</p>
                            <p><strong>Descripción:</strong> {{ $ticket->description }}</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold mb-4">Registrar Solución</h3>
                    
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form action="{{ route('tickets.update', $ticket) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6">
                            <div>
                                <x-label for="resolution_notes" value="Notas de la Solución (¿Qué se hizo?)" />
                                <textarea name="resolution_notes" id="resolution_notes" rows="6" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>{{ old('resolution_notes') }}</textarea>
                            </div>

                            <div>
                                <x-label for="evidence" value="Subir Evidencia Fotográfica (Opcional)" />
                                <input type="file" name="evidence" id="evidence" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('tickets.index') }}" class="text-gray-600 underline">Cancelar</a>
                            <x-button class="ml-4 bg-green-600 hover:bg-green-700">
                                Marcar como Concluido
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>