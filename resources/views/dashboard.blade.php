<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Oficios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Listado de Oficios</h3>
                        @if(Auth::user()->role == 'admin')
                        <a href="{{ route('oficios.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Registrar Nuevo Oficio</a>
                        @endif
                    </div>

                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">No. Oficio</th>
                                <th class="py-2 px-4 border-b">Remitente</th>
                                <th class="py-2 px-4 border-b">Asunto</th>
                                <th class="py-2 px-4 border-b">Fecha Recepción</th>
                                <th class="py-2 px-4 border-b">Estatus</th>
                                <th class="py-2 px-4 border-b">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($oficios as $oficio)
                                <tr class="hover:bg-gray-100">
                                    <td class="py-2 px-4 border-b">{{ $oficio->numero_oficio }}</td>
                                    <td class="py-2 px-4 border-b">{{ $oficio->remitente }}</td>
                                    <td class="py-2 px-4 border-b">{{ Str::limit($oficio->asunto, 40) }}</td>
                                    <td class="py-2 px-4 border-b">{{ $oficio->fecha_recepcion }}</td>
                                    <td class="py-2 px-4 border-b"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $oficio->estatus }}</span></td>
                                    <td class="py-2 px-4 border-b">
                                        <a href="{{ route('oficios.show', $oficio) }}" class="text-indigo-600 hover:text-indigo-900">Ver / Asignar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No hay oficios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>