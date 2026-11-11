<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-white shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Bitácora de Actividades (Auditoría del Sistema)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Registro cronológico de movimientos, firmas DG, oficios, comisiones, kardex RH, accesos y cambios en la plataforma
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden">
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir Reporte
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        modalDetalleOpen: false,
        detalleActual: null,
        verDetalle(item) {
            this.detalleActual = item;
            this.modalDetalleOpen = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs de Auditoría -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 print:hidden">
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Eventos</p>
                    <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($stats['total']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Histórico acumulado</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-guinda-ceaa">Movimientos Hoy</p>
                    <p class="text-2xl font-extrabold text-guinda-ceaa mt-1">{{ number_format($stats['hoy']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ now()->format('d/m/Y') }}</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Creaciones</p>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['creaciones']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Nuevos registros</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Actualizaciones</p>
                    <p class="text-2xl font-extrabold text-blue-600 mt-1">{{ number_format($stats['actualizaciones']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Ediciones y estatus</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Eliminaciones</p>
                    <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ number_format($stats['eliminaciones']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Registros borrados</p>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Accesos / Sesiones</p>
                    <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ number_format($stats['accesos']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Inicios y cierres</p>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm print:hidden">
                <form method="GET" action="{{ route('bitacora.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Buscar actividad, folio, usuario o IP</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Ej. Firma, CEAA-2026, nombre de usuario, IP..."
                            class="w-full h-[38px] px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Módulo</label>
                        <select name="modulo" class="w-full h-[38px] px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                            <option value="">Todos los módulos</option>
                            @foreach($modulos as $mod)
                                <option value="{{ $mod }}" @selected(request('modulo') === $mod)>{{ $mod }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Tipo de Acción</label>
                        <select name="accion" class="w-full h-[38px] px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                            <option value="">Todas las acciones</option>
                            <option value="Creación" @selected(request('accion') === 'Creación')>🟢 Creación</option>
                            <option value="Actualización" @selected(request('accion') === 'Actualización')>🔵 Actualización</option>
                            <option value="Cambio de Estatus" @selected(request('accion') === 'Cambio de Estatus')>🟣 Cambio de Estatus</option>
                            <option value="Eliminación" @selected(request('accion') === 'Eliminación')>🔴 Eliminación</option>
                            <option value="Inicio de Sesión" @selected(request('accion') === 'Inicio de Sesión')>🔑 Inicio de Sesión</option>
                            <option value="Cierre de Sesión" @selected(request('accion') === 'Cierre de Sesión')>🚪 Cierre de Sesión</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Usuario</label>
                        <select name="user_id" class="w-full h-[38px] px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                            <option value="">Todos los usuarios</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="flex-1 h-[38px] px-3 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'modulo', 'accion', 'user_id', 'fecha_desde', 'fecha_hasta']))
                            <a href="{{ route('bitacora.index') }}"
                                class="h-[38px] px-3 inline-flex items-center bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabla de Auditoría -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">Fecha y Hora</th>
                                <th class="px-4 py-3 text-left">Usuario / Área</th>
                                <th class="px-4 py-3 text-left">Módulo y Acción</th>
                                <th class="px-4 py-3 text-left">Descripción de la Actividad</th>
                                <th class="px-4 py-3 text-left">IP / Origen</th>
                                <th class="px-4 py-3 text-right print:hidden">Cambios</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($actividades as $act)
                                <tr class="hover:bg-slate-50/80 transition align-top">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-bold text-slate-800">{{ $act->created_at->format('d/m/Y') }}</div>
                                        <div class="text-[11px] text-slate-500">🕒 {{ $act->created_at->format('H:i:s') }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $act->created_at->diffForHumans() }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-800">{{ $act->user_nombre ?: 'Sistema' }}</div>
                                        @if($act->area_nombre)
                                            <div class="text-[11px] text-guinda-ceaa font-semibold">{{ $act->area_nombre }}</div>
                                        @endif
                                        @if($act->user_role)
                                            <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 text-[10px] font-mono">
                                                {{ $act->user_role }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-bold text-slate-700 text-[11px] mb-1">{{ $act->modulo }}</div>
                                        @php
                                            $badgeColors = [
                                                'Creación' => 'bg-emerald-100 text-emerald-800',
                                                'Actualización' => 'bg-blue-100 text-blue-800',
                                                'Cambio de Estatus' => 'bg-purple-100 text-purple-800',
                                                'Eliminación' => 'bg-rose-100 text-rose-800',
                                                'Inicio de Sesión' => 'bg-indigo-100 text-indigo-800',
                                                'Cierre de Sesión' => 'bg-slate-200 text-slate-700',
                                            ];
                                            $badgeColor = $badgeColors[$act->accion] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold text-[10px] {{ $badgeColor }}">
                                            {{ $act->accion }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 max-w-md">
                                        <p class="text-slate-800 font-medium leading-snug">{{ $act->descripcion }}</p>
                                        @if($act->modelo_type && $act->modelo_id)
                                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                                Entidad: {{ $act->modelo_type }} #{{ $act->modelo_id }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-mono text-[11px] text-slate-700">{{ $act->ip_address ?: 'Local' }}</div>
                                        @if($act->user_agent)
                                            <div class="text-[10px] text-slate-400 truncate max-w-[140px]" title="{{ $act->user_agent }}">
                                                {{ $act->user_agent }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-right whitespace-nowrap print:hidden">
                                        @if(!empty($act->datos_anteriores) || !empty($act->datos_nuevos))
                                            <button type="button"
                                                @click="verDetalle(@js([
                                                    'id' => $act->id,
                                                    'fecha' => $act->created_at->format('d/m/Y H:i:s'),
                                                    'usuario' => $act->user_nombre,
                                                    'modulo' => $act->modulo,
                                                    'accion' => $act->accion,
                                                    'descripcion' => $act->descripcion,
                                                    'anteriores' => $act->datos_anteriores,
                                                    'nuevos' => $act->datos_nuevos,
                                                ]))"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition">
                                                🔍 Ver Detalle
                                            </button>
                                        @else
                                            <span class="text-slate-300 text-[11px]">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                        <p class="font-bold text-slate-700 text-sm">Aún no hay eventos registrados con estos filtros</p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Conforme los usuarios inicien sesión, registren oficios, firmen documentos en DG o generen comisiones, aparecerán aquí en tiempo real.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($actividades->hasPages())
                    <div class="px-4 py-3 border-t border-slate-100 print:hidden">
                        {{ $actividades->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Modal de Detalle Técnico (Antes vs Después) -->
        <div x-show="modalDetalleOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs print:hidden">
            <div @click.away="modalDetalleOpen = false"
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full overflow-hidden">
                <div class="bg-slate-800 px-6 py-4 flex items-center justify-between text-white">
                    <div>
                        <h3 class="font-bold text-sm uppercase tracking-wide">Detalle de Auditoría (Valores Antes / Después)</h3>
                        <p class="text-[11px] text-slate-300 mt-0.5" x-text="detalleActual ? (detalleActual.modulo + ' · ' + detalleActual.accion + ' · ' + detalleActual.fecha) : ''"></p>
                    </div>
                    <button type="button" @click="modalDetalleOpen = false" class="text-slate-300 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-xs" x-show="detalleActual">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <p class="font-bold text-slate-800" x-text="detalleActual?.descripcion"></p>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Realizado por: <span class="font-semibold text-slate-700" x-text="detalleActual?.usuario"></span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-show="detalleActual?.anteriores">
                            <h4 class="font-extrabold text-[11px] uppercase text-rose-700 mb-1.5">Valores Anteriores</h4>
                            <pre class="bg-rose-50/70 border border-rose-200 rounded-xl p-3 text-[11px] text-slate-800 overflow-x-auto max-h-64 font-mono"
                                x-text="JSON.stringify(detalleActual?.anteriores, null, 2)"></pre>
                        </div>

                        <div x-show="detalleActual?.nuevos" :class="!detalleActual?.anteriores ? 'sm:col-span-2' : ''">
                            <h4 class="font-extrabold text-[11px] uppercase text-emerald-700 mb-1.5">Valores Nuevos / Registrados</h4>
                            <pre class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 text-[11px] text-slate-800 overflow-x-auto max-h-64 font-mono"
                                x-text="JSON.stringify(detalleActual?.nuevos, null, 2)"></pre>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end">
                        <button type="button" @click="modalDetalleOpen = false"
                            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
