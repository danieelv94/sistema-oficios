<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de la Solicitud de Soporte
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="border-b pb-4 mb-4">
                        <h3 class="text-lg font-bold">Solicitud: <span class="text-gray-700">{{ $ticket->subject }}</span></h3>
                        <div class="text-sm text-gray-500 mt-2">
                            <p><strong>Solicitante:</strong> {{ $ticket->user->name }}</p>
                            <p><strong>Área:</strong> {{ $ticket->user->area->name ?? 'N/A' }}</p>
                            <p><strong>Fecha de Solicitud:</strong> {{ $ticket->created_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-bold mb-2">Descripción del Problema:</h4>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->description }}</p>
                    </div>

                    @if($ticket->status == 'Concluido')
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                            <h4 class="font-bold text-green-800 mb-2">Solución Aplicada</h4>
                            <p class="text-gray-700 whitespace-pre-wrap mb-4">{{ $ticket->resolution_notes }}</p>

                            @if($ticket->evidence_path)
                                <h5 class="font-semibold mt-4 mb-2">Evidencia Fotográfica:</h5>
                                <img src="{{ asset('storage/' . $ticket->evidence_path) }}" alt="Evidencia de resolución" class="max-w-sm rounded-lg border">
                            @endif
                             <p class="text-xs text-gray-500 mt-4">Concluido el: {{ \Carbon\Carbon::parse($ticket->completed_at)->format('d/m/Y \a \l\a\s H:i') }}</p>
                        </div>
                    @else
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                            <h4 class="font-bold text-yellow-800">Esta solicitud aún está pendiente de atención.</h4>
                        </div>
                    @endif

                    <div class="mt-6 text-right">
                         <a href="{{ route('tickets.index') }}" class="text-gray-600 underline">Volver al Listado</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>