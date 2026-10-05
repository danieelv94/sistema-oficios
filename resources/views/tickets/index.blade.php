<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-6 h-6 text-guinda-ceaa inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ __('Mesa de Ayuda y Soporte Técnico') }}
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Atención a incidentes, soporte informático y seguimiento de tickets</p>
            </div>
            <div>
                <a href="{{ route('tickets.create') }}"
                    class="px-4 py-2 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded-lg font-bold shadow-xs hover:shadow-md transition text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Levantar Ticket de Soporte
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- BANNER DE NOTIFICACIONES PUSH --}}
            <div x-data="pushSubscription()" x-init="checkSubscription()" x-show="!isSubscribed && showBanner"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="bg-gradient-to-r from-guinda-ceaa to-guinda-ceaa-hover rounded-xl shadow-md p-6 text-white relative overflow-hidden"
                style="display: none;">

                {{-- Icono de fondo decorativo --}}
                <div class="absolute right-[-20px] top-[-20px] opacity-10">
                    <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 22a2 2 0 002-2H10a2 2 0 002 2zm6-6V11a6 6 0 00-9.33-5.05A3.003 3.003 0 005 11v5l-2 2v1h18v-1l-2-2z" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-white/20 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-base font-black uppercase tracking-tight">¿Deseas recibir alertas de soporte al instante?</h4>
                            <p class="text-xs text-white/90 font-medium mt-0.5">
                                Activa las notificaciones en tu navegador para saber de inmediato cuando tu ticket sea resuelto o existan comunicados.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 shrink-0">
                        <button @click="showBanner = false"
                            class="text-xs font-bold uppercase opacity-75 hover:opacity-100 transition tracking-wider underline">Omitir</button>
                        <button @click="toggleSubscription"
                            class="bg-white text-guinda-ceaa px-5 py-2 rounded-lg shadow-sm font-black text-xs uppercase tracking-wider hover:bg-slate-100 transition-all transform active:scale-95">
                            Activar Alertas
                        </button>
                    </div>
                </div>
            </div>

            {{-- SOLICITUDES EN PROCESO --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Solicitudes en Proceso</h3>
                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-black rounded-full">
                            {{ $ticketsPendientes->total() }} activas
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Página {{ $ticketsPendientes->currentPage() }} de {{ $ticketsPendientes->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">Fecha de Envío</th>
                                @if(Auth::user()->role == 'admin')
                                    <th class="py-3 px-4 text-left">Usuario / Área</th>
                                @endif
                                <th class="py-3 px-4 text-left">Asunto de Asistencia</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($ticketsPendientes as $ticket)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap font-medium">
                                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    @if(Auth::user()->role == 'admin')
                                        <td class="py-3.5 px-4 max-w-xs">
                                            <div class="font-bold text-slate-800">
                                                {{ $ticket->user ? $ticket->user->name : 'Usuario Inactivo' }}
                                            </div>
                                            <div class="text-[10.5px] text-slate-500 font-medium truncate mt-0.5">
                                                🏢 {{ ($ticket->user && $ticket->user->area) ? $ticket->user->area->name : 'Sin Área' }}
                                            </div>
                                        </td>
                                    @endif
                                    <td class="py-3.5 px-4 font-medium text-slate-800">
                                        {{ $ticket->subject }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if(Auth::user()->role == 'admin')
                                            <a href="{{ route('tickets.edit', $ticket) }}"
                                                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Resolver
                                            </a>
                                        @else
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                class="px-2.5 py-1 bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Ver Detalles
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-600">No hay tickets pendientes de atención.</p>
                                            <p class="text-xs text-slate-400">Todo el soporte técnico está al corriente.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $ticketsPendientes->links() }}
                </div>
            </div>

            {{-- HISTORIAL DE SOLUCIONES --}}
            <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-slate-50/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-wider">Historial de Soluciones</h3>
                        <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-black rounded-full">
                            {{ $ticketsConcluidos->total() }} concluidos
                        </span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Página {{ $ticketsConcluidos->currentPage() }} de {{ $ticketsConcluidos->lastPage() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left">Fecha Cierre</th>
                                @if(Auth::user()->role == 'admin')
                                    <th class="py-3 px-4 text-left">Solicitante</th>
                                @endif
                                <th class="py-3 px-4 text-left">Asunto</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($ticketsConcluidos as $ticket)
                                <tr class="hover:bg-slate-50/75 transition duration-150">
                                    <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap font-medium">
                                        {{ $ticket->completed_at ? \Carbon\Carbon::parse($ticket->completed_at)->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    @if(Auth::user()->role == 'admin')
                                        <td class="py-3.5 px-4 font-bold text-slate-700">
                                            {{ $ticket->user ? $ticket->user->name : 'Ex-empleado' }}
                                        </td>
                                    @endif
                                    <td class="py-3.5 px-4 text-slate-600 italic">
                                        {{ $ticket->subject }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                            class="px-2.5 py-1 bg-slate-700 hover:bg-slate-800 text-white rounded text-[10px] font-bold uppercase tracking-wider shadow-xs transition inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Revisar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-slate-400 italic">
                                        El historial de tickets concluidos está vacío.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50/60">
                    {{ $ticketsConcluidos->links() }}
                </div>
            </div>

        </div>
    </div>

    <script>
        function pushSubscription() {
            return {
                isSubscribed: false,
                showBanner: true,
                vapidPublicKey: "{{ env('VAPID_PUBLIC_KEY') }}",

                checkSubscription() {
                    if (!('Notification' in window)) {
                        this.showBanner = false;
                        return;
                    }
                    if (Notification.permission === 'granted') {
                        this.isSubscribed = true;
                    } else if (Notification.permission === 'denied') {
                        this.showBanner = false;
                    }
                },

                async toggleSubscription() {
                    const permission = await Notification.requestPermission();
                    if (permission === 'granted') {
                        this.isSubscribed = true;
                        this.subscribeUser();
                    } else if (permission === 'denied') {
                        this.showBanner = false;
                        alert('Has rechazado las notificaciones. Si cambias de opinión, ajusta los permisos de tu navegador.');
                    }
                },

                async subscribeUser() {
                    try {
                        const registration = await navigator.serviceWorker.ready;
                        const subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
                        });

                        const response = await fetch("{{ route('notifications.subscribe') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(subscription)
                        });

                        const text = await response.text();

                        try {
                            const jsonStart = text.indexOf('{');
                            const cleanJson = text.substring(jsonStart);
                            const data = JSON.parse(cleanJson);

                            if (data.success) {
                                alert('¡Suscripción guardada en la base de datos de la CEAA!');
                                this.isSubscribed = true;
                                this.showBanner = false;
                            }
                        } catch (e) {
                            console.error("Error al parsear respuesta limpia:", text);
                        }

                    } catch (error) {
                        console.error('Error al suscribirse:', error);
                    }
                },

                urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) {
                        outputArray[i] = rawData.charCodeAt(i);
                    }
                    return outputArray;
                }
            }
        }
    </script>
</x-app-layout>