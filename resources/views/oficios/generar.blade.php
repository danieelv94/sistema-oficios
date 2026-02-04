<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficio de Turno - {{ $oficio->numero_oficio }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .printable-area {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans p-8">

    <div class="max-w-4xl mx-auto mb-4 text-right no-print">
        <a href="{{ route('oficios.show', $oficio) }}"
            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">&larr; Volver a Detalles</a>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Imprimir
            Oficio</button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-12 border rounded-lg shadow-lg printable-area">

        <header class="flex justify-between items-start border-b pb-4 mb-8">
            <div>
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16">
            </div>
            <div class="text-right">
                <h1 class="text-2xl font-bold text-gray-800">OFICIO DE TURNO</h1>
                <p class="text-gray-600"><strong>No. Oficio Interno:</strong> {{ $oficio->numero_oficio }}</p>
                <p class="text-gray-600"><strong>Fecha de Emisión:</strong> {{ now()->format('d/m/Y') }}</p>
            </div>
        </header>

        <section class="mb-8">
            <h2 class="font-bold border-b pb-2 mb-4">DETALLES DEL OFICIO RECIBIDO</h2>
            <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
                <div><strong>Remitente:</strong> {{ $oficio->remitente }}</div>
                <div><strong>No. Oficio Dependencia:</strong> {{ $oficio->numero_oficio_dependencia }}</div>
                <div><strong>Municipio y Localidad:</strong> {{ $oficio->municipio }}, {{ $oficio->localidad }}</div>
                <div><strong>Tipo de Correspondencia:</strong> {{ $oficio->tipo_correspondencia }}</div>
                <div><strong>Fecha de Recepción:</strong>
                    {{ \Carbon\Carbon::parse($oficio->fecha_recepcion)->format('d/m/Y') }}</div>
                <div><strong>Prioridad:</strong> {{ $oficio->prioridad }}</div>
                <div><strong>Fecha Límite:</strong>
                    {{ $oficio->fecha_limite ? \Carbon\Carbon::parse($oficio->fecha_limite)->format('d/m/Y') : 'N/A' }}
                </div>
            </div>
        </section>

        <section class="mb-8">
            <h2 class="font-bold border-b pb-2 mb-2">ASUNTO:</h2>
            <p class="text-gray-700"><span class="block break-words w-full">{!! nl2br(e($oficio->asunto)) !!}</span></p>
        </section>

        <section class="mb-8">
            <h2 class="font-bold border-b pb-2 mb-4">TURNADO A:</h2>
            @forelse($turnosParaImprimir as $area)
                <div class="mb-4 pl-4 border-l-2">
                    <p><strong>ÁREA:</strong> {{ $area->name }}</p>
                    <p><strong>INSTRUCCIÓN:</strong> <span class="font-semibold">{{ $area->pivot->instruccion }}</span></p>
                    <p><strong>PARA ATENCIÓN DE:</strong>
                        {{ \App\Models\User::find($area->pivot->user_id)->name ?? 'Responsable del Área' }}</p>
                </div>
            @empty
                <p class="text-gray-500">Sin turnos asignados.</p>
            @endforelse
        </section>

        @if($oficio->observaciones)
            <section>
                <h2 class="font-bold border-b pb-2 mb-2">OBSERVACIONES ADICIONALES:</h2>
                <p class="text-gray-700">{{ $oficio->observaciones }}</p>
            </section>
        @endif

        <footer class="mt-16 pt-8 border-t text-center text-xs text-gray-500">
            <p>Este documento es generado por el Sistema de Gestión de Oficios.</p>
        </footer>

    </div>
</body>

</html>