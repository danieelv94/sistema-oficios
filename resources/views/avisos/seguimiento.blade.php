<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{ __('Seguimiento de Circular') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Control de lectura para: <strong class="text-slate-700">"{{ $aviso->titulo }}"</strong></p>
            </div>
            <div>
                <a href="{{ route('avisos.index') }}"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg font-bold shadow-xs transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al Historial
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stat KPI Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Total Destinatarios</p>
                        <p class="text-2xl font-black text-slate-800 mt-1">{{ number_format($total) }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Personal notificado</p>
                    </div>
                    <div class="p-3 bg-purple-50 text-purple-700 rounded-xl border border-purple-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-emerald-700 tracking-wider">Confirmados</p>
                        <p class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($leidos) }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Han marcado enterado</p>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-rose-700 tracking-wider">Pendientes</p>
                        <p class="text-2xl font-black text-rose-700 mt-1">{{ number_format($pendientes) }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Sin confirmar</p>
                    </div>
                    <div class="p-3 bg-rose-50 text-rose-700 rounded-xl border border-rose-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-black uppercase text-guinda-ceaa tracking-wider">% Cumplimiento</p>
                        <p class="text-2xl font-black text-guinda-ceaa mt-1">{{ $porcentaje }}%</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Tasa de respuesta</p>
                    </div>
                    <div class="p-3 bg-guinda-ceaa/10 text-guinda-ceaa rounded-xl border border-guinda-ceaa/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Tabla de Control de Lectura --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Lista de Control de Lectura</h3>
                        <span class="px-2.5 py-0.5 bg-guinda-ceaa/10 text-guinda-ceaa text-[11px] font-black rounded-full">
                            {{ count($usuarios) }} personas
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">Empleado</th>
                                <th class="py-3 px-4 text-left">Área</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-center">Fecha / Hora Lectura</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($usuarios as $user)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    <td class="py-3.5 px-4 font-bold text-slate-800 whitespace-nowrap">
                                        {{ $user->name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        {{ $user->area->nombre ?? ($user->area->name ?? 'N/A') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($user->pivot->leido_at)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Enterado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-slate-500 whitespace-nowrap">
                                        {{ $user->pivot->leido_at ? \Carbon\Carbon::parse($user->pivot->leido_at)->format('d/m/Y H:i:s') : '---' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-slate-400 italic">
                                        No hay usuarios registrados para esta circular.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>