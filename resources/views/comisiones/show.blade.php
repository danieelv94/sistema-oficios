<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Oficio de Comisión - {{ $comision->oficio_numero }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        @page { size: auto; margin: 0mm; }
        @media print {
            body { -webkit-print-color-adjust: exact; margin: 1.5cm; }
            .no-print { display: none !important; }
            .printable-area { border: none !important; box-shadow: none !important; padding: 0 !important; }
        }
        .signature-line { border-top: 1px solid black; width: 200px; margin: 4rem auto 0.5rem; text-align: center; }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto mb-4 text-right no-print">
        <a href="{{ route('comisiones.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 font-sans">&larr; Volver al Listado</a>
        @if($comision->status !== 'Cancelado' || Auth::user()->role == 'admin')
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-sans">Imprimir</button>
        @endif
    </div>

    <div class="max-w-4xl mx-auto bg-white p-12 printable-area relative">
        @if($comision->status === 'Cancelado')
            <div class="absolute inset-0 flex items-center justify-center z-0">
                <p style="font-size: 12rem; line-height: 1;" class="font-black text-red-500 opacity-20 transform -rotate-45 select-none">CANCELADO</p>
            </div>
        @endif
        
        <div class="relative z-10">
            <div class="flex justify-end mb-8">
                <img src="{{ asset('images/encabezado.png') }}" alt="Encabezado del Oficio" style="width: 9.09cm; height: 2.39cm;">
            </div>
            
            <p class="font-bold mt-4 text-[#932C43]">OFICIO DE COMISION</p>
            <div class="text-right mb-12">
                <p>Pachuca de Soto, Hgo. a {{ $fechaFormateada }}</p>
                <p class="font-bold">{{ $comision->oficio_numero }}</p>
            </div>

            <div class="mb-8">
                <p><strong class="text-[#932C43]">Nombre de Empleado: </strong>{{ $comision->user->prof }} {{ $comision->user->name }}</p>
                <p><strong class="text-[#932C43]">No. de empleado: </strong>{{ $comision->user->no_empleado ?? 'N/A' }}</p>
                <p><strong class="text-[#932C43]">Área de adscripción: </strong>{{ $comision->user->area->name ?? 'N/A' }}</p>
                <p class="font-bold mt-4">P R E S E N T E</p>
            </div>
            
            <p class="text-justify leading-relaxed mb-8">
                Por este conducto me permito comunicar a usted, que ha sido comisionado el (los) día (s): <strong>{{ $comision->dias_comision }}</strong> para realizar la siguiente actividad: <strong>{{ $comision->actividad }}</strong> en <strong>{{ $comision->lugar }}</strong>.
            </p>
            <p class="text-justify leading-relaxed mb-8">
                @if($comision->vehiculo)
                    Actividad que se realizará en el vehículo modelo: <strong>{{ $comision->vehiculo->marca }} {{ $comision->vehiculo->modelo }}</strong>, placas <strong>{{ $comision->vehiculo->placa }}</strong>.
                @else
                    Para esta actividad no sera necesaria la asignacion de un vehiculo institucional.
                @endif
            </p>
            @if($comision->proyecto)
            <div class="text-justify leading-relaxed">
                <p>Los gastos que se devenguen se cargan al:</p>
                <p><strong>Proyecto:</strong> {{ $comision->proyecto->nombre }}</p>
                @if($comision->unidadAdministrativa)
                <p><strong>Unidad Administrativa:</strong></p>
                <p>{{ $comision->unidadAdministrativa->clave }} {{ $comision->unidadAdministrativa->nombre }}</p>
                @endif
            </div>
            @endif

            <div class="grid grid-cols-3 text-center mt-20 text-sm">
                <div>
                    <p class="font-bold">Autorizó</p>
                    <div class="signature-line"></div>
                    
                    @if($comision->user->role === 'jefe_area')
                        <p>M.A.P. Juan Carlos Chavez Gonzalez</p>
                        <p>Director General</p>
                    @else
                        @php
                            $areaNombre = $comision->jefeArea->area->name ?? '';
                            $cargo = $areaNombre; // Valor por defecto
                            $areasFemeninas = [
                                'Dirección de Calidad del Agua',
                                'Dirección Jurídica y Unidad para la Igualdad entre Mujeres y Hombres'
                            ];
                            $esFemenino = false;
                            if (in_array($areaNombre, $areasFemeninas)) {
                                $esFemenino = true;
                            }
                            if ($esFemenino) {
                                $cargo = str_ireplace(
                                    ['Dirección', 'Subdirección', 'Jefatura'], 
                                    ['Directora', 'Subdirectora', 'Jefa'], 
                                    $areaNombre
                                );
                            } else {
                                $cargo = str_ireplace(
                                    ['Dirección', 'Subdirección', 'Jefatura'], 
                                    ['Director', 'Subdirector', 'Jefe'], 
                                    $areaNombre
                                );
                            }
                        @endphp
                        <p>{{ $comision->jefeArea->prof }} {{ $comision->jefeArea->name }}</p>
                        <p>{{ $cargo }}</p>
                        @endif
                </div>
                <div>
                    <p class="font-bold">Vo.Bo.</p>
                    <div class="signature-line"></div>
                    <p>L.A.E. Andrés Caudillo Rivero</p>
                    <p> Director de Administración </p>
                    <p> y Finanzas</p>
                </div>
                <div>
                    <p class="font-bold">Enterado</p>
                    <div class="signature-line"></div>
                    <p>{{ $comision->user->prof }} {{ $comision->user->name }}</p>
                </div>
            </div>
            <div class="text-center font-bold mt-8"> <p>Sello de Certificación</p>
            </div>

            <div class="text-right mt-16 text-xs text-gray-500">
                <p>Camino Real de la Plata No. 336</p>
                <p>Zona Plateada, Pachuca de Soto, Hgo. C.P. 42084</p>
                <p>Ofic: 771 715 8390 y 771 715 8391</p>
                <p>ceaa.hidalgo.gob.mx</p>
            </div>
        </div>
        
    </div>
</body>
</html>