<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficioFirmaDg extends Model
{
    use HasFactory;

    protected $table = 'oficios_firma_dg';

    protected $fillable = [
        'numero_oficio',
        'tipo_documento',
        'area_id',
        'entregado_por_user_id',
        'entregado_por_nombre',
        'destinatario',
        'asunto',
        'anexos',
        'prioridad',
        'fecha_recepcion',
        'estatus',
        'fecha_firma',
        'recogido_por_user_id',
        'recogido_por_nombre',
        'fecha_entrega',
        'observaciones',
        'motivo_cancelacion',
        'fecha_cancelacion',
        'registrado_por',
        'entregado_en_dg_por',
        'cancelado_por',
    ];

    protected $casts = [
        'fecha_recepcion' => 'datetime',
        'fecha_firma' => 'datetime',
        'fecha_entrega' => 'datetime',
        'fecha_cancelacion' => 'datetime',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function entregadoPor()
    {
        return $this->belongsTo(User::class, 'entregado_por_user_id')->withTrashed();
    }

    public function recogidoPor()
    {
        return $this->belongsTo(User::class, 'recogido_por_user_id')->withTrashed();
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por')->withTrashed();
    }

    public function entregadorDg()
    {
        return $this->belongsTo(User::class, 'entregado_en_dg_por')->withTrashed();
    }

    public function cancelador()
    {
        return $this->belongsTo(User::class, 'cancelado_por')->withTrashed();
    }
}

