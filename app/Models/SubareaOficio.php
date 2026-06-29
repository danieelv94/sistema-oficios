<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubareaOficio extends Model
{
    protected $table = 'subarea_oficio';

    protected $fillable = [
        'area_oficio_id',
        'subarea_id',
        'user_id',
        'instruccion',
        'estatus',
    ];

    /**
     * La subdirección asignada.
     */
    public function subarea()
    {
        return $this->belongsTo(Subarea::class);
    }

    /**
     * El usuario (personal operativo) asignado por el subdirector.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Las respuestas vinculadas a esta asignación de subdirección.
     */
    public function respuestas()
    {
        return $this->hasMany(OficioRespuesta::class, 'subarea_oficio_id');
    }

    /**
     * Obtener el registro padre area_oficio.
     */
    public function getAreaOficioAttribute()
    {
        return DB::table('area_oficio')->where('id', $this->area_oficio_id)->first();
    }

    /**
     * Obtener el oficio asociado a través de area_oficio.
     */
    public function getOficioAttribute()
    {
        $pivot = $this->areaOficio;
        return $pivot ? Oficio::find($pivot->oficio_id) : null;
    }
}
