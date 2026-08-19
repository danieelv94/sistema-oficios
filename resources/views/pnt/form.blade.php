@php
    $isEdit = isset($procedimiento);
    $canEditSec1 = Auth::user()->canEditPntSection(1);
    // Sección 2 y 3 se pueden llenar si el usuario tiene los permisos correspondientes (permitiendo llenado completo en creación para admins)
    $canEditSec2 = Auth::user()->canEditPntSection(2);
    $canEditSec3 = Auth::user()->canEditPntSection(3);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-lg text-slate-800 leading-tight uppercase tracking-wider">
                {{ $isEdit ? 'Editar Registro PNT: ' . $procedimiento->numero_expediente : 'Nuevo Registro de Transparencia PNT' }}
            </h2>
            <a href="{{ route('pnt.index') }}" 
               class="bg-slate-500 hover:bg-slate-600 text-white font-black uppercase text-[10px] tracking-widest px-4 py-2 rounded-lg transition shadow">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm text-xs font-semibold text-red-800 mb-6">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $isEdit ? route('pnt.update', $procedimiento) : route('pnt.store') }}" 
                  method="POST" 
                  class="space-y-6"
                  x-data="pntFormState()">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                {{-- Navegación por Pestañas (Tabs) --}}
                <div class="flex border-b border-slate-200 bg-white rounded-t-xl overflow-hidden shadow-sm">
                    <button type="button" 
                            @click="activeTab = 'sec1'"
                            :class="activeTab === 'sec1' ? 'border-b-4 border-guinda-ceaa bg-slate-50 font-black text-guinda-ceaa' : 'text-slate-500 hover:bg-slate-50/50'"
                            class="flex-1 py-3.5 text-xs font-black uppercase tracking-wider transition border-r border-slate-100 focus:outline-none">
                        Sección 1: Licitaciones
                    </button>
                    <button type="button" 
                            @click="activeTab = 'sec2'"
                            :class="activeTab === 'sec2' ? 'border-b-4 border-guinda-ceaa bg-slate-50 font-black text-guinda-ceaa' : 'text-slate-500 hover:bg-slate-50/50'"
                            class="flex-1 py-3.5 text-xs font-black uppercase tracking-wider transition border-r border-slate-100 focus:outline-none"
                            @if(!$isEdit && !$canEditSec2) disabled title="Primero debes guardar el registro base" @endif>
                        Sección 2: Suministros
                        @if(!$isEdit && !$canEditSec2)
                            <span class="text-[9px] text-slate-400 font-bold block normal-case">(Bloqueado)</span>
                        @endif
                    </button>
                    <button type="button" 
                            @click="activeTab = 'sec3'"
                            :class="activeTab === 'sec3' ? 'border-b-4 border-guinda-ceaa bg-slate-50 font-black text-guinda-ceaa' : 'text-slate-500 hover:bg-slate-50/50'"
                            class="flex-1 py-3.5 text-xs font-black uppercase tracking-wider transition focus:outline-none"
                            @if(!$isEdit && !$canEditSec3) disabled title="Primero debes guardar el registro base" @endif>
                        Sección 3: Infraestructura
                        @if(!$isEdit && !$canEditSec3)
                            <span class="text-[9px] text-slate-400 font-bold block normal-case">(Bloqueado)</span>
                        @endif
                    </button>
                </div>

                {{-- Contenedores de las Pestañas --}}
                <div class="bg-white shadow-xl rounded-b-xl p-6 border-b-4 border-slate-400">
                    
                    {{-- PESTAÑA 1: LICITACIONES --}}
                    <div x-show="activeTab === 'sec1'" class="space-y-6 animate-fade-in">
                        <div class="border-b pb-2 mb-4">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">
                                Sección 1: Datos Base y Licitaciones
                            </h3>
                            @if(!$canEditSec1)
                                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 mt-1 inline-block uppercase">Solo Lectura para tu Área</span>
                            @endif
                        </div>

                        @if($canEditSec1)
                        {{-- ASISTENTE DE LLENADO RÁPIDO (API LICITACIONES) --}}
                        <div class="mb-6 border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-gradient-to-br from-slate-50 to-white">
                            <button type="button" @click="assistantOpen = !assistantOpen; if(assistantOpen) initAssistant();" 
                                    class="w-full flex justify-between items-center px-4 py-3 bg-slate-100 hover:bg-slate-150 transition focus:outline-none">
                                <span class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    <svg class="w-4 h-4 text-guinda-ceaa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Asistente de Llenado Rápido (API Licitaciones)
                                </span>
                                <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="assistantOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="assistantOpen" x-transition class="p-4 border-t border-slate-100 space-y-4">
                                {{-- Alertas de Éxito / Error --}}
                                <div x-show="successMsg" x-transition class="bg-green-50 border-l-4 border-green-500 p-3 rounded text-xs font-semibold text-green-800">
                                    <span x-text="successMsg"></span>
                                </div>
                                <div x-show="apiError" x-transition class="bg-red-50 border-l-4 border-red-500 p-3 rounded text-xs font-semibold text-red-800">
                                    <span x-text="apiError"></span>
                                </div>

                                {{-- Pestañas Internas del Asistente --}}
                                <div class="flex border-b border-slate-200">
                                    <button type="button" @click="apiSubTab = 'search'" 
                                            :class="apiSubTab === 'search' ? 'border-b-2 border-guinda-ceaa font-bold text-guinda-ceaa' : 'text-slate-500 hover:text-slate-700'"
                                            class="pb-2 px-4 text-xs font-bold uppercase tracking-wider transition focus:outline-none">
                                        Buscar en la API
                                    </button>
                                    <button type="button" @click="apiSubTab = 'paste'" 
                                            :class="apiSubTab === 'paste' ? 'border-b-2 border-guinda-ceaa font-bold text-guinda-ceaa' : 'text-slate-500 hover:text-slate-700'"
                                            class="pb-2 px-4 text-xs font-bold uppercase tracking-wider transition focus:outline-none">
                                        Pegar JSON
                                    </button>
                                </div>

                                {{-- PESTAÑA 1: BUSCAR EN API --}}
                                <div x-show="apiSubTab === 'search'" class="space-y-4">
                                    <template x-if="!apiTokenSet">
                                        <div class="bg-amber-50 border-l-4 border-amber-500 p-3.5 rounded text-xs text-amber-800 space-y-2">
                                            <p class="font-bold">⚠️ Conexión de API No Configurada</p>
                                            <p class="leading-relaxed">
                                                Para buscar en vivo, debes configurar la variable <code class="bg-amber-100 px-1 py-0.5 rounded font-mono text-[10px]">PNT_API_TOKEN</code> en tu archivo <code class="bg-amber-100 px-1 py-0.5 rounded font-mono text-[10px]">.env</code>. 
                                                Mientras tanto, puedes usar la pestaña de <strong>"Pegar JSON"</strong> para rellenar de forma inmediata copiando los datos de la API.
                                            </p>
                                        </div>
                                    </template>

                                    <template x-if="apiTokenSet">
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-3">
                                                <div class="relative flex-1">
                                                    <input type="text" x-model="apiQuery" placeholder="Buscar por expediente, título o municipio..." 
                                                           class="w-full text-xs rounded-lg border-slate-350 shadow-sm focus:ring focus:ring-guinda-ceaa/20 pl-8">
                                                    <div class="absolute left-2.5 top-2.5 text-slate-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <button type="button" @click="fetchLicitaciones(apiPage)" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-[10px] uppercase tracking-wider px-3 py-2 rounded-lg transition">
                                                    Refrescar
                                                </button>
                                            </div>

                                            {{-- Listado de Licitaciones --}}
                                            <div class="relative">
                                                <div x-show="apiLoading" class="absolute inset-0 bg-white/70 flex items-center justify-center z-10">
                                                    <div class="animate-spin rounded-full h-5 w-5 border-2 border-guinda-ceaa border-t-transparent"></div>
                                                </div>

                                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[9px]">Licitaciones disponibles en la página actual</label>
                                                <select x-model="selectedLicitacionId" class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20">
                                                    <option value="">-- Selecciona una licitación --</option>
                                                    <template x-for="item in filteredLicitaciones()" :key="item.id">
                                                        <option :value="item.id" x-text="`[${item.numero_expediente}] ${item.titulo.substring(0, 80)}... (${item.ubicacion ? item.ubicacion.municipio : 'N/A'})`"></option>
                                                    </template>
                                                </select>
                                            </div>

                                            {{-- Controles de Paginación --}}
                                            <div class="flex justify-between items-center text-slate-500 text-[10px] font-bold">
                                                <span>Página <span x-text="apiPage"></span> de <span x-text="apiTotalPages"></span></span>
                                                <div class="flex gap-2">
                                                    <button type="button" @click="if(apiPage > 1) fetchLicitaciones(apiPage - 1)" :disabled="apiPage <= 1"
                                                            class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-650 px-2.5 py-1 rounded transition disabled:opacity-40">
                                                        Anterior
                                                    </button>
                                                    <button type="button" @click="if(apiPage < apiTotalPages) fetchLicitaciones(apiPage + 1)" :disabled="apiPage >= apiTotalPages"
                                                            class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-650 px-2.5 py-1 rounded transition disabled:opacity-40">
                                                        Siguiente
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="pt-2 flex justify-end">
                                                <button type="button" @click="loadFromSelected()" :disabled="!selectedLicitacionId"
                                                        class="bg-guinda-ceaa hover:bg-guinda-ceaa/90 text-white font-bold uppercase text-[10px] tracking-widest px-4 py-2 rounded-lg transition shadow disabled:opacity-40">
                                                    Autocompletar Formulario
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- PESTAÑA 2: PEGAR JSON --}}
                                <div x-show="apiSubTab === 'paste'" class="space-y-3">
                                    <div>
                                        <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[9px]">Pegar JSON del expediente de licitación</label>
                                        <textarea x-model="pastedJsonText" rows="6" 
                                                  placeholder='Ej. { "numero_expediente": "CEAA-OP-913023990-E88-2026", "titulo": "Rehabilitación de Planta...", ... }'
                                                  :class="pastedJsonValid === true ? 'border-green-500 focus:ring-green-200' : (pastedJsonValid === false ? 'border-red-500 focus:ring-red-200' : 'border-slate-350 focus:ring-guinda-ceaa/20')"
                                                  class="w-full text-[11px] font-mono rounded-lg shadow-sm focus:ring focus:outline-none"></textarea>
                                        <div class="mt-1 flex justify-between items-center text-[10px]">
                                            <span class="text-slate-400">Puedes pegar una licitación individual o la respuesta completa de la API.</span>
                                            <span x-show="pastedJsonValid === true" class="text-green-600 font-bold">✓ JSON Válido</span>
                                            <span x-show="pastedJsonValid === false" class="text-red-600 font-bold">✗ JSON Inválido</span>
                                        </div>
                                    </div>

                                    <div class="flex justify-end pt-1">
                                        <button type="button" @click="loadFromPasted()"
                                                class="bg-guinda-ceaa hover:bg-guinda-ceaa/90 text-white font-bold uppercase text-[10px] tracking-widest px-4 py-2 rounded-lg transition shadow">
                                            Procesar y Autocompletar
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Ejercicio</label>
                                <input type="number" name="ejercicio" x-model="ejercicio" @input="updatePeriodDates()" @disabled(!$canEditSec1) required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Trimestre que se informa</label>
                                <select x-model="trimestre" @change="updatePeriodDates()" @disabled(!$canEditSec1) required
                                        class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                                    <option value="">Selecciona...</option>
                                    <option value="1">1er Trimestre (Enero - Marzo)</option>
                                    <option value="2">2do Trimestre (Abril - Junio)</option>
                                    <option value="3">3er Trimestre (Julio - Septiembre)</option>
                                    <option value="4">4to Trimestre (Octubre - Diciembre)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Inicio Periodo que se informa</label>
                                <input type="date" name="periodo_inicio" x-model="periodo_inicio" readonly required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm bg-slate-100 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Término Periodo que se informa</label>
                                <input type="date" name="periodo_fin" x-model="periodo_fin" readonly required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm bg-slate-100 cursor-not-allowed">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Tipo de procedimiento</label>
                                <select name="tipo_procedimiento" @disabled(!$canEditSec1) required
                                        class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                                    <option value="">Selecciona...</option>
                                    <option value="Adjudicación directa" {{ old('tipo_procedimiento', $procedimiento->tipo_procedimiento ?? '') === 'Adjudicación directa' ? 'selected' : '' }}>Adjudicación directa</option>
                                    <option value="Licitación pública" {{ old('tipo_procedimiento', $procedimiento->tipo_procedimiento ?? '') === 'Licitación pública' ? 'selected' : '' }}>Licitación pública</option>
                                    <option value="Invitación a cuando menos tres personas" {{ old('tipo_procedimiento', $procedimiento->tipo_procedimiento ?? '') === 'Invitación a cuando menos tres personas' ? 'selected' : '' }}>Invitación a cuando menos tres personas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Materia o tipo de contratación</label>
                                <select name="tipo_contratacion" @disabled(!$canEditSec1) required
                                        class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                                    <option value="">Selecciona...</option>
                                    <option value="Adquisiciones" {{ old('tipo_contratacion', $procedimiento->tipo_contratacion ?? '') === 'Adquisiciones' ? 'selected' : '' }}>Adquisiciones</option>
                                    <option value="Arrendamientos" {{ old('tipo_contratacion', $procedimiento->tipo_contratacion ?? '') === 'Arrendamientos' ? 'selected' : '' }}>Arrendamientos</option>
                                    <option value="Servicios" {{ old('tipo_contratacion', $procedimiento->tipo_contratacion ?? '') === 'Servicios' ? 'selected' : '' }}>Servicios</option>
                                    <option value="Obra pública" {{ old('tipo_contratacion', $procedimiento->tipo_contratacion ?? '') === 'Obra pública' ? 'selected' : '' }}>Obra pública</option>
                                    <option value="Servicios relacionados con las mismas" {{ old('tipo_contratacion', $procedimiento->tipo_contratacion ?? '') === 'Servicios relacionados con las mismas' ? 'selected' : '' }}>Servicios relacionados con las mismas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Carácter del procedimiento</label>
                                <select name="caracter_procedimiento" @disabled(!$canEditSec1) required
                                        class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                                    <option value="">Selecciona...</option>
                                    <option value="Nacional" {{ old('caracter_procedimiento', $procedimiento->caracter_procedimiento ?? '') === 'Nacional' ? 'selected' : '' }}>Nacional</option>
                                    <option value="Internacional bajo cobertura de tratados" {{ old('caracter_procedimiento', $procedimiento->caracter_procedimiento ?? '') === 'Internacional bajo cobertura de tratados' ? 'selected' : '' }}>Internacional bajo cobertura de tratados</option>
                                    <option value="Internacional abierto" {{ old('caracter_procedimiento', $procedimiento->caracter_procedimiento ?? '') === 'Internacional abierto' ? 'selected' : '' }}>Internacional abierto</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Expediente / Folio / Nomenclatura</label>
                                <input type="text" name="numero_expediente" value="{{ old('numero_expediente', $procedimiento->numero_expediente ?? '') }}" @disabled(!$canEditSec1) required
                                       placeholder="Ej. CEAA-AD-001/2026"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Se declaró desierta la licitación</label>
                                <select name="declarado_desierto" @disabled(!$canEditSec1) required
                                        class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                                    <option value="">Selecciona...</option>
                                    <option value="Sí" {{ old('declarado_desierto', $procedimiento->declarado_desierto ?? '') === 'Sí' ? 'selected' : '' }}>Sí</option>
                                    <option value="No" {{ old('declarado_desierto', $procedimiento->declarado_desierto ?? '') === 'No' ? 'selected' : '' }}>No</option>
                                    <option value="Parcial" {{ old('declarado_desierto', $procedimiento->declarado_desierto ?? '') === 'Parcial' ? 'selected' : '' }}>Parcial</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Autorización suficiencia presupuestal (Hipervínculo)</label>
                                <input type="url" name="suficiencia_presupuestal_url" value="{{ old('suficiencia_presupuestal_url', $procedimiento->suficiencia_presupuestal_url ?? '') }}" @disabled(!$canEditSec1)
                                       placeholder="https://ejemplo.com/doc.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo a la convocatoria / invitaciones</label>
                                <input type="url" name="convocatoria_url" value="{{ old('convocatoria_url', $procedimiento->convocatoria_url ?? '') }}" @disabled(!$canEditSec1)
                                       placeholder="https://ejemplo.com/convocatoria.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Fecha de convocatoria o invitación</label>
                                <input type="date" name="fecha_convocatoria" value="{{ old('fecha_convocatoria', $isEdit && $procedimiento->fecha_convocatoria ? $procedimiento->fecha_convocatoria->format('Y-m-d') : '') }}" @disabled(!$canEditSec1)
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Descripción de obras / bienes / servicios</label>
                                <input type="text" name="descripcion_bienes" value="{{ old('descripcion_bienes', $procedimiento->descripcion_bienes ?? '') }}" @disabled(!$canEditSec1) required
                                       placeholder="Ej. Seguro Vehicular, Construcción de..."
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Motivos y fundamentos legales aplicados</label>
                            <textarea name="fundamentos_legales" @disabled(!$canEditSec1) required rows="3"
                                      placeholder="Especificar el fundamento legal de la adjudicación o procedimiento..."
                                      class="w-full text-xs rounded-lg border-slate-350 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">{{ old('fundamentos_legales', $procedimiento->fundamentos_legales ?? '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Fecha junta de aclaraciones</label>
                                <input type="date" name="fecha_junta_aclaraciones" value="{{ old('fecha_junta_aclaraciones', $isEdit && $procedimiento->fecha_junta_aclaraciones ? $procedimiento->fecha_junta_aclaraciones->format('Y-m-d') : '') }}" @disabled(!$canEditSec1)
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo al acta de junta de aclaraciones</label>
                                <input type="url" name="acta_junta_url" value="{{ old('acta_junta_url', $procedimiento->acta_junta_url ?? '') }}" @disabled(!$canEditSec1)
                                       placeholder="https://ejemplo.com/acta_junta.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo al acta de presentación/apertura</label>
                                <input type="url" name="acta_apertura_url" value="{{ old('acta_apertura_url', $procedimiento->acta_apertura_url ?? '') }}" @disabled(!$canEditSec1)
                                       placeholder="https://ejemplo.com/acta_apertura.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo a dictámenes base del fallo</label>
                                <input type="url" name="dictamen_fallo_url" value="{{ old('dictamen_fallo_url', $procedimiento->dictamen_fallo_url ?? '') }}" @disabled(!$canEditSec1)
                                       placeholder="https://ejemplo.com/dictamen.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo al acta de fallo / resolución</label>
                                <input type="url" name="acta_fallo_url" value="{{ old('acta_fallo_url', $procedimiento->acta_fallo_url ?? '') }}" @disabled(!$canEditSec1)
                                       placeholder="https://ejemplo.com/fallo.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 bg-white">
                            <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider mb-3">
                                Persona Física Ganadora, Asignada o Adjudicada (Si aplica)
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[9px]">Nombre(s)</label>
                                    <input type="text" name="ganador_fisico_nombre" value="{{ old('ganador_fisico_nombre', $procedimiento->ganador_fisico_nombre ?? '') }}" @disabled(!$canEditSec1)
                                           placeholder="Nombre(s)"
                                           class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[9px]">Primer Apellido</label>
                                    <input type="text" name="ganador_fisico_primer_apellido" value="{{ old('ganador_fisico_primer_apellido', $procedimiento->ganador_fisico_primer_apellido ?? '') }}" @disabled(!$canEditSec1)
                                           placeholder="Primer Apellido"
                                           class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[9px]">Segundo Apellido</label>
                                    <input type="text" name="ganador_fisico_segundo_apellido" value="{{ old('ganador_fisico_segundo_apellido', $procedimiento->ganador_fisico_segundo_apellido ?? '') }}" @disabled(!$canEditSec1)
                                           placeholder="Segundo Apellido"
                                           class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[9px]">Sexo</label>
                                    <select name="ganador_fisico_sexo" @disabled(!$canEditSec1)
                                            class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec1) bg-slate-50 @enddisabled">
                                        <option value="">Selecciona...</option>
                                        <option value="Hombre" {{ old('ganador_fisico_sexo', $procedimiento->ganador_fisico_sexo ?? '') === 'Hombre' ? 'selected' : '' }}>Hombre</option>
                                        <option value="Mujer" {{ old('ganador_fisico_sexo', $procedimiento->ganador_fisico_sexo ?? '') === 'Mujer' ? 'selected' : '' }}>Mujer</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SUBTABLA 1: LICITANTES (Tabla_579209) --}}
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                    Licitantes, Proveedores o Contratistas Participantes (Tabla_579209)
                                </h4>
                                @if($canEditSec1)
                                    <button type="button" @click="addLicitante()"
                                            class="bg-slate-700 hover:bg-slate-800 text-white font-black uppercase text-[9px] px-2.5 py-1.5 rounded-lg transition shadow-sm cursor-pointer">
                                        + Agregar Licitante
                                    </button>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <template x-for="(lic, index) in licitantes" :key="index">
                                    <div class="bg-white p-3 rounded-lg border border-slate-200 space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Nombre(s)</label>
                                                <input type="text" :name="`licitantes[${index}][primer_nombre]`" x-model="lic.primer_nombre" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Primer Apellido</label>
                                                <input type="text" :name="`licitantes[${index}][primer_apellido]`" x-model="lic.primer_apellido" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Segundo Apellido</label>
                                                <input type="text" :name="`licitantes[${index}][segundo_apellido]`" x-model="lic.segundo_apellido" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Sexo</label>
                                                <select :name="`licitantes[${index}][sexo]`" x-model="lic.sexo" @disabled(!$canEditSec1)
                                                        class="w-full text-xs rounded-lg border-slate-200 shadow-sm py-1">
                                                    <option value="">Selecciona...</option>
                                                    <option value="Hombre">Hombre</option>
                                                    <option value="Mujer">Mujer</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Denominación o Razón Social</label>
                                                <input type="text" :name="`licitantes[${index}][razon_social]`" x-model="lic.razon_social" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end border-t pt-2 border-slate-100">
                                            <div class="md:col-span-6">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">RFC</label>
                                                <input type="text" :name="`licitantes[${index}][rfc]`" x-model="lic.rfc" @disabled(!$canEditSec1) required
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-6 text-right">
                                                @if($canEditSec1)
                                                    <button type="button" @click="removeLicitante(index)"
                                                            class="bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-red-200 transition cursor-pointer">
                                                        Remover Licitante
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="licitantes.length === 0" class="text-center text-slate-400 italic text-[11px] py-4 bg-white rounded-lg border border-dashed">
                                    Ningún licitante registrado en la subtabla.
                                </div>
                            </div>
                        </div>

                        {{-- SUBTABLA 2: COTIZACIONES / OFERTAS (Tabla_579236) --}}
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                    Ofertas Económicas Recibidas (Tabla_579236)
                                </h4>
                                @if($canEditSec1)
                                    <button type="button" @click="addCotizacion()"
                                            class="bg-slate-700 hover:bg-slate-800 text-white font-black uppercase text-[9px] px-2.5 py-1.5 rounded-lg transition shadow-sm cursor-pointer">
                                        + Agregar Oferta
                                    </button>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <template x-for="(cot, index) in cotizaciones" :key="index">
                                    <div class="bg-white p-3 rounded-lg border border-slate-200 space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Nombre(s)</label>
                                                <input type="text" :name="`cotizaciones[${index}][primer_nombre]`" x-model="cot.primer_nombre" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Primer Apellido</label>
                                                <input type="text" :name="`cotizaciones[${index}][primer_apellido]`" x-model="cot.primer_apellido" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Segundo Apellido</label>
                                                <input type="text" :name="`cotizaciones[${index}][segundo_apellido]`" x-model="cot.segundo_apellido" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Sexo</label>
                                                <select :name="`cotizaciones[${index}][sexo]`" x-model="cot.sexo" @disabled(!$canEditSec1)
                                                        class="w-full text-xs rounded-lg border-slate-200 shadow-sm py-1">
                                                    <option value="">Selecciona...</option>
                                                    <option value="Hombre">Hombre</option>
                                                    <option value="Mujer">Mujer</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Denominación o Razón Social</label>
                                                <input type="text" :name="`cotizaciones[${index}][razon_social]`" x-model="cot.razon_social" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end border-t pt-2 border-slate-100">
                                            <div class="md:col-span-6">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">RFC</label>
                                                <input type="text" :name="`cotizaciones[${index}][rfc]`" x-model="cot.rfc" @disabled(!$canEditSec1) required
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-6 text-right">
                                                @if($canEditSec1)
                                                    <button type="button" @click="removeCotizacion(index)"
                                                            class="bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-red-200 transition cursor-pointer">
                                                        Remover Oferta
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="cotizaciones.length === 0" class="text-center text-slate-400 italic text-[11px] py-4 bg-white rounded-lg border border-dashed">
                                    Ninguna oferta económica registrada en la subtabla.
                                </div>
                            </div>
                        </div>

                        {{-- SUBTABLA 3: PARTICIPANTES JUNTA (Tabla_579237) --}}
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                    Participantes de la Junta de Aclaraciones (Tabla_579237)
                                </h4>
                                @if($canEditSec1)
                                    <button type="button" @click="addJuntaParticipante()"
                                            class="bg-slate-700 hover:bg-slate-800 text-white font-black uppercase text-[9px] px-2.5 py-1.5 rounded-lg transition shadow-sm cursor-pointer">
                                        + Agregar Participante
                                    </button>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <template x-for="(jp, index) in juntaParticipantes" :key="index">
                                    <div class="bg-white p-3 rounded-lg border border-slate-200 space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Nombre(s)</label>
                                                <input type="text" :name="`junta_participantes[${index}][primer_nombre]`" x-model="jp.primer_nombre" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Primer Apellido</label>
                                                <input type="text" :name="`junta_participantes[${index}][primer_apellido]`" x-model="jp.primer_apellido" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Segundo Apellido</label>
                                                <input type="text" :name="`junta_participantes[${index}][segundo_apellido]`" x-model="jp.segundo_apellido" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Sexo</label>
                                                <select :name="`junta_participantes[${index}][sexo]`" x-model="jp.sexo" @disabled(!$canEditSec1)
                                                        class="w-full text-xs rounded-lg border-slate-200 shadow-sm py-1">
                                                    <option value="">Selecciona...</option>
                                                    <option value="Hombre">Hombre</option>
                                                    <option value="Mujer">Mujer</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Denominación o Razón Social</label>
                                                <input type="text" :name="`junta_participantes[${index}][razon_social]`" x-model="jp.razon_social" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end border-t pt-2 border-slate-100">
                                            <div class="md:col-span-6">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">RFC</label>
                                                <input type="text" :name="`junta_participantes[${index}][rfc]`" x-model="jp.rfc" @disabled(!$canEditSec1) required
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-6 text-right">
                                                @if($canEditSec1)
                                                    <button type="button" @click="removeJuntaParticipante(index)"
                                                            class="bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-red-200 transition cursor-pointer">
                                                        Remover Participante
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="juntaParticipantes.length === 0" class="text-center text-slate-400 italic text-[11px] py-4 bg-white rounded-lg border border-dashed">
                                    Ningún participante registrado en la subtabla.
                                </div>
                            </div>
                        </div>

                        {{-- SUBTABLA 4: SERVIDORES PÚBLICOS JUNTA (Tabla_579238) --}}
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                    Servidores Públicos en la Junta (Tabla_579238)
                                </h4>
                                @if($canEditSec1)
                                    <button type="button" @click="addJuntaServidor()"
                                            class="bg-slate-700 hover:bg-slate-800 text-white font-black uppercase text-[9px] px-2.5 py-1.5 rounded-lg transition shadow-sm cursor-pointer">
                                        + Agregar Servidor Público
                                    </button>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <template x-for="(js, index) in juntaServidores" :key="index">
                                    <div class="bg-white p-3 rounded-lg border border-slate-200 space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Nombre(s)</label>
                                                <input type="text" :name="`junta_servidores[${index}][primer_nombre]`" x-model="js.primer_nombre" @disabled(!$canEditSec1) required
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Primer Apellido</label>
                                                <input type="text" :name="`junta_servidores[${index}][primer_apellido]`" x-model="js.primer_apellido" @disabled(!$canEditSec1) required
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Segundo Apellido</label>
                                                <input type="text" :name="`junta_servidores[${index}][segundo_apellido]`" x-model="js.segundo_apellido" @disabled(!$canEditSec1)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Sexo</label>
                                                <select :name="`junta_servidores[${index}][sexo]`" x-model="js.sexo" @disabled(!$canEditSec1) required
                                                        class="w-full text-xs rounded-lg border-slate-200 shadow-sm py-1">
                                                    <option value="">Selecciona...</option>
                                                    <option value="Hombre">Hombre</option>
                                                    <option value="Mujer">Mujer</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Cargo</label>
                                                <input type="text" :name="`junta_servidores[${index}][cargo]`" x-model="js.cargo" @disabled(!$canEditSec1) required
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end border-t pt-2 border-slate-100">
                                            <div class="md:col-span-6">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">RFC</label>
                                                <input type="text" :name="`junta_servidores[${index}][rfc]`" x-model="js.rfc" @disabled(!$canEditSec1) required
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-6 text-right">
                                                @if($canEditSec1)
                                                    <button type="button" @click="removeJuntaServidor(index)"
                                                            class="bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-red-200 transition cursor-pointer">
                                                        Remover Servidor Público
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="juntaServidores.length === 0" class="text-center text-slate-400 italic text-[11px] py-4 bg-white rounded-lg border border-dashed">
                                    Ningún servidor público registrado en la subtabla.
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- PESTAÑA 2: SUMINISTROS --}}
                    <div x-show="activeTab === 'sec2'" class="space-y-6 animate-fade-in">
                        <div class="border-b pb-2 mb-4">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">
                                Sección 2: Proveedor Ganador y Contrato (Suministros / Recursos Materiales)
                            </h3>
                            @if(!$canEditSec2)
                                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 mt-1 inline-block uppercase">Solo Lectura para tu Área</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Nombre / Razón Social Proveedor Ganador</label>
                                <input type="text" name="proveedor_ganador_nombre" value="{{ old('proveedor_ganador_nombre', $procedimiento->proveedor_ganador_nombre ?? '') }}" @disabled(!$canEditSec2) required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">RFC Proveedor Ganador</label>
                                <input type="text" name="proveedor_ganador_rfc" value="{{ old('proveedor_ganador_rfc', $procedimiento->proveedor_ganador_rfc ?? '') }}" @disabled(!$canEditSec2) required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Domicilio Fiscal Completo</label>
                                <input type="text" name="proveedor_ganador_domicilio" value="{{ old('proveedor_ganador_domicilio', $procedimiento->proveedor_ganador_domicilio ?? '') }}" @disabled(!$canEditSec2) required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Monto Mínimo Contrato ($)</label>
                                <input type="number" step="0.01" name="monto_contrato_min" value="{{ old('monto_contrato_min', $procedimiento->monto_contrato_min ?? '') }}" @disabled(!$canEditSec2) required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Monto Máximo Contrato ($)</label>
                                <input type="number" step="0.01" name="monto_contrato_max" value="{{ old('monto_contrato_max', $procedimiento->monto_contrato_max ?? '') }}" @disabled(!$canEditSec2) required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Inicio Vigencia Contrato</label>
                                <input type="date" name="fecha_inicio_contrato" value="{{ old('fecha_inicio_contrato', ($isEdit && $procedimiento->fecha_inicio_contrato) ? $procedimiento->fecha_inicio_contrato->format('Y-m-d') : '') }}" @disabled(!$canEditSec2) required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Término Vigencia Contrato</label>
                                <input type="date" name="fecha_fin_contrato" value="{{ old('fecha_fin_contrato', ($isEdit && $procedimiento->fecha_fin_contrato) ? $procedimiento->fecha_fin_contrato->format('Y-m-d') : '') }}" @disabled(!$canEditSec2) required
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Forma de Pago</label>
                                <input type="text" name="forma_pago" value="{{ old('forma_pago', $procedimiento->forma_pago ?? '') }}" @disabled(!$canEditSec2) required
                                       placeholder="Ej. Transferencia electrónica, Pago único, Crédito"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Objeto del Contrato</label>
                                <input type="text" name="objeto_contrato" value="{{ old('objeto_contrato', $procedimiento->objeto_contrato ?? '') }}" @disabled(!$canEditSec2) required
                                       placeholder="Ej. Adquisición de mobiliario y equipo de oficina..."
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Fecha del contrato</label>
                                <input type="date" name="fecha_contrato" value="{{ old('fecha_contrato', ($isEdit && $procedimiento->fecha_contrato) ? $procedimiento->fecha_contrato->format('Y-m-d') : '') }}" @disabled(!$canEditSec2)
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Tipo de cambio (si aplica)</label>
                                <input type="number" step="0.0001" name="tipo_cambio" value="{{ old('tipo_cambio', $procedimiento->tipo_cambio ?? '') }}" @disabled(!$canEditSec2)
                                       placeholder="1.0000"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Monto total de garantías ($)</label>
                                <input type="number" step="0.01" name="monto_garantias" value="{{ old('monto_garantias', $procedimiento->monto_garantias ?? '') }}" @disabled(!$canEditSec2)
                                       placeholder="0.00"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo al documento del contrato</label>
                                <input type="url" name="contrato_url" value="{{ old('contrato_url', $procedimiento->contrato_url ?? '') }}" @disabled(!$canEditSec2)
                                       placeholder="https://ejemplo.com/contrato.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo al comunicado de suspensión (si aplica)</label>
                                <input type="url" name="comunicado_suspension_url" value="{{ old('comunicado_suspension_url', $procedimiento->comunicado_suspension_url ?? '') }}" @disabled(!$canEditSec2)
                                       placeholder="https://ejemplo.com/suspension.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Breve justificación de elección del proveedor</label>
                                <input type="text" name="justificacion_adjudicacion" value="{{ old('justificacion_adjudicacion', $procedimiento->justificacion_adjudicacion ?? '') }}" @disabled(!$canEditSec2)
                                       placeholder="Ej. Cumplió con todas las condiciones y precio competitivo"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec2) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        {{-- SUBTABLA 5: BENEFICIARIOS (Tabla_579206) --}}
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                    Beneficiarios Finales (Tabla_579206)
                                </h4>
                                @if($canEditSec2)
                                    <button type="button" @click="addBeneficiario()"
                                            class="bg-slate-700 hover:bg-slate-800 text-white font-black uppercase text-[9px] px-2.5 py-1.5 rounded-lg transition shadow-sm cursor-pointer">
                                        + Agregar Beneficiario
                                    </button>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <template x-for="(b, index) in beneficiarios" :key="index">
                                    <div class="bg-white p-3 rounded-lg border border-slate-200 space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                            <div class="md:col-span-4">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Nombre(s)</label>
                                                <input type="text" :name="`beneficiarios[${index}][primer_nombre]`" x-model="b.primer_nombre" @disabled(!$canEditSec2) required
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Primer Apellido</label>
                                                <input type="text" :name="`beneficiarios[${index}][primer_apellido]`" x-model="b.primer_apellido" @disabled(!$canEditSec2) required
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Segundo Apellido</label>
                                                <input type="text" :name="`beneficiarios[${index}][segundo_apellido]`" x-model="b.segundo_apellido" @disabled(!$canEditSec2)
                                                       class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                            </div>
                                            <div class="md:col-span-2 text-right">
                                                @if($canEditSec2)
                                                    <button type="button" @click="removeBeneficiario(index)"
                                                            class="bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-800 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-red-200 transition cursor-pointer">
                                                        Remover
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="beneficiarios.length === 0" class="text-center text-slate-400 italic text-[11px] py-4 bg-white rounded-lg border border-dashed">
                                    Ningún beneficiario final registrado en la subtabla.
                                </div>
                            </div>
                        </div>

                        {{-- SUBTABLA 6: PARTIDAS PRESUPUESTALES (Tabla_579239) --}}
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                    Partidas Presupuestales y Conceptos Contratados (Tabla_579239)
                                </h4>
                                @if($canEditSec2)
                                    <button type="button" @click="addPartida()"
                                            class="bg-slate-700 hover:bg-slate-800 text-white font-black uppercase text-[9px] px-2.5 py-1.5 rounded-lg transition shadow-sm cursor-pointer">
                                        + Agregar Partida
                                    </button>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <template x-for="(part, index) in partidas" :key="index">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-white p-3 rounded-lg border border-slate-200">
                                        <div class="md:col-span-10">
                                            <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Partida Presupuestal</label>
                                            <input type="text" :name="`partidas[${index}][numero_partida]`" x-model="part.numero_partida" @disabled(!$canEditSec2) required
                                                   placeholder="Ej. 211001"
                                                   class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                        </div>
                                        <div class="md:col-span-2 text-center">
                                            @if($canEditSec2)
                                                <button type="button" @click="removePartida(index)"
                                                        class="text-red-650 hover:text-red-900 font-bold text-sm focus:outline-none mb-1 cursor-pointer">&times;</button>
                                            @else
                                                <span class="text-slate-300">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </template>
                                <div x-show="partidas.length === 0" class="text-center text-slate-400 italic text-[11px] py-4 bg-white rounded-lg border border-dashed">
                                    Ninguna partida registrada en la subtabla.
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- PESTAÑA 3: INFRAESTRUCTURA --}}
                    <div x-show="activeTab === 'sec3'" class="space-y-6 animate-fade-in">
                        <div class="border-b pb-2 mb-4">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">
                                Sección 3: Obras y Convenios (Infraestructura / Áreas Técnicas)
                            </h3>
                            @if(!$canEditSec3)
                                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 mt-1 inline-block uppercase">Solo Lectura para tu Área</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">¿Ejecución de obra pública?</label>
                                <select name="ejecucion_obra" @disabled(!$canEditSec3) required
                                        class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                                    <option value="">Selecciona...</option>
                                    <option value="Sí" {{ old('ejecucion_obra', $procedimiento->ejecucion_obra ?? '') === 'Sí' ? 'selected' : '' }}>Sí</option>
                                    <option value="No" {{ old('ejecucion_obra', $procedimiento->ejecucion_obra ?? '') === 'No' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Origen de los recursos</label>
                                <input type="text" name="origen_recursos" value="{{ old('origen_recursos', $procedimiento->origen_recursos ?? '') }}" @disabled(!$canEditSec3) required
                                       placeholder="Ej. Estatal, Federal, Propio"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Fuente de financiamiento</label>
                                <input type="text" name="fuente_financiamiento" value="{{ old('fuente_financiamiento', $procedimiento->fuente_financiamiento ?? '') }}" @disabled(!$canEditSec3) required
                                       placeholder="Ej. FISM, FOSEG, etc."
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Lugar de ejecución de la obra / servicios</label>
                                <input type="text" name="lugar_ejecucion" value="{{ old('lugar_ejecucion', $procedimiento->lugar_ejecucion ?? '') }}" @disabled(!$canEditSec3) required
                                       placeholder="Municipio, Localidad, Dirección..."
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Etapa de la obra pública</label>
                                <select name="etapa_obra" @disabled(!$canEditSec3) required
                                        class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                                    <option value="">Selecciona...</option>
                                    <option value="Concluida" {{ old('etapa_obra', $procedimiento->etapa_obra ?? '') === 'Concluida' ? 'selected' : '' }}>Concluida</option>
                                    <option value="En proceso" {{ old('etapa_obra', $procedimiento->etapa_obra ?? '') === 'En proceso' ? 'selected' : '' }}>En proceso</option>
                                    <option value="Pendiente" {{ old('etapa_obra', $procedimiento->etapa_obra ?? '') === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Observaciones Generales</label>
                            <textarea name="observaciones" @disabled(!$canEditSec3) rows="3"
                                      placeholder="Cualquier aclaración o nota general sobre la ejecución del contrato..."
                                      class="w-full text-xs rounded-lg border-slate-350 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">{{ old('observaciones', $procedimiento->observaciones ?? '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Tipo de fondo de aportación / participación</label>
                                <input type="text" name="tipo_fondo" value="{{ old('tipo_fondo', $procedimiento->tipo_fondo ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="Ej. Ramo 33, Aportaciones federales"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Breve descripción de la obra (si aplica)</label>
                                <input type="text" name="descripcion_obra" value="{{ old('descripcion_obra', $procedimiento->descripcion_obra ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="Ej. Construcción de planta tratadora..."
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo a estudios de impacto ambiental</label>
                                <input type="text" name="impacto_ambiental_url" value="{{ old('impacto_ambiental_url', $procedimiento->impacto_ambiental_url ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="Ej. No se realizaron, o URL correspondiente"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Observaciones dirigidas a la población sobre la obra</label>
                                <input type="text" name="observaciones_obra" value="{{ old('observaciones_obra', $procedimiento->observaciones_obra ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="Ej. Ninguna, obra concluida satisfactoriamente"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Mecanismos de vigilancia y supervisión</label>
                                <input type="text" name="mecanismos_vigilancia" value="{{ old('mecanismos_vigilancia', $procedimiento->mecanismos_vigilancia ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="Ej. Contraloría Social, Auditorías internas"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo a informes de avances físicos</label>
                                <input type="url" name="informe_avances_fisicos_url" value="{{ old('informe_avances_fisicos_url', $procedimiento->informe_avances_fisicos_url ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="https://ejemplo.com/fisico.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo a informes de avance financiero</label>
                                <input type="url" name="informe_avances_financieros_url" value="{{ old('informe_avances_financieros_url', $procedimiento->informe_avances_financieros_url ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="https://ejemplo.com/financiero.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo al acta de recepción física u homóloga</label>
                                <input type="url" name="acta_recepcion_url" value="{{ old('acta_recepcion_url', $procedimiento->acta_recepcion_url ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="https://ejemplo.com/recepcion.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo al finiquito o terminación anticipada</label>
                                <input type="url" name="finiquito_url" value="{{ old('finiquito_url', $procedimiento->finiquito_url ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="https://ejemplo.com/finiquito.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1 text-[10px]">Hipervínculo a la factura o documento fiscal</label>
                                <input type="url" name="factura_url" value="{{ old('factura_url', $procedimiento->factura_url ?? '') }}" @disabled(!$canEditSec3)
                                       placeholder="https://ejemplo.com/factura.pdf"
                                       class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:ring focus:ring-guinda-ceaa/20 @disabled(!$canEditSec3) bg-slate-50 @enddisabled">
                            </div>
                        </div>

                        {{-- SUBTABLA 7: CONVENIOS MODIFICATORIOS (Tabla_579240) --}}
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                    Convenios Modificatorios (Tabla_579240)
                                </h4>
                                @if($canEditSec3)
                                    <button type="button" @click="addConvenio()"
                                            class="bg-slate-700 hover:bg-slate-800 text-white font-black uppercase text-[9px] px-2.5 py-1.5 rounded-lg transition shadow-sm cursor-pointer">
                                        + Agregar Convenio
                                    </button>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <template x-for="(c, index) in convenios" :key="index">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-white p-3 rounded-lg border border-slate-200">
                                        <div class="md:col-span-2">
                                            <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">No. Convenio</label>
                                            <input type="text" :name="`convenios[${index}][numero_convenio]`" x-model="c.numero_convenio" @disabled(!$canEditSec3) required
                                                   placeholder="Ej. CEAA-CM-01"
                                                   class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                        </div>
                                        <div class="md:col-span-4">
                                            <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Objeto / Modificación</label>
                                            <input type="text" :name="`convenios[${index}][objeto]`" x-model="c.objeto" @disabled(!$canEditSec3) required
                                                   placeholder="Ej. Ampliación de plazo, Ajuste costo"
                                                   class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Monto Modificado ($)</label>
                                            <input type="number" step="0.01" :name="`convenios[${index}][monto_modificado]`" x-model="c.monto_modificado" @disabled(!$canEditSec3) required
                                                   class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block font-bold text-slate-400 uppercase tracking-wide mb-1 text-[9px]">Fecha Firma</label>
                                            <input type="date" :name="`convenios[${index}][fecha_firma]`" x-model="c.fecha_firma" @disabled(!$canEditSec3) required
                                                   class="w-full text-xs rounded-lg border-slate-200 shadow-sm">
                                        </div>
                                        <div class="md:col-span-1 text-center">
                                            @if($canEditSec3)
                                                <button type="button" @click="removeConvenio(index)"
                                                        class="text-red-650 hover:text-red-900 font-bold text-sm focus:outline-none mb-1 cursor-pointer">&times;</button>
                                            @else
                                                <span class="text-slate-300">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </template>
                                <div x-show="convenios.length === 0" class="text-center text-slate-400 italic text-[11px] py-4 bg-white rounded-lg border border-dashed">
                                    Ningún convenio modificatorio registrado en la subtabla.
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Botón de Guardado --}}
                    @if($canEditSec1 || $canEditSec2 || $canEditSec3)
                        <div class="flex justify-end pt-6 border-t mt-6">
                            <button type="submit" 
                                    class="bg-guinda-ceaa hover:bg-guinda-ceaa/90 text-white font-black uppercase text-xs tracking-widest px-6 py-3 rounded-lg transition shadow-md hover:shadow-lg cursor-pointer">
                                Guardar Cambios
                            </button>
                        </div>
                    @endif

                </div>
            </form>

        </div>
    </div>

    {{-- Preparar arreglos en PHP para evitar errores del compilador Blade en expresiones complejas --}}
    @php
        $licitantesJson = [];
        if ($isEdit) {
            $licitantesJson = $procedimiento->licitantes->map(function($lic) {
                return [
                    'primer_nombre' => $lic->primer_nombre,
                    'primer_apellido' => $lic->primer_apellido,
                    'segundo_apellido' => $lic->segundo_apellido,
                    'sexo' => $lic->sexo,
                    'razon_social' => $lic->razon_social,
                    'rfc' => $lic->rfc
                ];
            })->toArray();
        }
        $cotizacionesJson = [];
        if ($isEdit) {
            $cotizacionesJson = $procedimiento->cotizaciones->map(function($cot) {
                return [
                    'primer_nombre' => $cot->primer_nombre,
                    'primer_apellido' => $cot->primer_apellido,
                    'segundo_apellido' => $cot->segundo_apellido,
                    'sexo' => $cot->sexo,
                    'razon_social' => $cot->razon_social,
                    'rfc' => $cot->rfc
                ];
            })->toArray();
        }
        $juntaParticipantesJson = [];
        if ($isEdit) {
            $juntaParticipantesJson = $procedimiento->juntaParticipantes->map(function($jp) {
                return [
                    'primer_nombre' => $jp->primer_nombre,
                    'primer_apellido' => $jp->primer_apellido,
                    'segundo_apellido' => $jp->segundo_apellido,
                    'sexo' => $jp->sexo,
                    'razon_social' => $jp->razon_social,
                    'rfc' => $jp->rfc
                ];
            })->toArray();
        }
        $juntaServidoresJson = [];
        if ($isEdit) {
            $juntaServidoresJson = $procedimiento->juntaServidores->map(function($js) {
                return [
                    'primer_nombre' => $js->primer_nombre,
                    'primer_apellido' => $js->primer_apellido,
                    'segundo_apellido' => $js->segundo_apellido,
                    'sexo' => $js->sexo,
                    'rfc' => $js->rfc,
                    'cargo' => $js->cargo,
                ];
            })->toArray();
        }
        $beneficiariosJson = [];
        if ($isEdit) {
            $beneficiariosJson = $procedimiento->beneficiarios->map(function($b) {
                return [
                    'primer_nombre' => $b->primer_nombre,
                    'primer_apellido' => $b->primer_apellido,
                    'segundo_apellido' => $b->segundo_apellido,
                ];
            })->toArray();
        }
        $partidasJson = [];
        if ($isEdit) {
            $partidasJson = $procedimiento->partidas->map(function($part) {
                return [
                    'numero_partida' => $part->numero_partida
                ];
            })->toArray();
        }
        $conveniosJson = [];
        if ($isEdit) {
            $conveniosJson = $procedimiento->convenios->map(function($c) {
                return [
                    'numero_convenio' => $c->numero_convenio,
                    'objeto' => $c->objeto,
                    'monto_modificado' => $c->monto_modificado,
                    'fecha_firma' => $c->fecha_firma ? $c->fecha_firma->format('Y-m-d') : ''
                ];
            })->toArray();
        }

        $trimestreValue = '';
        if ($isEdit && $procedimiento->periodo_inicio) {
            $month = $procedimiento->periodo_inicio->format('m');
            if ($month === '01') $trimestreValue = '1';
            elseif ($month === '04') $trimestreValue = '2';
            elseif ($month === '07') $trimestreValue = '3';
            elseif ($month === '10') $trimestreValue = '4';
        }
    @endphp

    {{-- Script Reactivo con Alpine.js para las subtablas dinámicas --}}
    <script>
        function pntFormState() {
            return {
                activeTab: 'sec1',
                licitantes: @json($licitantesJson),
                cotizaciones: @json($cotizacionesJson),
                juntaParticipantes: @json($juntaParticipantesJson),
                juntaServidores: @json($juntaServidoresJson),
                beneficiarios: @json($beneficiariosJson),
                partidas: @json($partidasJson),
                convenios: @json($conveniosJson),
                ejercicio: {{ old('ejercicio', $procedimiento->ejercicio ?? now()->year) }},
                trimestre: '{{ old('trimestre', $trimestreValue) }}',
                periodo_inicio: '{{ old('periodo_inicio', $isEdit ? $procedimiento->periodo_inicio->format('Y-m-d') : '') }}',
                periodo_fin: '{{ old('periodo_fin', $isEdit ? $procedimiento->periodo_fin->format('Y-m-d') : '') }}',

                // --- QUICK FILL ASSISTANT STATE ---
                assistantOpen: {{ !$isEdit ? 'true' : 'false' }},
                apiTokenSet: {{ config('services.pnt.api_token') ? 'true' : 'false' }},
                apiSubTab: 'search',
                apiPage: 1,
                apiTotalPages: 1,
                apiLicitaciones: [],
                apiQuery: '',
                apiLoading: false,
                apiError: '',
                pastedJsonText: '',
                pastedJsonValid: null,
                selectedLicitacionId: '',
                successMsg: '',

                updatePeriodDates() {
                    if (!this.trimestre || !this.ejercicio) {
                        this.periodo_inicio = '';
                        this.periodo_fin = '';
                        return;
                    }
                    const year = this.ejercicio;
                    if (this.trimestre === '1') {
                        this.periodo_inicio = `${year}-01-01`;
                        this.periodo_fin = `${year}-03-31`;
                    } else if (this.trimestre === '2') {
                        this.periodo_inicio = `${year}-04-01`;
                        this.periodo_fin = `${year}-06-30`;
                    } else if (this.trimestre === '3') {
                        this.periodo_inicio = `${year}-07-01`;
                        this.periodo_fin = `${year}-09-30`;
                    } else if (this.trimestre === '4') {
                        this.periodo_inicio = `${year}-10-01`;
                        this.periodo_fin = `${year}-12-31`;
                    }
                },

                initAssistant() {
                    if (this.apiTokenSet && this.apiLicitaciones.length === 0) {
                        this.fetchLicitaciones(1);
                    }
                },

                fetchLicitaciones(page) {
                    this.apiLoading = true;
                    this.apiError = '';
                    fetch(`/pnt/external-licitaciones?page=${page}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Error al conectar con el servidor.');
                            return res.json();
                        })
                        .then(data => {
                            this.apiLicitaciones = data.data || [];
                            this.apiPage = data.meta ? data.meta.current_page : 1;
                            this.apiTotalPages = data.meta ? data.meta.last_page : 1;
                        })
                        .catch(err => {
                            this.apiError = 'No se pudieron recuperar las licitaciones en este momento.';
                            console.error(err);
                        })
                        .finally(() => {
                            this.apiLoading = false;
                        });
                },

                filteredLicitaciones() {
                    if (!this.apiQuery) return this.apiLicitaciones;
                    const q = this.apiQuery.toLowerCase();
                    return this.apiLicitaciones.filter(item => {
                        return (item.numero_expediente && item.numero_expediente.toLowerCase().includes(q)) ||
                               (item.titulo && item.titulo.toLowerCase().includes(q)) ||
                               (item.descripcion && item.descripcion.toLowerCase().includes(q));
                    });
                },

                loadFromSelected() {
                    if (!this.selectedLicitacionId) return;
                    const item = this.apiLicitaciones.find(x => x.id == this.selectedLicitacionId);
                    if (item) {
                        this.autofill(item);
                        this.successMsg = `¡Formulario completado con éxito con los datos del expediente ${item.numero_expediente}!`;
                        setTimeout(() => this.successMsg = '', 5000);
                    }
                },

                loadFromPasted() {
                    this.apiError = '';
                    this.successMsg = '';
                    this.pastedJsonValid = null;

                    if (!this.pastedJsonText.trim()) {
                        this.apiError = 'Por favor, pega un contenido JSON antes de continuar.';
                        return;
                    }

                    try {
                        const parsed = JSON.parse(this.pastedJsonText);
                        let item = null;
                        
                        if (parsed.data && Array.isArray(parsed.data)) {
                            if (parsed.data.length > 0) {
                                item = parsed.data[0];
                            } else {
                                throw new Error('El campo "data" del JSON está vacío.');
                            }
                        } else if (Array.isArray(parsed)) {
                            if (parsed.length > 0) {
                                item = parsed[0];
                            } else {
                                throw new Error('El arreglo JSON está vacío.');
                            }
                        } else {
                            item = parsed;
                        }

                        if (item && (item.numero_expediente || item.id)) {
                            this.autofill(item);
                            this.pastedJsonValid = true;
                            this.successMsg = `¡Formulario completado con éxito! Se cargó el expediente: ${item.numero_expediente || 'N/A'}`;
                            setTimeout(() => this.successMsg = '', 5000);
                        } else {
                            throw new Error('El JSON no tiene campos válidos de licitación.');
                        }
                    } catch (e) {
                        this.pastedJsonValid = false;
                        this.apiError = 'Error al procesar JSON: ' + e.message;
                    }
                },

                mapTipoProcedimiento(apiName) {
                    if (!apiName) return '';
                    const norm = apiName.toLowerCase();
                    if (norm.includes('licitacion') || norm.includes('licitación')) {
                        return 'Licitación pública';
                    }
                    if (norm.includes('adjudicacion') || norm.includes('adjudicación')) {
                        return 'Adjudicación directa';
                    }
                    if (norm.includes('tres personas') || norm.includes('3 personas') || norm.includes('invitacion') || norm.includes('invitación')) {
                        return 'Invitación a cuando menos tres personas';
                    }
                    return '';
                },

                autofill(item) {
                    // 1. Alpine fields
                    if (item.fechas && item.fechas.publicacion) {
                        const year = new Date(item.fechas.publicacion).getFullYear();
                        this.ejercicio = year;
                    } else if (item.numero_expediente) {
                        const parts = item.numero_expediente.split('-');
                        const lastPart = parts[parts.length - 1];
                        if (/^\d{4}$/.test(lastPart)) {
                            this.ejercicio = parseInt(lastPart);
                        }
                    }

                    if (item.fechas && item.fechas.publicacion) {
                        const month = new Date(item.fechas.publicacion).getMonth() + 1;
                        if (month >= 1 && month <= 3) this.trimestre = '1';
                        else if (month >= 4 && month <= 6) this.trimestre = '2';
                        else if (month >= 7 && month <= 9) this.trimestre = '3';
                        else if (month >= 10 && month <= 12) this.trimestre = '4';
                        this.updatePeriodDates();
                    }

                    // Parse empresa_ganadora details if it's an object to prevent [object Object]
                    let empName = '';
                    let empRfc = '';
                    let empDom = '';
                    if (item.adjudicacion && item.adjudicacion.empresa_ganadora) {
                        const eg = item.adjudicacion.empresa_ganadora;
                        if (typeof eg === 'object' && eg !== null) {
                            empName = eg.razon_social || eg.nombre || eg.nombre_comercial || eg.nombre_completo || '';
                            empRfc = eg.rfc || '';
                            if (eg.domicilio) {
                                if (typeof eg.domicilio === 'object') {
                                    empDom = eg.domicilio.calle || '';
                                    if (eg.domicilio.numero_exterior) empDom += ' Ext ' + eg.domicilio.numero_exterior;
                                    if (eg.domicilio.colonia) empDom += ', Col. ' + eg.domicilio.colonia;
                                    if (eg.domicilio.municipio) empDom += ', ' + eg.domicilio.municipio;
                                    if (eg.domicilio.estado) empDom += ', ' + eg.domicilio.estado;
                                } else {
                                    empDom = eg.domicilio;
                                }
                            }
                        } else {
                            empName = eg;
                        }
                    }

                    // 2. DOM fields mapping
                    const domFields = {
                        numero_expediente: item.numero_expediente || '',
                        descripcion_bienes: item.titulo || '',
                        descripcion_obra: item.descripcion || '',
                        objeto_contrato: item.descripcion || item.titulo || '',
                        tipo_procedimiento: this.mapTipoProcedimiento(item.tipo_procedimiento_nombre || item.tipo_procedimiento),
                        tipo_contratacion: (item.numero_expediente && item.numero_expediente.includes('-OP-')) ? 'Obra pública' : 'Adquisiciones',
                        caracter_procedimiento: 'Nacional',
                        declarado_desierto: 'No',
                        fecha_convocatoria: item.fechas && item.fechas.publicacion ? item.fechas.publicacion.substring(0, 10) : '',
                        
                        // Sección 2: Proveedor y contrato
                        proveedor_ganador_nombre: empName,
                        proveedor_ganador_rfc: empRfc,
                        proveedor_ganador_domicilio: empDom,
                        monto_contrato_min: item.adjudicacion && item.adjudicacion.monto ? item.adjudicacion.monto : (item.autorizacion && item.autorizacion.monto ? item.autorizacion.monto : ''),
                        monto_contrato_max: item.adjudicacion && item.adjudicacion.monto ? item.adjudicacion.monto : (item.autorizacion && item.autorizacion.monto ? item.autorizacion.monto : ''),
                        fecha_inicio_contrato: item.fechas && item.fechas.inicio_obra ? item.fechas.inicio_obra.substring(0, 10) : '',
                        fecha_fin_contrato: item.fechas && item.fechas.fin_obra ? item.fechas.fin_obra.substring(0, 10) : '',
                        
                        // Sección 3: Infraestructura
                        ejecucion_obra: item.ubicacion ? `${item.ubicacion.municipio}, ${item.ubicacion.localidad}` : '',
                        lugar_ejecucion: item.ubicacion ? `${item.ubicacion.municipio}, ${item.ubicacion.localidad}` : '',
                        origen_recursos: item.programa || 'Recursos Estatales',
                        fuente_financiamiento: item.programa || '',
                        tipo_fondo: item.programa || '',
                        justificacion_adjudicacion: item.autorizacion && item.autorizacion.oficio ? `Autorizado mediante oficio de suficiencia presupuestal: ${item.autorizacion.oficio}` : ''
                    };

                    Object.keys(domFields).forEach(name => {
                        const el = document.querySelector(`[name="${name}"]`);
                        if (el) {
                            el.value = domFields[name];
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                },

                addLicitante() {
                    this.licitantes.push({
                        primer_nombre: '',
                        primer_apellido: '',
                        segundo_apellido: '',
                        sexo: '',
                        razon_social: '',
                        rfc: ''
                    });
                },
                removeLicitante(index) {
                    this.licitantes.splice(index, 1);
                },

                addCotizacion() {
                    this.cotizaciones.push({
                        primer_nombre: '',
                        primer_apellido: '',
                        segundo_apellido: '',
                        sexo: '',
                        razon_social: '',
                        rfc: ''
                    });
                },
                removeCotizacion(index) {
                    this.cotizaciones.splice(index, 1);
                },

                addJuntaParticipante() {
                    this.juntaParticipantes.push({
                        primer_nombre: '',
                        primer_apellido: '',
                        segundo_apellido: '',
                        sexo: '',
                        razon_social: '',
                        rfc: ''
                    });
                },
                removeJuntaParticipante(index) {
                    this.juntaParticipantes.splice(index, 1);
                },

                addJuntaServidor() {
                    this.juntaServidores.push({
                        primer_nombre: '',
                        primer_apellido: '',
                        segundo_apellido: '',
                        sexo: '',
                        rfc: '',
                        cargo: ''
                    });
                },
                removeJuntaServidor(index) {
                    this.juntaServidores.splice(index, 1);
                },

                addBeneficiario() {
                    this.beneficiarios.push({
                        primer_nombre: '',
                        primer_apellido: '',
                        segundo_apellido: ''
                    });
                },
                removeBeneficiario(index) {
                    this.beneficiarios.splice(index, 1);
                },

                addPartida() {
                    this.partidas.push({ numero_partida: '' });
                },
                removePartida(index) {
                    this.partidas.splice(index, 1);
                },

                addConvenio() {
                    this.convenios.push({ numero_convenio: '', objeto: '', monto_modificado: 0, fecha_firma: '' });
                },
                removeConvenio(index) {
                    this.convenios.splice(index, 1);
                }
            };
        }
    </script>
</x-app-layout>
