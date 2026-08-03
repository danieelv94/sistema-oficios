<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight uppercase tracking-wider">
            Mi Perfil de Empleado
        </h2>
    </x-slot>

    @php
        $fechaAlta = $user->fecha_alta;
        $fechaAltaCarbon = $fechaAlta ? \Carbon\Carbon::parse($fechaAlta) : null;
        
        \Carbon\Carbon::setLocale('es');
        
        $antiguedad = '';
        $aptoVacaciones = false;
        $mesesRestantes = 0;
        
        if ($fechaAltaCarbon) {
            $diffAnios = $fechaAltaCarbon->diffInYears(now());
            $diffMeses = $fechaAltaCarbon->diffInMonths(now()) % 12;
            $diffDias = $fechaAltaCarbon->diffInDays(now()) % 30;
            
            $antiguedadParts = [];
            if ($diffAnios > 0) {
                $antiguedadParts[] = $diffAnios == 1 ? "1 año" : "$diffAnios años";
            }
            if ($diffMeses > 0) {
                $antiguedadParts[] = $diffMeses == 1 ? "1 mes" : "$diffMeses meses";
            }
            if ($diffAnios == 0 && $diffMeses == 0) {
                $antiguedadParts[] = $diffDias == 1 ? "1 día" : "$diffDias días";
            }
            $antiguedad = implode(', ', $antiguedadParts);
            
            $aptoVacaciones = $fechaAltaCarbon->diffInYears(now()) >= 1;
            
            if (!$aptoVacaciones) {
                $metaUnAnio = $fechaAltaCarbon->copy()->addYear();
                $diasRestantes = now()->diffInDays($metaUnAnio, false);
                $mesesRestantes = ceil($diasRestantes / 30.4);
            }
        }

        $rolesMap = [
            'admin' => 'Administrador(a)',
            'correspondencia' => 'Personal de Correspondencia',
            'recepcionista' => 'Recepcionista',
            'jefe_area' => 'Director(a) de Área / Jefe(a)',
            'secretaria_area' => 'Secretaria de Área',
            'subdirector' => 'Subdirector(a)',
            'user' => 'Personal Operativo',
        ];
        $rolLegible = $rolesMap[$user->role] ?? $user->role;

        // Initials for avatar
        $nameParts = explode(' ', $user->name);
        $initials = '';
        if (isset($nameParts[0])) $initials .= substr($nameParts[0], 0, 1);
        if (isset($nameParts[1])) $initials .= substr($nameParts[1], 0, 1);
        $initials = strtoupper($initials);
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de éxito y error --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-semibold rounded-r shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-semibold rounded-r shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- COLUMNA DE INFORMACIÓN DE EMPLEADO (Izquierda, más ancha) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Tarjeta Principal: Perfil y Datos --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-guinda-ceaa p-6">
                        <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-gray-100">
                            {{-- Avatar con Iniciales --}}
                            <div class="w-20 h-20 rounded-full bg-guinda-ceaa flex items-center justify-center text-white text-2xl font-black shadow-inner">
                                {{ $initials }}
                            </div>
                            <div class="text-center sm:text-left">
                                <h3 class="text-xl font-black text-gray-900 leading-tight">
                                    {{ $user->prof }} {{ $user->name }}
                                </h3>
                                <p class="text-xs font-bold text-dorado-ocre uppercase mt-1 tracking-wider">
                                    {{ $user->cargo ?? $rolLegible }}
                                </p>
                                <span class="inline-block mt-2 px-3 py-0.5 bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-bold rounded-full uppercase">
                                    N. Empleado: #{{ $user->no_empleado ?? 'S/N' }}
                                </span>
                            </div>
                        </div>

                        {{-- Cuadrícula de Detalles --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Correo Electrónico</span>
                                <span class="text-sm font-semibold text-gray-800 block mt-1">{{ $user->email }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Rol en el Sistema</span>
                                <span class="text-sm font-semibold text-gray-800 block mt-1">{{ $rolLegible }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Dirección / Área</span>
                                <span class="text-sm font-semibold text-gray-800 block mt-1">{{ $user->area->name ?? 'No asignada' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Subdirección / Subárea</span>
                                <span class="text-sm font-semibold text-gray-800 block mt-1">{{ $user->subarea->name ?? 'No asignada' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Nivel</span>
                                <span class="text-sm font-semibold text-gray-800 block mt-1">{{ $user->nivel->nombre ?? 'Sin nivel asignado' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Fecha de Alta / Ingreso</span>
                                <span class="text-sm font-semibold text-gray-800 block mt-1">
                                    @if($fechaAltaCarbon)
                                        {{ $fechaAltaCarbon->format('d/m/Y') }}
                                    @else
                                        <span class="text-amber-600 font-bold text-xs uppercase bg-amber-50 px-2 py-0.5 rounded border border-amber-100">No registrada</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Tarjeta de Vacaciones y Antigüedad --}}
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 border-b pb-2">
                            Estatus de Vacaciones y Antigüedad
                        </h4>

                        @if(!$fechaAltaCarbon)
                            {{-- Estatus sin fecha registrada --}}
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                                <div class="p-2 rounded-lg bg-slate-400 text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h5 class="text-sm font-black text-slate-700 uppercase">
                                        Fecha de Ingreso No Registrada
                                    </h5>
                                    <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                        Tu fecha oficial de ingreso institucional no ha sido registrada. Por favor, solicita al **Administrador del Sistema** registrar tu fecha de alta en tu expediente de usuario para poder calcular tu antigüedad real y habilitar tu derecho a vacaciones.
                                    </p>
                                </div>
                            </div>
                        @else
                            {{-- Estatus con fecha registrada --}}
                            <div class="flex items-start gap-4 p-4 rounded-xl {{ $aptoVacaciones ? 'bg-green-50/80 border border-green-200' : 'bg-amber-50/80 border border-amber-200' }}">
                                <div class="p-2 rounded-lg {{ $aptoVacaciones ? 'bg-green-500 text-white' : 'bg-amber-500 text-white' }}">
                                    @if($aptoVacaciones)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h5 class="text-sm font-black {{ $aptoVacaciones ? 'text-green-800' : 'text-amber-800' }} uppercase">
                                        {{ $aptoVacaciones ? 'Apto para Periodo Vacacional' : 'Periodo Vacacional Pendiente' }}
                                    </h5>
                                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                                        Tu antigüedad acumulada es de: <strong class="font-bold text-gray-800">{{ $antiguedad }}</strong>.
                                        @if($aptoVacaciones)
                                            Has cumplido el requisito mínimo de 1 año de servicio. A continuación puedes seleccionar tus días de vacaciones en el calendario y enviar tu solicitud.
                                        @else
                                            El reglamento institucional establece un mínimo de 1 año de servicio continuo para tener derecho a solicitar vacaciones. Te restan aproximadamente <strong class="font-bold text-gray-800">{{ $mesesRestantes }} {{ $mesesRestantes == 1 ? 'mes' : 'meses' }}</strong> para cumplir el periodo.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($aptoVacaciones)
                        @if(!$configuracion)
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-amber-50 border border-amber-200 mb-6 shadow-sm">
                                <div class="p-2 rounded-lg bg-amber-500 text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1 text-xs">
                                    <h5 class="text-sm font-black text-amber-800 uppercase">
                                        Períodos Vacacionales No Configurados
                                    </h5>
                                    <p class="text-slate-600 mt-2 leading-relaxed">
                                        El Administrador del Sistema aún no ha definido los rangos de fechas oficiales para los dos períodos vacacionales de este año. Por favor, solicita al administrador configurar las fechas de los períodos para habilitar la solicitud en línea.
                                    </p>
                                </div>
                            </div>
                        @else
                            {{-- Tarjeta: Solicitud de Vacaciones (Calendario) --}}
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-guinda-ceaa animate-fade-in"
                                 x-data="vacationCalendar()">
                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 border-b pb-2">
                                    Calendario de Solicitud de Vacaciones
                                </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Columna Izquierda: Calendario --}}
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    {{-- Navegación de Meses --}}
                                    <div class="flex items-center justify-between mb-4">
                                        <button type="button" @click="prevMonth()" class="p-1.5 rounded-lg hover:bg-slate-200 transition text-slate-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                        <span class="text-xs font-black uppercase text-slate-800 tracking-wider" x-text="`${monthNames[currentMonth]} ${currentYear}`"></span>
                                        <button type="button" @click="nextMonth()" class="p-1.5 rounded-lg hover:bg-slate-200 transition text-slate-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Encabezado de Días de la Semana --}}
                                    <div class="grid grid-cols-7 gap-1 text-center mb-1 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                        <div>Lu</div>
                                        <div>Ma</div>
                                        <div>Mi</div>
                                        <div>Ju</div>
                                        <div>Vi</div>
                                        <div class="text-red-400">Sá</div>
                                        <div class="text-red-400">Do</div>
                                    </div>

                                    {{-- Cuadrícula de Días --}}
                                    <div class="grid grid-cols-7 gap-1 text-center">
                                        <template x-for="dayObj in days" :key="dayObj.dateString ? dayObj.dateString : 'padding-' + Math.random()">
                                            <div @click="toggleDay(dayObj)"
                                                 :class="{
                                                     'p-2 rounded-lg text-xs font-bold transition select-none flex items-center justify-center relative aspect-square cursor-pointer': true,
                                                     'text-gray-300 pointer-events-none hover:bg-transparent': !dayObj.isCurrentMonth || (dayObj.dateString && (dayObj.dateString < getEarliestAllowedDate() || !isDayWithinPeriods(dayObj.dateString))),
                                                     'bg-slate-100 text-gray-400 cursor-not-allowed pointer-events-none': dayObj.isCurrentMonth && dayObj.isWeekend,
                                                     'hover:bg-slate-200 text-slate-800 bg-white shadow-sm border border-slate-150': dayObj.isCurrentMonth && !dayObj.isWeekend && dayObj.dateString >= getEarliestAllowedDate() && isDayWithinPeriods(dayObj.dateString) && !getDayStatus(dayObj.dateString),
                                                     'bg-blue-600 text-white shadow-md hover:bg-blue-700': getDayStatus(dayObj.dateString) === 'selected',
                                                     'bg-amber-400 text-slate-850 shadow-sm font-black cursor-not-allowed pointer-events-none': getDayStatus(dayObj.dateString) === 'Pendiente',
                                                     'bg-green-600 text-white shadow-md font-black cursor-not-allowed pointer-events-none': getDayStatus(dayObj.dateString) === 'Aprobado',
                                                     'bg-red-400 text-white shadow-sm font-black cursor-not-allowed pointer-events-none': getDayStatus(dayObj.dateString) === 'Rechazado'
                                                 }">
                                                <span x-text="dayObj.day"></span>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Leyenda --}}
                                    <div class="flex flex-wrap items-center justify-center gap-3 mt-4 pt-3 border-t border-slate-200 text-[9px] uppercase font-bold text-slate-500">
                                        <div class="flex items-center gap-1">
                                            <div class="w-3 h-3 bg-white border border-slate-200 rounded"></div>
                                            <span>Disponible</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <div class="w-3 h-3 bg-blue-600 rounded"></div>
                                            <span>Seleccionado</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <div class="w-3 h-3 bg-amber-400 rounded"></div>
                                            <span>Pendiente</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <div class="w-3 h-3 bg-green-600 rounded"></div>
                                            <span>Aprobado</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Columna Derecha: Formulario de Solicitud --}}
                                <div class="flex flex-col justify-between">
                                    <form action="{{ route('vacaciones.store') }}" method="POST" class="h-full flex flex-col justify-between gap-4">
                                        @csrf
                                        
                                        {{-- Inputs ocultos de fechas --}}
                                        <template x-for="date in selectedDays" :key="date">
                                            <input type="hidden" name="fechas[]" :value="date">
                                        </template>

                                        <div>
                                            {{-- Resumen de Periodos --}}
                                            <div class="grid grid-cols-2 gap-3 mb-4 text-center">
                                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                                    <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Período 1</span>
                                                    <span class="block text-[10px] font-bold text-slate-500 mt-1" x-text="formatPeriodRange('p1')"></span>
                                                    <div class="mt-2 text-xs font-black">
                                                        <span :class="(diasPeriodo1Usados + diasPeriodo1Seleccionados) >= 5 ? 'text-amber-600' : 'text-slate-700'"
                                                              x-text="`${diasPeriodo1Usados + diasPeriodo1Seleccionados}/5 días`"></span>
                                                    </div>
                                                </div>
                                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                                    <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Período 2</span>
                                                    <span class="block text-[10px] font-bold text-slate-500 mt-1" x-text="formatPeriodRange('p2')"></span>
                                                    <div class="mt-2 text-xs font-black">
                                                        <span :class="(diasPeriodo2Usados + diasPeriodo2Seleccionados) >= 5 ? 'text-amber-600' : 'text-slate-700'"
                                                              x-text="`${diasPeriodo2Usados + diasPeriodo2Seleccionados}/5 días`"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">
                                                Días seleccionados para vacaciones:
                                            </label>
                                            
                                            <div class="max-h-36 overflow-y-auto border border-slate-150 rounded-lg p-2 bg-slate-50 flex flex-wrap gap-1.5 min-h-[48px] items-center">
                                                <span x-show="selectedDays.length === 0" class="text-[10px] text-slate-400 italic px-2">Haz clic en los días disponibles del calendario para seleccionarlos...</span>
                                                <template x-for="date in selectedDays" :key="date">
                                                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-[10px] font-black px-2 py-0.5 rounded-full border border-blue-200">
                                                        <span x-text="formatDateReadable(date)"></span>
                                                        <button type="button" @click="removeSelectedDay(date)" class="text-blue-600 hover:text-blue-900 font-bold focus:outline-none">&times;</button>
                                                    </span>
                                                </template>
                                            </div>

                                            <div class="mt-4">
                                                <label for="observaciones" class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                                                    Observaciones (Opcional):
                                                </label>
                                                <textarea name="observaciones" id="observaciones" rows="3"
                                                          placeholder="Ej. Periodo vacacional de Verano, Asuntos personales, etc."
                                                          class="block w-full rounded-lg border-gray-305 text-xs focus:ring-guinda-ceaa focus:border-guinda-ceaa placeholder-gray-400"></textarea>
                                            </div>
                                        </div>

                                        <button type="submit" 
                                                x-bind:disabled="selectedDays.length === 0"
                                                :class="{
                                                    'w-full text-white py-2 rounded-lg font-black uppercase text-xs tracking-wider transition shadow-sm': true,
                                                    'bg-guinda-ceaa hover:bg-guinda-ceaa/90 cursor-pointer': selectedDays.length > 0,
                                                    'bg-slate-300 cursor-not-allowed': selectedDays.length === 0
                                                }">
                                            Solicitar Días Seleccionados
                                        </button>
                                    </form>
                                </div>
                            </div>
                            </div>
                        @endif
                    @endif

                    {{-- Tarjeta: Historial de Solicitudes (solo si hay solicitudes) --}}
                    @if($solicitudes->count() > 0)
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-slate-400">
                            <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 border-b pb-2">
                                Historial de Solicitudes de Vacaciones
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-250 text-xs">
                                    <thead class="bg-gray-50 font-bold uppercase tracking-wider text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Fechas Solicitadas</th>
                                            <th class="px-4 py-3 text-center">Cant. Días</th>
                                            <th class="px-4 py-3 text-left">Observaciones</th>
                                            <th class="px-4 py-3 text-center">Estatus</th>
                                            <th class="px-4 py-3 text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-150 text-slate-700">
                                        @foreach($solicitudes as $solicitud)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($solicitud->fechas as $fecha)
                                                            <span class="bg-slate-100 text-slate-800 text-[10px] font-bold px-2 py-0.5 rounded border border-slate-200">
                                                                {{ $fecha->fecha->format('d/m/Y') }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center font-bold">
                                                    {{ $solicitud->fechas->count() }}
                                                </td>
                                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $solicitud->observaciones }}">
                                                    {{ $solicitud->observaciones ?: '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($solicitud->estatus === 'Pendiente')
                                                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase border border-amber-200">Pendiente</span>
                                                    @elseif($solicitud->estatus === 'Aprobado')
                                                        <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase border border-green-200">Aprobado</span>
                                                    @else
                                                        <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase border border-red-200">Rechazado</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($solicitud->estatus === 'Pendiente')
                                                        <form action="{{ route('vacaciones.destroy', $solicitud) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta solicitud?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-650 hover:text-red-900 font-bold uppercase text-[10px] tracking-wider">
                                                                Cancelar
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-slate-400 font-bold uppercase text-[10px]">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Tarjeta: Configuración de Períodos Vacacionales (solo Administradores) --}}
                    @if(Auth::user()->role === 'admin')
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-slate-500 mb-6 animate-fade-in">
                            <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 border-b pb-2">
                                Configuración de Períodos Vacacionales (Año {{ now()->year }})
                            </h4>
                            <form action="{{ route('vacaciones.configuracion.store') }}" method="POST" class="space-y-4 text-xs">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- Periodo 1 --}}
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                        <h5 class="text-xs font-black text-slate-700 uppercase mb-3">Primer Período (5 días)</h5>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1">Fecha Inicio</label>
                                                <input type="date" name="periodo1_inicio" value="{{ $configuracion && $configuracion->periodo1_inicio ? $configuracion->periodo1_inicio->format('Y-m-d') : '' }}" required
                                                       class="w-full text-xs rounded-lg border-slate-350 shadow-sm focus:border-guinda-ceaa focus:ring focus:ring-guinda-ceaa/20">
                                            </div>
                                            <div>
                                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1">Fecha Fin</label>
                                                <input type="date" name="periodo1_fin" value="{{ $configuracion && $configuracion->periodo1_fin ? $configuracion->periodo1_fin->format('Y-m-d') : '' }}" required
                                                       class="w-full text-xs rounded-lg border-slate-350 shadow-sm focus:border-guinda-ceaa focus:ring focus:ring-guinda-ceaa/20">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Periodo 2 --}}
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                        <h5 class="text-xs font-black text-slate-700 uppercase mb-3">Segundo Período (5 días)</h5>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1">Fecha Inicio</label>
                                                <input type="date" name="periodo2_inicio" value="{{ $configuracion && $configuracion->periodo2_inicio ? $configuracion->periodo2_inicio->format('Y-m-d') : '' }}" required
                                                       class="w-full text-xs rounded-lg border-slate-350 shadow-sm focus:border-guinda-ceaa focus:ring focus:ring-guinda-ceaa/20">
                                            </div>
                                            <div>
                                                <label class="block font-bold text-slate-500 uppercase tracking-wide mb-1">Fecha Fin</label>
                                                <input type="date" name="periodo2_fin" value="{{ $configuracion && $configuracion->periodo2_fin ? $configuracion->periodo2_fin->format('Y-m-d') : '' }}" required
                                                       class="w-full text-xs rounded-lg border-slate-350 shadow-sm focus:border-guinda-ceaa focus:ring focus:ring-guinda-ceaa/20">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end pt-2">
                                    <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white font-black uppercase text-[10px] tracking-widest px-4 py-2 rounded-lg transition shadow-sm cursor-pointer">
                                        Guardar Períodos
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Tarjeta: Panel de Aprobación de Vacaciones (para Administradores y Jefes de Área con solicitudes pendientes) --}}
                    @if(in_array(Auth::user()->role, ['admin', 'jefe_area']) && $solicitudesPendientes->count() > 0)
                        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-dorado-ocre animate-pulse-subtle">
                            <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 border-b pb-2">
                                Panel de Aprobación: Solicitudes de Vacaciones Pendientes
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-250 text-xs">
                                    <thead class="bg-gray-50 font-bold uppercase tracking-wider text-slate-500">
                                        <tr>
                                            <th class="px-4 py-3 text-left">Empleado</th>
                                            <th class="px-4 py-3 text-left">Área</th>
                                            <th class="px-4 py-3 text-left">Fechas Solicitadas</th>
                                            <th class="px-4 py-3 text-center">Días</th>
                                            <th class="px-4 py-3 text-left">Observaciones</th>
                                            <th class="px-4 py-3 text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-150 text-slate-700">
                                        @foreach($solicitudesPendientes as $pendiente)
                                            <tr>
                                                <td class="px-4 py-3 font-black">
                                                    {{ $pendiente->user?->name }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    {{ $pendiente->user?->area?->name ?? 'No asignada' }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($pendiente->fechas as $fecha)
                                                            <span class="bg-slate-100 text-slate-800 text-[10px] font-bold px-2 py-0.5 rounded border border-slate-200">
                                                                {{ $fecha->fecha->format('d/m/Y') }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center font-bold">
                                                    {{ $pendiente->fechas->count() }}
                                                </td>
                                                <td class="px-4 py-3 max-w-xs truncate" title="{{ $pendiente->observaciones }}">
                                                    {{ $pendiente->observaciones ?: '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <form action="{{ route('vacaciones.procesar', $pendiente) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="accion" value="aprobar">
                                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold uppercase text-[9px] px-2.5 py-1 rounded shadow-sm hover:shadow transition">
                                                                Aprobar
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('vacaciones.procesar', $pendiente) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="accion" value="rechazar">
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold uppercase text-[9px] px-2.5 py-1 rounded shadow-sm hover:shadow transition">
                                                                Rechazar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- COLUMNA DE SEGURIDAD (Derecha, más angosta) --}}
                <div class="space-y-6">
                    <div class="p-6 bg-white shadow-xl sm:rounded-lg border-t-4 border-slate-300">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script de Alpine.js para la lógica del calendario interactivo --}}
    <script>
        function vacationCalendar() {
            return {
                currentYear: new Date().getFullYear(),
                currentMonth: new Date().getMonth(),
                days: [],
                selectedDays: [],
                existingVacations: @json($solicitudes->flatMap(function($s) {
                    return $s->fechas->map(function($f) use ($s) {
                        return [
                            'fecha' => $f->fecha->format('Y-m-d'),
                            'estatus' => $s->estatus
                        ];
                    });
                })),
                periodo1_inicio: '{{ $configuracion && $configuracion->periodo1_inicio ? $configuracion->periodo1_inicio->format('Y-m-d') : '' }}',
                periodo1_fin: '{{ $configuracion && $configuracion->periodo1_fin ? $configuracion->periodo1_fin->format('Y-m-d') : '' }}',
                periodo2_inicio: '{{ $configuracion && $configuracion->periodo2_inicio ? $configuracion->periodo2_inicio->format('Y-m-d') : '' }}',
                periodo2_fin: '{{ $configuracion && $configuracion->periodo2_fin ? $configuracion->periodo2_fin->format('Y-m-d') : '' }}',
                diasPeriodo1Usados: {{ $diasPeriodo1Usados ?? 0 }},
                diasPeriodo2Usados: {{ $diasPeriodo2Usados ?? 0 }},
                
                get diasPeriodo1Seleccionados() {
                    if (!this.periodo1_inicio || !this.periodo1_fin) return 0;
                    return this.selectedDays.filter(d => d >= this.periodo1_inicio && d <= this.periodo1_fin).length;
                },
                get diasPeriodo2Seleccionados() {
                    if (!this.periodo2_inicio || !this.periodo2_fin) return 0;
                    return this.selectedDays.filter(d => d >= this.periodo2_inicio && d <= this.periodo2_fin).length;
                },

                isDayWithinPeriods(dateStr) {
                    if (!dateStr) return false;
                    const p1Match = this.periodo1_inicio && this.periodo1_fin && dateStr >= this.periodo1_inicio && dateStr <= this.periodo1_fin;
                    const p2Match = this.periodo2_inicio && this.periodo2_fin && dateStr >= this.periodo2_inicio && dateStr <= this.periodo2_fin;
                    return p1Match || p2Match;
                },

                formatPeriodRange(period) {
                    if (period === 'p1') {
                        if (!this.periodo1_inicio || !this.periodo1_fin) return 'No config.';
                        const partsIni = this.periodo1_inicio.split('-');
                        const partsFin = this.periodo1_fin.split('-');
                        return `${partsIni[2]}/${partsIni[1]} al ${partsFin[2]}/${partsFin[1]}`;
                    } else {
                        if (!this.periodo2_inicio || !this.periodo2_fin) return 'No config.';
                        const partsIni = this.periodo2_inicio.split('-');
                        const partsFin = this.periodo2_fin.split('-');
                        return `${partsIni[2]}/${partsIni[1]} al ${partsFin[2]}/${partsFin[1]}`;
                    }
                },
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                
                init() {
                    this.generateCalendar();
                },
                
                generateCalendar() {
                    const year = this.currentYear;
                    const month = this.currentMonth;
                    
                    const firstDayInstance = new Date(year, month, 1);
                    // Map to Monday as index 0: (day + 6) % 7
                    const firstDayOfWeek = (firstDayInstance.getDay() + 6) % 7;
                    
                    const totalDays = new Date(year, month + 1, 0).getDate();
                    const daysArray = [];
                    
                    // Prev month padding
                    const prevMonthTotalDays = new Date(year, month, 0).getDate();
                    for (let i = firstDayOfWeek - 1; i >= 0; i--) {
                        daysArray.push({
                            day: prevMonthTotalDays - i,
                            isCurrentMonth: false,
                            dateString: null
                        });
                    }
                    
                    // Current month days
                    for (let i = 1; i <= totalDays; i++) {
                        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                        const dateInstance = new Date(year, month, i);
                        const isWeekend = dateInstance.getDay() === 0 || dateInstance.getDay() === 6;
                        
                        daysArray.push({
                            day: i,
                            isCurrentMonth: true,
                            isWeekend: isWeekend,
                            dateString: dateStr
                        });
                    }
                    
                    // Next month padding
                    const totalCells = daysArray.length;
                    const nextMonthPadding = (7 - (totalCells % 7)) % 7;
                    for (let i = 1; i <= nextMonthPadding; i++) {
                        daysArray.push({
                            day: i,
                            isCurrentMonth: false,
                            dateString: null
                        });
                    }
                    
                    this.days = daysArray;
                },
                
                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                    this.generateCalendar();
                },
                
                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                    this.generateCalendar();
                },
                
                getDayStatus(dateStr) {
                    if (!dateStr) return null;
                    if (this.selectedDays.includes(dateStr)) {
                        return 'selected';
                    }
                    const match = this.existingVacations.find(v => v.fecha === dateStr);
                    return match ? match.estatus : null;
                },
                
                getEarliestAllowedDate() {
                    let date = new Date();
                    date.setHours(0, 0, 0, 0);
                    let businessDaysAdded = 0;
                    while (businessDaysAdded < 3) {
                        date.setDate(date.getDate() + 1);
                        const dayOfWeek = date.getDay();
                        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                            businessDaysAdded++;
                        }
                    }
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                toggleDay(dayObj) {
                    if (!dayObj.isCurrentMonth || dayObj.isWeekend || !dayObj.dateString) return;
                    if (dayObj.dateString < this.getEarliestAllowedDate()) return;
                    if (!this.isDayWithinPeriods(dayObj.dateString)) return;
                    const status = this.getDayStatus(dayObj.dateString);
                    if (status === 'Pendiente' || status === 'Aprobado') return;
                    
                    const index = this.selectedDays.indexOf(dayObj.dateString);
                    if (index > -1) {
                        this.selectedDays.splice(index, 1);
                    } else {
                        const esP1 = dayObj.dateString >= this.periodo1_inicio && dayObj.dateString <= this.periodo1_fin;
                        if (esP1) {
                            if ((this.diasPeriodo1Usados + this.diasPeriodo1Seleccionados) >= 5) {
                                alert('Has alcanzado el límite máximo de 5 días para el Primer Período.');
                                return;
                            }
                        } else {
                            if ((this.diasPeriodo2Usados + this.diasPeriodo2Seleccionados) >= 5) {
                                alert('Has alcanzado el límite máximo de 5 días para el Segundo Período.');
                                return;
                            }
                        }
                        this.selectedDays.push(dayObj.dateString);
                    }
                    this.selectedDays.sort();
                },
                
                removeSelectedDay(dateStr) {
                    const index = this.selectedDays.indexOf(dateStr);
                    if (index > -1) {
                        this.selectedDays.splice(index, 1);
                    }
                },
                
                formatDateReadable(dateStr) {
                    const parts = dateStr.split('-');
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
            };
        }
    </script>
</x-app-layout>