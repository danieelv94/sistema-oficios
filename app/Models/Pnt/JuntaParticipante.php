<?php

namespace App\Models\Pnt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuntaParticipante extends Model
{
    use HasFactory;

    protected $table = 'pnt_junta_participantes';

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
