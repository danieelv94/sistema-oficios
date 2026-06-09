<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volante de Turno - {{ $oficio->numero_oficio }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f3f4f6;
        }

        /* CONFIGURACIÓN DE PÁGINA */
        @page {
            size: letter;
            margin: 0;
        }

        @media print {

            html,
            body {
                height: 100%;
                background: white;
            }

            .no-print {
                display: none !important;
            }

            .printable-area {
                width: 21.59cm;
                padding: 1.5cm 2cm !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                box-sizing: border-box;
            }

            p,
            div,
            strong,
            span {
                color: black !important;
            }

            .footer-oficio-fijo {
                margin-top: 4rem;
                border-top: 1px solid #932C43;
                padding-top: 0.4cm;
                display: flex !important;
                justify-content: space-between;
                align-items: center;
            }
        }

        @media screen {
            .printable-area {
                background: white;
                margin: 2rem auto;
                width: 21.59cm;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                padding: 1.5cm 2cm 3cm;
                border-radius: 8px;
            }

            .footer-oficio-fijo {
                margin-top: 4rem;
                border-top: 1px solid #932C43;
                padding-top: 0.4cm;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
        }

        .signature-line {
            border-top: 1px solid black;
            width: 200px;
            margin: 2.5rem auto 0.5rem;
            text-align: center;
        }
    </style>
</head>

<body class="p-4 sm:p-8">

    {{-- Controles superiores (No se imprimen) --}}
    <div
        class="max-w-[21.59cm] mx-auto mb-4 text-right no-print flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <a href="{{ route('oficios.show', $oficio) }}"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 font-bold text-xs uppercase tracking-wider transition">
            &larr; Volver a Expediente
        </a>
        <button onclick="window.print()"
            class="px-6 py-2 bg-[#932C43] text-white rounded-lg hover:bg-[#722134] font-black text-xs uppercase tracking-widest shadow-md hover:shadow-lg transition">
            Imprimir Turno
        </button>
    </div>

    {{-- Hoja de Volante de Turno (Imprimible) --}}
    <div class="printable-area relative">

        {{-- Logotipo Oficial CEAA --}}
        <div class="flex justify-end mb-6 text-right">
            <img src="{{ asset('images/encabezado.png') }}" alt="CEAA" style="width: 9.09cm; height: 2.39cm;">
        </div>

        <p class="font-bold text-[#932C43] text-lg uppercase mb-2 tracking-wider">Turno Interno</p>

        <div class="text-right mb-8">
            <p class="text-xs text-gray-500 uppercase font-semibold">Fecha de Emisión</p>
            <p class="font-bold text-sm text-gray-800">{{ now()->format('d/m/Y') }}</p>
            <p class="text-xs text-gray-500 uppercase font-semibold mt-1">No. Oficio Interno</p>
            <p class="font-bold text-base text-gray-900">{{ $oficio->numero_oficio }}</p>
            @if($turnosParaImprimir->count() === 1 && $turnosParaImprimir->first()->pivot->folio_interno)
                <p class="text-xs text-gray-500 uppercase font-semibold mt-1">Folio del Turno</p>
                <p class="font-bold text-base text-[#932C43]">{{ $turnosParaImprimir->first()->pivot->folio_interno }}</p>
            @endif
        </div>

        {{-- Datos de Recepción del Oficio --}}
        <section class="mb-8 bg-gray-50/50 p-5 rounded-xl border border-gray-100">
            <h2 class="font-bold text-[#932C43] border-b border-gray-200 pb-2 mb-4 uppercase text-xs tracking-wider">
                Detalles de la Correspondencia</h2>
            <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-xs text-gray-700">
                <div class="break-words"><strong class="text-gray-900 uppercase">Remitente:</strong>
                    {{ $oficio->remitente }}</div>
                <div><strong class="text-gray-900 uppercase">No. Oficio Dependencia:</strong>
                    {{ $oficio->numero_oficio_dependencia }}</div>
                <div><strong class="text-gray-900 uppercase">Municipio y Localidad:</strong> {{ $oficio->municipio }},
                    {{ $oficio->localidad }}</div>
                <div><strong class="text-gray-900 uppercase">Tipo:</strong> {{ $oficio->tipo_correspondencia }}</div>
                <div><strong class="text-gray-900 uppercase">Fecha de Recepción:</strong>
                    {{ \Carbon\Carbon::parse($oficio->fecha_recepcion)->format('d/m/Y') }}</div>
                <div>
                    <strong class="text-gray-900 uppercase">Prioridad:</strong>
                    <span
                        class="font-bold {{ $oficio->prioridad == 'Urgente' ? 'text-red-600' : 'text-blue-600' }}">{{ $oficio->prioridad }}</span>
                </div>
            </div>
        </section>

        {{-- Asunto --}}
        <section class="mb-8">
            <h2 class="font-bold text-[#932C43] border-b border-gray-200 pb-2 mb-2 uppercase text-xs tracking-wider">
                Asunto:</h2>
            <p class="text-xs text-gray-700 leading-relaxed italic break-words">
                "{!! nl2br(e($oficio->asunto)) !!}"
            </p>
        </section>

        {{-- Destinatario del Turno --}}
        <section class="mb-8">
            <h2 class="font-bold text-[#932C43] border-b border-gray-200 pb-2 mb-4 uppercase text-xs tracking-wider">
                Turnado A:</h2>
            @forelse($turnosParaImprimir as $area)
                @php
                    $userAsignado = $area->pivot->user_id ? \App\Models\User::find($area->pivot->user_id) : null;
                @endphp
                <div class="mb-4 pl-4 border-l-4 border-[#932C43]">
                    <div class="flex justify-between items-start">
                        <p class="text-xs text-gray-600"><strong class="text-gray-800 uppercase">Dirección / Área:</strong>
                            {{ $area->name }}</p>
                        @if($area->pivot->folio_interno)
                            <span class="text-xs font-bold text-[#932C43]">Folio: {{ $area->pivot->folio_interno }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-600 mt-1"><strong class="text-gray-800 uppercase">Instrucción:</strong>
                        <span class="font-black text-[#932C43]">{{ $area->pivot->instruccion }}</span></p>
                    <p class="text-xs text-gray-600 mt-1">
                        <strong class="text-gray-800 uppercase">Destinatario (Para Atención):</strong>
                        @if($userAsignado)
                            <span class="font-black text-gray-900">{{ $userAsignado->prof }} {{ $userAsignado->name }}</span>
                            @if($userAsignado->cargo)
                                <span class="block text-[10px] text-gray-500 font-semibold mt-0.5">{{ $userAsignado->cargo }}</span>
                            @else
                                <span class="block text-[10px] text-gray-400 italic mt-0.5">Personal CEAA</span>
                            @endif
                        @else
                            <span class="font-bold text-gray-500 italic">Responsable del Área</span>
                        @endif
                    </p>
                </div>
            @empty
                <p class="text-gray-400 italic text-xs">Sin turnos asignados.</p>
            @endforelse
        </section>

        {{-- Observaciones adicionales --}}
        @if($oficio->observaciones)
            <section class="mb-8">
                <h2 class="font-bold text-[#932C43] border-b border-gray-200 pb-2 mb-2 uppercase text-xs tracking-wider">
                    Observaciones:</h2>
                <p class="text-xs text-gray-600 break-words">{{ $oficio->observaciones }}</p>
            </section>
        @endif

        {{-- Línea de firmas físicas (Acuse de Recibido en Papel) --}}
        @if(collect($turnosParaImprimir)->contains(fn($area) => !empty($area->pivot->user_id)))
            <div class="mt-20 flex justify-center text-center text-xs">
                <div>
                    <p class="font-bold uppercase text-gray-600 text-[10px]">Acuse de Recibido</p>
                    <div class="signature-line"></div>
                    @php
                        $primerTurno = collect($turnosParaImprimir)->first(fn($area) => !empty($area->pivot->user_id));
                        $operativo = $primerTurno ? \App\Models\User::find($primerTurno->pivot->user_id) : null;
                    @endphp
                    <p class="font-bold text-gray-800">
                        {{ $operativo ? $operativo->prof . ' ' . $operativo->name : 'Operativo Asignado' }}</p>
                    <p class="text-[9px] text-gray-400 uppercase font-semibold leading-tight">
                        {{ $operativo && $operativo->cargo ? $operativo->cargo : 'Personal CEAA' }}</p>
                </div>
            </div>
        @endif

        {{-- Pie de Página Fijo e Institucional --}}
        <div class="footer-oficio-fijo">
            <div class="flex justify-left">
                <img src="{{ asset('images/SGC.webp') }}" alt="SGC" style="width:1.4cm; height:1.4cm;">
            </div>

            <div class="text-right text-[7pt] text-gray-400 leading-tight font-sans">
                <p class="font-bold text-gray-500">Camino Real de la Plata No. 336</p>
                <p>Zona Plateada, Pachuca de Soto, Hgo. C.P. 42084</p>
                <p>Ofic: 771 715 8390 y 771 715 8391</p>
                <p class="text-blue-300">ceaa.hidalgo.gob.mx</p>
            </div>
        </div>

    </div>
</body>

</html>