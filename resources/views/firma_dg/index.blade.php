<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-guinda-ceaa/10 flex items-center justify-center text-guinda-ceaa shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Control de Documentos para Firma del Director General
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Bitácora exclusiva de Dirección General · Registro de recepción, estatus de firma y entrega a las áreas
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 print:hidden">
                <a href="{{ route('firma-dg.mis-oficios') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold shadow-sm transition"
                    title="Ver pantalla de consulta como la ven las áreas y usuarios">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Vista de Usuario
                </a>
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir Bitácora
                </button>
                <button type="button" x-data @click="$dispatch('open-modal-nuevo-firma-dg')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Registrar Oficio para Firma
                </button>
            </div>
        </div>
    </x-slot>

    @php
        // Mapa JS de user_id => { id, name (sin título para la lista), full_name (con título), cargo, area_id, area_nombre }
        $personalMap = $personal->mapWithKeys(function ($u) {
            return [
                $u->id => [
                    'id' => (string) $u->id,
                    'name' => trim($u->name),
                    'full_name' => trim(($u->prof ? $u->prof . ' ' : '') . $u->name),
                    'cargo' => $u->cargo ?? '',
                    'area_id' => $u->area_id ? (string) $u->area_id : '',
                    'area_nombre' => $u->area ? $u->area->name : 'Sin área asignada',
                ]
            ];
        });

        // Agrupado por Área (ordenado por nombre de Área A-Z y dentro de cada área por nombre de persona A-Z sin título)
        $personalPorArea = $personal
            ->sortBy(function ($u) {
                $areaName = $u->area ? $u->area->name : 'ZZZZ_Sin área asignada';
                return mb_strtolower($areaName . '|' . trim($u->name));
            })
            ->groupBy(function ($u) {
                return $u->area ? $u->area->name : 'Sin área asignada';
            })
            ->map(function ($users, $areaNombre) {
                return [
                    'area' => $areaNombre,
                    'personas' => $users->map(function ($u) {
                        return [
                            'id' => (string) $u->id,
                            'name' => trim($u->name), // Sin título (C., Lic., Ing.) en la lista
                            'area_id' => $u->area_id ? (string) $u->area_id : '',
                            'area_nombre' => $u->area ? $u->area->name : 'Sin área asignada',
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    @endphp

    <div class="py-6" x-data="{
        openModalNuevo: {{ $errors->any() ? 'true' : 'false' }},
        openModalEntrega: false,
        personalMap: @js($personalMap),
        personalPorArea: @js($personalPorArea),

        // Datos del formulario de Nuevo Registro
        entregadoPorUserId: '{{ old('entregado_por_user_id', '') }}',
        entregadoPorNombre: '{{ old('entregado_por_nombre', '') }}',
        areaId: '{{ old('area_id', '') }}',
        areaAutoDetectada: false,
        searchPersonaDejo: '',
        dropdownPersonaDejoOpen: false,

        // Datos del modal de Entrega / Salida
        entregaRegistroId: null,
        entregaFolio: '',
        entregaAsunto: '',
        entregaAreaNombre: '',
        entregaQuienDejoId: '',
        entregaQuienDejoNombre: '',
        recogidoPorUserId: '',
        recogidoPorNombre: '',
        searchPersonaRecoge: '',
        dropdownPersonaRecogeOpen: false,
        entregaEstatus: 'Entregado',
        entregaFecha: '{{ now()->format('Y-m-d\TH:i') }}',
        entregaObservaciones: '',

        // Datos del modal de Cancelación
        openModalCancelar: false,
        cancelarRegistroId: null,
        cancelarFolio: '',
        cancelarAsunto: '',
        cancelarAreaNombre: '',
        motivoCancelacion: '',

        abrirModalCancelar(reg) {
            this.cancelarRegistroId = reg.id;
            this.cancelarFolio = 'No. ' + (reg.consecutivo || reg.id) + (reg.numero_oficio ? (' · ' + reg.numero_oficio) : '');
            this.cancelarAsunto = reg.asunto;
            this.cancelarAreaNombre = reg.area_nombre;
            this.motivoCancelacion = '';
            this.openModalCancelar = true;
        },

        normalizarTexto(txt) {
            return String(txt || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        },

        gruposFiltrados(termino) {
            const q = this.normalizarTexto(termino).trim();
            if (!q) {
                return this.personalPorArea;
            }
            const resultado = [];
            for (const grupo of this.personalPorArea) {
                const coincideArea = this.normalizarTexto(grupo.area).includes(q);
                const personasFiltradas = grupo.personas.filter(p =>
                    coincideArea || this.normalizarTexto(p.name).includes(q)
                );
                if (personasFiltradas.length > 0) {
                    resultado.push({
                        area: grupo.area,
                        personas: personasFiltradas
                    });
                }
            }
            return resultado;
        },

        seleccionarPersonaDejo(persona) {
            if (!persona) {
                this.entregadoPorUserId = '';
                this.searchPersonaDejo = '';
                this.areaAutoDetectada = false;
                this.dropdownPersonaDejoOpen = false;
                return;
            }
            this.entregadoPorUserId = String(persona.id);
            this.searchPersonaDejo = persona.name;
            if (persona.area_id) {
                this.areaId = String(persona.area_id);
                this.areaAutoDetectada = true;
            } else {
                this.areaAutoDetectada = false;
            }
            this.dropdownPersonaDejoOpen = false;
        },

        limpiarPersonaDejo() {
            this.entregadoPorUserId = '';
            this.searchPersonaDejo = '';
            this.areaAutoDetectada = false;
        },

        seleccionarPersonaRecoge(persona) {
            if (!persona) {
                this.recogidoPorUserId = '';
                this.searchPersonaRecoge = '';
                this.dropdownPersonaRecogeOpen = false;
                return;
            }
            this.recogidoPorUserId = String(persona.id);
            this.searchPersonaRecoge = persona.name;
            this.recogidoPorNombre = '';
            this.dropdownPersonaRecogeOpen = false;
        },

        limpiarPersonaRecoge() {
            this.recogidoPorUserId = '';
            this.searchPersonaRecoge = '';
        },

        abrirModalEntrega(reg) {
            this.entregaRegistroId = reg.id;
            this.entregaFolio = 'No. ' + (reg.consecutivo || reg.id) + (reg.numero_oficio ? (' · ' + reg.numero_oficio) : '');
            this.entregaAsunto = reg.asunto;
            this.entregaAreaNombre = reg.area_nombre;
            this.entregaQuienDejoId = reg.entregado_por_user_id ? String(reg.entregado_por_user_id) : '';
            this.entregaQuienDejoNombre = reg.quien_dejo_nombre;
            this.recogidoPorUserId = reg.recogido_por_user_id ? String(reg.recogido_por_user_id) : '';
            this.searchPersonaRecoge = (this.recogidoPorUserId && this.personalMap[this.recogidoPorUserId])
                ? this.personalMap[this.recogidoPorUserId].name
                : '';
            this.recogidoPorNombre = reg.recogido_por_nombre || '';
            this.dropdownPersonaRecogeOpen = false;
            this.entregaEstatus = reg.estatus === 'Devuelto' ? 'Devuelto' : 'Entregado';
            this.entregaFecha = reg.fecha_entrega || '{{ now()->format('Y-m-d\TH:i') }}';
            this.entregaObservaciones = reg.observaciones || '';
            this.openModalEntrega = true;
        },

        seleccionarMismaPersona() {
            if (this.entregaQuienDejoId && this.personalMap[this.entregaQuienDejoId]) {
                this.recogidoPorUserId = String(this.entregaQuienDejoId);
                this.searchPersonaRecoge = this.personalMap[this.entregaQuienDejoId].name;
                this.recogidoPorNombre = '';
                this.dropdownPersonaRecogeOpen = false;
            } else if (this.entregaQuienDejoNombre) {
                this.recogidoPorUserId = '';
                this.searchPersonaRecoge = '';
                this.recogidoPorNombre = this.entregaQuienDejoNombre;
                this.dropdownPersonaRecogeOpen = false;
            }
        }
    }" @open-modal-nuevo-firma-dg.window="openModalNuevo = true">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Encabezado exclusivo para Impresión -->
            <div class="hidden print:block border-b-2 border-slate-800 pb-4 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-extrabold uppercase text-slate-900">
                            Comisión Estatal del Agua y Alcantarillado (CEAA)
                        </h1>
                        <p class="text-xs font-bold text-slate-700 uppercase">
                            Dirección General · Bitácora de Control de Documentos para Firma del Director General
                        </p>
                    </div>
                    <div class="text-right text-xs text-slate-600">
                        <p class="font-bold">Fecha de impresión: {{ now()->format('d/m/Y H:i') }}</p>
                        <p>Impreso por: {{ Auth::user()->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Alertas de éxito o error -->
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm print:hidden">
                    <div class="flex items-center gap-2.5 text-xs font-semibold">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm print:hidden">
                    <p class="text-xs font-bold mb-1">Por favor corrige los siguientes detalles:</p>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- KPIs / Resumen Rápido -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 print:hidden">
                <!-- Total (Excluye Cancelados) -->
                <a href="{{ route('firma-dg.index') }}"
                    class="bg-white rounded-2xl p-4 border {{ !request('estatus') ? 'border-slate-400 ring-2 ring-slate-200' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Registrados</p>
                        <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $stats['total'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Activos en bitácora</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </a>

                <!-- En Firma -->
                <a href="{{ route('firma-dg.index', array_merge(request()->except('page'), ['estatus' => 'En Firma'])) }}"
                    class="bg-white rounded-2xl p-4 border {{ request('estatus') === 'En Firma' ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600">En Firma DG</p>
                        <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['en_firma'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pendientes de firma</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>

                <!-- Firmados (Listos para recoger) -->
                <a href="{{ route('firma-dg.index', array_merge(request()->except('page'), ['estatus' => 'Firmado'])) }}"
                    class="bg-white rounded-2xl p-4 border {{ request('estatus') === 'Firmado' ? 'border-blue-400 ring-2 ring-blue-100' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Ya Firmados</p>
                        <p class="text-2xl font-extrabold text-blue-600 mt-1">{{ $stats['firmados'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Listos para recoger</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </a>

                <!-- Entregados / Recogidos -->
                <a href="{{ route('firma-dg.index', array_merge(request()->except('page'), ['estatus' => 'Entregado'])) }}"
                    class="bg-white rounded-2xl p-4 border {{ request('estatus') === 'Entregado' ? 'border-emerald-400 ring-2 ring-emerald-100' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Entregados</p>
                        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['entregados'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Ya recogidos por área</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </a>

                <!-- Devueltos / Con Observaciones -->
                <a href="{{ route('firma-dg.index', array_merge(request()->except('page'), ['estatus' => 'Devuelto'])) }}"
                    class="bg-white rounded-2xl p-4 border {{ request('estatus') === 'Devuelto' ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Devueltos</p>
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

                <!-- Apartado Exclusivo: Cancelados -->
                <a href="{{ route('firma-dg.index', array_merge(request()->except('page'), ['estatus' => 'Cancelado'])) }}"
                    class="bg-white rounded-2xl p-4 border {{ request('estatus') === 'Cancelado' ? 'border-slate-700 ring-2 ring-slate-300 bg-slate-50' : 'border-slate-200/80' }} shadow-sm hover:shadow transition flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Cancelados</p>
                        <p class="text-2xl font-extrabold text-slate-600 mt-1">{{ $stats['cancelados'] }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">No cuentan en total</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Barra de Filtros y Búsqueda -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm print:hidden">
                <form method="GET" action="{{ route('firma-dg.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                    <!-- Búsqueda libre -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Buscar oficio, asunto o persona</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="No. oficio, asunto, quién dejó o quién recogió..."
                                class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Área / Dirección -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Área / Dirección</label>
                        <select name="area_id" class="w-full py-2 px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                            <option value="">Todas las áreas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" @selected(request('area_id') == $area->id)>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estatus -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Estatus</label>
                        <select name="estatus" class="w-full py-2 px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                            <option value="">Todos los activos</option>
                            <option value="En Firma" @selected(request('estatus') === 'En Firma')>⏳ En Firma DG</option>
                            <option value="Firmado" @selected(request('estatus') === 'Firmado')>✍️ Firmado (Listo p/ recoger)</option>
                            <option value="Entregado" @selected(request('estatus') === 'Entregado')>✅ Entregado / Recogido</option>
                            <option value="Devuelto" @selected(request('estatus') === 'Devuelto')>⚠️ Devuelto c/ observaciones</option>
                            <option value="Cancelado" @selected(request('estatus') === 'Cancelado')>🚫 Cancelados</option>
                        </select>
                    </div>

                    <!-- Fecha de recepción -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Fecha en que se dejó</label>
                        <input type="date" name="fecha" value="{{ request('fecha') }}"
                            class="w-full py-2 px-3 text-xs rounded-xl border-slate-200 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'area_id', 'estatus', 'prioridad', 'fecha']))
                            <a href="{{ route('firma-dg.index') }}"
                                class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition"
                                title="Limpiar filtros">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabla de Bitácora de Firma DG -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">No. / Documento</th>
                                <th class="px-4 py-3 text-left">Quién lo Dejó y Área</th>
                                <th class="px-4 py-3 text-left">Asunto / Destinatario</th>
                                <th class="px-4 py-3 text-left">Cuándo lo Dejaron</th>
                                <th class="px-4 py-3 text-center">Estatus Firma</th>
                                <th class="px-4 py-3 text-left">Quién Recogió y Cuándo</th>
                                <th class="px-4 py-3 text-right print:hidden">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($registros as $reg)
                                @php
                                    $numConsecutivo = $reg->consecutivo ?? $reg->id;

                                    $quienDejo = $reg->entregadoPor
                                        ? trim(($reg->entregadoPor->prof ? $reg->entregadoPor->prof . ' ' : '') . $reg->entregadoPor->name)
                                        : ($reg->entregado_por_nombre ?: 'Solo Área (Sin especificar persona)');

                                    $quienRecogio = $reg->recogidoPor
                                        ? trim(($reg->recogidoPor->prof ? $reg->recogidoPor->prof . ' ' : '') . $reg->recogidoPor->name)
                                        : $reg->recogido_por_nombre;

                                    $jsPayload = [
                                        'id' => $reg->id,
                                        'consecutivo' => $numConsecutivo,
                                        'numero_oficio' => $reg->numero_oficio,
                                        'asunto' => $reg->asunto,
                                        'area_nombre' => $reg->area ? $reg->area->name : 'Sin Área',
                                        'entregado_por_user_id' => $reg->entregado_por_user_id,
                                        'quien_dejo_nombre' => $reg->entregadoPor ? $quienDejo : ($reg->entregado_por_nombre ?? ''),
                                        'recogido_por_user_id' => $reg->recogido_por_user_id,
                                        'recogido_por_nombre' => $reg->recogido_por_nombre,
                                        'estatus' => $reg->estatus,
                                        'fecha_entrega' => $reg->fecha_entrega ? $reg->fecha_entrega->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'),
                                        'observaciones' => $reg->observaciones,
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition align-top">
                                    <!-- Col 1: Consecutivo / Documento / Folio / Prioridad -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-md bg-guinda-ceaa text-white font-extrabold text-[11px]" title="Número consecutivo de bitácora DG">
                                                #{{ str_pad($numConsecutivo, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px] uppercase">
                                                {{ $reg->tipo_documento }}
                                            </span>
                                            @if($reg->prioridad === 'Urgente')
                                                <span class="px-2 py-0.5 rounded-md bg-red-100 text-red-700 font-extrabold text-[10px] uppercase animate-pulse">
                                                    🔥 Urgente
                                                </span>
                                            @endif
                                        </div>
                                        <div class="font-bold text-slate-800 mt-1">
                                            {{ $reg->numero_oficio ?: 'S/N (Consecutivo #' . str_pad($numConsecutivo, 3, '0', STR_PAD_LEFT) . ')' }}
                                        </div>
                                        @if($reg->anexos)
                                            <div class="text-[11px] text-slate-500 mt-0.5">
                                                📎 {{ $reg->anexos }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Col 2: Quién lo dejó y Área -->
                                    <td class="px-4 py-3.5">
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-guinda-ceaa/10 text-guinda-ceaa font-bold text-[10px] uppercase mb-1">
                                            {{ $reg->area ? $reg->area->name : 'Área no especificada' }}
                                        </div>
                                        <div class="font-semibold text-slate-800">
                                            @if($reg->entregadoPor || $reg->entregado_por_nombre)
                                                👤 {{ $quienDejo }}
                                            @else
                                                <span class="text-slate-400 italic">Entregado por el área</span>
                                            @endif
                                        </div>
                                        @if($reg->entregadoPor && $reg->entregadoPor->cargo)
                                            <div class="text-[11px] text-slate-400">
                                                {{ $reg->entregadoPor->cargo }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Col 3: Asunto y Destinatario -->
                                    <td class="px-4 py-3.5 max-w-xs">
                                        <p class="font-medium text-slate-800 leading-snug whitespace-normal">
                                            {{ $reg->asunto }}
                                        </p>
                                        @if($reg->destinatario)
                                            <p class="text-[11px] text-slate-500 mt-1">
                                                <span class="font-semibold">Dirigido a:</span> {{ $reg->destinatario }}
                                            </p>
                                        @endif
                                        @if($reg->observaciones)
                                            <p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200/70 rounded-lg px-2 py-1 mt-1.5">
                                                <span class="font-bold">Nota:</span> {{ $reg->observaciones }}
                                            </p>
                                        @endif
                                        @if($reg->estatus === 'Cancelado' && $reg->motivo_cancelacion)
                                            <p class="text-[11px] text-rose-800 bg-rose-50 border border-rose-200 rounded-lg px-2 py-1 mt-1.5">
                                                <span class="font-bold">Motivo de Cancelación:</span> {{ $reg->motivo_cancelacion }}
                                            </p>
                                        @endif
                                    </td>

                                    <!-- Col 4: Cuándo lo dejaron -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="font-bold text-slate-800">
                                            {{ $reg->fecha_recepcion->format('d/m/Y') }}
                                        </div>
                                        <div class="text-[11px] text-slate-500">
                                            🕒 {{ $reg->fecha_recepcion->format('H:i') }} hrs
                                        </div>
                                        @if($reg->registrador)
                                            <div class="text-[10px] text-slate-400 mt-0.5" title="Registrado en DG por">
                                                Recibió: {{ Str::words($reg->registrador->name, 2, '') }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Col 5: Estatus Firma -->
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($reg->estatus === 'En Firma')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                En Firma DG
                                            </span>
                                            <!-- Botón rápido de 1 clic para marcar como Firmado (listo para recoger) -->
                                            <form method="POST" action="{{ route('firma-dg.estatus', $reg) }}" class="mt-1.5 print:hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estatus" value="Firmado">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[10px] border border-blue-200 transition"
                                                    title="Marcar que el Director General ya lo firmó y está listo para que el área lo recoja">
                                                    ✍️ Marcar Ya Firmado
                                                </button>
                                            </form>
                                        @elseif($reg->estatus === 'Firmado')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 font-bold text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                Firmado (Por recoger)
                                            </span>
                                            @if($reg->fecha_firma)
                                                <div class="text-[10px] text-blue-600 mt-1">
                                                    Firmado: {{ $reg->fecha_firma->format('d/m H:i') }}
                                                </div>
                                            @endif
                                        @elseif($reg->estatus === 'Entregado')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Entregado
                                            </span>
                                        @elseif($reg->estatus === 'Devuelto')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-bold text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                                Devuelto s/firma
                                            </span>
                                        @elseif($reg->estatus === 'Cancelado')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-200 text-slate-700 font-bold text-[11px]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                                🚫 Cancelado
                                            </span>
                                            @if($reg->fecha_cancelacion)
                                                <div class="text-[10px] text-slate-500 mt-1">
                                                    {{ $reg->fecha_cancelacion->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>

                                    <!-- Col 6: Quién recogió y Cuándo -->
                                    <td class="px-4 py-3.5">
                                        @if($reg->estatus === 'Cancelado')
                                            <div class="text-[11px] font-semibold text-slate-600">
                                                Cancelado en DG
                                            </div>
                                            @if($reg->cancelador)
                                                <div class="text-[10px] text-slate-400 mt-0.5">
                                                    Por: {{ Str::words($reg->cancelador->name, 2, '') }}
                                                </div>
                                            @endif
                                        @elseif($reg->fecha_entrega || $quienRecogio)
                                            <div class="font-bold text-emerald-800">
                                                🙋‍♂️ {{ $quienRecogio ?: 'Recogido por el área' }}
                                            </div>
                                            @if($reg->fecha_entrega)
                                                <div class="text-[11px] text-slate-600 mt-0.5">
                                                    📅 {{ $reg->fecha_entrega->format('d/m/Y') }} · 🕒 {{ $reg->fecha_entrega->format('H:i') }} hrs
                                                </div>
                                            @endif
                                            @if($reg->entregadorDg)
                                                <div class="text-[10px] text-slate-400 mt-0.5">
                                                    Entregó en DG: {{ Str::words($reg->entregadorDg->name, 2, '') }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Pendiente de recoger</span>
                                            <div class="hidden print:block h-8 border-b border-dashed border-slate-400 mt-2 text-[9px] text-slate-400">
                                                Nombre, fecha y firma de quien recibe
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Col 7: Acciones -->
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap print:hidden">
                                        @if($reg->estatus !== 'Cancelado')
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button"
                                                    @click="abrirModalEntrega(@js($jsPayload))"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl {{ $reg->estatus === 'Entregado' ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm' }} text-[11px] font-bold transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                    </svg>
                                                    {{ $reg->estatus === 'Entregado' ? 'Editar Entrega' : 'Registrar Quién Recoge' }}
                                                </button>

                                                <button type="button"
                                                    @click="abrirModalCancelar(@js($jsPayload))"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[11px] font-bold transition"
                                                    title="Cancelar registro (se moverá a la pestaña de Cancelados)">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                    Cancelar
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-[11px] font-semibold text-slate-400 italic">Registro cancelado</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="font-bold text-slate-700 text-sm">No hay documentos registrados con estos criterios</p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Utiliza el botón "Registrar Oficio para Firma" cuando un área deje documentación para firma del Director General.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($registros->hasPages())
                    <div class="px-4 py-3 border-t border-slate-100 print:hidden">
                        {{ $registros->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL 1: REGISTRAR NUEVO OFICIO PARA FIRMA DEL DIRECTOR GRAL.  -->
        <!-- ============================================================== -->
        <div x-show="openModalNuevo" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs print:hidden">
            <div @click.away="openModalNuevo = false"
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full overflow-hidden">

                <!-- Header del Modal -->
                <div class="bg-gradient-to-r from-guinda-ceaa to-guinda-ceaa-hover px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <div>
                            <h3 class="font-bold text-sm uppercase tracking-wide">Registrar Documento para Firma del Director General</h3>
                            <p class="text-[11px] text-white/80">Selecciona quién lo entrega (detecta su área en automático) o elige directamente el área</p>
                        </div>
                    </div>
                    <button type="button" @click="openModalNuevo = false" class="text-white/80 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Cuerpo del Formulario -->
                <form method="POST" action="{{ route('firma-dg.store') }}" class="p-6 space-y-4">
                    @csrf

                    <!-- Bloque 1: ¿Quién lo dejó y de qué Área? -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-guinda-ceaa">
                                1. ¿Quién dejó el documento en Dirección General?
                            </span>
                            <span x-show="areaAutoDetectada" x-transition
                                class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">
                                ✓ Área asignada automáticamente
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-end">
                            <!-- Buscador desplegable con el personal (agrupado por área y sin título en la lista) -->
                            <div class="relative" @click.away="dropdownPersonaDejoOpen = false">
                                <label class="block text-xs font-bold text-slate-700 mb-1 truncate">
                                    Personal que entrega <span class="text-slate-400 font-normal">(Opcional)</span>
                                </label>
                                <input type="hidden" name="entregado_por_user_id" :value="entregadoPorUserId">

                                <div class="relative">
                                    <input type="text"
                                        x-model="searchPersonaDejo"
                                        @focus="dropdownPersonaDejoOpen = true"
                                        @click="dropdownPersonaDejoOpen = true"
                                        @input="dropdownPersonaDejoOpen = true; if (entregadoPorUserId && (!personalMap[entregadoPorUserId] || searchPersonaDejo !== personalMap[entregadoPorUserId].name)) { entregadoPorUserId = ''; areaAutoDetectada = false; }"
                                        placeholder="Escribe para filtrar o clic para ver lista..."
                                        autocomplete="off"
                                        class="w-full h-[38px] pl-3 pr-14 text-xs rounded-xl border-slate-300 bg-white focus:border-guinda-ceaa focus:ring-guinda-ceaa">

                                    <!-- Botón limpiar selección -->
                                    <button type="button"
                                        x-show="searchPersonaDejo || entregadoPorUserId"
                                        @click="limpiarPersonaDejo(); dropdownPersonaDejoOpen = true"
                                        class="absolute inset-y-0 right-7 flex items-center px-1.5 text-slate-400 hover:text-slate-600"
                                        title="Limpiar selección">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <!-- Botón desplegar lista completa -->
                                    <button type="button"
                                        @click="dropdownPersonaDejoOpen = !dropdownPersonaDejoOpen"
                                        class="absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500 hover:text-slate-700">
                                        <svg class="w-4 h-4 transition-transform duration-150" :class="dropdownPersonaDejoOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Lista desplegable agrupada por Área y en orden alfabético -->
                                <div x-show="dropdownPersonaDejoOpen" x-transition
                                    class="absolute z-40 mt-1 w-full bg-white rounded-xl shadow-xl border border-slate-200 max-h-64 overflow-y-auto divide-y divide-slate-100">
                                    <!-- Opción para solo especificar Área -->
                                    <button type="button"
                                        @click="seleccionarPersonaDejo(null)"
                                        class="w-full text-left px-3 py-2 text-xs text-slate-500 hover:bg-slate-50 font-medium flex items-center justify-between">
                                        <span>-- Solo especificar Área / O escribir nombre --</span>
                                        <span x-show="!entregadoPorUserId" class="text-guinda-ceaa font-bold">✓</span>
                                    </button>

                                    <template x-for="grupo in gruposFiltrados(searchPersonaDejo)" :key="grupo.area">
                                        <div>
                                            <!-- Encabezado de Área -->
                                            <div class="px-3 py-1.5 bg-slate-100/95 text-[10px] font-extrabold uppercase tracking-wider text-guinda-ceaa sticky top-0">
                                                <span x-text="grupo.area"></span>
                                            </div>
                                            <!-- Personas del Área en orden A-Z (sin título) -->
                                            <template x-for="p in grupo.personas" :key="p.id">
                                                <button type="button"
                                                    @click="seleccionarPersonaDejo(p)"
                                                    class="w-full text-left px-3 py-2 text-xs hover:bg-guinda-ceaa/5 transition flex items-center justify-between"
                                                    :class="entregadoPorUserId == p.id ? 'bg-guinda-ceaa/10 font-bold text-guinda-ceaa' : 'text-slate-700'">
                                                    <span x-text="p.name"></span>
                                                    <span x-show="entregadoPorUserId == p.id" class="text-guinda-ceaa font-bold text-[11px]">✓</span>
                                                </button>
                                            </template>
                                        </div>
                                    </template>

                                    <div x-show="gruposFiltrados(searchPersonaDejo).length === 0"
                                        class="px-3 py-3 text-center text-xs text-slate-400">
                                        No se encontraron coincidencias con "<span x-text="searchPersonaDejo"></span>"
                                    </div>
                                </div>
                            </div>

                            <!-- Área / Dirección (se llena automático o se elige manual) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 truncate">
                                    Área / Dirección <span class="text-red-500">*</span>
                                </label>
                                <select name="area_id" x-model="areaId" required
                                    class="w-full h-[38px] py-1.5 px-3 text-xs rounded-xl border-slate-300 bg-white focus:border-guinda-ceaa focus:ring-guinda-ceaa font-semibold">
                                    <option value="">-- Selecciona el Área o Dirección --</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Campo opcional por si lo dejó alguien externo o que no está en la lista -->
                        <div x-show="!entregadoPorUserId" x-transition>
                            <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                                Nombre de quien lo dejó (Opcional, déjalo en blanco si solo deseas registrar el Área)
                            </label>
                            <input type="text" name="entregado_por_nombre" x-model="entregadoPorNombre"
                                placeholder="Ej. Juan Pérez / Mensajería (Opcional)"
                                class="w-full h-[38px] text-xs rounded-xl border-slate-200 bg-white focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                        </div>
                    </div>

                    <!-- Bloque 2: Datos del Oficio / Documento -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Tipo de Documento <span class="text-red-500">*</span>
                            </label>
                            <select name="tipo_documento" required
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                                <option value="Oficio" @selected(old('tipo_documento') === 'Oficio')>Oficio</option>
                                <option value="Tarjeta Informativa" @selected(old('tipo_documento') === 'Tarjeta Informativa')>Tarjeta Informativa</option>
                                <option value="Convenio / Contrato" @selected(old('tipo_documento') === 'Convenio / Contrato')>Convenio / Contrato</option>
                                <option value="Circular" @selected(old('tipo_documento') === 'Circular')>Circular</option>
                                <option value="Nombramiento" @selected(old('tipo_documento') === 'Nombramiento')>Nombramiento</option>
                                <option value="Minuta / Acta" @selected(old('tipo_documento') === 'Minuta / Acta')>Minuta / Acta</option>
                                <option value="Otro" @selected(old('tipo_documento') === 'Otro')>Otro</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Número de Oficio / Folio
                            </label>
                            <input type="text" name="numero_oficio" value="{{ old('numero_oficio') }}"
                                placeholder="Ej. CEAA-DG-2026-0145"
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Fecha y Hora en que lo dejaron <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="fecha_recepcion"
                                value="{{ old('fecha_recepcion', now()->format('Y-m-d\TH:i')) }}" required
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                        </div>
                    </div>

                    <!-- Asunto -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Asunto del Documento <span class="text-red-500">*</span>
                        </label>
                        <textarea name="asunto" rows="2" required
                            placeholder="Describe brevemente el asunto del oficio o documento que pasa a firma del Director General..."
                            class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">{{ old('asunto') }}</textarea>
                    </div>

                    <!-- Destinatario, Anexos y Prioridad -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Dirigido a (Destinatario)
                            </label>
                            <input type="text" name="destinatario" value="{{ old('destinatario') }}"
                                placeholder="Ej. CONAGUA / Sría. de Finanzas..."
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Anexos / Copias
                            </label>
                            <input type="text" name="anexos" value="{{ old('anexos') }}"
                                placeholder="Ej. Original y 2 tantos, Carpeta..."
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Prioridad <span class="text-red-500">*</span>
                            </label>
                            <select name="prioridad" required
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                                <option value="Normal" @selected(old('prioridad', 'Normal') === 'Normal')>Normal</option>
                                <option value="Urgente" @selected(old('prioridad') === 'Urgente')>🔥 Urgente</option>
                            </select>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Observaciones o Notas Internas (Opcional)
                        </label>
                        <input type="text" name="observaciones" value="{{ old('observaciones') }}"
                            placeholder="Ej. Urge para reunión de mañana a las 10:00 hrs / Falta sello del área..."
                            class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                    </div>

                    <!-- Footer Modal -->
                    <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button type="button" @click="openModalNuevo = false"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white text-xs font-bold shadow-sm transition">
                            Guardar en Bitácora DG
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL 2: REGISTRAR QUIÉN RECOGE Y CUÁNDO SE ENTREGÓ            -->
        <!-- ============================================================== -->
        <div x-show="openModalEntrega" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs print:hidden">
            <div @click.away="openModalEntrega = false"
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden">

                <!-- Header -->
                <div class="bg-slate-800 px-6 py-4 flex items-center justify-between text-white">
                    <div>
                        <h3 class="font-bold text-sm uppercase tracking-wide">Registrar Entrega / Quién Recoge el Oficio</h3>
                        <p class="text-[11px] text-slate-300 mt-0.5" x-text="entregaFolio + ' · ' + entregaAreaNombre"></p>
                    </div>
                    <button type="button" @click="openModalEntrega = false" class="text-slate-300 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="'{{ url('/firma-dg') }}/' + entregaRegistroId + '/entrega'" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs">
                        <p class="font-bold text-slate-700" x-text="entregaAsunto"></p>
                        <p class="text-[11px] text-slate-500 mt-1" x-show="entregaQuienDejoNombre">
                            Lo dejó originalmente: <span class="font-semibold text-slate-700" x-text="entregaQuienDejoNombre"></span>
                        </p>
                    </div>

                    <!-- Botón de atajo rápido: "Recogió la misma persona que lo dejó" -->
                    <div x-show="entregaQuienDejoId || entregaQuienDejoNombre" class="flex justify-end">
                        <button type="button" @click="seleccionarMismaPersona()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] border border-indigo-200 transition">
                            ⚡ Recogió la misma persona que lo dejó
                        </button>
                    </div>

                    <!-- Selección de personal que recoge (con filtro por letra, agrupado por área y sin título en la lista) -->
                    <div class="relative" @click.away="dropdownPersonaRecogeOpen = false">
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            ¿Quién recoge el documento? (Lista del Personal CEAA)
                        </label>
                        <input type="hidden" name="recogido_por_user_id" :value="recogidoPorUserId">

                        <div class="relative">
                            <input type="text"
                                x-model="searchPersonaRecoge"
                                @focus="dropdownPersonaRecogeOpen = true"
                                @click="dropdownPersonaRecogeOpen = true"
                                @input="dropdownPersonaRecogeOpen = true; if (recogidoPorUserId && (!personalMap[recogidoPorUserId] || searchPersonaRecoge !== personalMap[recogidoPorUserId].name)) { recogidoPorUserId = ''; }"
                                placeholder="Escribe una letra para filtrar o clic para ver lista por área..."
                                autocomplete="off"
                                class="w-full h-[38px] pl-3 pr-14 text-xs rounded-xl border-slate-300 bg-white focus:border-guinda-ceaa focus:ring-guinda-ceaa">

                            <!-- Botón limpiar selección -->
                            <button type="button"
                                x-show="searchPersonaRecoge || recogidoPorUserId"
                                @click="limpiarPersonaRecoge(); dropdownPersonaRecogeOpen = true"
                                class="absolute inset-y-0 right-7 flex items-center px-1.5 text-slate-400 hover:text-slate-600"
                                title="Limpiar selección">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <!-- Botón desplegar lista completa -->
                            <button type="button"
                                @click="dropdownPersonaRecogeOpen = !dropdownPersonaRecogeOpen"
                                class="absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500 hover:text-slate-700">
                                <svg class="w-4 h-4 transition-transform duration-150" :class="dropdownPersonaRecogeOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Lista desplegable agrupada por Área y en orden alfabético -->
                        <div x-show="dropdownPersonaRecogeOpen" x-transition
                            class="absolute z-40 mt-1 w-full bg-white rounded-xl shadow-xl border border-slate-200 max-h-60 overflow-y-auto divide-y divide-slate-100">
                            <button type="button"
                                @click="seleccionarPersonaRecoge(null)"
                                class="w-full text-left px-3 py-2 text-xs text-slate-500 hover:bg-slate-50 font-medium flex items-center justify-between">
                                <span>-- Escribir nombre manualmente abajo --</span>
                                <span x-show="!recogidoPorUserId" class="text-guinda-ceaa font-bold">✓</span>
                            </button>

                            <template x-for="grupo in gruposFiltrados(searchPersonaRecoge)" :key="'recoge-' + grupo.area">
                                <div>
                                    <div class="px-3 py-1.5 bg-slate-100/95 text-[10px] font-extrabold uppercase tracking-wider text-guinda-ceaa sticky top-0">
                                        <span x-text="grupo.area"></span>
                                    </div>
                                    <template x-for="p in grupo.personas" :key="'recoge-p-' + p.id">
                                        <button type="button"
                                            @click="seleccionarPersonaRecoge(p)"
                                            class="w-full text-left px-3 py-2 text-xs hover:bg-guinda-ceaa/5 transition flex items-center justify-between"
                                            :class="recogidoPorUserId == p.id ? 'bg-guinda-ceaa/10 font-bold text-guinda-ceaa' : 'text-slate-700'">
                                            <span x-text="p.name"></span>
                                            <span x-show="recogidoPorUserId == p.id" class="text-guinda-ceaa font-bold text-[11px]">✓</span>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <div x-show="gruposFiltrados(searchPersonaRecoge).length === 0"
                                class="px-3 py-3 text-center text-xs text-slate-400">
                                No se encontraron coincidencias con "<span x-text="searchPersonaRecoge"></span>"
                            </div>
                        </div>
                    </div>

                    <!-- O escribir nombre manual si no está en lista -->
                    <div x-show="!recogidoPorUserId" x-transition>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            O escribir nombre de quien recoge
                        </label>
                        <input type="text" name="recogido_por_nombre" x-model="recogidoPorNombre"
                            placeholder="Nombre de la persona que se lleva el oficio..."
                            class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Fecha y Hora en que lo recogió <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="fecha_entrega" x-model="entregaFecha" required
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Estatus de Salida <span class="text-red-500">*</span>
                            </label>
                            <select name="estatus" x-model="entregaEstatus" required
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa font-bold">
                                <option value="Entregado">✅ Entregado (Firmado)</option>
                                <option value="Devuelto">⚠️ Devuelto para corrección (Sin firma)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Observaciones de entrega / Motivo si fue devuelto
                        </label>
                        <textarea name="observaciones" x-model="entregaObservaciones" rows="2"
                            placeholder="Opcional: Notas sobre la entrega o correcciones solicitadas..."
                            class="w-full text-xs rounded-xl border-slate-300 focus:border-guinda-ceaa focus:ring-guinda-ceaa"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button type="button" @click="openModalEntrega = false"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition">
                            Confirmar Entrega
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL 3: CANCELAR DOCUMENTO (MOVER AL APARTADO DE CANCELADOS)  -->
        <!-- ============================================================== -->
        <div x-show="openModalCancelar" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs print:hidden">
            <div @click.away="openModalCancelar = false"
                class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">

                <div class="bg-rose-700 px-6 py-4 flex items-center justify-between text-white">
                    <div>
                        <h3 class="font-bold text-sm uppercase tracking-wide">Cancelar Registro de Firma DG</h3>
                        <p class="text-[11px] text-rose-100 mt-0.5" x-text="cancelarFolio + ' · ' + cancelarAreaNombre"></p>
                    </div>
                    <button type="button" @click="openModalCancelar = false" class="text-rose-200 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="'{{ url('/firma-dg') }}/' + cancelarRegistroId + '/cancelar'" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs">
                        <p class="font-bold text-slate-700" x-text="cancelarAsunto"></p>
                        <p class="text-[11px] text-slate-500 mt-1">
                            El documento no se borrará del sistema: pasará al apartado de <strong>Cancelados</strong> y dejará de sumarse en Total Registrados.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Motivo de la cancelación <span class="text-red-500">*</span>
                        </label>
                        <textarea name="motivo_cancelacion" x-model="motivoCancelacion" rows="3" required
                            placeholder="Ej. Captura duplicada, el área retiró el oficio antes de pasar a firma, error en número de oficio..."
                            class="w-full text-xs rounded-xl border-slate-300 focus:border-rose-600 focus:ring-rose-600"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                        <button type="button" @click="openModalCancelar = false"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            Volver
                        </button>
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition">
                            Confirmar Cancelación
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>

