<x-app-layout>
    <style>
        @media print {
            .no-print,
            nav,
            button,
            .modal-backdrop,
            footer {
                display: none !important;
            }

            .printable-header {
                display: block !important;
            }

            .print-force-show {
                display: block !important;
            }

            body {
                background: white !important;
                font-size: 9pt !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #cbd5e1 !important;
                padding: 5px 7px !important;
                font-size: 8pt !important;
                color: #0f172a !important;
            }

            th {
                background-color: #f1f5f9 !important;
            }
        }

        .printable-header {
            display: none;
        }
    </style>

    @php
        $fechaAlta = $empleado->fecha_alta;
        $fechaAltaCarbon = $fechaAlta ? \Carbon\Carbon::parse($fechaAlta) : null;
        $antiguedad = 'No registrada';
        $aptoVacaciones = false;

        if ($fechaAltaCarbon) {
            $diffAnios = $fechaAltaCarbon->diffInYears(now());
            $diffMeses = $fechaAltaCarbon->diffInMonths(now()) % 12;
            $diffDias = $fechaAltaCarbon->diffInDays(now()) % 30;

            $partes = [];
            if ($diffAnios > 0) $partes[] = $diffAnios == 1 ? '1 año' : "{$diffAnios} años";
            if ($diffMeses > 0) $partes[] = $diffMeses == 1 ? '1 mes' : "{$diffMeses} meses";
            if ($diffAnios == 0 && $diffMeses == 0) $partes[] = $diffDias == 1 ? '1 día' : "{$diffDias} días";
            $antiguedad = implode(', ', $partes);
            $aptoVacaciones = $diffAnios >= 1;
        }

        $nameParts = explode(' ', trim($empleado->name));
        $initials = strtoupper(substr($nameParts[0] ?? 'U', 0, 1) . substr($nameParts[1] ?? '', 0, 1));

        $nombresMeses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 no-print">
            <div class="flex items-center gap-3">
                <a href="{{ route('personal-ceaa.index', ['anio' => $anio]) }}"
                    class="p-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-guinda-ceaa hover:border-guinda-ceaa transition shadow-2xs"
                    title="Volver al Directorio de Personal">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <span>Kardex de Personal:</span>
                        <span class="text-guinda-ceaa">{{ $empleado->prof }} {{ $empleado->name }}</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                        Expediente individual de comisiones, incapacidades médicas, permisos, vacaciones y datos de emergencia
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Selector de Año --}}
                <form action="{{ route('personal-ceaa.show', $empleado->id) }}" method="GET" class="inline-flex items-center">
                    <select name="anio" onchange="this.form.submit()"
                        class="py-1.5 pl-3 pr-8 text-xs font-bold border border-slate-300 rounded-lg bg-white text-slate-700 focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        @for($y = now()->year + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ (int)$anio === $y ? 'selected' : '' }}>Kardex {{ $y }}</option>
                        @endfor
                    </select>
                </form>

                {{-- Botón Imprimir Kardex --}}
                <button type="button" onclick="window.print()"
                    class="px-3.5 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-2xs transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir Kardex
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen"
        x-data="{
            tabActiva: 'comisiones',
            modalIncapacidad: false,
            modalPermiso: false,
            modalVacacion: false,
            modalExpediente: false,
            mostrarCalendario: true,
            incInicio: '{{ now()->toDateString() }}',
            incFin: '{{ now()->toDateString() }}',
            permInicio: '{{ now()->toDateString() }}',
            permFin: '{{ now()->toDateString() }}',
            modoVacacion: 'rango',
            vacInicio: '{{ now()->toDateString() }}',
            vacFin: '{{ now()->toDateString() }}',
            soloHabiles: true,
            fechasVacacionesSueltas: ['{{ now()->toDateString() }}'],
            calcularDias(inicio, fin) {
                if (!inicio || !fin) return 0;
                const d1 = new Date(inicio + 'T00:00:00');
                const d2 = new Date(fin + 'T00:00:00');
                const diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
                return diff > 0 ? diff : 0;
            },
            calcularDiasVacacion(inicio, fin, habiles) {
                if (!inicio || !fin) return 0;
                const d1 = new Date(inicio + 'T00:00:00');
                const d2 = new Date(fin + 'T00:00:00');
                if (d2 < d1) return 0;
                if (!habiles) {
                    return Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
                }
                let count = 0;
                const cur = new Date(d1);
                while (cur <= d2) {
                    const day = cur.getDay();
                    if (day !== 0 && day !== 6) count++;
                    cur.setDate(cur.getDate() + 1);
                }
                return count;
            }
        }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Encabezado Exclusivo para Impresión --}}
            <div class="printable-header mb-4">
                <div class="flex justify-between items-center border-b-2 border-guinda-ceaa pb-3">
                    <img src="{{ asset('images/encabezado.png') }}" style="height: 55px;" alt="CEAA">
                    <div class="text-right">
                        <h1 class="text-base font-black uppercase text-guinda-ceaa">COMISIÓN ESTATAL DEL AGUA Y ALCANTARILLADO</h1>
                        <h2 class="text-xs font-bold uppercase text-slate-800">Subdirección de Recursos Humanos — Hoja de Kardex Individual ({{ $anio }})</h2>
                        <p class="text-[10px] text-slate-500">Fecha de impresión: {{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Alertas de sesión --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 text-xs font-bold rounded-r-xl shadow-2xs flex items-center gap-2 no-print">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 text-xs rounded-r-xl shadow-2xs no-print">
                    <p class="font-black uppercase mb-1">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SECCIÓN 1: FICHA LABORAL Y EXPEDIENTE BÁSICO DEL SERVIDOR PÚBLICO --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-guinda-ceaa to-guinda-ceaa-hover text-white flex items-center justify-center font-black text-lg shadow-xs shrink-0">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-black text-slate-900">
                                    {{ $empleado->prof }} {{ $empleado->name }}
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-guinda-ceaa/10 text-guinda-ceaa border border-guinda-ceaa/20">
                                    No. Empleado: #{{ $empleado->no_empleado ?? 'S/N' }}
                                </span>
                                @if($empleado->trashed())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700">
                                        Baja / Inactivo
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Activo
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-slate-600 uppercase mt-0.5">
                                {{ $empleado->cargo ?? 'Cargo no especificado' }}
                                @if($empleado->nivel)
                                    <span class="text-slate-400 mx-1">&bull;</span>
                                    <span class="text-guinda-ceaa">Nivel {{ $empleado->nivel->nombre }}</span>
                                @endif
                            </p>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                🏢 {{ $empleado->area->name ?? 'Sin Dirección' }}
                                @if($empleado->subarea)
                                    — <span class="font-semibold text-slate-600">{{ $empleado->subarea->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Botones rápidos de captura para RH y Admin --}}
                    <div class="flex flex-wrap items-center gap-2 no-print">
                        <button type="button" @click="modalIncapacidad = true"
                            class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-2xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Registrar Incapacidad
                        </button>

                        <button type="button" @click="modalVacacion = true"
                            class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-2xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Registrar Vacaciones
                        </button>

                        {{-- Comentado temporalmente: Registrar Permiso / Incidencia
                        <button type="button" @click="modalPermiso = true"
                            class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-2xs transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Registrar Permiso / Incidencia
                        </button>
                        --}}

                        <button type="button" @click="modalExpediente = true"
                            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar Expediente
                        </button>
                    </div>
                </div>

                {{-- Cuadrícula de Información del Expediente --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 pt-4 text-xs">
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Fecha de Ingreso</span>
                        <span class="font-bold text-slate-800 block mt-0.5">
                            {{ $fechaAltaCarbon ? $fechaAltaCarbon->format('d/m/Y') : 'Pendiente' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Antigüedad</span>
                        <span class="font-bold text-emerald-700 block mt-0.5">{{ $antiguedad }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">CURP / RFC</span>
                        <span class="font-mono text-[11px] font-bold text-slate-800 block mt-0.5">
                            {{ $empleado->curp ?? 'S/CURP' }}
                        </span>
                        <span class="font-mono text-[10px] text-slate-500 block">
                            RFC: {{ $empleado->rfc ?? 'S/RFC' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">NSS / ISSSTE y Sangre</span>
                        <span class="font-bold text-slate-800 block mt-0.5">
                            {{ $empleado->nss ?? 'No registrado' }}
                        </span>
                        <span class="text-[10px] font-bold text-rose-700 block">
                            Tipo Sangre: {{ $empleado->tipo_sangre ?? 'N/D' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Teléfono / Correo</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $empleado->telefono ?? 'Sin teléfono' }}</span>
                        <span class="text-[10px] text-slate-500 truncate block" title="{{ $empleado->email }}">{{ $empleado->email }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Contacto de Emergencia</span>
                        @if($empleado->contacto_emergencia_nombre || $empleado->contacto_emergencia_telefono)
                            <span class="font-bold text-slate-800 block mt-0.5">
                                {{ $empleado->contacto_emergencia_nombre ?? 'Contacto' }}
                                @if($empleado->contacto_emergencia_parentesco)
                                    ({{ $empleado->contacto_emergencia_parentesco }})
                                @endif
                            </span>
                            <span class="text-[11px] font-mono font-bold text-guinda-ceaa block">
                                📞 {{ $empleado->contacto_emergencia_telefono ?? 'S/T' }}
                            </span>
                        @else
                            <span class="text-slate-400 italic block mt-0.5">Sin registrar</span>
                        @endif
                    </div>
                </div>

                @if($empleado->notas_rh)
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-start gap-2 text-xs bg-amber-50/50 p-3 rounded-lg border border-amber-200/60">
                        <span class="font-black uppercase text-[10px] text-amber-800 shrink-0 mt-0.5">Notas RH:</span>
                        <p class="text-slate-700 font-medium">{{ $empleado->notas_rh }}</p>
                    </div>
                @endif
            </div>

            {{-- SECCIÓN 2: TARJETAS RESUMEN ANUAL DEL KARDEX --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Comisiones del Año --}}
                <div @click="tabActiva = 'comisiones'"
                    class="cursor-pointer bg-white rounded-xl p-5 shadow-xs border transition hover:shadow-md {{ $totalComisionesActivas > 0 ? 'border-sky-200' : 'border-slate-200/80' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-black uppercase text-sky-700 tracking-wider">Salidas de Comisión ({{ $anio }})</p>
                            <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalComisionesActivas }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">
                                {{ $comisionesUnificadas->where('es_odso', false)->count() }} Normales &bull;
                                {{ $comisionesUnificadas->where('es_odso', true)->count() }} ODSO
                            </p>
                        </div>
                        <div class="p-3 bg-sky-50 text-sky-700 rounded-xl border border-sky-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Incapacidades del Año --}}
                <div @click="tabActiva = 'incapacidades'"
                    class="cursor-pointer bg-white rounded-xl p-5 shadow-xs border transition hover:shadow-md {{ $incapacidades->count() > 0 ? 'border-rose-200' : 'border-slate-200/80' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-black uppercase text-rose-700 tracking-wider">Incapacidades ({{ $anio }})</p>
                            <div class="flex items-baseline gap-2 mt-1">
                                <p class="text-2xl font-black text-rose-700">{{ $incapacidades->count() }}</p>
                                <span class="text-xs font-bold text-rose-600">({{ $totalDiasIncapacidad }} {{ $totalDiasIncapacidad === 1 ? 'día' : 'días' }})</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5">Certificados y licencias médicas</p>
                        </div>
                        <div class="p-3 bg-rose-50 text-rose-700 rounded-xl border border-rose-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Comentado temporalmente: Permisos e Incidencias
                <div @click="tabActiva = 'permisos'"
                    class="cursor-pointer bg-white rounded-xl p-5 shadow-xs border transition hover:shadow-md {{ $permisos->count() > 0 ? 'border-amber-200' : 'border-slate-200/80' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-black uppercase text-amber-700 tracking-wider">Permisos / Incidencias ({{ $anio }})</p>
                            <div class="flex items-baseline gap-2 mt-1">
                                <p class="text-2xl font-black text-amber-700">{{ $permisos->count() }}</p>
                                <span class="text-xs font-bold text-amber-600">({{ $totalDiasPermisos }} {{ $totalDiasPermisos === 1 ? 'día' : 'días' }})</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5">Días económicos, pases y justificantes</p>
                        </div>
                        <div class="p-3 bg-amber-50 text-amber-700 rounded-xl border border-amber-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>
                --}}

                {{-- Vacaciones --}}
                <div @click="tabActiva = 'vacaciones'"
                    class="cursor-pointer bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-black uppercase text-emerald-700 tracking-wider">Vacaciones ({{ $anio }})</p>
                            <div class="flex items-baseline gap-2 mt-1">
                                <p class="text-2xl font-black text-emerald-700">{{ $diasVacacionesAprobados }}</p>
                                <span class="text-xs font-bold text-slate-500">/ 10 días aprobados</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5">
                                P1: {{ $diasP1Usados }}/5 &bull; P2: {{ $diasP2Usados }}/5
                                @if($diasVacacionesPendientes > 0)
                                    &bull; <span class="text-amber-600 font-bold">{{ $diasVacacionesPendientes }} pend.</span>
                                @endif
                            </p>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ALERTA DE TRASLAPES SI EXISTEN --}}
            @if(count($traslapes) > 0)
                <div class="bg-purple-50 border-l-4 border-purple-600 rounded-r-xl p-4 shadow-2xs">
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 bg-purple-600 text-white rounded-lg shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="text-xs">
                            <h4 class="font-black text-purple-900 uppercase tracking-wider">
                                Atención RH: Se detectaron {{ count($traslapes) }} {{ count($traslapes) === 1 ? 'fecha con traslape' : 'fechas con traslape' }} de eventos en {{ $anio }}
                            </h4>
                            <ul class="mt-1.5 space-y-1 text-purple-800">
                                @foreach($traslapes as $fechaT => $eventosT)
                                    <li>
                                        <strong class="font-mono">{{ \Carbon\Carbon::parse($fechaT)->format('d/m/Y') }}:</strong>
                                        {{ implode(' + ', array_column($eventosT, 'titulo')) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- SECCIÓN 3: CALENDARIO ANUAL VISUAL (SEMÁFORO DE COMISIONES, INCAPACIDADES, VACACIONES Y PERMISOS) --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden no-print">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-guinda-ceaa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">
                            Mapa Anual de Asistencia, Comisiones e Incidencias ({{ $anio }})
                        </h3>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-[11px] font-bold">
                        <span class="inline-flex items-center gap-1.5 text-sky-700">
                            <span class="w-3 h-3 rounded bg-sky-500 inline-block"></span> Comisión
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-rose-700">
                            <span class="w-3 h-3 rounded bg-rose-500 inline-block"></span> Incapacidad
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-emerald-700">
                            <span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span> Vacaciones
                        </span>
                        {{-- Comentado temporalmente: Permiso / Incidencia
                        <span class="inline-flex items-center gap-1.5 text-amber-700">
                            <span class="w-3 h-3 rounded bg-amber-500 inline-block"></span> Permiso / Incidencia
                        </span>
                        --}}
                        <span class="inline-flex items-center gap-1.5 text-purple-700">
                            <span class="w-3 h-3 rounded bg-purple-600 inline-block"></span> Traslape
                        </span>
                        <button type="button" @click="mostrarCalendario = !mostrarCalendario"
                            class="ml-2 text-xs text-guinda-ceaa underline font-bold">
                            <span x-text="mostrarCalendario ? 'Ocultar calendario' : 'Mostrar calendario'"></span>
                        </button>
                    </div>
                </div>

                <div x-show="mostrarCalendario" x-transition class="p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @for($mes = 1; $mes <= 12; $mes++)
                            @php
                                $primerDiaMes = \Carbon\Carbon::createFromDate($anio, $mes, 1);
                                $diasEnMes = $primerDiaMes->daysInMonth;
                                // Ajuste lunes=1 .. domingo=7
                                $diaSemanaInicio = $primerDiaMes->dayOfWeekIso;
                            @endphp
                            <div class="border border-slate-200/80 rounded-xl p-3 bg-slate-50/30">
                                <div class="text-center font-black text-xs uppercase tracking-wider text-slate-700 pb-2 mb-2 border-b border-slate-200/60">
                                    {{ $nombresMeses[$mes] }} {{ $anio }}
                                </div>
                                <div class="grid grid-cols-7 gap-1 text-center text-[9px] font-black text-slate-400 uppercase mb-1">
                                    <div>L</div><div>M</div><div>M</div><div>J</div><div>V</div><div>S</div><div>D</div>
                                </div>
                                <div class="grid grid-cols-7 gap-1 text-center text-[10px]">
                                    @for($blank = 1; $blank < $diaSemanaInicio; $blank++)
                                        <div></div>
                                    @endfor

                                    @for($dia = 1; $dia <= $diasEnMes; $dia++)
                                        @php
                                            $fechaClave = sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
                                            $eventosDia = $calendarioEventos[$fechaClave] ?? [];
                                            $tiposDia = array_unique(array_column($eventosDia, 'tipo'));

                                            $bgClase = 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-100';
                                            $tooltipTexto = '';

                                            if (count($tiposDia) > 1) {
                                                $bgClase = 'bg-purple-600 text-white font-black shadow-2xs ring-2 ring-purple-300';
                                            } elseif (in_array('incapacidad', $tiposDia)) {
                                                $bgClase = 'bg-rose-500 text-white font-black shadow-2xs';
                                            } elseif (in_array('vacacion', $tiposDia)) {
                                                $bgClase = 'bg-emerald-500 text-white font-black shadow-2xs';
                                            } elseif (in_array('permiso', $tiposDia)) {
                                                $bgClase = 'bg-amber-500 text-white font-black shadow-2xs';
                                            } elseif (in_array('comision', $tiposDia)) {
                                                $bgClase = 'bg-sky-500 text-white font-black shadow-2xs';
                                            }

                                            if (!empty($eventosDia)) {
                                                $lineasTooltip = [];
                                                foreach ($eventosDia as $ev) {
                                                    $lineasTooltip[] = $ev['titulo'] . ($ev['detalle'] ? " ({$ev['detalle']})" : '');
                                                }
                                                $tooltipTexto = implode(" | ", $lineasTooltip);
                                            }
                                        @endphp
                                        <div class="h-6 rounded flex items-center justify-center transition {{ $bgClase }}"
                                            @if($tooltipTexto) title="{{ $tooltipTexto }}" @endif>
                                            {{ $dia }}
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 4: NAVEGACIÓN POR PESTAÑAS DEL KARDEX --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/70 px-4 pt-3 flex flex-wrap items-center gap-2 no-print">
                    <button type="button" @click="tabActiva = 'comisiones'"
                        :class="tabActiva === 'comisiones' ? 'bg-white text-guinda-ceaa border-slate-200 border-b-white font-black shadow-2xs' : 'text-slate-600 hover:text-slate-900 border-transparent'"
                        class="px-4 py-2.5 text-xs uppercase tracking-wider rounded-t-xl border -mb-px transition flex items-center gap-2">
                        <span>🚗 Salidas de Comisión</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-sky-100 text-sky-800 font-black">{{ $comisionesUnificadas->count() }}</span>
                    </button>

                    <button type="button" @click="tabActiva = 'incapacidades'"
                        :class="tabActiva === 'incapacidades' ? 'bg-white text-guinda-ceaa border-slate-200 border-b-white font-black shadow-2xs' : 'text-slate-600 hover:text-slate-900 border-transparent'"
                        class="px-4 py-2.5 text-xs uppercase tracking-wider rounded-t-xl border -mb-px transition flex items-center gap-2">
                        <span>🏥 Incapacidades Médicas</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-rose-100 text-rose-800 font-black">{{ $incapacidades->count() }}</span>
                    </button>

                    {{-- Comentado temporalmente: Pestaña Permisos e Incidencias
                    <button type="button" @click="tabActiva = 'permisos'"
                        :class="tabActiva === 'permisos' ? 'bg-white text-guinda-ceaa border-slate-200 border-b-white font-black shadow-2xs' : 'text-slate-600 hover:text-slate-900 border-transparent'"
                        class="px-4 py-2.5 text-xs uppercase tracking-wider rounded-t-xl border -mb-px transition flex items-center gap-2">
                        <span>📋 Permisos e Incidencias</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-100 text-amber-800 font-black">{{ $permisos->count() }}</span>
                    </button>
                    --}}

                    <button type="button" @click="tabActiva = 'vacaciones'"
                        :class="tabActiva === 'vacaciones' ? 'bg-white text-guinda-ceaa border-slate-200 border-b-white font-black shadow-2xs' : 'text-slate-600 hover:text-slate-900 border-transparent'"
                        class="px-4 py-2.5 text-xs uppercase tracking-wider rounded-t-xl border -mb-px transition flex items-center gap-2">
                        <span>🌴 Vacaciones ({{ $anio }})</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-100 text-emerald-800 font-black">{{ $diasVacacionesAprobados }} días</span>
                    </button>
                </div>

                {{-- TAB 1: HISTORIAL DE COMISIONES DE LA PERSONA --}}
                <div x-show="tabActiva === 'comisiones'" class="p-5 print-force-show">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div>
                            <h4 class="text-xs font-black uppercase text-slate-800 tracking-wider">
                                Historial de Salidas de Comisión ({{ $anio }})
                            </h4>
                            <p class="text-[11px] text-slate-500">
                                Detalle de qué días salió de comisión el servidor público, destino, horario y actividad realizada
                            </p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-xs">
                            <thead class="bg-slate-50 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 text-left">No. Oficio</th>
                                    <th class="py-3 px-4 text-left">Tipo</th>
                                    <th class="py-3 px-4 text-left">Días que Salió de Comisión</th>
                                    <th class="py-3 px-4 text-left">Horario</th>
                                    <th class="py-3 px-4 text-left">Lugar / Destino</th>
                                    <th class="py-3 px-4 text-left">Actividad</th>
                                    <th class="py-3 px-4 text-center">Estatus</th>
                                    <th class="py-3 px-4 text-center no-print">Oficio</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($comisionesUnificadas as $com)
                                    <tr class="hover:bg-slate-50/80 transition {{ $com->status === 'Cancelado' ? 'opacity-60 bg-rose-50/20' : '' }}">
                                        <td class="py-3 px-4 font-mono font-black text-slate-800 whitespace-nowrap">
                                            {{ $com->oficio_numero }}
                                            <div class="text-[10px] font-sans text-slate-400 font-normal">
                                                Emitido: {{ \Carbon\Carbon::parse($com->created_at)->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            @if($com->es_odso)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-300">
                                                    ODSO
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-sky-50 text-sky-700 border border-sky-200">
                                                    Ordinaria
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-black text-guinda-ceaa">{{ $com->dias_comision }}</div>
                                            @if(!empty($com->fechas_detectadas))
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($com->fechas_detectadas as $fDet)
                                                        <span class="px-1.5 py-0.5 bg-sky-50 text-sky-800 border border-sky-200 rounded text-[10px] font-mono font-bold">
                                                            {{ \Carbon\Carbon::parse($fDet)->format('d/m/Y') }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap font-mono text-[11px]">
                                            {{ $com->hora_inicio ? substr($com->hora_inicio, 0, 5) : '--:--' }}
                                            a
                                            {{ $com->hora_fin ? substr($com->hora_fin, 0, 5) : '--:--' }} hrs
                                        </td>
                                        <td class="py-3 px-4 max-w-xs">
                                            <div class="font-semibold text-slate-800">{{ $com->lugar }}</div>
                                            @if($com->vehiculo)
                                                <div class="text-[10px] text-slate-400">
                                                    🚘 {{ $com->vehiculo->marca }} {{ $com->vehiculo->modelo }} ({{ $com->vehiculo->placa }})
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 max-w-xs text-slate-600">
                                            {{ \Illuminate\Support\Str::limit($com->actividad, 110) }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            @if($com->status === 'Cancelado')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 uppercase">
                                                    Cancelado
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                                    Autorizado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap no-print">
                                            <a href="{{ $com->url_show }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-guinda-ceaa hover:text-white text-slate-700 rounded text-[10px] font-bold uppercase transition">
                                                Ver Oficio
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-slate-400 italic">
                                            No cuenta con salidas de comisión registradas en el año {{ $anio }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: INCAPACIDADES MÉDICAS --}}
                <div x-show="tabActiva === 'incapacidades'" class="p-5 border-t border-slate-100 print-force-show">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div>
                            <h4 class="text-xs font-black uppercase text-rose-800 tracking-wider">
                                Registro de Incapacidades Médicas ({{ $anio }})
                            </h4>
                            <p class="text-[11px] text-slate-500">
                                Control de periodos de incapacidad de fecha inicio a fecha término
                            </p>
                        </div>
                        <button type="button" @click="modalIncapacidad = true"
                            class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5 no-print self-start">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nueva Incapacidad
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-xs">
                            <thead class="bg-rose-50/60 text-rose-900 text-[11px] font-black uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 text-left">Periodo (De Día Tal a Día Tal)</th>
                                    <th class="py-3 px-4 text-center">Días</th>
                                    <th class="py-3 px-4 text-left">Tipo de Incapacidad</th>
                                    <th class="py-3 px-4 text-left">Folio / Institución</th>
                                    <th class="py-3 px-4 text-left">Diagnóstico / Observaciones</th>
                                    <th class="py-3 px-4 text-center no-print">Justificante</th>
                                    <th class="py-3 px-4 text-center no-print">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($incapacidades as $inc)
                                    <tr class="hover:bg-rose-50/20 transition">
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <div class="font-black text-slate-800">
                                                Del {{ $inc->fecha_inicio->format('d/m/Y') }} al {{ $inc->fecha_fin->format('d/m/Y') }}
                                            </div>
                                            @if($inc->isVigenteHoy())
                                                <span class="inline-block mt-0.5 px-2 py-0.2 rounded-full text-[9px] font-black uppercase bg-rose-600 text-white">
                                                    Vigente Hoy
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-200">
                                                {{ $inc->dias }} {{ $inc->dias === 1 ? 'día' : 'días' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-slate-800">
                                            {{ $inc->tipo }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-mono font-bold text-slate-700">{{ $inc->folio ?? 'Sin folio' }}</div>
                                            <div class="text-[10px] text-slate-500">{{ $inc->institucion_medica ?? 'ISSSTE / Médico' }}</div>
                                        </td>
                                        <td class="py-3 px-4 max-w-sm text-slate-600">
                                            {{ $inc->observaciones ?? 'Sin observaciones adicionales' }}
                                            @if($inc->registrador)
                                                <div class="text-[10px] text-slate-400 mt-0.5">
                                                    Capturó: {{ $inc->registrador->name }} ({{ $inc->created_at->format('d/m/Y') }})
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap no-print">
                                            @if($inc->archivo_path)
                                                <a href="{{ asset('storage/' . $inc->archivo_path) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[10px] font-bold uppercase transition">
                                                    📎 Ver Archivo
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-[10px]">Sin adjunto</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap no-print">
                                            <form action="{{ route('personal-ceaa.incidencias.destroy', $inc->id) }}" method="POST"
                                                onsubmit="return confirm('¿Deseas eliminar este registro de incapacidad?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 rounded text-[10px] font-bold uppercase transition">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                            No hay incapacidades médicas registradas para este servidor público en {{ $anio }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Comentado temporalmente: TAB 3: PERMISOS E INCIDENCIAS
                <div x-show="tabActiva === 'permisos'" class="p-5 border-t border-slate-100 print-force-show">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                        <div>
                            <h4 class="text-xs font-black uppercase text-amber-800 tracking-wider">
                                Historial de Permisos, Días Económicos e Incidencias ({{ $anio }})
                            </h4>
                            <p class="text-[11px] text-slate-500">
                                Días económicos, permisos con/sin goce de sueldo, licencias, justificantes y pases de salida
                            </p>
                        </div>
                        <button type="button" @click="modalPermiso = true"
                            class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5 no-print self-start">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nuevo Permiso / Incidencia
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-xs">
                            <thead class="bg-amber-50/60 text-amber-900 text-[11px] font-black uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 text-left">Periodo</th>
                                    <th class="py-3 px-4 text-center">Días</th>
                                    <th class="py-3 px-4 text-left">Tipo de Incidencia / Permiso</th>
                                    <th class="py-3 px-4 text-left">Referencia / Oficio</th>
                                    <th class="py-3 px-4 text-left">Motivo / Observaciones</th>
                                    <th class="py-3 px-4 text-center no-print">Documento</th>
                                    <th class="py-3 px-4 text-center no-print">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($permisos as $perm)
                                    <tr class="hover:bg-amber-50/20 transition">
                                        <td class="py-3 px-4 whitespace-nowrap font-black text-slate-800">
                                            Del {{ $perm->fecha_inicio->format('d/m/Y') }} al {{ $perm->fecha_fin->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200">
                                                {{ $perm->dias }} {{ $perm->dias === 1 ? 'día' : 'días' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-slate-800">
                                            {{ $perm->tipo }}
                                        </td>
                                        <td class="py-3 px-4 font-mono text-slate-700">
                                            {{ $perm->folio ?? 'S/R' }}
                                        </td>
                                        <td class="py-3 px-4 max-w-sm text-slate-600">
                                            {{ $perm->observaciones ?? 'Sin observaciones' }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap no-print">
                                            @if($perm->archivo_path)
                                                <a href="{{ asset('storage/' . $perm->archivo_path) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[10px] font-bold uppercase transition">
                                                    📎 Ver Archivo
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-[10px]">Sin adjunto</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap no-print">
                                            <form action="{{ route('personal-ceaa.incidencias.destroy', $perm->id) }}" method="POST"
                                                onsubmit="return confirm('¿Deseas eliminar esta incidencia?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 rounded text-[10px] font-bold uppercase transition">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                            No hay permisos ni incidencias registradas en {{ $anio }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                --}}

                {{-- TAB 4: VACACIONES Y PERÍODOS --}}
                <div x-show="tabActiva === 'vacaciones'" class="p-5 border-t border-slate-100 print-force-show">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div>
                            <h4 class="text-xs font-black uppercase text-emerald-800 tracking-wider">
                                Control de Vacaciones Solicitadas por Oficio ({{ $anio }})
                            </h4>
                            <p class="text-[11px] text-slate-500">
                                Ingreso: {{ $fechaAltaCarbon ? $fechaAltaCarbon->format('d/m/Y') : 'Sin fecha de alta' }} ({{ $antiguedad }}) — Los días capturados aquí se reflejan automáticamente en el calendario anual
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold">
                                1er Período: <strong>{{ $diasP1Usados }} / 5 días</strong>
                            </span>
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold">
                                2do Período: <strong>{{ $diasP2Usados }} / 5 días</strong>
                            </span>
                            <button type="button" @click="modalVacacion = true"
                                class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5 no-print">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Registrar Vacaciones
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-xs">
                            <thead class="bg-emerald-50/60 text-emerald-900 text-[11px] font-black uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 text-left">Días de Vacaciones Solicitados</th>
                                    <th class="py-3 px-4 text-center">Total Días</th>
                                    <th class="py-3 px-4 text-left">Período Vacacional</th>
                                    <th class="py-3 px-4 text-left">No. de Oficio / Folio</th>
                                    <th class="py-3 px-4 text-left">Observaciones</th>
                                    <th class="py-3 px-4 text-center no-print">Oficio Adjunto</th>
                                    <th class="py-3 px-4 text-center no-print">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($vacacionesKardex as $vac)
                                    @php
                                        $soloHabilesVac = ($vac->institucion_medica === 'Solo días hábiles');
                                        $fechasDesglosadas = [];
                                        $curV = $vac->fecha_inicio->copy()->startOfDay();
                                        $finV = $vac->fecha_fin->copy()->startOfDay();
                                        while ($curV->lte($finV)) {
                                            if (!$soloHabilesVac || !$curV->isWeekend()) {
                                                $fechasDesglosadas[] = $curV->format('d/m/Y');
                                            }
                                            $curV->addDay();
                                        }
                                    @endphp
                                    <tr class="hover:bg-emerald-50/20 transition">
                                        <td class="py-3 px-4">
                                            <div class="font-black text-slate-800">
                                                @if($vac->fecha_inicio->equalTo($vac->fecha_fin))
                                                    Día {{ $vac->fecha_inicio->format('d/m/Y') }}
                                                @else
                                                    Del {{ $vac->fecha_inicio->format('d/m/Y') }} al {{ $vac->fecha_fin->format('d/m/Y') }}
                                                @endif
                                            </div>
                                            @if(count($fechasDesglosadas) > 0 && count($fechasDesglosadas) <= 12)
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($fechasDesglosadas as $fd)
                                                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[10px] font-bold">
                                                            {{ $fd }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                {{ $vac->dias }} {{ $vac->dias === 1 ? 'día' : 'días' }}
                                            </span>
                                            <div class="text-[9px] text-slate-400 font-semibold mt-0.5">
                                                {{ $vac->institucion_medica ?? 'Días hábiles' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-emerald-900">
                                            {{ $vac->tipo }}
                                        </td>
                                        <td class="py-3 px-4 font-mono font-bold text-slate-700">
                                            {{ $vac->folio ?? 'Por oficio' }}
                                        </td>
                                        <td class="py-3 px-4 max-w-xs text-slate-600">
                                            {{ $vac->observaciones ?? 'Sin observaciones' }}
                                            @if($vac->registrador)
                                                <div class="text-[10px] text-slate-400 mt-0.5">
                                                    Capturó: {{ $vac->registrador->name }} ({{ $vac->created_at->format('d/m/Y') }})
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap no-print">
                                            @if($vac->archivo_path)
                                                <a href="{{ asset('storage/' . $vac->archivo_path) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[10px] font-bold uppercase transition">
                                                    📎 Ver Oficio
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-[10px]">Sin adjunto</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap no-print">
                                            <form action="{{ route('personal-ceaa.incidencias.destroy', $vac->id) }}" method="POST"
                                                onsubmit="return confirm('¿Deseas eliminar este registro de vacaciones del Kardex?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 rounded text-[10px] font-bold uppercase transition">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                            No se han capturado días de vacaciones por oficio para este servidor público en {{ $anio }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($solicitudesVacaciones->count() > 0)
                        <div class="mt-6 pt-4 border-t border-slate-100">
                            <h5 class="text-[11px] font-black uppercase text-slate-600 tracking-wider mb-2">
                                Solicitudes Digitales Previas en Plataforma ({{ $anio }})
                            </h5>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100 text-xs">
                                    <thead class="bg-slate-50 text-slate-600 text-[10px] font-black uppercase tracking-wider">
                                        <tr>
                                            <th class="py-2 px-3 text-left">Fecha Solicitud</th>
                                            <th class="py-2 px-3 text-left">Días Solicitados</th>
                                            <th class="py-2 px-3 text-center">Total Días</th>
                                            <th class="py-2 px-3 text-left">Observaciones</th>
                                            <th class="py-2 px-3 text-center">Estatus</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        @foreach($solicitudesVacaciones as $sol)
                                            <tr>
                                                <td class="py-2 px-3 whitespace-nowrap font-semibold">
                                                    {{ $sol->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="py-2 px-3">
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($sol->fechas->sortBy('fecha') as $fObj)
                                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono text-[10px] font-bold">
                                                                {{ $fObj->fecha->format('d/m/Y') }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3 text-center font-black">{{ $sol->fechas->count() }}</td>
                                                <td class="py-2 px-3 text-slate-600">{{ $sol->observaciones ?? '—' }}</td>
                                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800">
                                                        {{ $sol->estatus }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- MODAL 1: REGISTRAR INCAPACIDAD MÉDICA --}}
        <div x-show="modalIncapacidad" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 no-print"
            x-transition>
            <div @click.away="modalIncapacidad = false"
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden">
                <div class="bg-rose-600 px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <h3 class="text-sm font-black uppercase tracking-wider">Registrar Incapacidad Médica</h3>
                    </div>
                    <button type="button" @click="modalIncapacidad = false" class="text-white/80 hover:text-white">✕</button>
                </div>

                <form action="{{ route('personal-ceaa.incidencias.store', $empleado->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="categoria" value="incapacidad">

                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-700">
                        Servidor Público: <strong class="font-black text-slate-900">{{ $empleado->prof }} {{ $empleado->name }}</strong>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Del Día (Fecha Inicio) *</label>
                            <input type="date" name="fecha_inicio" x-model="incInicio" required
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-rose-500 focus:border-rose-500">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Al Día (Fecha Término) *</label>
                            <input type="date" name="fecha_fin" x-model="incFin" required
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-rose-500 focus:border-rose-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-3.5 py-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-800">
                        <span class="font-bold">Total de días cubiertos por la incapacidad:</span>
                        <span class="text-sm font-black" x-text="calcularDias(incInicio, incFin) + ' día(s)'"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Tipo de Incapacidad *</label>
                            <select name="tipo" required class="w-full rounded-lg border-slate-300 text-xs focus:ring-rose-500 focus:border-rose-500">
                                <option value="Enfermedad General">Enfermedad General</option>
                                <option value="Riesgo de Trabajo">Riesgo de Trabajo</option>
                                <option value="Maternidad / Paternidad">Maternidad / Paternidad</option>
                                <option value="Cuidados Médicos Familiares">Cuidados Médicos Familiares</option>
                                <option value="Licencia Médica ISSSTE">Licencia Médica ISSSTE</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Institución Médica</label>
                            <input type="text" name="institucion_medica" placeholder="Ej: ISSSTE, Clínica Particular..."
                                value="ISSSTE"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-rose-500 focus:border-rose-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Folio Certificado Médico</label>
                            <input type="text" name="folio" placeholder="Ej: SERIE-A-10492"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-rose-500 focus:border-rose-500">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Adjuntar PDF / Foto (Opcional)</label>
                            <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-[11px] text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                        </div>
                    </div>

                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1">Diagnóstico / Observaciones</label>
                        <textarea name="observaciones" rows="2" placeholder="Observaciones médicas o administrativas..."
                            class="w-full rounded-lg border-slate-300 text-xs focus:ring-rose-500 focus:border-rose-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="modalIncapacidad = false"
                            class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-black uppercase tracking-wider shadow-xs">
                            Guardar Incapacidad
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Comentado temporalmente: MODAL 2: REGISTRAR PERMISO / INCIDENCIA
        <div x-show="modalPermiso" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 no-print"
            x-transition>
            <div @click.away="modalPermiso = false"
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden">
                <div class="bg-amber-600 px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                        <h3 class="text-sm font-black uppercase tracking-wider">Registrar Permiso o Incidencia</h3>
                    </div>
                    <button type="button" @click="modalPermiso = false" class="text-white/80 hover:text-white">✕</button>
                </div>

                <form action="{{ route('personal-ceaa.incidencias.store', $empleado->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="categoria" value="incidencia">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Fecha Inicio *</label>
                            <input type="date" name="fecha_inicio" x-model="permInicio" required
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Fecha Término *</label>
                            <input type="date" name="fecha_fin" x-model="permFin" required
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-3.5 py-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-800">
                        <span class="font-bold">Total de días:</span>
                        <span class="text-sm font-black" x-text="calcularDias(permInicio, permFin) + ' día(s)'"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Tipo de Permiso / Incidencia *</label>
                            <select name="tipo" required class="w-full rounded-lg border-slate-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                                <option value="Día Económico">Día Económico</option>
                                <option value="Permiso con Goce de Sueldo">Permiso con Goce de Sueldo</option>
                                <option value="Permiso sin Goce de Sueldo">Permiso sin Goce de Sueldo</option>
                                <option value="Pase de Salida">Pase de Salida</option>
                                <option value="Justificante Oficial / Médico">Justificante Oficial / Médico</option>
                                <option value="Licencia Especial">Licencia Especial</option>
                                <option value="Comisión Sindical">Comisión Sindical</option>
                                <option value="Falta Injustificada">Falta Injustificada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">No. Oficio / Referencia</label>
                            <input type="text" name="folio" placeholder="Opcional..."
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1">Adjuntar Documento (Opcional)</label>
                        <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full text-[11px] text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    </div>

                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1">Motivo / Observaciones</label>
                        <textarea name="observaciones" rows="2" placeholder="Detalle o motivo del permiso..."
                            class="w-full rounded-lg border-slate-300 text-xs focus:ring-amber-500 focus:border-amber-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="modalPermiso = false"
                            class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-black uppercase tracking-wider shadow-xs">
                            Guardar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
        --}}

        {{-- MODAL 4: REGISTRAR VACACIONES SOLICITADAS POR OFICIO --}}
        <div x-show="modalVacacion" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 no-print"
            x-transition>
            <div @click.away="modalVacacion = false"
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden">
                <div class="bg-emerald-600 px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-sm font-black uppercase tracking-wider">Registrar Vacaciones (Por Oficio)</h3>
                    </div>
                    <button type="button" @click="modalVacacion = false" class="text-white/80 hover:text-white">✕</button>
                </div>

                <form action="{{ route('personal-ceaa.incidencias.store', $empleado->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs max-h-[82vh] overflow-y-auto">
                    @csrf
                    <input type="hidden" name="categoria" value="vacacion">

                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 flex items-center justify-between">
                        <span>Servidor Público: <strong class="font-black text-slate-900">{{ $empleado->prof }} {{ $empleado->name }}</strong></span>
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                            P1: {{ $diasP1Usados }}/5 &bull; P2: {{ $diasP2Usados }}/5
                        </span>
                    </div>

                    {{-- Selector de modalidad: Rango de fechas vs Días específicos --}}
                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1.5">Forma de Capturar los Días Solicitados</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer border rounded-xl p-2.5 flex items-center gap-2 transition"
                                :class="modoVacacion === 'rango' ? 'border-emerald-600 bg-emerald-50/70 text-emerald-900 font-black' : 'border-slate-200 text-slate-600'">
                                <input type="radio" name="modo_vacacion" value="rango" x-model="modoVacacion" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Por Rango (Del día al día)</span>
                            </label>
                            <label class="cursor-pointer border rounded-xl p-2.5 flex items-center gap-2 transition"
                                :class="modoVacacion === 'especificas' ? 'border-emerald-600 bg-emerald-50/70 text-emerald-900 font-black' : 'border-slate-200 text-slate-600'">
                                <input type="radio" name="modo_vacacion" value="especificas" x-model="modoVacacion" class="text-emerald-600 focus:ring-emerald-500">
                                <span>Días Sueltos / Específicos</span>
                            </label>
                        </div>
                    </div>

                    {{-- Opción A: Por Rango de Fechas --}}
                    <div x-show="modoVacacion === 'rango'" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-black uppercase text-slate-700 mb-1">Del Día (Fecha Inicio) *</label>
                                <input type="date" name="fecha_inicio" x-model="vacInicio" :required="modoVacacion === 'rango'"
                                    class="w-full rounded-lg border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block font-black uppercase text-slate-700 mb-1">Al Día (Fecha Término) *</label>
                                <input type="date" name="fecha_fin" x-model="vacFin" :required="modoVacacion === 'rango'"
                                    class="w-full rounded-lg border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer select-none text-slate-700 font-semibold">
                            <input type="checkbox" name="solo_dias_habiles" value="1" x-model="soloHabiles"
                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span>Contar y marcar en el calendario solo días hábiles (omitir sábado y domingo)</span>
                        </label>

                        <div class="flex items-center justify-between px-3.5 py-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800">
                            <span class="font-bold">Días de vacaciones a descontar / pintar en calendario:</span>
                            <span class="text-sm font-black" x-text="calcularDiasVacacion(vacInicio, vacFin, soloHabiles) + ' día(s)'"></span>
                        </div>
                    </div>

                    {{-- Opción B: Por Días Sueltos / Específicos --}}
                    <div x-show="modoVacacion === 'especificas'" class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block font-black uppercase text-slate-700">Selecciona cada día solicitado en el oficio *</label>
                            <button type="button" @click="fechasVacacionesSueltas.push('')"
                                class="px-2.5 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 rounded-lg font-black text-[11px] transition">
                                + Agregar otro día
                            </button>
                        </div>

                        <template x-for="(fechaItem, idx) in fechasVacacionesSueltas" :key="idx">
                            <div class="flex items-center gap-2">
                                <input type="date" name="fechas_especificas[]" x-model="fechasVacacionesSueltas[idx]"
                                    class="flex-1 rounded-lg border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                                <button type="button" x-show="fechasVacacionesSueltas.length > 1"
                                    @click="fechasVacacionesSueltas.splice(idx, 1)"
                                    class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-bold text-xs">
                                    Quitar
                                </button>
                            </div>
                        </template>

                        <div class="flex items-center justify-between px-3.5 py-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800">
                            <span class="font-bold">Total de días específicos seleccionados:</span>
                            <span class="text-sm font-black" x-text="fechasVacacionesSueltas.filter(Boolean).length + ' día(s)'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Período Vacacional *</label>
                            <select name="tipo" required class="w-full rounded-lg border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="1er Período Vacacional">1er Período Vacacional</option>
                                <option value="2do Período Vacacional">2do Período Vacacional</option>
                                <option value="Período Vacacional Extraordinario">Período Vacacional Extraordinario</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">No. de Oficio de Solicitud</label>
                            <input type="text" name="folio" placeholder="Ej: CEAA/DAyF/088/2026"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1">Adjuntar Oficio de Vacaciones (PDF o Imagen opcional)</label>
                        <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full text-[11px] text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>

                    <div>
                        <label class="block font-black uppercase text-slate-700 mb-1">Observaciones</label>
                        <textarea name="observaciones" rows="2" placeholder="Anotaciones sobre el oficio o autorización de vacaciones..."
                            class="w-full rounded-lg border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="modalVacacion = false"
                            class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-black uppercase tracking-wider shadow-xs">
                            Guardar Vacaciones
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL 3: EDITAR EXPEDIENTE Y DATOS DE EMERGENCIA --}}
        <div x-show="modalExpediente" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 no-print"
            x-transition>
            <div @click.away="modalExpediente = false"
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full overflow-hidden">
                <div class="bg-guinda-ceaa px-6 py-4 flex items-center justify-between text-white">
                    <h3 class="text-sm font-black uppercase tracking-wider">
                        Actualizar Ficha de Expediente y Emergencia — {{ $empleado->name }}
                    </h3>
                    <button type="button" @click="modalExpediente = false" class="text-white/80 hover:text-white">✕</button>
                </div>

                <form action="{{ route('personal-ceaa.expediente.update', $empleado->id) }}" method="POST" class="p-6 space-y-4 text-xs max-h-[80vh] overflow-y-auto">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">No. de Empleado</label>
                            <input type="text" name="no_empleado" value="{{ old('no_empleado', $empleado->no_empleado) }}"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Fecha de Ingreso (Alta)</label>
                            <input type="date" name="fecha_alta" value="{{ old('fecha_alta', optional($empleado->fecha_alta)->format('Y-m-d')) }}"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Cargo</label>
                            <input type="text" name="cargo" value="{{ old('cargo', $empleado->cargo) }}"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2 border-t border-slate-100">
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">CURP</label>
                            <input type="text" name="curp" value="{{ old('curp', $empleado->curp) }}" placeholder="18 caracteres"
                                class="w-full rounded-lg border-slate-300 text-xs uppercase font-mono focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">RFC</label>
                            <input type="text" name="rfc" value="{{ old('rfc', $empleado->rfc) }}" placeholder="13 caracteres"
                                class="w-full rounded-lg border-slate-300 text-xs uppercase font-mono focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">NSS / ISSSTE</label>
                            <input type="text" name="nss" value="{{ old('nss', $empleado->nss) }}"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Tipo de Sangre</label>
                            <input type="text" name="tipo_sangre" value="{{ old('tipo_sangre', $empleado->tipo_sangre) }}" placeholder="Ej: O+, A+..."
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Teléfono Personal</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $empleado->telefono) }}" placeholder="Ej: 771 123 4567"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Nombre Contacto de Emergencia</label>
                            <input type="text" name="contacto_emergencia_nombre" value="{{ old('contacto_emergencia_nombre', $empleado->contacto_emergencia_nombre) }}"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Parentesco Emergencia</label>
                            <input type="text" name="contacto_emergencia_parentesco" value="{{ old('contacto_emergencia_parentesco', $empleado->contacto_emergencia_parentesco) }}" placeholder="Ej: Esposo(a), Padre, Madre..."
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                        <div>
                            <label class="block font-black uppercase text-slate-700 mb-1">Teléfono de Emergencia</label>
                            <input type="text" name="contacto_emergencia_telefono" value="{{ old('contacto_emergencia_telefono', $empleado->contacto_emergencia_telefono) }}"
                                class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <label class="block font-black uppercase text-slate-700 mb-1">Notas / Observaciones de Recursos Humanos</label>
                        <textarea name="notas_rh" rows="2" placeholder="Anotaciones internas del expediente laboral..."
                            class="w-full rounded-lg border-slate-300 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa">{{ old('notas_rh', $empleado->notas_rh) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="modalExpediente = false"
                            class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white font-black uppercase tracking-wider shadow-xs">
                            Guardar Expediente
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>

