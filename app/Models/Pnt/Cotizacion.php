<?php

namespace App\Models\Pnt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'pnt_cotizaciones';

    protected $fillable = [
        'pnt_procedimiento_id',
        'primer_nombre',
        'primer_apellido',
        'segundo_apellido',
        'sexo',
        'razon_social',
        'rfc',
    ];

    public function procedimiento()
    {
        return $this->belongsTo(Procedimiento::class, 'pnt_procedimiento_id');
    }
}
