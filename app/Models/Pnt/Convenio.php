<?php

namespace App\Models\Pnt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    use HasFactory;

    protected $table = 'pnt_convenios';

    protected $fillable = [
        'pnt_procedimiento_id',
        'numero_convenio',
        'objeto',
        'monto_modificado',
        'fecha_firma',
    ];

    protected $casts = [
        'monto_modificado' => 'decimal:2',
        'fecha_firma' => 'date',
    ];

    public function procedimiento()
    {
        return $this->belongsTo(Procedimiento::class, 'pnt_procedimiento_id');
    }
}
