<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Los oficios que han sido turnados a esta área.
     */
    public function oficios()
    {
        return $this->belongsToMany(Oficio::class, 'area_oficio')
                    ->withPivot('id', 'instruccion', 'user_id', 'estatus', 'folio_interno', 'consecutivo', 'anio')
                    ->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'area_proyecto');
    }

    public function subareas()
    {
        return $this->hasMany(Subarea::class);
    }
}