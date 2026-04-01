<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',    // El ID del administrador o secretaria que crea el aviso
        'titulo',     // Asunto del mensaje
        'mensaje',    // Contenido de la circular
        'prioridad',  // 'Normal' o 'Urgente'
        'area_id',    // ID del área destino (null si es para toda la CEAA)
        'archivo',    // Archivo adjunto
    ];

    /**
     * Relación: El autor del aviso (quien lo redactó).
     */
    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: El área específica a la que va dirigida (si aplica).
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Relación Muchos a Muchos: Los usuarios que deben leer este aviso.
     * * Usamos 'withPivot' para poder acceder al campo 'leido_at' 
     * de la tabla intermedia 'aviso_user'.
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'aviso_user')
            ->withPivot('leido_at')
            ->withTimestamps();
    }

    /**
     * Scope: Filtrar solo avisos urgentes.
     */
    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'Urgente');
    }

    /**
     * Helper: Verificar si un usuario específico ya leyó este aviso.
     */
    public function fueLeidoPor($userId)
    {
        return $this->usuarios()
            ->where('user_id', $userId)
            ->whereNotNull('leido_at')
            ->exists();
    }
}