<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudVacacionFecha extends Model
{
    use HasFactory;

    protected $table = 'solicitud_vacacion_fechas';

    protected $fillable = [
        'solicitud_vacacion_id',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudVacacion::class, 'solicitud_vacacion_id');
    }
}
