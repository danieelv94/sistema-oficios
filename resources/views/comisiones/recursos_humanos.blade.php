<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Control de Acuses de Comisión (Recursos Humanos)') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Seguimiento de entrega de acuses para comisiones autorizadas del organismo</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filtros y Buscador --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    {{-- Tabs de Filtro --}}
                    <div class="flex flex-wrap items-center gap-1 bg-slate-100/80 p-1 rounded-lg border border-slate-200/60">
                        <a href="{{ request()->fullUrlWithQuery(['filtro' => 'Todos']) }}"
                            class="px-3.5 py-1.5 rounded-md font-bold text-xs uppercase tracking-wider transition-all {{ $filtro === 'Todos' ? 'bg-white text-guinda-ceaa shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Todos
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filtro' => 'Pendientes']) }}"
                            class="px-3.5 py-1.5 rounded-md font-bold text-xs uppercase tracking-wider transition-all {{ $filtro === 'Pendientes' ? 'bg-white text-guinda-ceaa shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Pendientes
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['filtro' => 'Entregados']) }}"
                            class="px-3.5 py-1.5 rounded-md font-bold text-xs uppercase tracking-wider transition-all {{ $filtro === 'Entregados' ? 'bg-white text-guinda-ceaa shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Entregados
                        </a>
                    </div>

                    {{-- Formulario de búsqueda --}}
                    <form action="{{ route('comisiones.recursos_humanos') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto flex-1 md:max-w-md justify-end">
                        <input type="hidden" name="filtro" value="{{ $filtro }}">
                        <div class="relative flex-1 w-full">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search"
                                placeholder="Buscar por oficio, comisionado, actividad..."
                                class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa transition"
                                value="{{ request('search') }}">
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Buscar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('comisiones.recursos_humanos', ['filtro' => $filtro]) }}"
                                    class="w-full sm:w-auto px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs font-semibold rounded-r-lg shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Tabla de Comisiones --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Acuses de Comisiones</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $comisiones->total() }} registros
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Página {{ $comisiones->currentPage() }} de {{ $comisiones->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
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
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($comisiones as $comision)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    <td class="py-3.5 px-4 font-black text-guinda-ceaa whitespace-nowrap">
                                        <a href="{{ route('comisiones.show', $comision) }}" class="hover:underline">
                                            {{ $comision->oficio_numero }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-800 whitespace-nowrap">
                                        {{ $comision->user->prof }} {{ $comision->user->name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        {{ $comision->user->area->name ?? 'Sin Dirección' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap font-medium">
                                        {{ $comision->dias_comision }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate" title="{{ $comision->actividad }}">
                                        <span class="font-bold text-slate-800">{{ $comision->lugar }}</span> - <span class="italic text-slate-500">"{{ $comision->actividad }}"</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                            {{ $comision->status == 'Cancelado' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}
                                        ">
                                            {{ $comision->status ?: 'Autorizado' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
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
                                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                                                <span class="ml-2 text-[10px] font-bold uppercase tracking-wider {{ $comision->entregado_acuse ? 'text-emerald-700' : 'text-slate-400' }} status-label" id="label-{{ $comision->id }}">
                                                    {{ $comision->entregado_acuse ? 'Entregado' : 'Pendiente' }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <a href="{{ route('comisiones.show', $comision) }}"
                                            class="px-2.5 py-1 bg-slate-800 hover:bg-guinda-ceaa text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Expediente
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No se encontraron oficios de comisión.</p>
                                            <p class="text-xs text-slate-400">Intenta modificando los filtros de búsqueda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $comisiones->appends(request()->query())->links() }}
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
                                statusLabel.classList.remove('text-slate-400');
                                statusLabel.classList.add('text-emerald-700');
                                
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
                                statusLabel.classList.remove('text-emerald-700');
                                statusLabel.classList.add('text-slate-400');
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
