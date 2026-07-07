<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Comision;
use App\Models\Subarea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== Test 1: Setting up mock users and commission ===\n";

// 1. Create a commission
$comision = Comision::create([
    'consecutivo' => 999,
    'anio' => 2026,
    'oficio_numero' => 'TEST-OC-99/2026',
    'user_id' => 1,
    'jefe_area_id' => 1,
    'dias_comision' => '15 de mayo',
    'hora_inicio' => '08:00',
    'hora_fin' => '18:00',
    'actividad' => 'Reunión de vinculación',
    'lugar' => 'Tulancingo',
]);

echo "Initial entregado_acuse is false? " . ($comision->entregado_acuse === false ? 'SÍ ✅' : 'NO ❌') . "\n";

// 2. Create RH user
$subareaRH = Subarea::where('prefijo', 'SRH')->first();
if (!$subareaRH) {
    $subareaRH = Subarea::create([
        'name' => 'Subdirección de Recursos Humanos',
        'area_id' => 4,
        'prefijo' => 'SRH',
    ]);
}

$rhUser = User::create([
    'name' => 'RH Staff Member',
    'email' => 'rh_staff_' . uniqid() . '@ceaa.gob.mx',
    'password' => bcrypt('password'),
    'role' => 'user',
    'area_id' => 4,
    'subarea_id' => $subareaRH->id,
]);

// 3. Create regular user
$regularUser = User::create([
    'name' => 'Regular Area User',
    'email' => 'regular_' . uniqid() . '@ceaa.gob.mx',
    'password' => bcrypt('password'),
    'role' => 'user',
    'area_id' => 2,
]);

$controller = new \App\Http\Controllers\ComisionController();

echo "\n=== Test 2: Access & Permission controls ===\n";

// A. Log in as regular user -> should fail to load index
Auth::login($regularUser);
try {
    $controller->recursosHumanosIndex(new Request());
    echo "Regular user index access: ALLOWED (FAIL) ❌\n";
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    echo "Regular user index access: DENIED with 403 (PASS) ✅\n";
}

// B. Log in as regular user -> should fail to toggle status
try {
    $request = new Request();
    $controller->toggleAcuse($request, $comision);
    echo "Regular user toggle access: ALLOWED (FAIL) ❌\n";
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    echo "Regular user toggle access: DENIED with 403 (PASS) ✅\n";
}

// C. Log in as RH staff user -> should succeed to load index
Auth::login($rhUser);
try {
    $response = $controller->recursosHumanosIndex(new Request());
    echo "RH staff user index access: ALLOWED (PASS) ✅\n";
} catch (\Exception $e) {
    echo "RH staff user index access: DENIED (FAIL) ❌ - Error: " . $e->getMessage() . "\n";
}

// D. Log in as RH staff user -> should succeed to toggle status
try {
    $request = new Request();
    $controller->toggleAcuse($request, $comision);
    $comision->refresh();
    echo "RH staff user toggle acuse (to checked): SUCCESS (PASS) ✅ - Status: " . ($comision->entregado_acuse ? 'Entregado' : 'Pendiente') . "\n";
} catch (\Exception $e) {
    echo "RH staff user toggle acuse (to checked): FAILED (FAIL) ❌ - Error: " . $e->getMessage() . "\n";
}

// E. Log in as RH staff user -> should FAIL to toggle back (deactivate)
try {
    $request = new Request();
    $controller->toggleAcuse($request, $comision);
    echo "RH staff user deactivate acuse: ALLOWED (FAIL) ❌\n";
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    echo "RH staff user deactivate acuse: DENIED with 403 (PASS) ✅ - Msg: " . $e->getMessage() . "\n";
}

// F. Log in as Admin -> should succeed to toggle back (deactivate)
$adminUser = User::where('role', 'admin')->first();
if ($adminUser) {
    Auth::login($adminUser);
    try {
        $request = new Request();
        $controller->toggleAcuse($request, $comision);
        $comision->refresh();
        echo "Admin user deactivate acuse: SUCCESS (PASS) ✅ - Status: " . ($comision->entregado_acuse ? 'Entregado' : 'Pendiente') . "\n";
    } catch (\Exception $e) {
        echo "Admin user deactivate acuse: FAILED (FAIL) ❌ - Error: " . $e->getMessage() . "\n";
    }
}

// Clean up
$comision->delete();
$rhUser->forceDelete();
$regularUser->forceDelete();
echo "\nTest database cleaned up successfully!\n";
