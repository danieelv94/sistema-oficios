<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use NotificationChannels\WebPush\HasPushSubscriptions;


class User extends Authenticatable
{
    use Notifiable, HasPushSubscriptions;
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use HasApiTokens, HasFactory, Notifiable;

    protected $attributes = [
        'recibir_correos' => true,
    ];

    protected $fillable = [
        'name',
        'prof',
        'cargo',
        'email',
        'password',
        'role',
        'area_id',
        'subarea_id',
        'no_empleado',
        'nivel_id',
        'recibir_correos',
        'fecha_alta',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'recibir_correos' => 'boolean',
        'fecha_alta' => 'date',
    ];


    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function subarea()
    {
        return $this->belongsTo(Subarea::class);
    }

    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function avisos()
    {
        return $this->belongsToMany(Aviso::class)->withPivot('leido_at')->withTimestamps();
    }

    public function solicitudesVacaciones()
    {
        return $this->hasMany(SolicitudVacacion::class, 'user_id');
    }

    /**
     * Determina si el usuario puede editar una sección del formulario PNT.
     */
    public function canEditPntSection($section)
    {
        if ($this->role === 'admin') {
            return true;
        }

        if ($section === 1) {
            // Licitaciones: Habilitado para Dirección de Administración y Finanzas (4)
            return $this->area_id == 4;
        }

        if ($section === 2) {
            // Suministros/Recursos Materiales: Habilitado para la subdirección de Recursos Materiales (15)
            return $this->area_id == 4 && $this->subarea_id == 15;
        }

        if ($section === 3) {
            // Infraestructura / Áreas Técnicas: Habilitado para la Dirección de Infraestructura Hidráulica (8)
            return $this->area_id == 8;
        }

        return false;
    }
}
