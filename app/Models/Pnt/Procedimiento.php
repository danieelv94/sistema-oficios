<?php

namespace App\Models\Pnt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedimiento extends Model
{
    use HasFactory;

    protected $table = 'pnt_procedimientos';

    protected $fillable = [
        'ejercicio',
        'periodo_inicio',
        'periodo_fin',
        'tipo_procedimiento',
        'tipo_contratacion',
        'caracter_procedimiento',
        'numero_expediente',
        'declarado_desierto',
        'fundamentos_legales',
        'suficiencia_presupuestal_url',
        'convocatoria_url',
        'fecha_convocatoria',
        'descripcion_bienes',
        'fecha_junta_aclaraciones',
        'acta_junta_url',
        'acta_apertura_url',
        'dictamen_fallo_url',
        'acta_fallo_url',
        'ganador_fisico_nombre',
        'ganador_fisico_primer_apellido',
        'ganador_fisico_segundo_apellido',
        'ganador_fisico_sexo',
        'proveedor_ganador_nombre',
        'proveedor_ganador_rfc',
        'proveedor_ganador_domicilio',
        'monto_contrato_min',
        'monto_contrato_max',
        'fecha_inicio_contrato',
        'fecha_fin_contrato',
        'forma_pago',
        'objeto_contrato',
        'justificacion_adjudicacion',
        'fecha_contrato',
        'tipo_cambio',
        'monto_garantias',
        'contrato_url',
        'comunicado_suspension_url',
        'ejecucion_obra',
        'origen_recursos',
        'fuente_financiamiento',
        'tipo_fondo',
        'lugar_ejecucion',
        'descripcion_obra',
        'impacto_ambiental_url',
        'observaciones_obra',
        'etapa_obra',
        'mecanismos_vigilancia',
        'informe_avances_fisicos_url',
        'informe_avances_financieros_url',
        'acta_recepcion_url',
        'finiquito_url',
        'factura_url',
        'observaciones',
    ];

    protected $casts = [
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
        'fecha_convocatoria' => 'date',
        'fecha_junta_aclaraciones' => 'date',
        'fecha_contrato' => 'date',
        'fecha_inicio_contrato' => 'date',
        'fecha_fin_contrato' => 'date',
        'monto_contrato_min' => 'decimal:2',
        'monto_contrato_max' => 'decimal:2',
        'tipo_cambio' => 'decimal:4',
        'monto_garantias' => 'decimal:2',
    ];

    public function licitantes()
    {
        return $this->hasMany(Licitante::class, 'pnt_procedimiento_id');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'pnt_procedimiento_id');
    }

    public function juntaParticipantes()
    {
        return $this->hasMany(JuntaParticipante::class, 'pnt_procedimiento_id');
    }

    public function juntaServidores()
    {
        return $this->hasMany(JuntaServidor::class, 'pnt_procedimiento_id');
    }

    public function beneficiarios()
    {
        return $this->hasMany(Beneficiario::class, 'pnt_procedimiento_id');
    }

    public function partidas()
    {
        return $this->hasMany(Partida::class, 'pnt_procedimiento_id');
    }

    public function convenios()
    {
        return $this->hasMany(Convenio::class, 'pnt_procedimiento_id');
    }
}
