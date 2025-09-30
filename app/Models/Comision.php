<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comision extends Model
{
    use HasFactory;
    protected $guarded = []; // Esto ya permite todos los campos

    public function user() { return $this->belongsTo(User::class); }
    public function jefeArea() { return $this->belongsTo(User::class, 'jefe_area_id'); }
    public function vehiculo() { return $this->belongsTo(Vehiculo::class); }
    public function proyecto() { return $this->belongsTo(Proyecto::class); }
    public function unidadAdministrativa()
{
    return $this->belongsTo(UnidadAdministrativa::class);
}
}