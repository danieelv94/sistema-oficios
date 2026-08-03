<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pnt\Procedimiento;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PntController extends Controller
{
    /**
     * Display a listing of PNT procedures.
     */
    public function index()
    {
        $procedimientos = Procedimiento::orderBy('created_at', 'desc')->get();
        return view('pnt.index', compact('procedimientos'));
    }

    /**
     * Show the form for creating a new PNT procedure.
     */
    public function create()
    {
        // Solo Licitaciones (o Admin) puede iniciar el registro
        if (!Auth::user()->canEditPntSection(1)) {
            abort(403, 'No tienes permisos para crear un nuevo registro de Transparencia.');
        }

        return view('pnt.form');
    }

    /**
     * Store a newly created PNT procedure in database.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->canEditPntSection(1)) {
            abort(403, 'Acción no autorizada.');
        }

        $request->validate([
            'ejercicio' => 'required|integer|min:2000|max:2099',
            'periodo_inicio' => 'required|date',
            'periodo_fin' => 'required|date|after_or_equal:periodo_inicio',
            'tipo_procedimiento' => 'required|string',
            'tipo_contratacion' => 'required|string',
            'caracter_procedimiento' => 'required|string',
            'numero_expediente' => 'required|string|unique:pnt_procedimientos,numero_expediente',
            'declarado_desierto' => 'required|string',
            'fundamentos_legales' => 'required|string',
            'suficiencia_presupuestal_url' => 'nullable|url',
            'convocatoria_url' => 'nullable|url',
            'fecha_convocatoria' => 'nullable|date',
            'descripcion_bienes' => 'nullable|string',
            'fecha_junta_aclaraciones' => 'nullable|date',
            'acta_junta_url' => 'nullable|url',
            'acta_apertura_url' => 'nullable|url',
            'dictamen_fallo_url' => 'nullable|url',
            'acta_fallo_url' => 'nullable|url',
            'ganador_fisico_nombre' => 'nullable|string',
            'ganador_fisico_primer_apellido' => 'nullable|string',
            'ganador_fisico_segundo_apellido' => 'nullable|string',
            'ganador_fisico_sexo' => 'nullable|string',
        ]);

        $procedimiento = Procedimiento::create($request->only([
            'ejercicio', 'periodo_inicio', 'periodo_fin', 'tipo_procedimiento',
            'tipo_contratacion', 'caracter_procedimiento', 'numero_expediente',
            'declarado_desierto', 'fundamentos_legales', 'suficiencia_presupuestal_url',
            'convocatoria_url', 'fecha_convocatoria', 'descripcion_bienes',
            'fecha_junta_aclaraciones', 'acta_junta_url', 'acta_apertura_url',
            'dictamen_fallo_url', 'acta_fallo_url',
            'ganador_fisico_nombre', 'ganador_fisico_primer_apellido', 'ganador_fisico_segundo_apellido', 'ganador_fisico_sexo'
        ]));

        // Guardar subtablas de la Sección 1
        if ($request->has('licitantes')) {
            foreach ($request->input('licitantes') as $item) {
                if (!empty($item['primer_nombre']) || !empty($item['razon_social'])) {
                    $procedimiento->licitantes()->create($item);
                }
            }
        }

        if ($request->has('cotizaciones')) {
            foreach ($request->input('cotizaciones') as $item) {
                if (!empty($item['primer_nombre']) || !empty($item['razon_social'])) {
                    $procedimiento->cotizaciones()->create($item);
                }
            }
        }

        if ($request->has('junta_participantes')) {
            foreach ($request->input('junta_participantes') as $item) {
                if (!empty($item['primer_nombre']) || !empty($item['razon_social'])) {
                    $procedimiento->juntaParticipantes()->create($item);
                }
            }
        }

        if ($request->has('junta_servidores')) {
            foreach ($request->input('junta_servidores') as $item) {
                if (!empty($item['primer_nombre'])) {
                    $procedimiento->juntaServidores()->create($item);
                }
            }
        }

        return redirect()->route('pnt.index')->with('success', 'Registro de Transparencia creado correctamente.');
    }

    /**
     * Show the form for editing the specified PNT procedure.
     */
    public function edit(Procedimiento $procedimiento)
    {
        // Cargar relaciones para mostrarlas en los listados del formulario
        $procedimiento->load([
            'licitantes', 'cotizaciones', 'juntaParticipantes',
            'juntaServidores', 'beneficiarios', 'partidas', 'convenios'
        ]);

        return view('pnt.form', compact('procedimiento'));
    }

    /**
     * Update the specified PNT procedure in database.
     */
    public function update(Request $request, Procedimiento $procedimiento)
    {
        $user = Auth::user();

        // SECCIÓN 1: Licitaciones
        if ($user->canEditPntSection(1) && $request->has('ejercicio')) {
            $request->validate([
                'ejercicio' => 'required|integer|min:2000|max:2099',
                'periodo_inicio' => 'required|date',
                'periodo_fin' => 'required|date|after_or_equal:periodo_inicio',
                'tipo_procedimiento' => 'required|string',
                'tipo_contratacion' => 'required|string',
                'caracter_procedimiento' => 'required|string',
                'numero_expediente' => 'required|string|unique:pnt_procedimientos,numero_expediente,' . $procedimiento->id,
                'declarado_desierto' => 'required|string',
                'fundamentos_legales' => 'required|string',
                'suficiencia_presupuestal_url' => 'nullable|url',
                'convocatoria_url' => 'nullable|url',
                'fecha_convocatoria' => 'nullable|date',
                'descripcion_bienes' => 'nullable|string',
                'fecha_junta_aclaraciones' => 'nullable|date',
                'acta_junta_url' => 'nullable|url',
                'acta_apertura_url' => 'nullable|url',
                'dictamen_fallo_url' => 'nullable|url',
                'acta_fallo_url' => 'nullable|url',
                'ganador_fisico_nombre' => 'nullable|string',
                'ganador_fisico_primer_apellido' => 'nullable|string',
                'ganador_fisico_segundo_apellido' => 'nullable|string',
                'ganador_fisico_sexo' => 'nullable|string',
            ]);

            $procedimiento->update($request->only([
                'ejercicio', 'periodo_inicio', 'periodo_fin', 'tipo_procedimiento',
                'tipo_contratacion', 'caracter_procedimiento', 'numero_expediente',
                'declarado_desierto', 'fundamentos_legales', 'suficiencia_presupuestal_url',
                'convocatoria_url', 'fecha_convocatoria', 'descripcion_bienes',
                'fecha_junta_aclaraciones', 'acta_junta_url', 'acta_apertura_url',
                'dictamen_fallo_url', 'acta_fallo_url',
                'ganador_fisico_nombre', 'ganador_fisico_primer_apellido', 'ganador_fisico_segundo_apellido', 'ganador_fisico_sexo'
            ]));

            // Sincronizar subtablas de la Sección 1
            $procedimiento->licitantes()->delete();
            if ($request->has('licitantes')) {
                foreach ($request->input('licitantes') as $item) {
                    if (!empty($item['primer_nombre']) || !empty($item['razon_social'])) {
                        $procedimiento->licitantes()->create($item);
                    }
                }
            }

            $procedimiento->cotizaciones()->delete();
            if ($request->has('cotizaciones')) {
                foreach ($request->input('cotizaciones') as $item) {
                    if (!empty($item['primer_nombre']) || !empty($item['razon_social'])) {
                        $procedimiento->cotizaciones()->create($item);
                    }
                }
            }

            $procedimiento->juntaParticipantes()->delete();
            if ($request->has('junta_participantes')) {
                foreach ($request->input('junta_participantes') as $item) {
                    if (!empty($item['primer_nombre']) || !empty($item['razon_social'])) {
                        $procedimiento->juntaParticipantes()->create($item);
                    }
                }
            }

            $procedimiento->juntaServidores()->delete();
            if ($request->has('junta_servidores')) {
                foreach ($request->input('junta_servidores') as $item) {
                    if (!empty($item['primer_nombre'])) {
                        $procedimiento->juntaServidores()->create($item);
                    }
                }
            }
        }

        // SECCIÓN 2: Suministros / Recursos Materiales
        if ($user->canEditPntSection(2) && $request->has('proveedor_ganador_nombre')) {
            $request->validate([
                'proveedor_ganador_nombre' => 'required|string',
                'proveedor_ganador_rfc' => 'required|string',
                'proveedor_ganador_domicilio' => 'required|string',
                'monto_contrato_min' => 'required|numeric|min:0',
                'monto_contrato_max' => 'required|numeric|min:0|gte:monto_contrato_min',
                'fecha_inicio_contrato' => 'required|date',
                'fecha_fin_contrato' => 'required|date|after_or_equal:fecha_inicio_contrato',
                'forma_pago' => 'required|string',
                'objeto_contrato' => 'required|string',
                'justificacion_adjudicacion' => 'nullable|string',
                'fecha_contrato' => 'nullable|date',
                'tipo_cambio' => 'nullable|numeric|min:0',
                'monto_garantias' => 'nullable|numeric|min:0',
                'contrato_url' => 'nullable|url',
                'comunicado_suspension_url' => 'nullable|url',
            ]);

            $procedimiento->update($request->only([
                'proveedor_ganador_nombre', 'proveedor_ganador_rfc', 'proveedor_ganador_domicilio',
                'monto_contrato_min', 'monto_contrato_max', 'fecha_inicio_contrato',
                'fecha_fin_contrato', 'forma_pago', 'objeto_contrato',
                'justificacion_adjudicacion', 'fecha_contrato', 'tipo_cambio', 'monto_garantias',
                'contrato_url', 'comunicado_suspension_url'
            ]));

            // Sincronizar subtablas de la Sección 2
            $procedimiento->beneficiarios()->delete();
            if ($request->has('beneficiarios')) {
                foreach ($request->input('beneficiarios') as $item) {
                    if (!empty($item['primer_nombre'])) {
                        $procedimiento->beneficiarios()->create($item);
                    }
                }
            }

            $procedimiento->partidas()->delete();
            if ($request->has('partidas')) {
                foreach ($request->input('partidas') as $item) {
                    if (!empty($item['numero_partida'])) {
                        $procedimiento->partidas()->create($item);
                    }
                }
            }
        }

        // SECCIÓN 3: Infraestructura / Áreas Técnicas
        if ($user->canEditPntSection(3) && $request->has('ejecucion_obra')) {
            $request->validate([
                'ejecucion_obra' => 'required|string',
                'origen_recursos' => 'required|string',
                'fuente_financiamiento' => 'required|string',
                'lugar_ejecucion' => 'required|string',
                'etapa_obra' => 'required|string',
                'observaciones' => 'nullable|string',
                'tipo_fondo' => 'nullable|string',
                'descripcion_obra' => 'nullable|string',
                'impacto_ambiental_url' => 'nullable|url',
                'observaciones_obra' => 'nullable|string',
                'mecanismos_vigilancia' => 'nullable|string',
                'informe_avances_fisicos_url' => 'nullable|url',
                'informe_avances_financieros_url' => 'nullable|url',
                'acta_recepcion_url' => 'nullable|url',
                'finiquito_url' => 'nullable|url',
                'factura_url' => 'nullable|url',
            ]);

            $procedimiento->update($request->only([
                'ejecucion_obra', 'origen_recursos', 'fuente_financiamiento',
                'lugar_ejecucion', 'etapa_obra', 'observaciones',
                'tipo_fondo', 'descripcion_obra', 'impacto_ambiental_url', 'observaciones_obra',
                'mecanismos_vigilancia', 'informe_avances_fisicos_url', 'informe_avances_financieros_url',
                'acta_recepcion_url', 'finiquito_url', 'factura_url'
            ]));

            // Sincronizar subtablas de la Sección 3
            $procedimiento->convenios()->delete();
            if ($request->has('convenios')) {
                foreach ($request->input('convenios') as $item) {
                    if (!empty($item['numero_convenio'])) {
                        $procedimiento->convenios()->create($item);
                    }
                }
            }
        }

        return redirect()->route('pnt.index')->with('success', 'Registro de Transparencia actualizado correctamente.');
    }

    /**
     * Delete the specified procedure (Admin only).
     */
    public function destroy(Procedimiento $procedimiento)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acción no autorizada.');
        }

        $procedimiento->delete();
        return redirect()->route('pnt.index')->with('success', 'Registro de Transparencia eliminado.');
    }

    /**
     * Export all captured data into the official PNT Excel template.
     */
    public function export()
    {
        $templatePath = storage_path('app/templates/a69_f28_bCEAA.xlsx');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'La plantilla oficial de Excel no se encuentra en storage/app/templates/a69_f28_bCEAA.xlsx');
        }

        // Obtener todos los procedimientos con sus relaciones
        $procedimientos = Procedimiento::with([
            'licitantes', 'cotizaciones', 'juntaParticipantes',
            'juntaServidores', 'beneficiarios', 'partidas', 'convenios'
        ])->get();

        if ($procedimientos->isEmpty()) {
            return back()->with('error', 'No hay registros de transparencia para exportar.');
        }

        try {
            // Cargar plantilla con PhpSpreadsheet
            $spreadsheet = IOFactory::load($templatePath);
            
            // 1. Escribir en la hoja principal: Reporte de Formatos (Datos generales)
            $sheetMain = $spreadsheet->getSheetByName('Reporte de Formatos');
            if ($sheetMain) {
                $row = 9; // Primera fila disponible para datos
                foreach ($procedimientos as $p) {
                    // SECCIÓN 1: Licitaciones
                    $sheetMain->setCellValue("A{$row}", $p->ejercicio);
                    $sheetMain->setCellValue("B{$row}", $p->periodo_inicio ? $p->periodo_inicio->format('d/m/Y') : '');
                    $sheetMain->setCellValue("C{$row}", $p->periodo_fin ? $p->periodo_fin->format('d/m/Y') : '');
                    $sheetMain->setCellValue("D{$row}", $p->tipo_procedimiento);
                    $sheetMain->setCellValue("E{$row}", $p->tipo_contratacion);
                    $sheetMain->setCellValue("F{$row}", $p->caracter_procedimiento);
                    $sheetMain->setCellValue("G{$row}", $p->numero_expediente);
                    $sheetMain->setCellValue("H{$row}", $p->declarado_desierto);
                    $sheetMain->setCellValue("I{$row}", $p->fundamentos_legales);
                    $sheetMain->setCellValue("J{$row}", $p->suficiencia_presupuestal_url);
                    $sheetMain->setCellValue("L{$row}", $p->convocatoria_url);
                    $sheetMain->setCellValue("M{$row}", $p->fecha_convocatoria ? $p->fecha_convocatoria->format('d/m/Y') : '');
                    $sheetMain->setCellValue("N{$row}", $p->descripcion_bienes);
                    $sheetMain->setCellValue("P{$row}", $p->fecha_junta_aclaraciones ? $p->fecha_junta_aclaraciones->format('d/m/Y') : '');
                    $sheetMain->setCellValue("S{$row}", $p->acta_junta_url);
                    $sheetMain->setCellValue("T{$row}", $p->acta_apertura_url);
                    $sheetMain->setCellValue("U{$row}", $p->dictamen_fallo_url);
                    $sheetMain->setCellValue("V{$row}", $p->acta_fallo_url);
                    $sheetMain->setCellValue("W{$row}", $p->ganador_fisico_nombre);
                    $sheetMain->setCellValue("X{$row}", $p->ganador_fisico_primer_apellido);
                    $sheetMain->setCellValue("Y{$row}", $p->ganador_fisico_segundo_apellido);
                    $sheetMain->setCellValue("Z{$row}", $p->ganador_fisico_sexo);
                    
                    // Vinculaciones (Subtablas) Sección 1
                    $sheetMain->setCellValue("K{$row}", $p->id); // Licitantes (Tabla_579209)
                    $sheetMain->setCellValue("O{$row}", $p->id); // Ofertas/Cotizaciones (Tabla_579236)
                    $sheetMain->setCellValue("Q{$row}", $p->id); // Participantes Junta (Tabla_579237)
                    $sheetMain->setCellValue("R{$row}", $p->id); // Servidores Junta (Tabla_579238)
                            // SECCIÓN 2: Proveedor y Contrato
                    $sheetMain->setCellValue("AA{$row}", $p->proveedor_ganador_nombre);
                    $sheetMain->setCellValue("AB{$row}", $p->id); // Vinculación Beneficiarios (Tabla_579206)
                    $sheetMain->setCellValue("AC{$row}", $p->proveedor_ganador_rfc);
                    $sheetMain->setCellValue("AE{$row}", $p->proveedor_ganador_domicilio); // Vialidad/Domicilio completo
                    $sheetMain->setCellValue("AU{$row}", $p->justificacion_adjudicacion);
                    
                    $sheetMain->setCellValue("AV{$row}", 'Dirección General (CEAA), Dirección de Administración y Finanzas (CEAA).');
                    $sheetMain->setCellValue("AW{$row}", 'Dirección General (CEAA)');
                    $sheetMain->setCellValue("AX{$row}", 'Dirección de Administración y Finanzas (CEAA)');
                    
                    $sheetMain->setCellValue("AY{$row}", $p->numero_expediente); // ID contrato
                    $sheetMain->setCellValue("AZ{$row}", $p->fecha_contrato ? $p->fecha_contrato->format('d/m/Y') : '');
                    $sheetMain->setCellValue("BA{$row}", $p->fecha_inicio_contrato ? $p->fecha_inicio_contrato->format('d/m/Y') : '');
                    $sheetMain->setCellValue("BB{$row}", $p->fecha_fin_contrato ? $p->fecha_fin_contrato->format('d/m/Y') : '');
                    $sheetMain->setCellValue("BC{$row}", $p->monto_contrato_min);
                    $sheetMain->setCellValue("BD{$row}", $p->monto_contrato_max);
                    $sheetMain->setCellValue("BE{$row}", $p->monto_contrato_min);
                    $sheetMain->setCellValue("BF{$row}", $p->monto_contrato_max);
                    
                    $sheetMain->setCellValue("BG{$row}", 'Pesos Mexicanos');
                    $sheetMain->setCellValue("BH{$row}", $p->tipo_cambio);
                    $sheetMain->setCellValue("BI{$row}", $p->forma_pago);
                    $sheetMain->setCellValue("BJ{$row}", $p->objeto_contrato);
                    $sheetMain->setCellValue("BK{$row}", $p->monto_garantias);
                    $sheetMain->setCellValue("BL{$row}", $p->fecha_inicio_contrato ? $p->fecha_inicio_contrato->format('d/m/Y') : '');
                    $sheetMain->setCellValue("BM{$row}", $p->fecha_fin_contrato ? $p->fecha_fin_contrato->format('d/m/Y') : '');
                    $sheetMain->setCellValue("BN{$row}", $p->contrato_url);
                    $sheetMain->setCellValue("BO{$row}", $p->comunicado_suspension_url);
                    
                    // Vinculación Partidas Sección 2
                    $sheetMain->setCellValue("BP{$row}", $p->id); // Partidas (Tabla_579239)
 
                    // SECCIÓN 3: Obras y Convenios
                    $sheetMain->setCellValue("BQ{$row}", 'Estatales'); // Origen recursos
                    $sheetMain->setCellValue("BR{$row}", $p->origen_recursos ?: 'Recursos Propios'); // Origen recursos especificado
                    $sheetMain->setCellValue("BS{$row}", $p->fuente_financiamiento);
                    $sheetMain->setCellValue("BT{$row}", $p->lugar_ejecucion);
                    $sheetMain->setCellValue("BU{$row}", $p->descripcion_obra);
                    $sheetMain->setCellValue("BV{$row}", $p->impacto_ambiental_url ?: 'No se realizaron');
                    $sheetMain->setCellValue("BW{$row}", $p->observaciones_obra);
                    $sheetMain->setCellValue("BX{$row}", $p->etapa_obra);
                    $sheetMain->setCellValue("BY{$row}", $p->convenios->count() > 0 ? 'Sí' : 'No');
                    
                    // Vinculación Convenios Sección 3
                    $sheetMain->setCellValue("BZ{$row}", $p->id); // Convenios (Tabla_579240)
                    $sheetMain->setCellValue("CA{$row}", $p->mecanismos_vigilancia);
                    $sheetMain->setCellValue("CB{$row}", $p->informe_avances_fisicos_url);
                    $sheetMain->setCellValue("CC{$row}", $p->informe_avances_financieros_url);
                    $sheetMain->setCellValue("CD{$row}", $p->acta_recepcion_url);
                    $sheetMain->setCellValue("CE{$row}", $p->finiquito_url);
                    $sheetMain->setCellValue("CF{$row}", $p->factura_url);
                    
                    // Metadatos finales
                    $sheetMain->setCellValue("CG{$row}", 'Dirección de Administración y Finanzas (CEAA)');
                    $sheetMain->setCellValue("CH{$row}", now()->format('d/m/Y'));
                    $sheetMain->setCellValue("CI{$row}", $p->observaciones);
                    
                    $row++;
                }
            }

            // Licitantes (Tabla_579209)
            $sheetLicitantes = $spreadsheet->getSheetByName('Tabla_579209');
            if ($sheetLicitantes) {
                $rowLicitantes = 4;
                foreach ($procedimientos as $p) {
                    foreach ($p->licitantes as $lic) {
                        $sheetLicitantes->setCellValue("A{$rowLicitantes}", $p->id); // Link ID
                        $sheetLicitantes->setCellValue("B{$rowLicitantes}", $lic->primer_nombre);
                        $sheetLicitantes->setCellValue("C{$rowLicitantes}", $lic->primer_apellido);
                        $sheetLicitantes->setCellValue("D{$rowLicitantes}", $lic->segundo_apellido);
                        $sheetLicitantes->setCellValue("E{$rowLicitantes}", $lic->sexo);
                        $sheetLicitantes->setCellValue("F{$rowLicitantes}", $lic->razon_social);
                        $sheetLicitantes->setCellValue("G{$rowLicitantes}", $lic->rfc);
                        $rowLicitantes++;
                    }
                }
            }

            // Ofertas (Tabla_579236)
            $sheetOfertas = $spreadsheet->getSheetByName('Tabla_579236');
            if ($sheetOfertas) {
                $rowOfertas = 4;
                foreach ($procedimientos as $p) {
                    foreach ($p->cotizaciones as $cot) {
                        $sheetOfertas->setCellValue("A{$rowOfertas}", $p->id); // Link ID
                        $sheetOfertas->setCellValue("B{$rowOfertas}", $cot->primer_nombre);
                        $sheetOfertas->setCellValue("C{$rowOfertas}", $cot->primer_apellido);
                        $sheetOfertas->setCellValue("D{$rowOfertas}", $cot->segundo_apellido);
                        $sheetOfertas->setCellValue("E{$rowOfertas}", $cot->sexo);
                        $sheetOfertas->setCellValue("F{$rowOfertas}", $cot->razon_social);
                        $sheetOfertas->setCellValue("G{$rowOfertas}", $cot->rfc);
                        $rowOfertas++;
                    }
                }
            }

            // Participantes Junta (Tabla_579237)
            $sheetJuntaPart = $spreadsheet->getSheetByName('Tabla_579237');
            if ($sheetJuntaPart) {
                $rowJuntaPart = 4;
                foreach ($procedimientos as $p) {
                    foreach ($p->juntaParticipantes as $jp) {
                        $sheetJuntaPart->setCellValue("A{$rowJuntaPart}", $p->id); // Link ID
                        $sheetJuntaPart->setCellValue("B{$rowJuntaPart}", $jp->primer_nombre);
                        $sheetJuntaPart->setCellValue("C{$rowJuntaPart}", $jp->primer_apellido);
                        $sheetJuntaPart->setCellValue("D{$rowJuntaPart}", $jp->segundo_apellido);
                        $sheetJuntaPart->setCellValue("E{$rowJuntaPart}", $jp->sexo);
                        $sheetJuntaPart->setCellValue("F{$rowJuntaPart}", $jp->razon_social);
                        $sheetJuntaPart->setCellValue("G{$rowJuntaPart}", $jp->rfc);
                        $rowJuntaPart++;
                    }
                }
            }

            // Servidores Junta (Tabla_579238)
            $sheetJuntaServ = $spreadsheet->getSheetByName('Tabla_579238');
            if ($sheetJuntaServ) {
                $rowJuntaServ = 4;
                foreach ($procedimientos as $p) {
                    foreach ($p->juntaServidores as $js) {
                        $sheetJuntaServ->setCellValue("A{$rowJuntaServ}", $p->id); // Link ID
                        $sheetJuntaServ->setCellValue("B{$rowJuntaServ}", $js->primer_nombre);
                        $sheetJuntaServ->setCellValue("C{$rowJuntaServ}", $js->primer_apellido);
                        $sheetJuntaServ->setCellValue("D{$rowJuntaServ}", $js->segundo_apellido);
                        $sheetJuntaServ->setCellValue("E{$rowJuntaServ}", $js->sexo);
                        $sheetJuntaServ->setCellValue("F{$rowJuntaServ}", $js->rfc);
                        $sheetJuntaServ->setCellValue("G{$rowJuntaServ}", $js->cargo);
                        $rowJuntaServ++;
                    }
                }
            }

            // Beneficiarios (Tabla_579206)
            $sheetBeneficiarios = $spreadsheet->getSheetByName('Tabla_579206');
            if ($sheetBeneficiarios) {
                $rowBenef = 4;
                foreach ($procedimientos as $p) {
                    foreach ($p->beneficiarios as $b) {
                        $sheetBeneficiarios->setCellValue("A{$rowBenef}", $p->id); // Link ID
                        $sheetBeneficiarios->setCellValue("B{$rowBenef}", $b->primer_nombre);
                        $sheetBeneficiarios->setCellValue("C{$rowBenef}", $b->primer_apellido);
                        $sheetBeneficiarios->setCellValue("D{$rowBenef}", $b->segundo_apellido);
                        $rowBenef++;
                    }
                }
            }

            // Partidas (Tabla_579239)
            $sheetPartidas = $spreadsheet->getSheetByName('Tabla_579239');
            if ($sheetPartidas) {
                $rowPartidas = 4;
                foreach ($procedimientos as $p) {
                    foreach ($p->partidas as $part) {
                        $sheetPartidas->setCellValue("A{$rowPartidas}", $p->id); // Link ID
                        $sheetPartidas->setCellValue("B{$rowPartidas}", $part->numero_partida);
                        $rowPartidas++;
                    }
                }
            }

            // Convenios (Tabla_579240)
            $sheetConvenios = $spreadsheet->getSheetByName('Tabla_579240');
            if ($sheetConvenios) {
                $rowConvenios = 4;
                foreach ($procedimientos as $p) {
                    foreach ($p->convenios as $c) {
                        $sheetConvenios->setCellValue("A{$rowConvenios}", $p->id); // Link ID
                        $sheetConvenios->setCellValue("B{$rowConvenios}", $c->numero_convenio);
                        $sheetConvenios->setCellValue("C{$rowConvenios}", $c->objeto);
                        $sheetConvenios->setCellValue("D{$rowConvenios}", $c->monto_modificado);
                        $sheetConvenios->setCellValue("E{$rowConvenios}", $c->fecha_firma ? $c->fecha_firma->format('d/m/Y') : '');
                        $rowConvenios++;
                    }
                }
            }

            // Forzar descarga del archivo
            $filename = 'PNT_a69_f28_bCEAA_' . now()->format('Ymd_His') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el archivo Excel: ' . $e->getMessage());
        }
    }
}
