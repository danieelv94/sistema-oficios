<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Oficios de Comisión') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">
                            @if(Auth::user()->role == 'admin')
                                Todas las Solicitudes
                            @else
                                Mis Solicitudes
                            @endif
                        </h3>
                        <a href="{{ route('comisiones.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            + Solicitar Nueva Comisión
                        </a>
                    </div>
                    
                    @if(Auth::user()->role == 'admin')
                    <div class="mb-4">
                        <form action="{{ route('comisiones.index') }}" method="GET">
                            <div class="flex items-center">
                                <input type="text" name="search" placeholder="Buscar por no. de oficio, solicitante, actividad..." 
                                       class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ request('search') }}">
                                <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded-r-md hover:bg-gray-800">Buscar</button>
                            </div>
                        </form>
                    </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">No. Oficio</th>
                                    <th class="py-2 px-4 border-b text-left">Fecha</th>
                                    @if(Auth::user()->role == 'admin')
                                    <th class="py-2 px-4 border-b text-left">Solicitante</th>
                                    @endif
                                    <th class="py-2 px-4 border-b text-left">Actividad</th>
                                    <th class="py-2 px-4 border-b text-left">Estado</th>
                                    <th class="py-2 px-4 border-b text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comisiones as $comision)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ $comision->oficio_numero }}</td>
                                        <td class="py-2 px-4 border-b">{{ $comision->created_at->format('d/m/Y') }}</td>
                                        @if(Auth::user()->role == 'admin')
                                        <td class="py-2 px-4 border-b">{{ $comision->user->name }}</td>
                                        @endif
                                        <td class="py-2 px-4 border-b">{{ Str::limit($comision->actividad, 40) }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if($comision->status === 'Cancelado')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelado</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <div class="flex items-center space-x-2">
                                                @if($comision->status === 'Activo' || Auth::user()->role == 'admin')
                                                    <a href="{{ route('comisiones.show', $comision) }}" class="px-3 py-1 bg-gray-500 text-white rounded-md text-xs">Ver</a>
                                                @endif
                                                @if(Auth::user()->role == 'admin' && $comision->status === 'Activo')
                                                <form action="{{ route('comisiones.cancelar', $comision) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres cancelar este oficio?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-3 py-1 bg-yellow-500 text-white rounded-md text-xs">Cancelar</button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-4 text-gray-500">No se han encontrado comisiones que coincidan con la búsqueda.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $comisiones->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>