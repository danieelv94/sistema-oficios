<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudVacacion extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_vacaciones';

    protected $fillable = [
        'user_id',
        'estatus',
        'observaciones',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fechas()
    {
        return $this->hasMany(SolicitudVacacionFecha::class, 'solicitud_vacacion_id');
    }
}
