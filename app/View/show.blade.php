<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Oficio de Comisión - {{ $comision->oficio_numero }}</title>
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

        .signature-line {
            border-top: 1px solid black;
            width: 200px;
            /* Ancho ajustado para las firmas */
            margin: 4rem auto 0.5rem;
            /* Margen superior aumentado */
            text-align: center;
        }
    </style>
</head>

<body class="bg-gray-100 font-serif p-8">

    <div class="max-w-4xl mx-auto mb-4 text-right no-print">
        <a href="{{ route('principal') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">&larr;
            Volver</a>
        <button onclick="window.print()"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Imprimir</button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-12 printable-area">
        <div class="text-right mb-12">
            <p>Pachuca de Soto, Hgo. a {{ $fechaFormateada }}</p>
            <p class="font-bold">{{ $comision->oficio_numero }}</p>
        </div>

        <div class="mb-8">
            <p class="font-bold">{{ $comision->user->name }}</p>
            <p>No. de empleado: {{ $comision->user->no_empleado ?? 'N/A' }}</p>
            <p>Área de adscripción: {{ $comision->user->area->name ?? 'N/A' }}</p>
            <p class="font-bold mt-4">P R E S E N T E</p>
        </div>

        <p class="text-justify leading-relaxed mb-8">
            Por este conducto me permito comunicar a usted, que ha sido comisionado el (los) día (s):
            <strong>{{ $comision->dias_comision }}</strong> para realizar la siguiente actividad:
            <strong>{{ $comision->actividad }}</strong> en <strong>{{ $comision->lugar }}</strong>.
        </p>

        <p class="text-justify leading-relaxed mb-8">
            @if($comision->vehiculo)
                Actividad que se realizará en el vehículo modelo: <strong>{{ $comision->vehiculo->marca }}
                    {{ $comision->vehiculo->modelo }}</strong>,
                placas <strong>{{ $comision->vehiculo->placa }}</strong>.
            @else
                Para esta actividad no sera necesaria la asignacion de un vehiculo institucional.
            @endif
        </p>

        @if($comision->proyecto)
            <div class="text-justify leading-relaxed">
                <p>Los gastos que se devenguen se cargan al:</p>
                <p><strong>Proyecto:</strong> {{ $comision->proyecto->nombre }}</p>
                @if($comision->proyecto->unidadesAdministrativas->isNotEmpty())
                    <p><strong>Unidad Administrativa:</strong></p>
                    <ul class="list-none ml-0">
                        @foreach($comision->proyecto->unidadesAdministrativas as $unidad)
                            <li>{{ $unidad->clave }} {{ $unidad->nombre }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-3 text-center mt-24 text-sm">
            <div>
                <p class="font-bold">Autorizó</p>
                <div class="signature-line"></div>
                <p>{{ $comision->jefeArea->name }}</p>
                <p>{{ $comision->jefeArea->area->name ?? '' }}</p>
            </div>
            <div>
                <p class="font-bold">Vo.Bo.</p>
                <div class="signature-line"></div>
                <p>L.A.E. Andrés Caudillo Rivero</p>
                <p>Director de Administración y Finanzas</p>
            </div>
            <div>
                <p class="font-bold">Enterado</p>
                <div class="signature-line"></div>
                <p>{{ $comision->user->name }}</p>
                <p>(Comisionado)</p>
            </div>
        </div>
    </div>
</body>

</html>