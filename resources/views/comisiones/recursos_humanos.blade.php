<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight uppercase tracking-widest">
                {{ __('Control de Acuses de Comisión') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-guinda-ceaa">
                <div class="p-6 text-gray-900">

                    {{-- Encabezado --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div class="flex items-center">
                            <span class="p-2 bg-red-50 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-guinda-ceaa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-black text-gray-700 uppercase italic">Control de Acuses de Comisión (Recursos Humanos)</h3>
                                <p class="text-xs text-gray-400 font-bold uppercase mt-0.5">Seguimiento de entrega de acuses para comisiones autorizadas del organismo</p>
                            </div>
                        </div>
                    </div>

                    {{-- Filtros y Buscador --}}
                    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
                        {{-- Tabs de Filtro --}}
                        <div class="flex border-b border-gray-200">
                            <a href="{{ request()->fullUrlWithQuery(['filtro' => 'Todos']) }}"
                                class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition-all {{ $filtro === 'Todos' ? 'border-guinda-ceaa text-guinda-ceaa' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Todos
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['filtro' => 'Pendientes']) }}"
                                class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition-all {{ $filtro === 'Pendientes' ? 'border-guinda-ceaa text-guinda-ceaa' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Pendientes
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['filtro' => 'Entregados']) }}"
                                class="px-4 py-2 border-b-2 font-bold text-xs uppercase tracking-wider transition-all {{ $filtro === 'Entregados' ? 'border-guinda-ceaa text-guinda-ceaa' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Entregados
                            </a>
                        </div>

                        {{-- Formulario de búsqueda --}}
                        <form action="{{ route('comisiones.recursos_humanos') }}" method="GET" class="flex gap-2 flex-1 md:max-w-md">
                            <input type="hidden" name="filtro" value="{{ $filtro }}">
                            <input type="text" name="search"
                                placeholder="Buscar por oficio, comisionado, actividad..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-guinda-ceaa focus:border-guinda-ceaa focus:outline-none text-xs"
                                value="{{ request('search') }}">
                            <button type="submit"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-black transition text-xs font-bold uppercase tracking-wider">Buscar</button>
                            @if(request('search'))
                                <a href="{{ route('comisiones.recursos_humanos', ['filtro' => $filtro]) }}"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-xs font-bold uppercase tracking-wider flex items-center justify-center">Limpiar</a>
                            @endif
                        </form>
                    </div>

                    {{-- Mensaje de éxito --}}
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-semibold rounded-r">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Tabla de Comisiones --}}
                    <div class="overflow-x-auto rounded-xl border border-gray-150 shadow-sm">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-150 text-gray-600 text-[10px] font-black uppercase tracking-wider">
                                    <th class="py-3 px-4 text-left">No. Oficio</th>
                                    <th class="py-3 px-4 text-left">Comisionado</th>
                                    <th class="py-3 px-4 text-left">Área</th>
                                    <th class="py-3 px-4 text-left">Fecha Comisión</th>
                                    <th class="py-3 px-4 text-left">Lugar / Actividad</th>
                                    <th class="py-3 px-4 text-center">Estatus Oficio</th>
                                    <th class="py-3 px-4 text-center">¿Entregó Acuse?</th>
                                    <th class="py-3 px-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs">
                                @forelse($comisiones as $comision)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                            {{ $comision->oficio_numero }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-gray-750">
                                            {{ $comision->user->prof }} {{ $comision->user->name }}
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-600">
                                            {{ $comision->user->area->name ?? 'Sin Dirección' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">
                                            {{ $comision->dias_comision }}
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate" title="{{ $comision->actividad }}">
                                            <span class="font-bold text-gray-700">{{ $comision->lugar }}</span> - <span class="italic text-gray-500">"{{ $comision->actividad }}"</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase
                                                {{ $comision->status == 'Cancelado' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}
                                            ">
                                                {{ $comision->status ?: 'Autorizado' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="flex items-center justify-center">
                                                @php
                                                    $canToggle = Auth::user()->role === 'admin' || !$comision->entregado_acuse;
                                                @endphp
                                                <label class="relative inline-flex items-center {{ $canToggle ? 'cursor-pointer' : 'cursor-not-allowed opacity-75' }}">
                                                    <input type="checkbox"
                                                           class="sr-only peer toggle-acuse"
                                                           data-id="{{ $comision->id }}"
                                                           {{ $comision->entregado_acuse ? 'checked' : '' }}
                                                           {{ $canToggle ? '' : 'disabled' }}>
                                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                                                    <span class="ml-2 text-[10px] font-bold uppercase tracking-wider {{ $comision->entregado_acuse ? 'text-green-600' : 'text-gray-400' }} status-label" id="label-{{ $comision->id }}">
                                                        {{ $comision->entregado_acuse ? 'Entregado' : 'Pendiente' }}
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <a href="{{ route('comisiones.show', $comision) }}"
                                                class="px-2.5 py-1 bg-gray-800 text-white rounded text-[10px] font-bold hover:bg-guinda-ceaa transition uppercase tracking-wider shadow-sm">
                                                Expediente
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-gray-400 italic">
                                            No se encontraron oficios de comisión.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="mt-4">
                        {{ $comisiones->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggles = document.querySelectorAll('.toggle-acuse');
            toggles.forEach(toggle => {
                toggle.addEventListener('change', function () {
                    const comisionId = this.dataset.id;
                    const statusLabel = document.getElementById('label-' + comisionId);
                    
                    // Deshabilitar temporalmente para evitar doble clic
                    this.disabled = true;
                    
                    fetch(`/comisiones/${comisionId}/toggle-acuse`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            if (response.status === 419) {
                                throw new Error('Sesión expirada (419). Por favor recarga la página.');
                            }
                            if (response.status === 403) {
                                throw new Error('No autorizado (403). No tienes los permisos necesarios.');
                            }
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            this.checked = data.entregado;
                            statusLabel.textContent = data.entregado ? 'Entregado' : 'Pendiente';
                            
                            if (data.entregado) {
                                statusLabel.classList.remove('text-gray-400');
                                statusLabel.classList.add('text-green-600');
                                
                                @if(Auth::user()->role !== 'admin')
                                    this.disabled = true;
                                    const labelEl = this.parentElement;
                                    labelEl.classList.remove('cursor-pointer');
                                    labelEl.classList.add('cursor-not-allowed', 'opacity-75');
                                @else
                                    this.disabled = false;
                                @endif
                            } else {
                                this.disabled = false;
                                statusLabel.classList.remove('text-green-600');
                                statusLabel.classList.add('text-gray-400');
                            }
                        } else {
                            this.disabled = false;
                        }
                    })
                    .catch(error => {
                        this.disabled = false;
                        this.checked = !this.checked; // revertir
                        alert('No se pudo actualizar el estatus del acuse: ' + error.message);
                        console.error(error);
                    });
                });
            });
        });
    </script>
</x-app-layout>
