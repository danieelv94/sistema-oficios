<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficioHistorial extends Model
{
    use HasFactory;

    protected $fillable = [
        'oficio_id',
        'user_id',
        'area_id',
        'subarea_id',
        'accion',
        'descripcion',
    ];

    public static function registrar($oficioId, $accion, $descripcion, $areaId = null, $subareaId = null)
    {
        return self::create([
            'oficio_id' => $oficioId,
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'area_id' => $areaId,
            'subarea_id' => $subareaId,
            'accion' => $accion,
            'descripcion' => $descripcion,
        ]);
    }

    public function oficio()
    {
        return $this->belongsTo(Oficio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function subarea()
    {
        return $this->belongsTo(Subarea::class);
    }
}
