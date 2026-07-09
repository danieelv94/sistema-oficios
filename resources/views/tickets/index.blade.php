<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Soporte Técnico CEAA') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- ========================================================== --}}
            {{-- BANNER DE NOTIFICACIONES PUSH --}}
            {{-- ========================================================== --}}
            <div x-data="pushSubscription()" x-init="checkSubscription()" x-show="!isSubscribed && showBanner"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="bg-gradient-to-r from-guinda-ceaa to-guinda-ceaa-hover rounded-xl shadow-lg p-6 text-white relative overflow-hidden"
                style="display: none;">

                {{-- Icono de fondo decorativo --}}
                <div class="absolute right-[-20px] top-[-20px] opacity-10">
                    <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 22a2 2 0 002-2H10a2 2 0 002 2zm6-6V11a6 6 0 00-9.33-5.05A3.003 3.003 0 005 11v5l-2 2v1h18v-1l-2-2z" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-white/20 rounded-full mr-4 animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-black uppercase tracking-tighter italic">¿Quieres recibir alertas de
                                soporte?</h4>
                            <p class="text-xs opacity-90 font-medium uppercase tracking-widest leading-relaxed">
                                Activa las notificaciones para saber al instante cuando tu ticket sea resuelto o existan
                                nuevos avisos.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button @click="showBanner = false"
                            class="text-[10px] font-black uppercase opacity-60 hover:opacity-100 transition tracking-widest underline">Omitir</button>
                        <button @click="toggleSubscription"
                            class="bg-white text-guinda-ceaa px-8 py-2.5 rounded shadow-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-gray-100 transition-all transform active:scale-95">
                            Activar Alertas
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end no-print">
                <a href="{{ route('tickets.create') }}"
                    class="px-6 py-2 bg-guinda-ceaa text-white rounded-md hover:bg-guinda-ceaa-hover font-bold shadow-lg transition-all transform hover:scale-105 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Levantar Ticket de Soporte
                </a>
            </div>

            {{-- SOLICITUDES EN PROCESO --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-guinda-ceaa">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center mb-6">
                        <div class="p-2 bg-orange-100 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-700 uppercase tracking-tight">Solicitudes en Proceso
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-100 text-guinda-ceaa uppercase text-xs font-bold tracking-wider">
                                    <th class="py-3 px-4 border-b text-left">Fecha de Envío</th>
                                    @if(Auth::user()->role == 'admin')
                                        <th class="py-3 px-4 border-b text-left">Usuario / Área</th>
                                    @endif
                                    <th class="py-3 px-4 border-b text-left">Asunto de Asistencia</th>
                                    <th class="py-3 px-4 border-b text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($ticketsPendientes as $ticket)
                                    <tr class="hover:bg-red-50 transition-colors">
                                        <td class="py-3 px-4 text-sm">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        @if(Auth::user()->role == 'admin')
                                            <td class="py-3 px-4">
                                                <div class="text-sm font-bold text-gray-800">
                                                    {{ $ticket->user ? $ticket->user->name : 'Usuario Inactivo' }}</div>
                                                <div class="text-[10px] text-gray-500 uppercase">
                                                    {{ ($ticket->user && $ticket->user->area) ? $ticket->user->area->name : 'Sin Área' }}
                                                </div>
                                            </td>
                                        @endif
                                        <td class="py-3 px-4 text-sm font-medium text-gray-700">{{ $ticket->subject }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if(Auth::user()->role == 'admin')
                                                <a href="{{ route('tickets.edit', $ticket) }}"
                                                    class="inline-block px-4 py-1 bg-green-600 text-white rounded text-xs font-bold hover:bg-green-700">Resolver</a>
                                            @else
                                                <a href="{{ route('tickets.show', $ticket) }}"
                                                    class="inline-block px-4 py-1 bg-guinda-ceaa text-white rounded text-xs font-bold hover:bg-guinda-ceaa-hover">Ver
                                                    Detalles</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10 text-gray-400 italic font-medium">No hay
                                            folios pendientes de atención.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $ticketsPendientes->links() }}
                    </div>
                </div>
            </div>

            {{-- HISTORIAL DE SOLUCIONES --}}
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-t-4 border-gray-400">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center mb-6">
                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-500 uppercase tracking-tight">Historial de Soluciones
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 uppercase text-xs font-bold tracking-wider">
                                    <th class="py-3 px-4 border-b text-left">Fecha Cierre</th>
                                    @if(Auth::user()->role == 'admin')
                                        <th class="py-3 px-4 border-b text-left">Solicitante</th>
                                    @endif
                                    <th class="py-3 px-4 border-b text-left">Asunto</th>
                                    <th class="py-3 px-4 border-b text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($ticketsConcluidos as $ticket)
                                    <tr class="hover:bg-gray-50 grayscale-[0.5] transition-all">
                                        <td class="py-3 px-4 text-sm text-gray-500">
                                            {{ $ticket->completed_at ? \Carbon\Carbon::parse($ticket->completed_at)->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                        @if(Auth::user()->role == 'admin')
                                            <td class="py-3 px-4 text-sm">
                                                {{ $ticket->user ? $ticket->user->name : 'Ex-empleado' }}</td>
                                        @endif
                                        <td class="py-3 px-4 text-sm text-gray-400 italic">{{ $ticket->subject }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="{{ route('tickets.show', $ticket) }}"
                                                class="inline-block px-4 py-1 bg-gray-400 text-white rounded text-xs font-bold hover:bg-gray-500">Revisar</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-gray-300 italic text-sm">El historial
                                            está vacío.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $ticketsConcluidos->links() }}
                    </div>
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

                        // En lugar de response.json() directamente, leemos el texto primero
                        const text = await response.text();

                        try {
                            // Buscamos dónde empieza el JSON real
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

                // Función auxiliar para convertir la llave VAPID
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