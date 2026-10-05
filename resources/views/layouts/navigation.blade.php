<nav x-data="{ openDropdownOficios: {{ request()->routeIs('oficios.*') ? 'true' : 'false' }}, openDropdownAvisos: {{ request()->routeIs('avisos.*') ? 'true' : 'false' }}, openDropdownComisiones: {{ request()->routeIs('comisiones.*') ? 'true' : 'false' }}, openUserMenu: false, openMobileSidebar: false }" class="no-print">
    <!-- TOP NAVBAR -->
    <div
        class="fixed top-0 right-0 left-0 h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6 z-40 shadow-xs transition-all">

        <div class="flex items-center gap-4">
            <!-- Botón Escritorio Toggle Sidebar -->
            <button @click="openSidebar = !openSidebar"
                class="hidden md:inline-flex p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none transition"
                title="Alternar menú lateral">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Botón Móvil Toggle Sidebar -->
            <button @click="openMobileSidebar = !openMobileSidebar"
                class="inline-flex md:hidden p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none transition">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- LOGO INSTITUCIONAL Y TÍTULO -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo CEAA" class="h-9 w-auto object-contain">
                <div class="hidden sm:flex flex-col">
                    <span class="font-black text-base text-slate-800 tracking-wider uppercase leading-none font-sans">
                        Sistema <span class="text-guinda-ceaa">Oficios</span>
                    </span>
                    <span class="text-[9px] font-bold text-slate-400 tracking-widest uppercase mt-0.5">CEAA Hidalgo</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 sm:gap-4">

            <!-- Icono Campanita con Contador Directo de la Base de Datos -->
            <div class="relative">
                <a href="{{ route('avisos.pendientes') }}"
                    class="p-2 text-slate-500 hover:text-guinda-ceaa rounded-xl hover:bg-slate-100 block transition"
                    title="Avisos Pendientes">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            class="absolute top-1 right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-black leading-none text-white bg-rose-600 rounded-full border-2 border-white transform translate-x-1/4 -translate-y-1/4 animate-pulse">
                            {{ $contadorAvisos }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Separador vertical sutil -->
            <div class="h-6 w-px bg-slate-200"></div>

            <!-- Dropdown Perfil de Usuario -->
            <div class="relative">
                <button @click="openUserMenu = !openUserMenu" @click.away="openUserMenu = false"
                    class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 text-slate-700 hover:text-slate-900 focus:outline-none transition">
                    <div class="w-8 h-8 rounded-lg bg-guinda-ceaa/10 text-guinda-ceaa flex items-center justify-center font-black text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <div class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-400 font-medium leading-none mt-0.5 truncate max-w-[140px]">
                            {{ Auth::user()->cargo ?? (Auth::user()->role === 'admin' ? 'Administrador' : 'Usuario') }}
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="openUserMenu" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl py-1.5 border border-slate-150 z-50 divide-y divide-slate-100">
                    <div class="px-4 py-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Conectado como</p>
                        <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-guinda-ceaa transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ __('Mi Perfil') }}
                        </a>
                    </div>
                    <div class="py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Cerrar Sesión') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- SIDEBAR ESCRITORIO -->
    <div :class="openSidebar ? 'w-64' : 'w-0 md:w-16'"
        class="hidden md:flex fixed top-16 left-0 bottom-0 bg-slate-900 text-slate-300 flex-col transition-all duration-300 z-30 overflow-x-hidden border-r border-slate-800/80 shadow-lg">
        <div class="flex-1 py-4 flex flex-col justify-between overflow-y-auto">
            <div class="space-y-1.5 px-3">

                <!-- Link: Inicio -->
                <a href="{{ route('principal') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 group {{ request()->routeIs('principal') ? 'bg-gradient-to-r from-guinda-ceaa to-guinda-ceaa-hover text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}"
                    title="Inicio">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('principal') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="openSidebar" class="text-xs font-semibold tracking-wide uppercase">Inicio</span>
                </a>

                <!-- Dropdown: Oficios (Internos y Externos) -->
                @if(Auth::user()->area_id || Auth::user()->role == 'admin')
                    <div class="space-y-1">
                        <button @click="openDropdownOficios = !openDropdownOficios"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-slate-800/70 hover:text-white transition text-left group {{ request()->routeIs('oficios.*') ? 'bg-slate-800/80 text-white' : 'text-slate-300' }}"
                            title="Oficios">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('oficios.*') ? 'text-guinda-ceaa' : 'text-slate-400 group-hover:text-white' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="openSidebar" class="text-xs font-semibold tracking-wide uppercase">Oficios</span>
                            </div>
                            <svg x-show="openSidebar" :class="openDropdownOficios ? 'transform rotate-180' : ''"
                                class="w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openDropdownOficios && openSidebar" x-transition
                            class="ml-4 pl-4 border-l-2 border-slate-700/60 space-y-1 py-1">
                            <a href="{{ route('oficios.internos.index') }}"
                                class="block py-1.5 px-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('oficios.internos.*') ? 'text-white bg-slate-800 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                Oficios Internos
                            </a>

                            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'correspondencia' || (Auth::user()->role == 'jefe_area' && Auth::user()->area_id == 2))
                                <a href="{{ route('oficios.index') }}"
                                    class="block py-1.5 px-2 rounded-lg text-xs font-medium transition {{ (request()->routeIs('oficios.index') || request()->routeIs('oficios.create') || (request()->routeIs('oficios.show') && request('mode') !== 'gestion' && request('mode') !== 'operativo')) ? 'text-white bg-slate-800 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                    Oficios Externos
                                </a>
                            @endif

                            @if((Auth::user()->area_id && !in_array(Auth::user()->role, ['recepcionista', 'correspondencia'])) || Auth::user()->role == 'admin')
                                <a href="{{ route('oficios.gestion') }}"
                                    class="block py-1.5 px-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('oficios.gestion') || request()->routeIs('oficios.atender') || (request()->routeIs('oficios.show') && in_array(request('mode'), ['gestion', 'operativo'])) ? 'text-white bg-slate-800 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                    Gestión de Turnos
                                </a>
                            @endif

                            @if(Auth::user()->role == 'admin' || Auth::user()->role == 'correspondencia' || Auth::user()->role == 'dg' || Auth::user()->id == 326 || (Auth::user()->role == 'jefe_area' && Auth::user()->area_id == 2))
                                <a href="{{ route('oficios.seguimiento') }}"
                                    class="block py-1.5 px-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('oficios.seguimiento') ? 'text-white bg-slate-800 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                    Seguimiento General
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @php
                    $isRHUser = Auth::user()->isRecursosHumanos();
                    $hasExtraComisionPerms = Auth::user()->canViewAllComisiones() || (Auth::user()->role === 'admin' || $isRHUser);
                @endphp

                @if($hasExtraComisionPerms)
                    <!-- Dropdown: Comisión -->
                    <div class="space-y-1">
                        <button @click="openDropdownComisiones = !openDropdownComisiones"
                            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-slate-800/70 hover:text-white transition text-left group {{ request()->routeIs('comisiones.*') ? 'bg-slate-800/80 text-white' : 'text-slate-300' }}"
                            title="Comisiones">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('comisiones.*') ? 'text-guinda-ceaa' : 'text-slate-400 group-hover:text-white' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span x-show="openSidebar" class="text-xs font-semibold tracking-wide uppercase">Comisión</span>
                            </div>
                            <svg x-show="openSidebar" :class="openDropdownComisiones ? 'transform rotate-180' : ''"
                                class="w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openDropdownComisiones && openSidebar" x-transition
                            class="ml-4 pl-4 border-l-2 border-slate-700/60 space-y-1 py-1">
                            <a href="{{ route('comisiones.index') }}"
                                class="block py-1.5 px-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('comisiones.index') || request()->routeIs('comisiones.show') || request()->routeIs('comisiones.create') || request()->routeIs('comisiones.edit') ? 'text-white bg-slate-800 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                Mis Oficios Comisión
                            </a>
                            @if(Auth::user()->canViewAllComisiones())
                                <a href="{{ route('comisiones.general') }}"
                                    class="block py-1.5 px-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('comisiones.general') ? 'text-white bg-slate-800 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                    Registro General
                                </a>
                            @endif
                            @if(Auth::user()->role === 'admin' || $isRHUser)
                                <a href="{{ route('comisiones.recursos_humanos') }}"
                                    class="block py-1.5 px-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('comisiones.recursos_humanos') ? 'text-white bg-slate-800 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                    Acuses Comisión (RH)
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Link Directo: Oficios Comisión (Sin permisos extra) -->
                    <a href="{{ route('comisiones.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 group {{ request()->routeIs('comisiones.*') ? 'bg-gradient-to-r from-guinda-ceaa to-guinda-ceaa-hover text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}"
                        title="Oficios Comisión">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('comisiones.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <span x-show="openSidebar" class="text-xs font-semibold tracking-wide uppercase">Oficios Comisión</span>
                    </a>
                @endif

                <!-- Link: Transparencia PNT -->
                @if(Auth::user()->canEditPntSection(1) || Auth::user()->canEditPntSection(2) || Auth::user()->canEditPntSection(3))
                    <a href="{{ route('pnt.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 group {{ request()->routeIs('pnt.*') ? 'bg-gradient-to-r from-guinda-ceaa to-guinda-ceaa-hover text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}"
                        title="Transparencia PNT">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('pnt.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span x-show="openSidebar" class="text-xs font-semibold tracking-wide uppercase">Transparencia PNT</span>
                    </a>
                @endif

                <!-- Link: Soporte Técnico -->
                <a href="{{ route('tickets.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 group {{ request()->routeIs('tickets.*') ? 'bg-gradient-to-r from-guinda-ceaa to-guinda-ceaa-hover text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}"
                    title="Soporte Técnico">
                    <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('tickets.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="openSidebar" class="text-xs font-semibold tracking-wide uppercase">Soporte Técnico</span>
                </a>

                <!-- Dropdown: Avisos -->
                <div class="space-y-1">
                    <button @click="openDropdownAvisos = !openDropdownAvisos"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-slate-800/70 hover:text-white transition text-left group {{ request()->routeIs('avisos.*') ? 'bg-slate-800/80 text-white' : 'text-slate-300' }}"
                        title="Avisos y Circulares">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('avisos.*') ? 'text-guinda-ceaa' : 'text-slate-400 group-hover:text-white' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <span x-show="openSidebar" class="text-xs font-semibold tracking-wide uppercase">Avisos</span>
                        </div>
                        <svg x-show="openSidebar" :class="openDropdownAvisos ? 'transform rotate-180' : ''"
                            class="w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openDropdownAvisos && openSidebar" x-transition
                        class="ml-4 pl-4 border-l-2 border-slate-700/60 space-y-1 py-1">
                        @if(in_array(Auth::user()->role, ['admin', 'secretaria_area', 'jefe_area']))
                            <a href="{{ route('avisos.index') }}"
                                class="block py-1.5 px-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('avisos.index') ? 'text-white bg-slate-800 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                Historial Circulares
                            </a>
                        @endif
                        <a href="{{ route('avisos.pendientes') }}"
                            class="block py-1.5 px-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('avisos.pendientes') ? 'text-white bg-slate-800 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                            Muro de Avisos
                        </a>
                    </div>
                </div>

                <!-- Módulo de Usuarios (Solo Admin) -->
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('usuarios.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition duration-150 group {{ request()->routeIs('usuarios.*') ? 'bg-gradient-to-r from-guinda-ceaa to-guinda-ceaa-hover text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}"
                        title="Administración de Usuarios">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('usuarios.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="openSidebar" class="text-xs font-semibold tracking-wide uppercase">Control Usuarios</span>
                    </a>
                @endif

            </div>

            <div class="px-4 py-3 text-[10px] text-slate-500 border-t border-slate-800/80 whitespace-nowrap text-center">
                <p x-show="openSidebar" class="font-bold tracking-widest uppercase text-slate-500">CEAA &bull; {{ now()->year }}</p>
                <p x-show="!openSidebar" class="font-bold text-slate-600">V1</p>
            </div>
        </div>
    </div>

    <!-- SIDEBAR RESPONSIVO MÓVIL OVERLAY -->
    <div x-show="openMobileSidebar" class="md:hidden fixed inset-0 flex z-50 no-print" x-transition
        style="display: none;">
        <!-- Fondo oscuro translúcido -->
        <div @click="openMobileSidebar = false" class="fixed inset-0 bg-slate-900/80 backdrop-blur-xs transition-opacity">
        </div>

        <!-- Cuerpo del Sidebar Móvil -->
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-slate-900 pt-5 pb-4 shadow-2xl border-r border-slate-800">
            <div class="absolute top-0 right-0 -mr-12 pt-3">
                <button @click="openMobileSidebar = false"
                    class="ml-1 flex items-center justify-center h-10 w-10 rounded-full bg-slate-800 text-white focus:outline-none focus:ring-2 focus:ring-white">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-5 pb-4 border-b border-slate-800 flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo CEAA" class="h-8 w-auto">
                <span class="font-black text-sm text-white uppercase tracking-wider">Sistema <span class="text-guinda-ceaa">Oficios</span></span>
            </div>

            <div class="mt-4 flex-1 h-0 overflow-y-auto px-4 space-y-1.5">
                <!-- Enlace: Inicio -->
                <a href="{{ route('principal') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wide transition {{ request()->routeIs('principal') ? 'bg-guinda-ceaa text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- Sección Agrupada: Oficios (Móvil) -->
                @if(Auth::user()->area_id || Auth::user()->role == 'admin')
                    <div class="border-t border-slate-800/80 pt-2 mt-2">
                        <div class="flex items-center gap-2 px-3 py-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Oficios</span>
                        </div>
                        <a href="{{ route('oficios.internos.index') }}"
                            class="block pl-8 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('oficios.internos.*') ? 'text-white font-bold bg-guinda-ceaa' : 'text-slate-400 hover:text-white' }}">
                            Oficios Internos
                        </a>
                        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'correspondencia' || (Auth::user()->role == 'jefe_area' && Auth::user()->area_id == 2))
                            <a href="{{ route('oficios.index') }}"
                                class="block pl-8 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('oficios.index') || request()->routeIs('oficios.create') ? 'text-white font-bold bg-guinda-ceaa' : 'text-slate-400 hover:text-white' }}">
                                Oficios Externos
                            </a>
                        @endif
                        @if((Auth::user()->area_id && !in_array(Auth::user()->role, ['recepcionista', 'correspondencia'])) || Auth::user()->role == 'admin')
                            <a href="{{ route('oficios.gestion') }}"
                                class="block pl-8 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('oficios.gestion') || request()->routeIs('oficios.atender') ? 'text-white font-bold bg-guinda-ceaa' : 'text-slate-400 hover:text-white' }}">
                                Gestión de Turnos
                            </a>
                        @endif
                        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'correspondencia' || Auth::user()->role == 'dg' || Auth::user()->id == 326 || (Auth::user()->role == 'jefe_area' && Auth::user()->area_id == 2))
                            <a href="{{ route('oficios.seguimiento') }}"
                                class="block pl-8 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('oficios.seguimiento') ? 'text-white font-bold bg-guinda-ceaa' : 'text-slate-400 hover:text-white' }}">
                                Seguimiento General
                            </a>
                        @endif
                    </div>
                @endif

                @if($hasExtraComisionPerms)
                    <!-- Sección Agrupada: Comisión (Móvil) -->
                    <div class="border-t border-slate-800/80 pt-2 mt-2">
                        <div class="flex items-center gap-2 px-3 py-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            <span>Comisiones</span>
                        </div>
                        <a href="{{ route('comisiones.index') }}"
                            class="block pl-8 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('comisiones.index') || request()->routeIs('comisiones.show') || request()->routeIs('comisiones.create') || request()->routeIs('comisiones.edit') ? 'text-white font-bold bg-guinda-ceaa' : 'text-slate-400 hover:text-white' }}">
                            Mis Oficios Comisión
                        </a>
                        @if(Auth::user()->canViewAllComisiones())
                            <a href="{{ route('comisiones.general') }}"
                                class="block pl-8 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('comisiones.general') ? 'text-white font-bold bg-guinda-ceaa' : 'text-slate-400 hover:text-white' }}">
                                Registro General
                            </a>
                        @endif
                        @if(Auth::user()->role === 'admin' || $isRHUser)
                            <a href="{{ route('comisiones.recursos_humanos') }}"
                                class="block pl-8 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('comisiones.recursos_humanos') ? 'text-white font-bold bg-guinda-ceaa' : 'text-slate-400 hover:text-white' }}">
                                Acuses Comisión (RH)
                            </a>
                        @endif
                    </div>
                @else
                    <!-- Enlace Directo: Oficios Comisión (Móvil) -->
                    <a href="{{ route('comisiones.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wide transition {{ request()->routeIs('comisiones.*') ? 'bg-guinda-ceaa text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('comisiones.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <span>Oficios Comisión</span>
                    </a>
                @endif

                <!-- Enlace: Transparencia PNT -->
                @if(Auth::user()->canEditPntSection(1) || Auth::user()->canEditPntSection(2) || Auth::user()->canEditPntSection(3))
                    <a href="{{ route('pnt.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wide transition {{ request()->routeIs('pnt.*') ? 'bg-guinda-ceaa text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('pnt.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Transparencia PNT</span>
                    </a>
                @endif

                <!-- Enlace: Soporte Técnico -->
                <a href="{{ route('tickets.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wide transition {{ request()->routeIs('tickets.*') ? 'bg-guinda-ceaa text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Soporte Técnico</span>
                </a>

                <!-- Sección Agrupada: Avisos -->
                <div class="border-t border-slate-800/80 pt-2 mt-2">
                    <div class="flex items-center gap-2 px-3 py-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        <span>Avisos</span>
                    </div>
                    @if(in_array(Auth::user()->role, ['admin', 'secretaria_area', 'jefe_area']))
                        <a href="{{ route('avisos.index') }}"
                            class="block pl-8 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('avisos.index') ? 'text-white font-bold bg-guinda-ceaa' : 'text-slate-400 hover:text-white' }}">
                            Historial Circulares
                        </a>
                    @endif
                    <a href="{{ route('avisos.pendientes') }}"
                        class="block pl-8 py-2 rounded-xl text-xs font-medium transition {{ request()->routeIs('avisos.pendientes') ? 'text-white font-bold bg-guinda-ceaa' : 'text-slate-400 hover:text-white' }}">
                        Muro de Avisos
                    </a>
                </div>

                <!-- Enlace: Control Usuarios -->
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('usuarios.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold uppercase tracking-wide transition {{ request()->routeIs('usuarios.*') ? 'bg-guinda-ceaa text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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