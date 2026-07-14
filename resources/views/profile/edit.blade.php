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
                                            Has cumplido el requisito mínimo de 1 año de servicio. Las opciones para registrar y solicitar tus periodos vacacionales e incidencias estarán disponibles próximamente en este módulo.
                                        @else
                                            El reglamento institucional establece un mínimo de 1 año de servicio continuo para tener derecho a solicitar vacaciones. Te restan aproximadamente <strong class="font-bold text-gray-800">{{ $mesesRestantes }} {{ $mesesRestantes == 1 ? 'mes' : 'meses' }}</strong> para cumplir el periodo.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

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
</x-app-layout>