<x-guest-layout>
    {{-- Contenedor Principal en Pantalla Completa --}}
    <div class="min-h-screen flex bg-gray-50">

        {{-- LADO IZQUIERDO: Panel de Identidad con Imagen de Fondo --}}
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-12 relative overflow-hidden bg-guinda-ceaa">

            {{-- Imagen de Fondo desde public/images --}}
            <img src="{{ asset('images/ceaa.webp') }}"
                class="absolute inset-0 w-full h-full object-cover opacity-25 mix-blend-multiply"
                alt="Fondo Institucional CEAA">

            {{-- Capa de gradado para que el texto resalte (Overlay) --}}
            <div class="absolute inset-0 bg-gradient-to-b from-guinda-ceaa via-transparent to-guinda-ceaa opacity-50">
            </div>

            <div class="text-center z-10">
                <h1 class="text-6xl font-black text-white uppercase tracking-tighter mb-4 drop-shadow-2xl">
                    Sistema de Oficios
                </h1>
                <div class="w-20 h-1.5 bg-white mx-auto mb-8 rounded-full shadow-lg"></div>
                <p class="text-xl text-white max-w-md mx-auto leading-relaxed italic font-medium drop-shadow-md px-4">
                    Plataforma Oficial de Gestión Documental de la Comisión Estatal del Agua y Alcantarillado de
                    Hidalgo.
                </p>
            </div>
        </div>

        {{-- LADO DERECHO: Formulario de Login --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-16 bg-white">
            <div class="w-full max-w-md">

                <div class="text-center mb-12">
                    <a href="/">
                        <x-application-logo class="h-32 w-auto mx-auto" />
                    </a>
                    <p class="mt-6 text-xs text-gray-400 uppercase font-black tracking-widest border-t pt-4">
                        Inicia sesión en tu cuenta
                    </p>
                </div>

                {{-- Status de Sesión y Errores --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />
                <x-auth-validation-errors class="mb-4" :errors="$errors" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <x-label for="email" :value="__('Correo Electrónico')"
                            class="text-gray-600 font-bold text-xs uppercase tracking-wider" />
                        <x-input id="email"
                            class="block mt-1.5 w-full rounded-lg border-gray-300 focus:ring-guinda-ceaa focus:border-guinda-ceaa p-3 shadow-sm"
                            type="email" name="email" :value="old('email')" required autofocus />
                    </div>

                    {{-- Password --}}
                    <div class="mt-4">
                        <div class="flex justify-between items-center">
                            <x-label for="password" :value="__('Contraseña')"
                                class="text-gray-600 font-bold text-xs uppercase tracking-wider" />
                            @if (Route::has('password.request'))
                                <a class="text-xs text-guinda-ceaa hover:underline font-medium"
                                    href="{{ route('password.request') }}">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>
                        <x-input id="password"
                            class="block mt-1.5 w-full rounded-lg border-gray-300 focus:ring-guinda-ceaa focus:border-guinda-ceaa p-3 shadow-sm"
                            type="password" name="password" required autocomplete="current-password" />
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between mt-6">
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-gray-300 text-guinda-ceaa shadow-sm focus:ring-red-200"
                                name="remember">
                            <label for="remember_me"
                                class="ml-2 text-sm text-gray-600 font-medium">{{ __('Recordarme') }}</label>
                        </div>
                    </div>

                    {{-- Botón de Acción --}}
                    <div class="mt-8">
                        <button type="submit"
                            class="w-full flex justify-center bg-guinda-ceaa text-white p-4 rounded-lg font-black uppercase tracking-widest hover:bg-opacity-90 transition active:scale-95 shadow-lg">
                            {{ __('Acceder al Sistema') }}
                        </button>
                    </div>
                </form>

                {{-- Pie de página --}}
                <div class="mt-16 text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                    <p>&copy; 2026 CEAA - HIDALGO</p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>