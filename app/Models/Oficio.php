<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oficio extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_oficio',
        'remitente',
        'municipio',
        'asunto',
        'fecha_recepcion',
        'estatus', // El estatus general del oficio
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