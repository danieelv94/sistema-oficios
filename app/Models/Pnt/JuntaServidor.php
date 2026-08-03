<?php

namespace App\Models\Pnt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuntaServidor extends Model
{
    use HasFactory;

    protected $table = 'pnt_junta_servidores';

    protected $fillable = [
        'pnt_procedimiento_id',
        'primer_nombre',
        'primer_apellido',
        'segundo_apellido',
        'sexo',
        'rfc',
        'cargo',
    ];

    public function procedimiento()
    {
        return $this->belongsTo(Procedimiento::class, 'pnt_procedimiento_id');
    }
}
