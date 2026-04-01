<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tickets de Soporte Técnico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="flex justify-end no-print">
                <a href="{{ route('tickets.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-md">
                    + Solicitar Asistencia
                </a>
            </div>

            {{-- TABLA DE PENDIENTES --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-yellow-600 mb-4 tracking-tight">Solicitudes Pendientes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="py-3 px-4 text-left text-xs font-bold uppercase text-gray-500">Fecha</th>
                                    @if(Auth::user()->role == 'admin')
                                        <th class="py-3 px-4 text-left text-xs font-bold uppercase text-gray-500">Usuario
                                        </th>
                                        <th class="py-3 px-4 text-left text-xs font-bold uppercase text-gray-500">Área</th>
                                    @endif
                                    <th class="py-3 px-4 text-left text-xs font-bold uppercase text-gray-500">Asunto
                                    </th>
                                    <th class="py-3 px-4 text-center text-xs font-bold uppercase text-gray-500">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketsPendientes as $ticket)
                                    <tr class="hover:bg-gray-50 border-b transition">
                                        <td class="py-3 px-4 text-sm">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        @if(Auth::user()->role == 'admin')
                                            <td class="py-3 px-4 text-sm font-medium text-gray-700">
                                                {{ $ticket->user ? $ticket->user->name : 'Usuario Deshabilitado' }}
                                            </td>
                                            <td class="py-3 px-4 text-sm text-gray-500 italic">
                                                {{ ($ticket->user && $ticket->user->area) ? $ticket->user->area->name : 'Sin Área' }}
                                            </td>
                                        @endif
                                        <td class="py-3 px-4 text-sm">{{ $ticket->subject }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if(Auth::user()->role == 'admin')
                                                <a href="{{ route('tickets.edit', $ticket) }}"
                                                    class="px-3 py-1 bg-green-500 text-white rounded-md text-xs">Resolver</a>
                                            @else
                                                <a href="{{ route('tickets.show', $ticket) }}"
                                                    class="px-3 py-1 bg-blue-500 text-white rounded-md text-xs">Ver</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-gray-400 italic">No hay solicitudes
                                            pendientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TABLA DE CONCLUIDOS --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold text-green-600 mb-4 tracking-tight">Solicitudes Concluidas</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="py-3 px-4 text-left text-xs font-bold uppercase text-gray-500">Fecha
                                        Conclusión</th>
                                    @if(Auth::user()->role == 'admin')
                                        <th class="py-3 px-4 text-left text-xs font-bold uppercase text-gray-500">Usuario
                                        </th>
                                    @endif
                                    <th class="py-3 px-4 text-left text-xs font-bold uppercase text-gray-500">Asunto
                                    </th>
                                    <th class="py-3 px-4 text-center text-xs font-bold uppercase text-gray-500">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ticketsConcluidos as $ticket)
                                    <tr class="hover:bg-gray-50 border-b transition">
                                        <td class="py-3 px-4 text-sm">
                                            {{ $ticket->completed_at ? \Carbon\Carbon::parse($ticket->completed_at)->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                        @if(Auth::user()->role == 'admin')
                                            <td class="py-3 px-4 text-sm">
                                                {{ $ticket->user ? $ticket->user->name : 'Ex-usuario' }}
                                            </td>
                                        @endif
                                        <td class="py-3 px-4 text-sm">{{ $ticket->subject }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                class="px-3 py-1 bg-gray-500 text-white rounded-md text-xs">Detalles</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-gray-400 italic">No hay registros
                                            históricos.</td>
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