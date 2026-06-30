<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;
use App\Models\User;
use App\Models\SubareaOficio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Find a correspondencia user
$correspondenciaUser = User::where('role', 'correspondencia')->first();
if (!$correspondenciaUser) {
    echo "No correspondencia user found, creating a temp one...\n";
    $correspondenciaUser = User::create([
        'name' => 'Temp Correspondencia',
        'email' => 'temp_corr@ceaa.gob.mx',
        'password' => bcrypt('password'),
        'role' => 'correspondencia',
        'area_id' => 1
    ]);
}

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
$oficio = Oficio::find(29);
if (!$oficio) {
    DB::table('oficios')->insert([
        'id' => 29,
        'numero_oficio' => '29-TEST',
        'remitente' => 'Test Remitente',
        'municipio' => 'Pachuca',
        'localidad' => 'Pachuca',
        'asunto' => 'Test Asunto Oficio 29',
        'tipo_correspondencia' => 'Externa',
        'fecha_recepcion' => date('Y-m-d'),
        'prioridad' => 'Ordinaria',
        'numero_oficio_dependencia' => 'DEP-29',
    ]);
    $oficio = Oficio::find(29);
}
$pivot = DB::table('area_oficio')->where('id', 41)->first();
if (!$pivot) {
    DB::table('area_oficio')->insert([
        'id' => 41,
        'oficio_id' => 29,
        'area_id' => 2,
        'estatus' => 'Turnado',
        'instruccion' => 'Test Instruccion 29',
        'consecutivo' => 29,
        'anio' => 2026,
    ]);
}
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "=== Test 1: Correspondencia user ({$correspondenciaUser->name}) printing Oficio 29 ===\n";
Auth::login($correspondenciaUser);

$controller = new \App\Http\Controllers\OficioController();

$request = new \Illuminate\Http\Request();
$response = $controller->generarOficio($request, $oficio);
$html = $response->render();

// The HTML should print all turnos of the oficio (not just area 1)
// Oficio 29 is turnado to Area 2 (Dirección de Gestión Institucional)
$hasArea2 = strpos($html, 'Dirección de Gestión Institucional') !== false;
echo "1. Correspondencia user sees Area 2 turnado? " . ($hasArea2 ? 'SÍ ✅' : 'NO ❌') . "\n";

// Let's create temporary subareas assignments on Oficio 29's pivot 41 to test print layout
$pivotId = 41;
SubareaOficio::where('area_oficio_id', $pivotId)->delete();

$subarea2 = SubareaOficio::create([
    'area_oficio_id' => $pivotId,
    'subarea_id' => 2,
    'user_id' => 220, // Let's say user 220
    'instruccion' => 'Atender urgente con el equipo de Informática',
    'estatus' => 'Asignado'
]);

$response = $controller->generarOficio($request, $oficio);
$html = $response->render();

$hasSubareaInPrint = strpos($html, 'Subdirección de Informática y Transparencia') !== false;
$hasAssignedUserInPrint = strpos($html, 'Atender urgente con el equipo de Informática') !== false;
$hasSignatureLine = strpos($html, 'Acuse de Recibido') !== false;

echo "2. Print view displays assigned subarea? " . ($hasSubareaInPrint ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "3. Print view displays subarea instruction? " . ($hasAssignedUserInPrint ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "4. Print view signature line is removed? " . (!$hasSignatureLine ? 'SÍ ✅' : 'NO ❌') . "\n";

// === Test 1b: Print view with MULTIPLE areas (should HIDE subareas/folios) ===
echo "\n=== Test 1b: Print view with MULTIPLE areas (should HIDE subareas/folios) ===\n";
// Insert temporary turn to Area 3 to make total count = 2
$tempTurnId = DB::table('area_oficio')->insertGetId([
    'oficio_id' => 29,
    'area_id' => 3,
    'instruccion' => 'Revisar y proceder',
    'estatus' => 'Turnado'
]);

$responseMulti = $controller->generarOficio($request, $oficio);
$htmlMulti = $responseMulti->render();

$hasSubareaInPrintMulti = strpos($htmlMulti, 'Subdirección de Informática y Transparencia') !== false;
$hasFolioInPrintMulti = strpos($htmlMulti, 'Folio:') !== false;

echo "1. Subareas are hidden on multi-area print? " . (!$hasSubareaInPrintMulti ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "2. Folio badges are hidden on multi-area print? " . (!$hasFolioInPrintMulti ? 'SÍ ✅' : 'NO ❌') . "\n";

// Delete temporary turn to Area 3
DB::table('area_oficio')->where('id', $tempTurnId)->delete();

// === Test 1c: Cancelled Turn test ===
echo "\n=== Test 1c: Cancelled Turn test (watermark and print exclusion) ===\n";
// Cancel the main turn (Area 2, ID 41)
DB::table('area_oficio')->where('id', 41)->update(['estatus' => 'Cancelado']);

// Print specifically for Area 2 (should show CANCELADO watermark)
$requestSingleCancelled = new \Illuminate\Http\Request();
$requestSingleCancelled->merge(['area_id' => 2]);
$htmlSingleCancelled = $controller->generarOficio($requestSingleCancelled, $oficio)->render();
$hasWatermark = strpos($htmlSingleCancelled, 'CANCELADO') !== false;
echo "1. Watermark CANCELADO visible on specific cancelled print? " . ($hasWatermark ? 'SÍ ✅' : 'NO ❌') . "\n";

// Print mass (should exclude Area 2 because it is cancelled, leaving 0 printouts)
$requestMass = new \Illuminate\Http\Request();
$htmlMass = $controller->generarOficio($requestMass, $oficio)->render();
$hasArea2InMass = strpos($htmlMass, 'Dirección de Gestión Institucional') !== false;
echo "2. Cancelled turn excluded from mass print? " . (!$hasArea2InMass ? 'SÍ ✅' : 'NO ❌') . "\n";

// Revert pivot status to Asignado for the rest of the test
DB::table('area_oficio')->where('id', 41)->update(['estatus' => 'Asignado']);

// === Test 2: Tracking / Consola de Monitoreo Global ===
echo "\n=== Test 2: Consola de Monitoreo Global (seguimiento) ===\n";
$requestSeguimiento = new \Illuminate\Http\Request();
$requestSeguimiento->merge(['search' => '29-TEST']);
$responseSeguimiento = $controller->seguimiento($requestSeguimiento);
$htmlSeguimiento = $responseSeguimiento->render();

$hasSubareasInTracking = strpos($htmlSeguimiento, 'Subdirección de Informática y Transparencia:') !== false;
echo "5. Tracking page displays subarea name for turn? " . ($hasSubareasInTracking ? 'SÍ ✅' : 'NO ❌') . "\n";

// === Test 3: Direct User Assignment ===
echo "\n=== Test 3: Direct User Assignment ===\n";

// Find or create a direct user in Area 2 (Management area)
$directUser = User::where('area_id', 2)->whereNull('subarea_id')->where('role', 'user')->first();
$tempDirectUser = false;
if (!$directUser) {
    $directUser = User::create([
        'name' => 'Temp Direct Operative',
        'email' => 'temp_direct_op@ceaa.gob.mx',
        'password' => bcrypt('password'),
        'role' => 'user',
        'area_id' => 2,
        'subarea_id' => null
    ]);
    $tempDirectUser = true;
}

// Prepare request for assigning
$requestAssign = new \Illuminate\Http\Request();
$requestAssign->merge([
    'pivote_id' => 41,
    'subarea_ids' => ['user_' . $directUser->id]
]);

// Call asignar
$controller->asignar($requestAssign, $oficio);

// Check if assignment exists
$assignment = SubareaOficio::where('area_oficio_id', 41)
    ->whereNull('subarea_id')
    ->where('user_id', $directUser->id)
    ->first();

echo "6. Direct user assignment record created in DB? " . ($assignment ? 'SÍ ✅' : 'NO ❌') . "\n";

// Render show view and verify "Atención Directa" is shown
$responseShow = $controller->show(new \Illuminate\Http\Request(), $oficio);
$htmlShow = $responseShow->render();

$hasAtencionDirecta = strpos($htmlShow, 'Atención Directa:') !== false && strpos($htmlShow, $directUser->name) !== false;
echo "7. Show view displays Atención Directa text? " . ($hasAtencionDirecta ? 'SÍ ✅' : 'NO ❌') . "\n";

// Clean up Test 3
SubareaOficio::where('area_oficio_id', 41)->delete();
if ($tempDirectUser) {
    $directUser->delete();
}

// Cleanup database
SubareaOficio::where('area_oficio_id', $pivotId)->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('area_oficio')->where('id', 41)->delete();
DB::table('oficios')->where('id', 29)->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

if ($correspondenciaUser->name === 'Temp Correspondencia') {
    $correspondenciaUser->delete();
}
echo "\nDB cleaned up successfully!\n";
