<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CEAA') }}</title>

    {{-- Tus configuraciones originales de estilos e iconos --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta name="theme-color" content="#4a5568">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    {{-- Agregamos JS al head con defer para asegurar que Alpine.js responda antes de renderizar el menú lateral --}}
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>

<body class="font-sans antialiased bg-gray-100" x-data="{ openSidebar: true }">

    {{-- Incluye la barra lateral responsiva --}}
    @include('layouts.navigation')

    {{-- Contenedor con el margen dinámico corregido --}}
    <div :class="openSidebar ? 'md:pl-64' : 'md:pl-16'"
        class="pt-16 transition-all duration-300 min-h-screen flex flex-col justify-between">

        <main class="flex-1">
            @if (isset($header))
                <header class="bg-white shadow no-print">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <div class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </div>
        </main>

    </div>

    {{-- MODAL DE AVISOS URGENTES --}}
    @include('components.aviso-modal')
    </div>

    {{-- Tu Script Original del Service Worker --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').then(function (registration) {
                    console.log('ServiceWorker registrado con éxito: ', registration.scope);
                }, function (err) {
                    console.log('Fallo en el registro de ServiceWorker: ', err);
                });
            });
        }
    </script>
</body>

</html>