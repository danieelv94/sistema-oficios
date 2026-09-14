<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pnt\Procedimiento;
use App\Models\Pnt\Licitante;
use App\Models\Pnt\Cotizacion;
use App\Models\Pnt\JuntaParticipante;
use App\Models\Pnt\JuntaServidor;
use App\Models\Pnt\Beneficiario;
use App\Models\Pnt\Partida;
use App\Models\Pnt\Convenio;

class PntSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Procedimiento 1: Adquisición de Equipo de Cómputo (Licitación Pública)
        $proc1 = Procedimiento::create([
            'ejercicio' => 2026,
            'periodo_inicio' => '2026-01-01',
            'periodo_fin' => '2026-03-31',
            'tipo_procedimiento' => 'Licitación pública',
            'tipo_contratacion' => 'Adquisiciones',
            'caracter_procedimiento' => 'Nacional',
            'numero_expediente' => 'CEAA-LP-001-2026',
            'declarado_desierto' => 'No',
            'fundamentos_legales' => 'Artículo 26 fracción I, 28 y 30 de la Ley de Adquisiciones, Arrendamientos y Servicios del Sector Público.',
            'suficiencia_presupuestal_url' => 'https://example.com/pnt/suficiencia-presupuestal-001.pdf',
            'convocatoria_url' => 'https://example.com/pnt/convocatoria-001.pdf',
            'fecha_convocatoria' => '2026-01-15',
            'descripcion_bienes' => 'Adquisición de computadoras de escritorio, laptops y servidores para el personal de la institución.',
            'fecha_junta_aclaraciones' => '2026-01-22',
            'acta_junta_url' => 'https://example.com/pnt/acta-junta-001.pdf',
            'acta_apertura_url' => 'https://example.com/pnt/acta-apertura-001.pdf',
            'dictamen_fallo_url' => 'https://example.com/pnt/dictamen-fallo-001.pdf',
            'acta_fallo_url' => 'https://example.com/pnt/acta-fallo-001.pdf',
            'ganador_fisico_nombre' => null,
            'ganador_fisico_primer_apellido' => null,
            'ganador_fisico_segundo_apellido' => null,
            'ganador_fisico_sexo' => null,
            'proveedor_ganador_nombre' => 'TECNOLOGÍAS DE INFORMACIÓN DE MÉXICO S.A. DE C.V.',
            'proveedor_ganador_rfc' => 'TIM081015AA1',
            'proveedor_ganador_domicilio' => 'Av. de la Reforma 505, Col. Cuauhtémoc, Ciudad de México, CP 06500',
            'monto_contrato_min' => 1250000.00,
            'monto_contrato_max' => 1500000.00,
            'fecha_inicio_contrato' => '2026-02-10',
            'fecha_fin_contrato' => '2026-12-31',
            'forma_pago' => 'Transferencia electrónica a la entrega a entera satisfacción.',
            'objeto_contrato' => 'Adquisición de equipo de cómputo para la modernización tecnológica.',
            'justificacion_adjudicacion' => 'Proveedor que cumple con todas las especificaciones técnicas y ofrece el menor precio.',
            'fecha_contrato' => '2026-02-05',
            'tipo_cambio' => 1.0000,
            'monto_garantias' => 150000.00,
            'contrato_url' => 'https://example.com/pnt/contrato-001.pdf',
            'comunicado_suspension_url' => null,
            'ejecucion_obra' => 'No',
            'origen_recursos' => 'Federales',
            'fuente_financiamiento' => 'Fondo de Aportaciones para la Seguridad Pública',
            'tipo_fondo' => 'Fondo de Aportaciones',
            'lugar_ejecucion' => 'Oficinas Centrales de la CEAA, Pachuca, Hidalgo',
            'descripcion_obra' => null,
            'impacto_ambiental_url' => null,
            'observaciones_obra' => null,
            'etapa_obra' => null,
            'mecanismos_vigilancia' => null,
            'informe_avances_fisicos_url' => null,
            'informe_avances_financieros_url' => null,
            'acta_recepcion_url' => null,
            'finiquito_url' => null,
            'factura_url' => null,
            'observaciones' => 'Ninguna.',
        ]);

        $proc1->licitantes()->createMany([
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'TECNOLOGÍAS DE INFORMACIÓN DE MÉXICO S.A. DE C.V.', 'rfc' => 'TIM081015AA1'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'COMPUTADORAS DEL CENTRO S.A. DE C.V.', 'rfc' => 'CCE990420BB2'],
            ['primer_nombre' => 'JORGE', 'primer_apellido' => 'RAMIREZ', 'segundo_apellido' => 'GOMEZ', 'sexo' => 'Hombre', 'razon_social' => null, 'rfc' => 'RAGJ820311XX1'],
        ]);

        $proc1->cotizaciones()->createMany([
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'TECNOLOGÍAS DE INFORMACIÓN DE MÉXICO S.A. DE C.V.', 'rfc' => 'TIM081015AA1'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'COMPUTADORAS DEL CENTRO S.A. DE C.V.', 'rfc' => 'CCE990420BB2'],
        ]);

        $proc1->juntaParticipantes()->createMany([
            ['primer_nombre' => 'JUAN', 'primer_apellido' => 'PEREZ', 'segundo_apellido' => 'LOPEZ', 'sexo' => 'Hombre', 'razon_social' => 'TECNOLOGÍAS DE INFORMACIÓN DE MÉXICO S.A. DE C.V.', 'rfc' => 'PELJ750912YY1'],
            ['primer_nombre' => 'MARIA', 'primer_apellido' => 'HERNANDEZ', 'segundo_apellido' => 'DIAZ', 'sexo' => 'Mujer', 'razon_social' => 'COMPUTADORAS DEL CENTRO S.A. DE C.V.', 'rfc' => 'HEDM880101ZZ2'],
        ]);

        $proc1->juntaServidores()->createMany([
            ['primer_nombre' => 'CARLOS', 'primer_apellido' => 'GONZALEZ', 'segundo_apellido' => 'SANCHEZ', 'sexo' => 'Hombre', 'rfc' => 'GOSC801123UU3', 'cargo' => 'Director de Administración'],
            ['primer_nombre' => 'ANA', 'primer_apellido' => 'MARTINEZ', 'segundo_apellido' => 'CRUZ', 'sexo' => 'Mujer', 'rfc' => 'MACA850415VV4', 'cargo' => 'Jefa de Compras'],
        ]);

        $proc1->partidas()->createMany([
            ['numero_partida' => '51501 - Equipo de Cómputo y de Tecnologías de la Información'],
        ]);


        // Procedimiento 2: Rehabilitación de Sistema de Agua Potable (Invitación a cuando menos 3 personas)
        $proc2 = Procedimiento::create([
            'ejercicio' => 2026,
            'periodo_inicio' => '2026-04-01',
            'periodo_fin' => '2026-06-30',
            'tipo_procedimiento' => 'Invitación a cuando menos tres personas',
            'tipo_contratacion' => 'Obra pública',
            'caracter_procedimiento' => 'Nacional',
            'numero_expediente' => 'CEAA-IR-005-2026',
            'declarado_desierto' => 'No',
            'fundamentos_legales' => 'Artículo 43 de la Ley de Obras Públicas y Servicios Relacionados con las Mismas.',
            'suficiencia_presupuestal_url' => 'https://example.com/pnt/suficiencia-presupuestal-005.pdf',
            'convocatoria_url' => 'https://example.com/pnt/invitacion-005.pdf',
            'fecha_convocatoria' => '2026-04-10',
            'descripcion_bienes' => 'Rehabilitación y ampliación de la red de distribución de agua potable en la localidad de El Tezontle.',
            'fecha_junta_aclaraciones' => '2026-04-18',
            'acta_junta_url' => 'https://example.com/pnt/acta-junta-005.pdf',
            'acta_apertura_url' => 'https://example.com/pnt/acta-apertura-005.pdf',
            'dictamen_fallo_url' => 'https://example.com/pnt/dictamen-fallo-005.pdf',
            'acta_fallo_url' => 'https://example.com/pnt/acta-fallo-005.pdf',
            'ganador_fisico_nombre' => 'RICARDO',
            'ganador_fisico_primer_apellido' => 'MENDOZA',
            'ganador_fisico_segundo_apellido' => 'GALVAN',
            'ganador_fisico_sexo' => 'Hombre',
            'proveedor_ganador_nombre' => 'RICARDO MENDOZA GALVAN',
            'proveedor_ganador_rfc' => 'MEGR780512KK1',
            'proveedor_ganador_domicilio' => 'Calle Francisco I. Madero 102, Col. Centro, Pachuca de Soto, Hidalgo, CP 42000',
            'monto_contrato_min' => 2450000.00,
            'monto_contrato_max' => 2450000.00,
            'fecha_inicio_contrato' => '2026-05-02',
            'fecha_fin_contrato' => '2026-09-30',
            'forma_pago' => 'Mediante estimaciones mensuales por conceptos de trabajo ejecutados.',
            'objeto_contrato' => 'Rehabilitación y ampliación de red de agua potable.',
            'justificacion_adjudicacion' => 'Presenta propuesta técnica y económica solvente que garantiza el cumplimiento de la obra.',
            'fecha_contrato' => '2026-04-28',
            'tipo_cambio' => 1.0000,
            'monto_garantias' => 245000.00,
            'contrato_url' => 'https://example.com/pnt/contrato-005.pdf',
            'comunicado_suspension_url' => null,
            'ejecucion_obra' => 'Sí',
            'origen_recursos' => 'Estatal',
            'fuente_financiamiento' => 'Fondo de Infraestructura Social para las Entidades (FISE)',
            'tipo_fondo' => 'FISE 2026',
            'lugar_ejecucion' => 'Localidad El Tezontle, Municipio de Pachuca de Soto, Hidalgo',
            'descripcion_obra' => 'Instalación de 1,200 metros de tubería de PVC de 3 pulgadas, interconexión a tanque de distribución y 45 tomas domiciliarias.',
            'impacto_ambiental_url' => 'https://example.com/pnt/impacto-ambiental-005.pdf',
            'observaciones_obra' => null,
            'etapa_obra' => 'En proceso',
            'mecanismos_vigilancia' => 'Contraloría Social del Municipio y Comité de Obra de la Localidad.',
            'informe_avances_fisicos_url' => 'https://example.com/pnt/avance-fisico-005-junio.pdf',
            'informe_avances_financieros_url' => 'https://example.com/pnt/avance-financiero-005-junio.pdf',
            'acta_recepcion_url' => null,
            'finiquito_url' => null,
            'factura_url' => null,
            'observaciones' => 'Avance físico al 60% al corte de junio.',
        ]);

        $proc2->licitantes()->createMany([
            ['primer_nombre' => 'RICARDO', 'primer_apellido' => 'MENDOZA', 'segundo_apellido' => 'GALVAN', 'sexo' => 'Hombre', 'razon_social' => null, 'rfc' => 'MEGR780512KK1'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'CONSTRUCTORA HIDALGUENSE S.A. DE C.V.', 'rfc' => 'CHI920701AA3'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'INGENIERÍA Y DISEÑO HÍDRICO S.A. DE C.V.', 'rfc' => 'IDH051210BB4'],
        ]);

        $proc2->cotizaciones()->createMany([
            ['primer_nombre' => 'RICARDO', 'primer_apellido' => 'MENDOZA', 'segundo_apellido' => 'GALVAN', 'sexo' => 'Hombre', 'razon_social' => null, 'rfc' => 'MEGR780512KK1'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'CONSTRUCTORA HIDALGUENSE S.A. DE C.V.', 'rfc' => 'CHI920701AA3'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'INGENIERÍA Y DISEÑO HÍDRICO S.A. DE C.V.', 'rfc' => 'IDH051210BB4'],
        ]);

        $proc2->juntaParticipantes()->createMany([
            ['primer_nombre' => 'RICARDO', 'primer_apellido' => 'MENDOZA', 'segundo_apellido' => 'GALVAN', 'sexo' => 'Hombre', 'razon_social' => null, 'rfc' => 'MEGR780512KK1'],
            ['primer_nombre' => 'LUIS', 'primer_apellido' => 'GARCIA', 'segundo_apellido' => 'VILLARREAL', 'sexo' => 'Hombre', 'razon_social' => 'CONSTRUCTORA HIDALGUENSE S.A. DE C.V.', 'rfc' => 'GAVL720815AA9'],
        ]);

        $proc2->juntaServidores()->createMany([
            ['primer_nombre' => 'SOFIA', 'primer_apellido' => 'DIAZ', 'segundo_apellido' => 'ROSAS', 'sexo' => 'Mujer', 'rfc' => 'DIRS911005GG9', 'cargo' => 'Residente de Obra'],
            ['primer_nombre' => 'HUGO', 'primer_apellido' => 'VALDEZ', 'segundo_apellido' => 'TAPIA', 'sexo' => 'Hombre', 'rfc' => 'VATH840302HH8', 'cargo' => 'Director de Infraestructura Hidráulica'],
        ]);

        $proc2->partidas()->createMany([
            ['numero_partida' => '61401 - División de Terrenos y Construcción de Obras de Urbanización'],
        ]);

        $proc2->convenios()->createMany([
            ['numero_convenio' => 'CEAA-IR-005-2026-CONV01', 'objeto' => 'Ampliación de plazo y adición de metas sin incremento en costo.', 'monto_modificado' => 2450000.00, 'fecha_firma' => '2026-08-15'],
        ]);


        // Procedimiento 3: Adquisición de Papelería y Útiles de Oficina (Adjudicación Directa)
        $proc3 = Procedimiento::create([
            'ejercicio' => 2026,
            'periodo_inicio' => '2026-07-01',
            'periodo_fin' => '2026-09-30',
            'tipo_procedimiento' => 'Adjudicación directa',
            'tipo_contratacion' => 'Adquisiciones',
            'caracter_procedimiento' => 'Nacional',
            'numero_expediente' => 'CEAA-AD-012-2026',
            'declarado_desierto' => 'No',
            'fundamentos_legales' => 'Artículo 38 y 42 de la Ley de Adquisiciones, Arrendamientos y Servicios del Sector Público.',
            'suficiencia_presupuestal_url' => 'https://example.com/pnt/suficiencia-presupuestal-012.pdf',
            'convocatoria_url' => null,
            'fecha_convocatoria' => null,
            'descripcion_bienes' => 'Adquisición de consumibles de papelería, hojas bond, carpetas, bolígrafos, engrapadoras para el tercer trimestre del año.',
            'fecha_junta_aclaraciones' => null,
            'acta_junta_url' => null,
            'acta_apertura_url' => null,
            'dictamen_fallo_url' => 'https://example.com/pnt/resolucion-adjudicacion-012.pdf',
            'acta_fallo_url' => null,
            'ganador_fisico_nombre' => null,
            'ganador_fisico_primer_apellido' => null,
            'ganador_fisico_segundo_apellido' => null,
            'ganador_fisico_sexo' => null,
            'proveedor_ganador_nombre' => 'PAPELERA EL ARCO IRIS S.A. DE C.V.',
            'proveedor_ganador_rfc' => 'PAI010320UU9',
            'proveedor_ganador_domicilio' => 'Blvd. Colosio Km. 4.5, Col. El Palmar, Pachuca de Soto, Hidalgo',
            'monto_contrato_min' => 85000.00,
            'monto_contrato_max' => 95000.00,
            'fecha_inicio_contrato' => '2026-07-10',
            'fecha_fin_contrato' => '2026-09-30',
            'forma_pago' => 'Pago único contra entrega.',
            'objeto_contrato' => 'Adquisición de consumibles de oficina y papelería.',
            'justificacion_adjudicacion' => 'Bajo costo, disponibilidad inmediata y cumplimiento de la normatividad de compras institucionales por debajo del umbral de licitación.',
            'fecha_contrato' => '2026-07-08',
            'tipo_cambio' => 1.0000,
            'monto_garantias' => 0.00,
            'contrato_url' => 'https://example.com/pnt/contrato-012.pdf',
            'comunicado_suspension_url' => null,
            'ejecucion_obra' => 'No',
            'origen_recursos' => 'Propios',
            'fuente_financiamiento' => 'Gasto Corriente',
            'tipo_fondo' => 'Presupuesto Ordinario',
            'lugar_ejecucion' => 'Almacén de la CEAA, Pachuca, Hidalgo',
            'descripcion_obra' => null,
            'impacto_ambiental_url' => null,
            'observaciones_obra' => null,
            'etapa_obra' => null,
            'mecanismos_vigilancia' => null,
            'informe_avances_fisicos_url' => null,
            'informe_avances_financieros_url' => null,
            'acta_recepcion_url' => null,
            'finiquito_url' => null,
            'factura_url' => null,
            'observaciones' => 'Entrega parcial completada.',
        ]);

        $proc3->licitantes()->createMany([
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'PAPELERA EL ARCO IRIS S.A. DE C.V.', 'rfc' => 'PAI010320UU9'],
        ]);

        $proc3->cotizaciones()->createMany([
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'PAPELERA EL ARCO IRIS S.A. DE C.V.', 'rfc' => 'PAI010320UU9'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'DISTRIBUIDORA DE PAPELERÍA INDUSTRIAL S.A.', 'rfc' => 'DPI950612XX2'],
        ]);

        $proc3->partidas()->createMany([
            ['numero_partida' => '21101 - Materiales, Útiles y Equipos Menores de Oficina'],
        ]);


        // Procedimiento 4: Suministro de Combustibles y Lubricantes (Licitación Pública)
        $proc4 = Procedimiento::create([
            'ejercicio' => 2026,
            'periodo_inicio' => '2026-01-01',
            'periodo_fin' => '2026-12-31',
            'tipo_procedimiento' => 'Licitación pública',
            'tipo_contratacion' => 'Servicios',
            'caracter_procedimiento' => 'Nacional',
            'numero_expediente' => 'CEAA-LP-002-2026',
            'declarado_desierto' => 'No',
            'fundamentos_legales' => 'Artículo 26 fracción I, 28 y 30 de la LAASSP.',
            'suficiencia_presupuestal_url' => 'https://example.com/pnt/suficiencia-presupuestal-002.pdf',
            'convocatoria_url' => 'https://example.com/pnt/convocatoria-002.pdf',
            'fecha_convocatoria' => '2026-01-20',
            'descripcion_bienes' => 'Suministro de gasolina y diésel mediante monedero electrónico para el parque vehicular institucional.',
            'fecha_junta_aclaraciones' => '2026-01-28',
            'acta_junta_url' => 'https://example.com/pnt/acta-junta-002.pdf',
            'acta_apertura_url' => 'https://example.com/pnt/acta-apertura-002.pdf',
            'dictamen_fallo_url' => 'https://example.com/pnt/dictamen-fallo-002.pdf',
            'acta_fallo_url' => 'https://example.com/pnt/acta-fallo-002.pdf',
            'ganador_fisico_nombre' => null,
            'ganador_fisico_primer_apellido' => null,
            'ganador_fisico_segundo_apellido' => null,
            'ganador_fisico_sexo' => null,
            'proveedor_ganador_nombre' => 'EFECTIVALE S.A. DE C.V.',
            'proveedor_ganador_rfc' => 'EFE891012AB4',
            'proveedor_ganador_domicilio' => 'Av. Insurgentes Sur 1431, Mixcoac, Ciudad de México, CP 03910',
            'monto_contrato_min' => 3000000.00,
            'monto_contrato_max' => 5000000.00,
            'fecha_inicio_contrato' => '2026-02-15',
            'fecha_fin_contrato' => '2026-12-31',
            'forma_pago' => 'Depósito mensual conforme a los consumos reportados y autorizados.',
            'objeto_contrato' => 'Servicio de administración de vales y valeras electrónicas de combustible.',
            'justificacion_adjudicacion' => 'Mayor cobertura de estaciones de servicio y mejores condiciones comerciales en comisiones.',
            'fecha_contrato' => '2026-02-10',
            'tipo_cambio' => 1.0000,
            'monto_garantias' => 500000.00,
            'contrato_url' => 'https://example.com/pnt/contrato-002.pdf',
            'comunicado_suspension_url' => null,
            'ejecucion_obra' => 'No',
            'origen_recursos' => 'Estatal/Federal',
            'fuente_financiamiento' => 'Recursos Propios y Participaciones Federales',
            'tipo_fondo' => 'Gasto de Operación',
            'lugar_ejecucion' => 'Toda la entidad federativa de Hidalgo',
            'descripcion_obra' => null,
            'impacto_ambiental_url' => null,
            'observaciones_obra' => null,
            'etapa_obra' => null,
            'mecanismos_vigilancia' => null,
            'informe_avances_fisicos_url' => null,
            'informe_avances_financieros_url' => null,
            'acta_recepcion_url' => null,
            'finiquito_url' => null,
            'factura_url' => null,
            'observaciones' => null,
        ]);

        $proc4->licitantes()->createMany([
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'EFECTIVALE S.A. DE C.V.', 'rfc' => 'EFE891012AB4'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'EDENRED MÉXICO S.A. DE C.V.', 'rfc' => 'EME940608CC1'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'TICKET CAR MÉXICO S.A.', 'rfc' => 'TCM021201DD3'],
        ]);

        $proc4->cotizaciones()->createMany([
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'EFECTIVALE S.A. DE C.V.', 'rfc' => 'EFE891012AB4'],
            ['primer_nombre' => null, 'primer_apellido' => null, 'segundo_apellido' => null, 'sexo' => null, 'razon_social' => 'EDENRED MÉXICO S.A. DE C.V.', 'rfc' => 'EME940608CC1'],
        ]);

        $proc4->juntaParticipantes()->createMany([
            ['primer_nombre' => 'RICARDO', 'primer_apellido' => 'HERNANDEZ', 'segundo_apellido' => 'SOSA', 'sexo' => 'Hombre', 'razon_social' => 'EFECTIVALE S.A. DE C.V.', 'rfc' => 'HESR770214JK9'],
            ['primer_nombre' => 'MARIA', 'primer_apellido' => 'RODRIGUEZ', 'segundo_apellido' => 'PASCUAL', 'sexo' => 'Mujer', 'razon_social' => 'EDENRED MÉXICO S.A. DE C.V.', 'rfc' => 'ROPM840901LM2'],
        ]);

        $proc4->juntaServidores()->createMany([
            ['primer_nombre' => 'CARLOS', 'primer_apellido' => 'GONZALEZ', 'segundo_apellido' => 'SANCHEZ', 'sexo' => 'Hombre', 'rfc' => 'GOSC801123UU3', 'cargo' => 'Director de Administración'],
            ['primer_nombre' => 'LUIS', 'primer_apellido' => 'FERNANDEZ', 'segundo_apellido' => 'RAMIREZ', 'sexo' => 'Hombre', 'rfc' => 'FERL880312XX1', 'cargo' => 'Auxiliar Administrativo de Recursos Materiales'],
        ]);

        $proc4->partidas()->createMany([
            ['numero_partida' => '26111 - Combustibles y Lubricantes para Vehículos Terrestres'],
        ]);
    }
}
