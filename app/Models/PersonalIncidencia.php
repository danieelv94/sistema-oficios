<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalIncidencia extends Model
{
    use HasFactory;

    protected $table = 'personal_incidencias';

    protected $fillable = [
        'user_id',
        'registrado_por',
        'categoria',
        'tipo',
        'folio',
        'institucion_medica',
        'fecha_inicio',
        'fecha_fin',
        'dias',
        'observaciones',
        'archivo_path',
        'estatus',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'dias' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por')->withTrashed();
    }

    /**
     * Determina si la incapacidad/incidencia se encuentra vigente el día de hoy.
     */
    public function isVigenteHoy(): bool
    {
        $hoy = now()->startOfDay();
        return $this->estatus === 'Activo'
            && $this->fecha_inicio
            && $this->fecha_fin
            && $hoy->betweenIncluded($this->fecha_inicio->startOfDay(), $this->fecha_fin->startOfDay());
    }
}

