<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-guinda-ceaa/10 flex items-center justify-center text-guinda-ceaa shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Mis Oficios en Firma de Dirección General
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if($esDirectorArea)
                            Seguimiento en tiempo real de documentos entregados por {{ Auth::user()->area ? Auth::user()->area->name : 'tu Dirección' }} para firma del Director General
                        @else
                            Seguimiento en tiempo real de los documentos que entregaste personalmente en Dirección General para firma
                        @endif
                    </p>
                </div>
            </div>

            @if(Auth::user()->canAccessFirmaDg())
                <div class="flex items-center gap-2">
                    <a href="{{ route('firma-dg.index') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white text-xs font-bold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Ir al Panel de Control DG
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- AVISO DESTACADO SI HAY OFICIOS YA FIRMADOS LISTOS PARA PASAR A RECOGER -->
            @if($stats['firmados'] > 0)
                <div class="bg-blue-50 border-2 border-blue-400 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-start sm:items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 animate-bounce">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-blue-900">
                                ¡Tienes {{ $stats['firmados'] }} {{ $stats['firmados'] === 1 ? 'documento YA FIRMADO listo' : 'documentos YA FIRMADOS listos' }} para recoger!
                            </h3>
                            <p class="text-xs text-blue-700 mt-0.5">
                                El Director General ya firmó {{ $stats['firmados'] === 1 ? 'este documento' : 'estos documentos' }}. Ya pueden pasar a Dirección General a recogerlo.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('firma-dg.mis-oficios', ['estatus' => 'Firmado']) }}"
                        class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold whitespace-nowrap transition shadow-sm">
                        Ver listos para recoger ({{ $stats['firmados'] }})
                    </a>
                </div>
            @endif

            <!-- AVISO SI HAY OFICIOS DEVUELTOS CON OBSERVACIONES -->
            @if($stats['devueltos'] > 0)
                <div class="bg-rose-50 border border-rose-300 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-extrabold text-rose-900 uppercase">
                                {{ $stats['devueltos'] }} {{ $stats['devueltos'] === 1 ? 'documento devuelto' : 'documentos devueltos' }} con observaciones
                            </h3>
                            <p class="text-xs text-rose-700">
                                Revisa las notas de Dirección General en la tabla inferior para atender las correcciones solicitadas.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tarjetas KPI del Área / Usuario -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
                <!-- Total -->
                <a href="{{ route('firma-dg.mis-oficios', ['alcance' => $alcance]) }}"
                    class="bg-white rounded-2xl p-4 border {{ empty($estatus) ? 'border-slate-400 ring-2 ring-slate-200' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            {{ $esDirectorArea ? 'Total de mi Dirección' : 'Total Mis Oficios' }}
                        </p>
                        <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $stats['total'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Dejados en DG</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </a>

                <!-- En Firma -->
                <a href="{{ route('firma-dg.mis-oficios', ['estatus' => 'En Firma', 'alcance' => $alcance]) }}"
                    class="bg-white rounded-2xl p-4 border {{ $estatus === 'En Firma' ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600">⏳ En Firma DG</p>
                        <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['en_firma'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">En espera de firma</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>

                <!-- Ya Firmados (Listos para recoger) -->
                <a href="{{ route('firma-dg.mis-oficios', ['estatus' => 'Firmado', 'alcance' => $alcance]) }}"
                    class="bg-white rounded-2xl p-4 border {{ $estatus === 'Firmado' ? 'border-blue-500 ring-2 ring-blue-100' : ($stats['firmados'] > 0 ? 'border-blue-300 bg-blue-50/30' : 'border-slate-200/80') }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">✍️ ¡Ya Firmados!</p>
                        <p class="text-2xl font-extrabold text-blue-600 mt-1">{{ $stats['firmados'] }}</p>
                        <p class="text-[11px] text-blue-700 font-semibold mt-0.5">Pasar a recoger a DG</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>

                <!-- Entregados / Ya recogidos -->
                <a href="{{ route('firma-dg.mis-oficios', ['estatus' => 'Entregado', 'alcance' => $alcance]) }}"
                    class="bg-white rounded-2xl p-4 border {{ $estatus === 'Entregado' ? 'border-emerald-400 ring-2 ring-emerald-100' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">✅ Ya Recogidos</p>
                        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['entregados'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Entregados al área</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </a>

                <!-- Devueltos -->
                <a href="{{ route('firma-dg.mis-oficios', ['estatus' => 'Devuelto', 'alcance' => $alcance]) }}"
                    class="bg-white rounded-2xl p-4 border {{ $estatus === 'Devuelto' ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600">⚠️ Devueltos</p>
                        <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ $stats['devueltos'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Para corrección</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
                <form method="GET" action="{{ route('firma-dg.mis-oficios') }}" class="grid grid-cols-1 {{ $esDirectorArea ? 'sm:grid-cols-4' : 'sm:grid-cols-3' }} gap-3 items-end">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Buscar por No. de Oficio o Asunto</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Ej. CEAA-2026-0145, convenio, solicitud..."
                            class="w-full h-[38px] px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                    </div>

                    @if($esDirectorArea)
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Mostrar</label>
                            <select name="alcance" class="w-full h-[38px] px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                                <option value="area" @selected($alcance === 'area')>Todos los de mi Dirección ({{ Auth::user()->area ? Auth::user()->area->name : 'Mi Área' }})</option>
                                <option value="mios" @selected($alcance === 'mios')>Solo los que yo entregué personalmente</option>
                            </select>
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="flex-1 h-[38px] px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'estatus', 'alcance']))
                            <a href="{{ route('firma-dg.mis-oficios') }}"
                                class="h-[38px] px-3 inline-flex items-center bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabla de Seguimiento -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">No. / Documento</th>
                                <th class="px-4 py-3 text-left">Asunto / Destinatario</th>
                                <th class="px-4 py-3 text-left">Quién lo Dejó y Cuándo</th>
                                <th class="px-4 py-3 text-center">Estatus Actual en DG</th>
                                <th class="px-4 py-3 text-left">Quién lo Recogió y Cuándo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($registros as $reg)
                                @php
                                    $numConsecutivo = $reg->consecutivo ?? $reg->id;

                                    $quienDejo = $reg->entregadoPor
                                        ? trim(($reg->entregadoPor->prof ? $reg->entregadoPor->prof . ' ' : '') . $reg->entregadoPor->name)
                                        : ($reg->entregado_por_nombre ?: 'Entregado por el área');

                                    $quienRecogio = $reg->recogidoPor
                                        ? trim(($reg->recogidoPor->prof ? $reg->recogidoPor->prof . ' ' : '') . $reg->recogidoPor->name)
                                        : $reg->recogido_por_nombre;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition align-top {{ $reg->estatus === 'Firmado' ? 'bg-blue-50/40' : '' }}">
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-md bg-guinda-ceaa text-white font-extrabold text-[11px]" title="Número consecutivo">
                                                #{{ str_pad($numConsecutivo, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px] uppercase">
                                                {{ $reg->tipo_documento }}
                                            </span>
                                            @if($reg->prioridad === 'Urgente')
                                                <span class="px-2 py-0.5 rounded-md bg-red-100 text-red-700 font-extrabold text-[10px] uppercase">
                                                    🔥 Urgente
                                                </span>
                                            @endif
                                        </div>
                                        <div class="font-bold text-slate-800 mt-1">
                                            {{ $reg->numero_oficio ?: 'S/N (Consecutivo #' . str_pad($numConsecutivo, 3, '0', STR_PAD_LEFT) . ')' }}
                                        </div>
                                        @if($reg->anexos)
                                            <div class="text-[11px] text-slate-500 mt-0.5">📎 {{ $reg->anexos }}</div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3.5 max-w-md">
                                        <p class="font-semibold text-slate-800 leading-snug">{{ $reg->asunto }}</p>
                                        @if($reg->destinatario)
                                            <p class="text-[11px] text-slate-500 mt-1">
                                                <span class="font-semibold">Dirigido a:</span> {{ $reg->destinatario }}
                                            </p>
                                        @endif
                                        @if($reg->observaciones)
                                            <div class="mt-1.5 px-2.5 py-1.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-[11px]">
                                                <span class="font-bold">Observación de DG:</span> {{ $reg->observaciones }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800">👤 {{ $quienDejo }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            📅 {{ $reg->fecha_recepcion->format('d/m/Y') }} · 🕒 {{ $reg->fecha_recepcion->format('H:i') }} hrs
                                        </div>
                                    </td>

                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($reg->estatus === 'En Firma')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-[11px]">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                ⏳ En Firma del Director General
                                            </span>
                                            <div class="text-[10px] text-slate-400 mt-1">En proceso de revisión y firma</div>
                                        @elseif($reg->estatus === 'Firmado')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-600 text-white font-extrabold text-[11px] shadow-sm">
                                                🔔 ¡YA FIRMADO! Pasar a recoger a DG
                                            </span>
                                            @if($reg->fecha_firma)
                                                <div class="text-[10px] text-blue-700 font-semibold mt-1">
                                                    Firmado el {{ $reg->fecha_firma->format('d/m/Y H:i') }} hrs
                                                </div>
                                            @endif
                                        @elseif($reg->estatus === 'Entregado')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px]">
                                                ✅ Firmado y Entregado al Área
                                            </span>
                                        @elseif($reg->estatus === 'Devuelto')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-800 font-bold text-[11px]">
                                                ⚠️ Devuelto con Observaciones
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        @if($reg->fecha_entrega || $quienRecogio)
                                            <div class="font-bold text-emerald-800">
                                                🙋‍♂️ {{ $quienRecogio ?: 'Recogido por el área' }}
                                            </div>
                                            @if($reg->fecha_entrega)
                                                <div class="text-[11px] text-slate-500 mt-0.5">
                                                    📅 {{ $reg->fecha_entrega->format('d/m/Y') }} · 🕒 {{ $reg->fecha_entrega->format('H:i') }} hrs
                                                </div>
                                            @endif
                                        @elseif($reg->estatus === 'Firmado')
                                            <span class="text-blue-600 font-bold text-[11px]">
                                                👉Disponible en recepción de DG
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Aún en Dirección General</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        <p class="font-bold text-slate-700 text-sm">No tienes documentos registrados en Firma de Dirección General</p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Cuando Dirección General registre un oficio entregado por ti o por tu área, aquí podrás consultar en tiempo real si sigue en firma o si ya puedes pasar a recogerlo.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($registros->hasPages())
                    <div class="px-4 py-3 border-t border-slate-100">
                        {{ $registros->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
