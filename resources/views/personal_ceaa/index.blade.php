<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    {{ __('Personal CEAA — Kardex de Recursos Humanos') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Expediente institucional del personal: salidas de comisión, incapacidades médicas, permisos, vacaciones y antigüedad
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 shadow-2xs">
                    Ejercicio: <span class="text-guinda-ceaa font-black">{{ $anioActual }}</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Tarjetas KPI de Resumen General --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Total Personal Activo --}}
                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Personal Activo CEAA</p>
                        <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalActivos) }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ number_format($totalInactivos) }} dados de baja / inactivos</p>
                    </div>
                    <div class="p-3 bg-guinda-ceaa/10 text-guinda-ceaa rounded-xl border border-guinda-ceaa/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Comisiones en el Año --}}
                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-sky-700 tracking-wider">Comisiones ({{ $anioActual }})</p>
                        <p class="text-2xl font-black text-sky-700 mt-1">{{ number_format($totalComisionesAnio) }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Oficios ordinarios y ODSO activos</p>
                    </div>
                    <div class="p-3 bg-sky-50 text-sky-700 rounded-xl border border-sky-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                </div>

                {{-- Incapacidades Registradas --}}
                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-rose-700 tracking-wider">Incapacidades ({{ $anioActual }})</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <p class="text-2xl font-black text-rose-700">{{ number_format($totalIncapacidadesAnio) }}</p>
                            <span class="text-xs font-bold text-rose-600/80">({{ number_format($diasIncapacidadAnio) }} días)</span>
                        </div>
                        <p class="text-[10px] mt-0.5 {{ $totalIncapacitadosHoy > 0 ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                            {{ $totalIncapacitadosHoy }} {{ $totalIncapacitadosHoy === 1 ? 'persona incapacitada hoy' : 'personas incapacitadas hoy' }}
                        </p>
                    </div>
                    <div class="p-3 bg-rose-50 text-rose-700 rounded-xl border border-rose-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Comentado temporalmente: Permisos e Incidencias
                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-amber-700 tracking-wider">Permisos / Incidencias ({{ $anioActual }})</p>
                        <p class="text-2xl font-black text-amber-700 mt-1">{{ number_format($totalPermisosAnio) }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Días económicos, licencias y pases</p>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-700 rounded-xl border border-amber-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                --}}
            </div>

            {{-- Buscador y Filtros --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5">
                <form action="{{ route('personal-ceaa.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        {{-- Buscador --}}
                        <div class="lg:col-span-2">
                            <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">
                                Buscar Servidor Público
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Nombre, No. empleado, cargo, CURP, RFC..."
                                    class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa transition">
                            </div>
                        </div>

                        {{-- Filtro por Dirección / Área --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">
                                Dirección / Área
                            </label>
                            <select name="area_id"
                                class="w-full py-2 px-3 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                                <option value="todas">Todas las Direcciones</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->prefijo ? $area->prefijo . ' — ' : '' }}{{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro por Estado --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">
                                Estatus Personal
                            </label>
                            <select name="estado"
                                class="w-full py-2 px-3 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                                <option value="activos" {{ $estado === 'activos' ? 'selected' : '' }}>Personal Activo</option>
                                <option value="incapacitados_hoy" {{ $estado === 'incapacitados_hoy' ? 'selected' : '' }}>Con Incapacidad Vigente Hoy</option>
                                <option value="inactivos" {{ $estado === 'inactivos' ? 'selected' : '' }}>Inactivos / Bajas</option>
                                <option value="todos" {{ $estado === 'todos' ? 'selected' : '' }}>Todos</option>
                            </select>
                        </div>

                        {{-- Filtro por Año --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">
                                Año Kardex
                            </label>
                            <select name="anio"
                                class="w-full py-2 px-3 text-xs border border-slate-300 rounded-lg focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                                @for($y = now()->year + 1; $y >= 2024; $y--)
                                    <option value="{{ $y }}" {{ (int)$anioActual === $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        @if(request()->hasAny(['search', 'area_id', 'nivel_id', 'estado', 'anio']))
                            <a href="{{ route('personal-ceaa.index') }}"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Limpiar Filtros
                            </a>
                        @endif
                        <button type="submit"
                            class="px-5 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Filtrar Personal
                        </button>
                    </div>
                </form>
            </div>

            {{-- Directorio de Personal CEAA --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Plantilla de Personal CEAA</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ $usuarios->total() }} servidores públicos
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Haz clic en <strong>Abrir Kardex</strong> para consultar comisiones por fecha, capturar incapacidades o permisos
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">No. Emp.</th>
                                <th class="py-3 px-4 text-left">Servidor Público / Cargo</th>
                                <th class="py-3 px-4 text-left">Adscripción</th>
                                <th class="py-3 px-4 text-left">Ingreso / Antigüedad</th>
                                <th class="py-3 px-4 text-center">Comisiones ({{ $anioActual }})</th>
                                <th class="py-3 px-4 text-center">Incapacidades ({{ $anioActual }})</th>
                                <th class="py-3 px-4 text-center">Vacaciones ({{ $anioActual }})</th>
                                {{-- Comentado temporalmente: <th class="py-3 px-4 text-center">Permisos ({{ $anioActual }})</th> --}}
                                <th class="py-3 px-4 text-center">Kardex</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($usuarios as $usuario)
                                @php
                                    $totalComisionesUsuario = ($usuario->comisiones_anio_count ?? 0) + ($usuario->comisiones_odso_anio_count ?? 0);
                                    $antiguedadTexto = 'Sin registro';
                                    if ($usuario->fecha_alta) {
                                        $aniosAnt = $usuario->fecha_alta->diffInYears(now());
                                        $mesesAnt = $usuario->fecha_alta->diffInMonths(now()) % 12;
                                        if ($aniosAnt > 0) {
                                            $antiguedadTexto = $aniosAnt . ($aniosAnt == 1 ? ' año' : ' años') . ($mesesAnt > 0 ? ", {$mesesAnt} m" : '');
                                        } elseif ($mesesAnt > 0) {
                                            $antiguedadTexto = $mesesAnt . ($mesesAnt == 1 ? ' mes' : ' meses');
                                        } else {
                                            $antiguedadTexto = 'Reciente ingreso';
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition {{ $usuario->trashed() ? 'bg-rose-50/20' : '' }}">
                                    <td class="py-3.5 px-4 whitespace-nowrap font-mono font-bold text-slate-700">
                                        #{{ $usuario->no_empleado ?? 'S/N' }}
                                        @if($usuario->nivel)
                                            <div class="text-[10px] font-sans text-slate-400 font-semibold">
                                                Niv: {{ $usuario->nivel->nombre }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-guinda-ceaa/10 text-guinda-ceaa flex items-center justify-center font-black text-xs shrink-0">
                                                {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('personal-ceaa.show', ['user' => $usuario->id, 'anio' => $anioActual]) }}"
                                                    class="font-black text-slate-800 hover:text-guinda-ceaa transition block">
                                                    {{ $usuario->prof }} {{ $usuario->name }}
                                                </a>
                                                <div class="text-[11px] text-slate-500 font-medium">
                                                    {{ $usuario->cargo ?? 'Sin cargo especificado' }}
                                                </div>
                                                @if($usuario->incapacidad_activa_hoy_count > 0)
                                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-rose-100 text-rose-700 border border-rose-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-ping"></span>
                                                        Incapacitado(a) hoy
                                                    </span>
                                                @endif
                                                @if($usuario->trashed())
                                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-200 text-slate-700">
                                                        Baja / Inactivo
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="font-bold text-slate-800 truncate" title="{{ $usuario->area->name ?? 'Sin Dirección' }}">
                                            {{ $usuario->area->name ?? 'Sin Dirección' }}
                                        </div>
                                        @if($usuario->subarea)
                                            <div class="text-[10px] text-slate-500 truncate" title="{{ $usuario->subarea->name }}">
                                                {{ $usuario->subarea->name }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($usuario->fecha_alta)
                                            <div class="font-semibold text-slate-800">{{ $usuario->fecha_alta->format('d/m/Y') }}</div>
                                            <div class="text-[10px] text-emerald-700 font-bold">{{ $antiguedadTexto }}</div>
                                        @else
                                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold uppercase">
                                                F. Alta Pendiente
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($totalComisionesUsuario > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-sky-50 text-sky-700 border border-sky-200">
                                                {{ $totalComisionesUsuario }} {{ $totalComisionesUsuario === 1 ? 'salida' : 'salidas' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">0</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if(($usuario->incapacidades_anio_count ?? 0) > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                                {{ $usuario->incapacidades_anio_count }} {{ $usuario->incapacidades_anio_count === 1 ? 'registro' : 'registros' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">0</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if(($usuario->dias_vacaciones_kardex_sum ?? 0) > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ (int) $usuario->dias_vacaciones_kardex_sum }} {{ (int) $usuario->dias_vacaciones_kardex_sum === 1 ? 'día' : 'días' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">0 días</span>
                                        @endif
                                    </td>

                                    {{-- Comentado temporalmente: Permisos
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if(($usuario->permisos_anio_count ?? 0) > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                                                {{ $usuario->permisos_anio_count }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">0</span>
                                        @endif
                                    </td>
                                    --}}

                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <a href="{{ route('personal-ceaa.show', ['user' => $usuario->id, 'anio' => $anioActual]) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg text-[11px] font-bold uppercase tracking-wider shadow-2xs transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Abrir Kardex
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-slate-400">
                                        <p class="text-sm font-bold text-slate-600">No se encontraron servidores públicos con los filtros seleccionados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $usuarios->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

