<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionVacacion extends Model
{
    use HasFactory;

    protected $table = 'configuracion_vacaciones';

    protected $fillable = [
        'anio',
        'periodo1_inicio',
        'periodo1_fin',
        'periodo2_inicio',
        'periodo2_fin',
    ];

    protected $casts = [
        'periodo1_inicio' => 'date',
        'periodo1_fin' => 'date',
        'periodo2_inicio' => 'date',
        'periodo2_fin' => 'date',
    ];
}
