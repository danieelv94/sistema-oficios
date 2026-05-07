<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva Solicitud de Soporte') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Botón Regresar --}}
            <div class="mb-6">
                <a href="{{ route('tickets.index') }}"
                    class="text-sm font-bold text-gray-500 hover:text-[#932C43] flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Historial
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-lg border-t-8 border-[#932C43]">
                <div class="p-8">

                    <div class="mb-8 text-center">
                        <h3 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Formulario de
                            Asistencia</h3>
                        <p class="text-gray-500 text-sm">Por favor, detalle su problema técnico para brindarle atención
                            inmediata.</p>
                    </div>

                    <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6">
                        @csrf

                        {{-- Campo Asunto --}}
                        <div>
                            <x-label for="subject" class="text-[#932C43] font-bold uppercase text-xs" :value="__('Asunto o Problema')" />
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <x-input id="subject"
                                    class="block mt-1 w-full pl-10 border-gray-300 focus:border-[#932C43] focus:ring-[#932C43] rounded-md shadow-sm"
                                    type="text" name="subject" :value="old('subject')" required autofocus
                                    placeholder="Ej. Mi impresora no enciende" />
                            </div>
                            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                        </div>

                        {{-- Campo Descripción --}}
                        <div>
                            <x-label for="description" class="text-[#932C43] font-bold uppercase text-xs"
                                :value="__('Descripción Detallada')" />
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="5"
                                    class="block w-full border-gray-300 focus:border-[#932C43] focus:ring-[#932C43] rounded-md shadow-sm placeholder-gray-300"
                                    required
                                    placeholder="Describa brevemente qué sucede, si hay algún mensaje de error o desde cuándo presenta la falla...">{{ old('description') }}</textarea>
                            </div>
                            <p class="mt-2 text-xs text-gray-400 italic">Incluya detalles como número de inventario del
                                equipo si es necesario.</p>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <hr class="border-gray-100">

                        {{-- Información del Solicitante (Informativo) --}}
                        <div class="bg-gray-50 p-4 rounded-lg flex items-start space-x-3 border border-gray-100">
                            <div class="p-2 bg-[#932C43] rounded text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Solicitante Detectado:</p>
                                <p class="text-sm font-bold text-gray-700">{{ Auth::user()->name }}</p>
                                {{-- CAMBIO AQUÍ: Usamos lógica tradicional de PHP para el área --}}
                                <p class="text-xs text-gray-500">
                                    {{ (Auth::user()->area) ? Auth::user()->area->name : 'Área no asignada' }}
                                </p>
                            </div>
                        </div>

                        {{-- Botón de Envío --}}
                        <div class="flex items-center justify-end mt-8">
                            <button type="submit"
                                class="w-full sm:w-auto px-8 py-3 bg-[#932C43] text-white rounded-md hover:bg-[#722134] font-black uppercase tracking-widest shadow-xl transition-all transform hover:-translate-y-1 active:scale-95">
                                Enviar Solicitud de Soporte
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Nota de Seguridad --}}
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400">
                    Esta solicitud será enviada al departamento de Informática de la CEAA.
                    Recibirá una notificación cuando un técnico sea asignado.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>