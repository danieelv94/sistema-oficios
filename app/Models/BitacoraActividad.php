<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

class BitacoraActividad extends Model
{
    use HasFactory;

    protected $table = 'bitacora_actividades';

    protected $fillable = [
        'user_id',
        'user_nombre',
        'user_role',
        'area_nombre',
        'modulo',
        'accion',
        'descripcion',
        'modelo_type',
        'modelo_id',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Registra un evento en la bitácora de auditoría de forma segura.
     */
    public static function registrar(
        string $modulo,
        string $accion,
        string $descripcion,
        ?Model $modelo = null,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null,
        ?User $usuarioExplicito = null
    ): void {
        try {
            if (!Schema::hasTable('bitacora_actividades')) {
                return;
            }

            $user = $usuarioExplicito ?? Auth::user();

            // Filtrar campos sensibles
            $ocultar = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];
            if (is_array($datosAnteriores)) {
                $datosAnteriores = array_diff_key($datosAnteriores, array_flip($ocultar));
            }
            if (is_array($datosNuevos)) {
                $datosNuevos = array_diff_key($datosNuevos, array_flip($ocultar));
            }

            self::create([
                'user_id' => $user ? $user->id : null,
                'user_nombre' => $user ? trim(($user->prof ? $user->prof . ' ' : '') . $user->name) : 'Sistema / No autenticado',
                'user_role' => $user ? $user->role : null,
                'area_nombre' => ($user && $user->area) ? $user->area->name : null,
                'modulo' => mb_substr($modulo, 0, 80),
                'accion' => mb_substr($accion, 0, 60),
                'descripcion' => mb_substr($descripcion, 0, 500),
                'modelo_type' => $modelo ? class_basename($modelo) : null,
                'modelo_id' => $modelo ? $modelo->getKey() : null,
                'datos_anteriores' => !empty($datosAnteriores) ? $datosAnteriores : null,
                'datos_nuevos' => !empty($datosNuevos) ? $datosNuevos : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent() ? mb_substr(Request::userAgent(), 0, 255) : null,
            ]);
        } catch (\Throwable $e) {
            // No interrumpir el flujo principal si ocurre un error de auditoría
        }
    }
}
