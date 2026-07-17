<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Oficio de Comisión - {{ $comision->oficio_numero }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'guinda-ceaa': '#691B31',
                        'guinda-ceaa-hover': '#A02142',
                        'guinda-medio': '#A02142',
                        'dorado-ocre': '#BC955B',
                        'arena-claro': '#DDC9A3',
                        'gris-claro': '#98989A',
                        'gris-oscuro': '#6F7271',
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

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
                overflow: hidden;
                background: white;
            }

            .no-print {
                display: none !important;
            }

            .printable-area {
                width: 21.59cm;
                height: 27.94cm;
                padding: 1.5cm 2cm !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                position: relative;
                box-sizing: border-box;
                page-break-after: avoid;
                page-break-before: avoid;
            }

            p,
            div,
            strong {
                font-size: 11pt !important;
                line-height: 1.4 !important;
                color: black !important;
            }

            .footer-oficio-fijo {
                position: absolute;
                bottom: 1.2cm;
                left: 2cm;
                right: 2cm;
                border-top: 1px solid #691B31;
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
                height: 27.94cm;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                position: relative;
                padding: 1.5cm 2cm;
            }

            .footer-oficio-fijo {
                position: absolute;
                bottom: 1.2cm;
                left: 2cm;
                right: 2cm;
                border-top: 1px solid #691B31;
                padding-top: 0.4cm;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
        }

        .signature-line {
            border-top: 1px solid black;
            width: 170px;
            margin: 2.5rem auto 0.5rem;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="max-w-[21.59cm] mx-auto pt-8 text-right no-print">
        <a href="{{ route('comisiones.index') }}"
            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 font-sans text-sm inline-flex items-center">
            &larr; Volver al Listado
        </a>
        @if($comision->status !== 'Cancelado' || Auth::user()->role == 'admin')
             <button onclick="window.print()"
                class="ml-2 px-6 py-2 bg-guinda-ceaa text-white rounded-md hover:bg-guinda-ceaa-hover font-bold shadow-lg">
                Imprimir Oficio
            </button>
        @endif
    </div>

    <div class="printable-area relative">

        @if($comision->status === 'Cancelado')
            <div class="absolute inset-0 flex items-center justify-center z-0 pointer-events-none">
                <p style="font-size: 10rem;"
                    class="font-black text-red-500 opacity-10 transform -rotate-45 select-none uppercase">CANCELADO</p>
            </div>
        @endif

        <div class="relative z-10">
            <div class="flex justify-end mb-6 text-right">
                <img src="{{ asset('images/encabezado.png') }}" alt="CEAA" style="width: 9.09cm; height: 2.39cm;">
            </div>

            <p class="font-bold text-guinda-ceaa text-lg uppercase mb-2">Oficio de Comisión</p>

            <div class="text-right mb-10">
                <p>Pachuca de Soto, Hgo. a {{ $fechaFormateada }}</p>
                <p class="font-bold text-xl">{{ $comision->oficio_numero }}</p>
            </div>

            <div class="mb-8 space-y-1">
                {{-- Corregido: Uso de optional() en lugar de ?-> para compatibilidad con PHP < 8.0 --}} <p><strong
                        class="text-guinda-ceaa">Nombre de Empleado: </strong>{{ optional($comision->user)->prof }}
                    {{ optional($comision->user)->name }}</p>
                    <p><strong class="text-guinda-ceaa">No. de empleado:
                        </strong>{{ optional($comision->user)->no_empleado ?? 'N/A' }}</p>
                    <p><strong class="text-guinda-ceaa">Área de adscripción:
                        </strong>{{ optional(optional($comision->user)->area)->name ?? 'N/A' }}</p>
                    <p class="font-bold mt-6 tracking-widest">P R E S E N T E</p>
            </div>

            <p class="text-justify mb-6">
                Por este conducto me permito comunicar a usted, que ha sido comisionado el (los) día (s):
                <strong>{{ $comision->dias_comision }}</strong>,
                @if($comision->hora_inicio && $comision->hora_fin)
                    en un horario de <strong>{{ \Carbon\Carbon::parse($comision->hora_inicio)->format('H:i') }}</strong> a
                    <strong>{{ \Carbon\Carbon::parse($comision->hora_fin)->format('H:i') }}</strong> horas,
                @endif
                para realizar la siguiente actividad: <strong>{{ $comision->actividad }}</strong> en
                <strong>{{ $comision->lugar }}</strong>.
            </p>

            <p class="text-justify mb-8">
                @if($comision->vehiculo)
                    Actividad que se realizará en el vehículo modelo: <strong>{{ $comision->vehiculo->marca }}
                        {{ $comision->vehiculo->modelo }}</strong>, placas
                    <strong>{{ $comision->vehiculo->placa }}</strong>.
                @else
                    Para esta actividad no será necesaria la asignación de un vehículo institucional.
                @endif
            </p>

            @if($comision->proyecto)
                <div class="mb-8 p-4 bg-gray-50 border-l-4 border-gray-200">
                    <p class="text-xs font-bold text-gray-400 uppercase italic mb-1">Gastos con cargo al:</p>
                    <p><strong>Proyecto:</strong> {{ $comision->proyecto->nombre }}</p>
                </div>
            @endif

            {{-- FIRMAS --}}
            <div class="grid grid-cols-3 text-center mt-12 text-[10pt]">
                <div>
                    <p class="font-bold uppercase text-gray-600">Autorizó</p>
                    <div class="signature-line"></div>
                    @php
                        $comisionado = $comision->user;
                        $isJefeArea = optional($comisionado)->role === 'jefe_area';
                        $isArea1 = optional($comisionado)->area_id == 1;
                        $isSpecialUserArea1 = in_array(optional($comisionado)->id, [326, 328]);
                    @endphp

                    @if($isJefeArea || ($isArea1 && $isSpecialUserArea1))
                        <p class="font-bold">M.A.P. Juan Carlos Chávez González</p>
                        <p>Director General</p>
                    @elseif($isArea1)
                        @php
                            $omar = \App\Models\User::find(326);
                            $omarProf = $omar ? $omar->prof : 'L.M.M.C.';
                            $omarName = $omar ? $omar->name : 'Omar Arturo Islas Calderón';
                        @endphp
                        <p class="font-bold">{{ $omarProf }} {{ $omarName }}</p>
                        <p class="leading-tight">{{ !empty($omar->cargo) ? $omar->cargo : 'Líder de Proyecto' }}</p>
                    @else
                        @php
                            $areaNombre = optional(optional($comision->jefeArea)->area)->name ?? 'Área No Asignada';
                            $areasFemeninas = ['Dirección de Calidad del Agua', 'Dirección Jurídica y Unidad para la Igualdad entre Mujeres y Hombres'];
                            $esFemenino = in_array($areaNombre, $areasFemeninas);
                            $reemplazos = $esFemenino ? ['Directora', 'Subdirectora', 'Jefa'] : ['Director', 'Subdirector', 'Jefe'];
                            $cargo = str_ireplace(['Dirección', 'Subdirección', 'Jefatura'], $reemplazos, $areaNombre);
                        @endphp
                        <p class="font-bold">{{ optional($comision->jefeArea)->prof }}
                            {{ optional($comision->jefeArea)->name }}</p>
                        <p class="leading-tight">{{ $cargo }}</p>
                    @endif
                </div>
                <div>
                    <p class="font-bold uppercase text-gray-600">Vo.Bo.</p>
                    <div class="signature-line"></div>
                    <p class="font-bold">L.A.E. Andrés Caudillo Rivero</p>
                    <p class="leading-tight">Director de Administración y Finanzas</p>
                </div>
                <div>
                    <p class="font-bold uppercase text-gray-600">Enterado</p>
                    <div class="signature-line"></div>
                    <p class="font-bold">{{ optional($comision->user)->prof }} {{ optional($comision->user)->name }}</p>
                    <p class="leading-tight">{{ optional($comision->user)->cargo ?? 'Personal CEAA' }}</p>
                </div>
            </div>

            <div class="text-center font-bold mt-10 text-gray-300 uppercase text-[10px]">
                Sello de Certificación
            </div>
        </div>

        <div class="footer-oficio-fijo">
            <div class="flex justify-left">
                <img src="{{ asset('images/SGC.webp') }}" alt="SGC" style="width:1.6cm; height:1.6cm;">
            </div>

            <div class="text-right text-[8pt] text-gray-400 leading-tight font-sans">
                <p class="font-bold text-gray-500">Camino Real de la Plata No. 336</p>
                <p>Zona Plateada, Pachuca de Soto, Hgo. C.P. 42084</p>
                <p>Ofic: 771 715 8390 y 771 715 8391</p>
                <p class="text-gris-claro">ceaa.hidalgo.gob.mx</p>
            </div>
        </div>
    </div>
</body>

</html>