<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tickets de Soporte Técnico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="flex justify-end">
                <a href="{{ route('tickets.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    + Solicitar Asistencia
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-yellow-600 mb-4">Solicitudes Pendientes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Fecha de Solicitud</th>
                                    @if(Auth::user()->role == 'admin')
                                    <th class="py-2 px-4 border-b">Usuario Solicitante</th>
                                    <th class="py-2 px-4 border-b">Área</th>
                                    @endif
                                    <th class="py-2 px-4 border-b">Asunto</th>
                                    <th class="py-2 px-4 border-b">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketsPendientes as $ticket)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        @if(Auth::user()->role == 'admin')
                                        <td class="py-2 px-4 border-b">{{ $ticket->user->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $ticket->user->area->name ?? 'N/A' }}</td>
                                        @endif
                                        <td class="py-2 px-4 border-b">{{ $ticket->subject }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if(Auth::user()->role == 'admin')
                                            <a href="{{ route('tickets.edit', $ticket) }}" class="px-3 py-1 bg-green-500 text-white rounded-md text-xs whitespace-nowrap">Resolver</a>
                                            @else
                                            <a href="{{ route('tickets.show', $ticket) }}" class="px-3 py-1 bg-green-500 text-white rounded-md text-xs whitespace-nowrap">Ver Detalles</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-gray-500">No hay solicitudes pendientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-green-600 mb-4">Solicitudes Concluidas</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Fecha de Conclusión</th>
                                    @if(Auth::user()->role == 'admin')
                                    <th class="py-2 px-4 border-b">Usuario Solicitante</th>
                                    @endif
                                    <th class="py-2 px-4 border-b">Asunto</th>
                                    <th class="py-2 px-4 border-b">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketsConcluidos as $ticket)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($ticket->completed_at)->format('d/m/Y H:i') }}</td>
                                        @if(Auth::user()->role == 'admin')
                                        <td class="py-2 px-4 border-b">{{ $ticket->user->name }}</td>
                                        @endif
                                        <td class="py-2 px-4 border-b">{{ $ticket->subject }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="px-3 py-1 bg-green-500 text-white rounded-md text-xs whitespace-nowrap">Ver Detalles</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-gray-500">No hay solicitudes concluidas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>