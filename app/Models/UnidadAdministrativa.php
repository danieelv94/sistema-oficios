<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadAdministrativa extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'proyecto_unidad_administrativa');
    }
}