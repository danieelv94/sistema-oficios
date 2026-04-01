<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Seguimiento de Circular:') }} {{ $aviso->titulo }}
            </h2>
            <a href="{{ route('avisos.index') }}"
                class="text-sm bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600">Volver al Historial</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-6 rounded-lg shadow border-b-4 border-blue-500 text-center">
                    <p class="text-gray-500 text-xs uppercase font-bold">Total Destinatarios</p>
                    <p class="text-3xl font-black">{{ $total }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-b-4 border-green-500 text-center">
                    <p class="text-gray-500 text-xs uppercase font-bold">Confirmados</p>
                    <p class="text-3xl font-black text-green-600">{{ $leidos }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-b-4 border-red-500 text-center">
                    <p class="text-gray-500 text-xs uppercase font-bold">Pendientes</p>
                    <p class="text-3xl font-black text-red-600">{{ $pendientes }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow border-b-4 border-[#932C43] text-center">
                    <p class="text-gray-500 text-xs uppercase font-bold">% de Cumplimiento</p>
                    <p class="text-3xl font-black text-[#932C43]">{{ $porcentaje }}%</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Lista de Control de Lectura</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Empleado</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Área</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Estado</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Fecha/Hora Lectura</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($usuarios as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->area->nombre ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->pivot->leido_at)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">✅
                                                    Enterado</span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">⏳
                                                    Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->pivot->leido_at ? \Carbon\Carbon::parse($user->pivot->leido_at)->format('d/m/Y H:i:s') : '---' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>