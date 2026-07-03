<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Oficio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

echo "=== Test 1: Testing internosIndex query with different filter types ===\n";

// Set up a mock user
$user = User::where('role', 'jefe_area')->first();
if (!$user) {
    echo "No jefe_area user found in DB! Creating one...\n";
    $user = User::create([
        'name' => 'Mock Jefe Area Filter Test',
        'email' => 'mock_jefe_filter_' . uniqid() . '@ceaa.gob.mx',
        'password' => bcrypt('password'),
        'role' => 'jefe_area',
        'area_id' => 2,
    ]);
}

Auth::login($user);
$areaId = $user->area_id;

// Create test oficios:
// 1. One sent by our area
$oficioSent = Oficio::create([
    'numero_oficio' => 'TEST-SENT-INT-1',
    'remitente' => 'Mock Sender Sent',
    'municipio' => 'Pachuca',
    'localidad' => 'Pachuca',
    'asunto' => 'Test Sent internal oficio',
    'tipo_correspondencia' => 'Interna',
    'fecha_recepcion' => date('Y-m-d'),
    'prioridad' => 'Ordinaria',
    'area_origen_id' => $areaId,
    'numero_oficio_dependencia' => 'DEP-SENT-1',
]);

// 2. One received/captured by our area
$oficioRecv = Oficio::create([
    'numero_oficio' => 'TEST-RECV-INT-1',
    'remitente' => 'Mock Sender Recv',
    'municipio' => 'Pachuca',
    'localidad' => 'Pachuca',
    'asunto' => 'Test Recv internal oficio',
    'tipo_correspondencia' => 'Interna',
    'fecha_recepcion' => date('Y-m-d'),
    'prioridad' => 'Ordinaria',
    'area_origen_id' => 3, // another area
    'numero_oficio_dependencia' => 'DEP-RECV-1',
]);

$pivotIdRecv = DB::table('area_oficio')->insertGetId([
    'oficio_id' => $oficioRecv->id,
    'area_id' => $areaId,
    'estatus' => 'Notificado',
    'instruccion' => 'Atender',
    'consecutivo' => 9991,
    'anio' => 2026,
]);

// 3. One received and solvented by our area
$oficioSolvented = Oficio::create([
    'numero_oficio' => 'TEST-SOLV-INT-1',
    'remitente' => 'Mock Sender Solv',
    'municipio' => 'Pachuca',
    'localidad' => 'Pachuca',
    'asunto' => 'Test Solv internal oficio',
    'tipo_correspondencia' => 'Interna',
    'fecha_recepcion' => date('Y-m-d'),
    'prioridad' => 'Ordinaria',
    'area_origen_id' => 3, // another area
    'numero_oficio_dependencia' => 'DEP-SOLV-1',
]);

$pivotIdSolv = DB::table('area_oficio')->insertGetId([
    'oficio_id' => $oficioSolvented->id,
    'area_id' => $areaId,
    'estatus' => 'Solventado',
    'instruccion' => 'Atender',
    'consecutivo' => 9992,
    'anio' => 2026,
]);

$controller = new \App\Http\Controllers\OficioController();

// 1. Test "Enviados"
$request = new Request(['tipo' => 'Enviados']);
$response = $controller->internosIndex($request);
$oficiosData = $response->getData()['oficios'];
$sentIds = collect($oficiosData->items())->pluck('id')->toArray();
echo "1. 'Enviados' tab returns the sent oficio? " . (in_array($oficioSent->id, $sentIds) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "   Does NOT return the received oficio? " . (!in_array($oficioRecv->id, $sentIds) ? 'SÍ ✅' : 'NO ❌') . "\n";

// 2. Test "Recibidos"
$request = new Request(['tipo' => 'Recibidos']);
$response = $controller->internosIndex($request);
$oficiosData = $response->getData()['oficios'];
$recvIds = collect($oficiosData->items())->pluck('id')->toArray();
echo "2. 'Recibidos' tab returns the received oficio? " . (in_array($oficioRecv->id, $recvIds) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "   Does NOT return the sent oficio? " . (!in_array($oficioSent->id, $recvIds) ? 'SÍ ✅' : 'NO ❌') . "\n";

// 3. Test "Solventados"
$request = new Request(['tipo' => 'Solventados']);
$response = $controller->internosIndex($request);
$oficiosData = $response->getData()['oficios'];
$solvIds = collect($oficiosData->items())->pluck('id')->toArray();
echo "3. 'Solventados' tab returns the solvented received oficio? " . (in_array($oficioSolvented->id, $solvIds) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "   Does NOT return the pending received oficio? " . (!in_array($oficioRecv->id, $solvIds) ? 'SÍ ✅' : 'NO ❌') . "\n";

// Clean up
$oficioSent->delete();
$oficioRecv->delete();
$oficioSolvented->delete();
DB::table('area_oficio')->whereIn('id', [$pivotIdRecv, $pivotIdSolv])->delete();
if (strpos($user->email, 'mock_jefe_filter_') === 0) {
    $user->forceDelete();
}

echo "\nTest database cleaned up successfully!\n";
