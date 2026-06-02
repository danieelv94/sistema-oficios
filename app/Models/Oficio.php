<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OficioRespuesta;

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
        'pdf_path',
    ];

    /**
     * Las áreas a las que este oficio fue turnado.
     */
    public function areas()
    {
        // Cambiamos a string 'area_oficio' para que Laravel sepa que es una tabla
        return $this->belongsToMany(Area::class, 'area_oficio', 'oficio_id', 'area_id')
            ->withPivot('id', 'user_id', 'instruccion', 'estatus')
            ->withTimestamps();
    }

    public function respuestas()
    {
        // Obtenemos los IDs de la tabla pivote de manera segura usando DB::table
        $idsTurnos = \Illuminate\Support\Facades\DB::table('area_oficio')
            ->where('oficio_id', $this->id)
            ->pluck('id');

        // Retornamos la consulta directa al modelo de respuestas
        return \App\Models\OficioRespuesta::whereIn('area_oficio_id', $idsTurnos);
    }
}