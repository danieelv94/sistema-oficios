<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Circulares Emitidas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Registro de Circulares</h3>
                        @if(in_array(Auth::user()->role, ['admin', 'secretaria_area', 'jefe_area']))
                            <a href="{{ route('avisos.create') }}"
                                class="px-4 py-2 bg-[#932C43] text-white rounded-md text-sm font-bold">
                                + Nueva Circular
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-bold uppercase text-gray-500">Circular
                                    </th>
                                    <th class="py-3 px-4 text-center text-xs font-bold uppercase text-gray-500">
                                        Prioridad</th>
                                    <th class="py-3 px-4 text-center text-xs font-bold uppercase text-gray-500">Lecturas
                                    </th>
                                    <th class="py-3 px-4 text-center text-xs font-bold uppercase text-gray-500">Fecha
                                    </th>
                                    <th class="py-3 px-4 text-center text-xs font-bold uppercase text-gray-500">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($avisos as $aviso)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-gray-800">{{ $aviso->titulo }}</div>
                                            <div class="text-xs text-gray-500">
                                                Emitido por:
                                                {{ $aviso->autor ? $aviso->autor->name : 'Usuario Deshabilitado' }}
                                                ({{ $aviso->autor && $aviso->autor->area ? $aviso->autor->area->name : 'Área N/D' }})
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full {{ $aviso->prioridad == 'Urgente' ? 'bg-red-100 text-red-700 font-bold' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $aviso->prioridad }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="text-sm font-bold text-gray-700">{{ $aviso->leidos }} /
                                                {{ $aviso->total }}
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                @php $porcentaje = $aviso->total > 0 ? ($aviso->leidos / $aviso->total) * 100 : 0; @endphp
                                                <div class="bg-green-500 h-1.5 rounded-full"
                                                    style="width: {{ $porcentaje }}%"></div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-center text-sm text-gray-500">
                                            {{ $aviso->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <a href="{{ route('avisos.seguimiento', $aviso) }}"
                                                class="inline-flex items-center text-[#932C43] hover:underline text-sm font-bold">
                                                Ver Seguimiento &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-gray-400 italic">No hay circulares
                                            registradas para su área.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $avisos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>