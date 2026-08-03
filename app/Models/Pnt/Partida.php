<?php

namespace App\Models\Pnt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    use HasFactory;

    protected $table = 'pnt_partidas';

    protected $fillable = [
        'pnt_procedimiento_id',
        'numero_partida',
    ];

    public function procedimiento()
    {
        return $this->belongsTo(Procedimiento::class, 'pnt_procedimiento_id');
    }
}
