<nav x-data="{ openDropdownAvisos: false, openUserMenu: false, openMobileSidebar: false }" class="no-print">
    <!-- TOP NAVBAR -->
    <div
        class="fixed top-0 right-0 left-0 h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 z-40 shadow-sm">

        <div class="flex items-center gap-4">
            <!-- Botón Escritorio Toggle Sidebar -->
            <button @click="openSidebar = !openSidebar"
                class="hidden md:inline-flex p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Botón Móvil Toggle Sidebar -->
            <button @click="openMobileSidebar = !openMobileSidebar"
                class="inline-flex md:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- LOGO INSTITUCIONAL Y TÍTULO -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo CEAA" class="h-9 w-auto object-contain">
                <span class="font-bold text-lg text-gray-800 tracking-wider uppercase font-sans hidden sm:inline">
                    Sistema <span class="text-[#932C43]">Oficios</span>
                </span>
            </div>
        </div>

        <div class="flex items-center gap-4">

            <!-- Icono Campanita con Contador Directo de la Base de Datos -->
            <div class="relative">
                <a href="{{ route('avisos.pendientes') }}"
                    class="p-2 text-gray-400 hover:text-gray-500 rounded-full hover:bg-gray-100 block transition"
                    title="Avisos Pendientes">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>

                    @php
                        $contadorAvisos = \DB::table('aviso_user')
                            ->where('user_id', Auth::id())
                            ->whereNull('leido_at')
                            ->count();
                    @endphp

                    @if($contadorAvisos > 0)
                        <span
                            class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                            {{ $contadorAvisos }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Dropdown Perfil de Usuario -->
            <div class="relative">
                <button @click="openUserMenu = !openUserMenu" @click.away="openUserMenu = false"
                    class="flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition">
                    <div>{{ Auth::user()->name }}</div>
                    <svg class="fill-current h-4 w-4" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5H7z" />
                    </svg>
                </button>

                <div x-show="openUserMenu" x-transition
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border z-50">
                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                        {{ __('Mi Perfil') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            {{ __('Cerrar Sesión') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- SIDEBAR ESCRITORIO -->
    <div :class="openSidebar ? 'w-64' : 'w-0 md:w-16'"
        class="hidden md:flex fixed top-16 left-0 bottom-0 bg-gray-900 text-gray-300 flex-col transition-all duration-300 z-30 overflow-x-hidden border-r border-gray-800">
        <div class="flex-1 py-4 flex flex-col justify-between">
            <div class="space-y-1 px-3">

                <!-- Link: Inicio -->
                <a href="{{ route('principal') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md hover:bg-gray-800 hover:text-white transition group {{ request()->routeIs('principal') ? 'bg-[#932C43] text-white' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0 text-gray-400 group-hover:text-white {{ request()->routeIs('principal') ? 'text-white' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="openSidebar" class="text-sm font-medium tracking-wide">Inicio</span>
                </a>

                <!-- Link: Oficios Comisión -->
                <a href="{{ route('comisiones.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md hover:bg-gray-800 hover:text-white transition group {{ request()->routeIs('comisiones.*') ? 'bg-[#932C43] text-white' : '' }}">
                    <svg class="w-5 h-5 flex-shrink-0 text-gray-400 group-hover:text-white {{ request()->routeIs('comisiones.*') ? 'text-white' : '' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    <span x-show="openSidebar" class="text-sm font-medium tracking-wide">Oficios Comisión</span>
                </a>

                <!-- Dropdown: Avisos -->
                <div class="space-y-1">
                    <button @click="openDropdownAvisos = !openDropdownAvisos"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-md hover:bg-gray-800 hover:text-white transition text-left group">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-gray-400 group-hover:text-white {{ request()->routeIs('avisos.*') ? 'text-white' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <span x-show="openSidebar" class="text-sm font-medium tracking-wide">Avisos</span>
                        </div>
                        <svg x-show="openSidebar" :class="openDropdownAvisos ? 'transform rotate-180' : ''"
                            class="w-4 h-4 transition-transform text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openDropdownAvisos && openSidebar" x-transition
                        class="pl-11 space-y-1 bg-gray-950 rounded-md py-1">
                        @if(in_array(Auth::user()->role, ['admin', 'secretaria_area']))
                            <a href="{{ route('avisos.index') }}"
                                class="block py-2 text-xs font-medium text-gray-400 hover:text-white transition {{ request()->routeIs('avisos.index') ? 'text-white font-bold' : '' }}">
                                Historial Avisos
                            </a>
                        @endif
                        <a href="{{ route('avisos.pendientes') }}"
                            class="block py-2 text-xs font-medium text-gray-400 hover:text-white transition {{ request()->routeIs('avisos.pendientes') ? 'text-white font-bold' : '' }}">
                            Mis Avisos
                        </a>
                    </div>
                </div>

                <!-- Módulo de Usuarios (Solo Admin) -->
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('usuarios.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-md hover:bg-gray-800 hover:text-white transition group {{ request()->routeIs('usuarios.*') ? 'bg-[#932C43] text-white' : '' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-gray-400 group-hover:text-white {{ request()->routeIs('usuarios.*') ? 'text-white' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="openSidebar" class="text-sm font-medium tracking-wide">Control Usuarios</span>
                    </a>
                @endif

            </div>

            <div class="py-2 text-[10px] text-gray-500 border-t border-gray-800 whitespace-nowrap text-center">
                <p x-show="openSidebar">CEAA — © {{ now()->year }}</p>
                <p x-show="!openSidebar">V1</p>
            </div>
        </div>
    </div>

    <!-- SIDEBAR RESPONSIVO MÓVIL OVERLAY -->
    <div x-show="openMobileSidebar" class="md:hidden fixed inset-0 flex z-50 no-print" x-transition
        style="display: none;">
        <!-- Fondo oscuro translúcido -->
        <div @click="openMobileSidebar = false" class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity">
        </div>

        <!-- Cuerpo del Sidebar Móvil -->
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-900 pt-5 pb-4Mac">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="openMobileSidebar = false"
                    class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-5 flex-1 h-0 overflow-y-auto px-4 space-y-2">
                <!-- Enlace: Inicio (Mismo Icono SVG) -->
                <a href="{{ route('principal') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Enlace: Oficios Comisión (Mismo Icono SVG) -->
                <a href="{{ route('comisiones.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    <span>Oficios Comisión</span>
                </a>

                <!-- Sección Agrupada: Avisos (Mismos Iconos SVG) -->
                <div class="border-t border-gray-800 pt-2 mt-2">
                    <div
                        class="flex items-center gap-3 px-3 py-1 text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        <span>Módulo Avisos</span>
                    </div>
                    @if(in_array(Auth::user()->role, ['admin', 'secretaria_area']))
                        <a href="{{ route('avisos.index') }}"
                            class="block pl-10 py-2 rounded-md text-sm font-medium text-gray-400 hover:text-white transition">
                            Historial Avisos
                        </a>
                    @endif
                    <a href="{{ route('avisos.pendientes') }}"
                        class="block pl-10 py-2 rounded-md text-sm font-medium text-gray-400 hover:text-white transition">
                        Mis Avisos
                    </a>
                </div>

                <!-- Enlace: Control Usuarios (Mismo Icono SVG) -->
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('usuarios.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Control Usuarios</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</nav>