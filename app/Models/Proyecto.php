<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function unidadesAdministrativas()
    {
        return $this->belongsToMany(UnidadAdministrativa::class, 'proyecto_unidad_administrativa');
    }
}