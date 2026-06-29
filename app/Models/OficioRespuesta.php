<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OficioRespuesta extends Model
{
    protected $fillable = [
        'area_oficio_id',
        'subarea_oficio_id',
        'user_id',
        'tipo_respuesta',
        'mensaje',
        'archivo_evidencia'
    ];

    /**
     * La asignación de subdirección a la que pertenece esta respuesta.
     */
    public function subareaOficio()
    {
        return $this->belongsTo(SubareaOficio::class, 'subarea_oficio_id');
    }

    // Obtener la información del turno (pivote) sin usar un modelo inexistente
    public function getAreaOficioAttribute()
    {
        return \Illuminate\Support\Facades\DB::table('area_oficio')
            ->where('id', $this->area_oficio_id)
            ->first();
    }

    // Obtener el oficio correspondiente a través de la información del turno
    public function getOficioAttribute()
    {
        $pivot = $this->areaOficio;
        return $pivot ? \App\Models\Oficio::find($pivot->oficio_id) : null;
    }

    // Relación con el usuario que registra la respuesta
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
