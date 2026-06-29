<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;
use App\Models\User;

$user = User::find(221);
$oficio = Oficio::with('areas')->find(245);

echo "User Role: " . $user->role . "\n";
echo "User Area: " . $user->area_id . "\n";

// Let's check how turnosParaMostrar is filtered
        if (in_array($user->role, ['admin', 'recepcionista', 'correspondencia']) || ($user->role == 'jefe_area' && $user->area_id == 2)) {
            $turnosParaMostrar = $oficio->areas;
        } elseif ($user->role == 'jefe_area' || $user->role == 'secretaria_area') {
            $turnosParaMostrar = $oficio->areas->where('id', $user->area_id);
        } elseif ($user->role === 'subdirector') {
            $turnosParaMostrar = $oficio->areas->where('id', $user->area_id)->filter(function ($area) use ($user) {
                if ($area->pivot->user_id == $user->id) {
                    return true;
                }
                $assignedUser = \App\Models\User::find($area->pivot->user_id);
                return $assignedUser && $assignedUser->subarea_id == $user->subarea_id;
            });
        } else {
            $turnosParaMostrar = $oficio->areas->filter(function ($area) use ($user) {
                return $area->pivot->user_id == $user->id;
            });
        }

echo "Turnos Para Mostrar count: " . $turnosParaMostrar->count() . "\n";
foreach($turnosParaMostrar as $a) {
    echo "  - Area: {$a->name}, Pivot user_id: " . ($a->pivot->user_id ?? 'None') . ", status: " . $a->pivot->estatus . "\n";
}

$mode = in_array($user->role, ['admin', 'recepcionista', 'correspondencia']) ? 'recepcion' : (in_array($user->role, ['jefe_area', 'secretaria_area', 'subdirector']) ? 'gestion' : 'operativo');
echo "Mode: " . $mode . "\n";
