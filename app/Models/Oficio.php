<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Oficio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero_oficio',
        'remitente',
        'municipio',
        'asunto',
        'estatus',
        'fecha_recepcion',
        'tipo_correspondencia',
        'prioridad',
        'numero_oficio_dependencia',
        'fecha_limite',
        'localidad',
        'observaciones',
    ];

    /**
     * Las áreas a las que este oficio fue turnado.
     */
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_oficio')
                    ->withPivot('id', 'instruccion', 'user_id', 'estatus') // Campos extra que queremos leer de la tabla pivote
                    ->withTimestamps();
    }
}