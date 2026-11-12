<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\Aviso;
use App\Models\BitacoraActividad;
use App\Models\Comision;
use App\Models\ComisionOdso;
use App\Models\Oficio;
use App\Models\OficioFirmaDg;
use App\Models\OficioHistorial;
use App\Models\OficioRespuesta;
use App\Models\PersonalIncidencia;
use App\Models\Proyecto;
use App\Models\SolicitudVacacion;
use App\Models\Subarea;
use App\Models\SubareaOficio;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        $this->registrarEventosAuditoria();
    }

    /**
     * Registra automáticamente en la Bitácora de Actividades (Auditoría)
     * los inicios/cierres de sesión y las creaciones, ediciones y eliminaciones en los módulos del sistema.
     */
    private function registrarEventosAuditoria(): void
    {
        // 1. Eventos de Autenticación (Login / Logout)
        Event::listen(Login::class, function (Login $event) {
            if ($event->user instanceof User) {
                BitacoraActividad::registrar(
                    'Seguridad / Acceso',
                    'Inicio de Sesión',
                    "El usuario {$event->user->name} ({$event->user->email}) inició sesión en el sistema.",
                    $event->user,
                    null,
                    null,
                    $event->user
                );
            }
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user instanceof User) {
                BitacoraActividad::registrar(
                    'Seguridad / Acceso',
                    'Cierre de Sesión',
                    "El usuario {$event->user->name} ({$event->user->email}) cerró su sesión.",
                    $event->user,
                    null,
                    null,
                    $event->user
                );
            }
        });

        // 2. Modelos auditables del sistema y su nombre de módulo amigable
        $modelosAuditables = [
            Oficio::class => 'Oficios / Correspondencia',
            OficioRespuesta::class => 'Oficios / Respuestas',
            OficioHistorial::class => 'Oficios / Turnos e Historial',
            SubareaOficio::class => 'Oficios / Subáreas',
            OficioFirmaDg::class => 'Firma Director General',
            Comision::class => 'Comisiones y Viáticos',
            ComisionOdso::class => 'Comisiones ODSO',
            PersonalIncidencia::class => 'Personal CEAA / Kardex RH',
            SolicitudVacacion::class => 'Vacaciones',
            Aviso::class => 'Circulares / Avisos',
            Ticket::class => 'Tickets de Soporte',
            User::class => 'Usuarios y Personal',
            Area::class => 'Catálogo de Áreas',
            Subarea::class => 'Catálogo de Subáreas',
            Vehiculo::class => 'Catálogo de Vehículos',
            Proyecto::class => 'Catálogo de Proyectos',
        ];

        foreach ($modelosAuditables as $claseModelo => $nombreModulo) {
            if (!class_exists($claseModelo)) {
                continue;
            }

            // Al CREAR un registro
            $claseModelo::created(function (Model $modelo) use ($nombreModulo) {
                $etiqueta = self::etiquetaModelo($modelo);
                $descripcion = self::descripcionPersonalizada($modelo, 'Creación')
                    ?? "Creó registro en {$nombreModulo}: {$etiqueta}";

                BitacoraActividad::registrar(
                    $nombreModulo,
                    'Creación',
                    $descripcion,
                    $modelo,
                    null,
                    $modelo->getAttributes()
                );
            });

            // Al ACTUALIZAR un registro
            $claseModelo::updated(function (Model $modelo) use ($nombreModulo) {
                $cambios = $modelo->getChanges();
                unset($cambios['updated_at'], $cambios['remember_token']);

                if (empty($cambios)) {
                    return;
                }

                $anteriores = [];
                foreach (array_keys($cambios) as $campo) {
                    $anteriores[$campo] = $modelo->getOriginal($campo);
                }

                $etiqueta = self::etiquetaModelo($modelo);
                $accion = isset($cambios['estatus']) || isset($cambios['status']) ? 'Cambio de Estatus' : 'Actualización';
                $descripcion = self::descripcionPersonalizada($modelo, $accion, $anteriores, $cambios)
                    ?? "Actualizó registro en {$nombreModulo}: {$etiqueta} (Campos: " . implode(', ', array_keys($cambios)) . ")";

                BitacoraActividad::registrar(
                    $nombreModulo,
                    $accion,
                    $descripcion,
                    $modelo,
                    $anteriores,
                    $cambios
                );
            });

            // Al ELIMINAR un registro
            $claseModelo::deleted(function (Model $modelo) use ($nombreModulo) {
                $etiqueta = self::etiquetaModelo($modelo);
                $descripcion = self::descripcionPersonalizada($modelo, 'Eliminación')
                    ?? "Eliminó registro en {$nombreModulo}: {$etiqueta}";

                BitacoraActividad::registrar(
                    $nombreModulo,
                    'Eliminación',
                    $descripcion,
                    $modelo,
                    $modelo->getOriginal(),
                    null
                );
            });
        }
    }

    /**
     * Genera una etiqueta legible para identificar el registro afectado.
     */
    private static function etiquetaModelo(Model $modelo): string
    {
        if ($modelo instanceof Oficio) {
            return "Oficio No. " . ($modelo->numero_oficio ?: "#{$modelo->id}") . " ({$modelo->asunto})";
        }
        if ($modelo instanceof OficioFirmaDg) {
            $folio = $modelo->numero_oficio ?: "Folio #{$modelo->id}";
            return "{$modelo->tipo_documento} {$folio} - {$modelo->asunto}";
        }
        if ($modelo instanceof Comision || $modelo instanceof ComisionOdso) {
            return "Comisión #" . ($modelo->consecutivo ?? $modelo->id) . " ({$modelo->lugar})";
        }
        if ($modelo instanceof PersonalIncidencia) {
            $fi = $modelo->fecha_inicio ? $modelo->fecha_inicio->format('d/m/Y') : '';
            $ff = $modelo->fecha_fin ? $modelo->fecha_fin->format('d/m/Y') : '';
            return strtoupper($modelo->categoria) . " ({$modelo->tipo}) del {$fi} al {$ff}";
        }
        if ($modelo instanceof User) {
            return "Usuario {$modelo->name} ({$modelo->email})";
        }
        if ($modelo instanceof Aviso) {
            return "Circular/Aviso: " . ($modelo->titulo ?? "#{$modelo->id}");
        }
        if ($modelo instanceof Ticket) {
            return "Ticket #{$modelo->id} ({$modelo->asunto})";
        }

        return class_basename($modelo) . " #" . $modelo->getKey();
    }

    /**
     * Genera descripciones aún más claras en español para casos clave como Firma DG, Oficios, etc.
     */
    private static function descripcionPersonalizada(Model $modelo, string $accion, array $anteriores = [], array $cambios = []): ?string
    {
        if ($modelo instanceof OficioFirmaDg) {
            $folio = $modelo->numero_oficio ?: "Folio #{$modelo->id}";
            $areaNombre = $modelo->area ? $modelo->area->name : 'Área';
            if ($accion === 'Creación') {
                return "Registró {$modelo->tipo_documento} ({$folio}) del área {$areaNombre} para firma del Director General. Asunto: {$modelo->asunto}";
            }
            if (isset($cambios['estatus'])) {
                if ($cambios['estatus'] === 'Firmado') {
                    return "Marcó como YA FIRMADO por el Director General el {$modelo->tipo_documento} ({$folio}) de {$areaNombre} (Listo para recoger).";
                }
                if ($cambios['estatus'] === 'Entregado') {
                    $quien = $modelo->recogido_por_nombre ?: 'personal del área';
                    return "Entregó el {$modelo->tipo_documento} firmado ({$folio}) de {$areaNombre}. Recogió: {$quien}.";
                }
                if ($cambios['estatus'] === 'Devuelto') {
                    $quien = $modelo->recogido_por_nombre ?: 'personal del área';
                    return "Devolvió con observaciones el {$modelo->tipo_documento} ({$folio}) de {$areaNombre}. Recogió: {$quien}.";
                }
                if ($cambios['estatus'] === 'Cancelado') {
                    $motivo = $modelo->motivo_cancelacion ?: 'Sin motivo especificado';
                    return "Canceló el {$modelo->tipo_documento} ({$folio}) de {$areaNombre}. Motivo: {$motivo}.";
                }
            }
        }

        return null;
    }
}
