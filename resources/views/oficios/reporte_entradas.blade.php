<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Entradas de Correspondencia - @if($fechaInicio == $fechaFin)
    {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} @else
            {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al
        {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }} @endif
    </title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (for quick layout, print friendly native style) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'guinda-ceaa': '#691B31',
                        'arena-claro': '#DDC9A3',
                        'gris-claro': '#98989A',
                        'guinda-medio': '#A02142',
                        'dorado-ocre': '#BC955B',
                        'gris-oscuro': '#6F7271',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #fff;
            color: #1f2937;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .print-border {
                border: 1px solid #d1d5db !important;
            }

            body {
                background-color: #fff !important;
                color: #000 !important;
            }
        }
    </style>
</head>

<body class="p-8 bg-gray-50 min-h-screen">

    {{-- Control superior (No se imprime) --}}
    <div
        class="no-print max-w-7xl mx-auto mb-8 p-6 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('oficios.index') }}"
                class="text-xs font-black uppercase text-gray-500 hover:text-gray-800 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Regresar
            </a>
            <div class="w-px h-5 bg-gray-200"></div>
            <div>
                <h2 class="text-sm font-black text-gray-800 uppercase">Reporte de Entrada de Correspondencia</h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase">Impresión diaria y control de documentos
                    recibidos</p>
            </div>
        </div>

        <form action="{{ route('oficios.reporteEntradas') }}" method="GET"
            class="flex items-center gap-3 flex-wrap sm:flex-nowrap">
            <div class="flex items-center gap-2">
                <label for="fecha_inicio"
                    class="text-[10px] font-black uppercase text-gray-400 whitespace-nowrap">Desde:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}"
                    class="text-xs rounded-lg border-gray-300 focus:ring-guinda-ceaa focus:border-guinda-ceaa py-1.5 px-3">
            </div>
            <div class="flex items-center gap-2">
                <label for="fecha_fin"
                    class="text-[10px] font-black uppercase text-gray-400 whitespace-nowrap">Hasta:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}"
                    class="text-xs rounded-lg border-gray-300 focus:ring-guinda-ceaa focus:border-guinda-ceaa py-1.5 px-3">
            </div>
            <button type="submit"
                class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-black uppercase px-4 py-1.5 rounded-lg transition shadow-sm">
                Cargar
            </button>
            <button type="button" onclick="window.print()"
                class="bg-guinda-ceaa hover:bg-guinda-ceaa-hover text-white text-xs font-black uppercase px-5 py-1.5 rounded-lg transition shadow-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Imprimir Reporte
            </button>
        </form>
    </div>

    {{-- Hoja de Reporte (Imprimible) --}}
    <div
        class="max-w-7xl mx-auto bg-white p-10 rounded-2xl shadow-sm border border-gray-100 print:shadow-none print:border-none print:p-0">

        {{-- Encabezado Institucional --}}
        <div class="flex justify-between items-start border-b-2 border-gray-100 pb-6 mb-8">
            <div>
                <h1 class="text-xl font-black text-guinda-ceaa uppercase tracking-tight">SISTEMA DE OFICIOS Y
                    CORRESPONDENCIA</h1>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mt-1">Dirección de Gestión
                    Institucional</p>
            </div>
            <div class="text-right flex flex-col items-end">
                <img src="{{ asset('images/encabezado.png') }}" alt="CEAA" class="h-12 object-contain mb-2">
                <p class="text-xs font-bold text-gray-800 uppercase mt-0.5">
                    @if($fechaInicio == $fechaFin)
                        {{ \Carbon\Carbon::parse($fechaInicio)->locale('es')->translatedFormat('d \d\e F \d\e Y') }}
                    @else
                        Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al
                        {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                    @endif
                </p>
            </div>
        </div>



        {{-- Tabla de Reporte --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs text-left border border-gray-200 print-border">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-700 font-black uppercase">
                    <tr>
                        <th class="px-4 py-3 border-r border-gray-200 text-center w-20">Consecutivo</th>
                        <th class="px-4 py-3 border-r border-gray-200">Remitente</th>
                        <th class="px-4 py-3 border-r border-gray-200">No. Oficio Dependencia</th>
                        <th class="px-4 py-3 border-r border-gray-200">Procedencia</th>
                        <th class="px-4 py-3 border-r border-gray-200">Asunto</th>
                        <th class="px-4 py-3 text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($oficios as $oficio)
                        <tr class="hover:bg-gray-50/50 transition">
                            {{-- Consecutivo --}}
                            <td
                                class="px-4 py-3 border-r border-gray-200 font-black text-gray-900 text-center whitespace-nowrap">
                                {{ $oficio->numero_oficio }}
                            </td>

                            {{-- Remitente --}}
                            <td class="px-4 py-3 border-r border-gray-200 font-semibold text-gray-700">
                                {{ $oficio->remitente }}
                            </td>

                            {{-- No. Oficio Dependencia --}}
                            <td class="px-4 py-3 border-r border-gray-200 font-medium text-gray-600">
                                {{ $oficio->numero_oficio_dependencia }}
                            </td>

                            {{-- Procedencia --}}
                            <td class="px-4 py-3 border-r border-gray-200 text-gray-700">
                                {{ $oficio->localidad }}, {{ $oficio->municipio }}
                            </td>

                            {{-- Asunto --}}
                            <td class="px-4 py-3 border-r border-gray-200 text-gray-500 italic max-w-xs">
                                {{ $oficio->asunto }}
                            </td>

                            {{-- Estatus --}}
                            <td class="px-4 py-3 text-center font-black uppercase text-[10px]">
                                <span class="px-2 py-0.5 rounded
                                            {{ (is_null($oficio->estatus) || $oficio->estatus == 'Recibido') ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $oficio->estatus == 'Turnado' ? 'bg-orange-100 text-orange-700' : '' }}
                                            {{ $oficio->estatus == 'En Proceso' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $oficio->estatus == 'Atendido' ? 'bg-purple-100 text-purple-700' : '' }}
                                            {{ $oficio->estatus == 'Solventado' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $oficio->estatus == 'Cancelado' ? 'bg-red-100 text-red-700' : '' }}
                                        ">
                                    {{ $oficio->estatus ?? 'Pendiente' }}
                                </span>
                                @if($oficio->estatus == 'Cancelado' && $oficio->motivo_cancelacion)
                                    <p
                                        class="text-[8px] text-red-500 font-bold italic mt-1 max-w-[120px] mx-auto print:max-w-none">
                                        Motivo: {{ $oficio->motivo_cancelacion }}
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400 italic">
                                No se encontraron oficios registrados en el período seleccionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Firmas de Control (Solo para reporte físico) --}}
        <div class="mt-20 grid grid-cols-2 gap-16 text-center text-xs">
            <div>
                <div class="border-t border-gray-400 w-64 mx-auto pt-2">
                    <p class="font-bold text-gray-800 uppercase">Registrado por</p>
                    <p class="text-[11px] text-gray-900 font-black mt-1 uppercase">{{ Auth::user()->prof }}
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">
                        {{ Auth::user()->cargo ?? 'Personal de Correspondencia' }}
                    </p>
                </div>
            </div>
            <div>
                <div class="border-t border-gray-400 w-64 mx-auto pt-2">
                    <p class="font-bold text-gray-800 uppercase">Autorizado y Vo.Bo.</p>
                    <p class="text-[11px] text-gray-900 font-black mt-1 uppercase">
                        {{ $directorGestion ? $directorGestion->prof . ' ' . $directorGestion->name : 'Responsable del Área' }}
                    </p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">
                        {{ $directorGestion && $directorGestion->cargo ? $directorGestion->cargo : 'Director de Gestión Institucional' }}
                    </p>
                </div>
            </div>
        </div>

    </div>

</body>

</html>