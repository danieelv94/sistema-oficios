<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Oficio;
use App\Models\Area;
use App\Models\Subarea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "=== Test 1: Setting up mock users and internal oficios ===\n";

// Area
$area = Area::find(2); // Gestión Institucional
$subarea = Subarea::find(1); // Subdirección de Enlace Institucional y Seguimiento

// Users
$director = User::where('area_id', 2)->where('role', 'jefe_area')->first();
$secretaria = User::where('area_id', 2)->where('role', 'secretaria_area')->first();

if (!$director || !$secretaria) {
    echo "ERROR: Director or Secretary not found for Area 2! ❌\n";
    exit(1);
}

// Create two mock operatives in Area 2
$op1 = User::create([
    'name' => 'Operativo Uno',
    'email' => 'op1_' . uniqid() . '@ceaa.gob.mx',
    'password' => bcrypt('password'),
    'role' => 'operativo',
    'area_id' => 2,
    'subarea_id' => $subarea->id,
]);

$op2 = User::create([
    'name' => 'Operativo Dos',
    'email' => 'op2_' . uniqid() . '@ceaa.gob.mx',
    'password' => bcrypt('password'),
    'role' => 'operativo',
    'area_id' => 2,
    'subarea_id' => $subarea->id,
]);

// Create two internal oficios
$oficio1 = Oficio::create([
    'numero_oficio' => 'TEST-INT-101/2026',
    'numero_oficio_dependencia' => 'ORIGEN-101',
    'remitente' => 'Emisor Test 1',
    'asunto' => 'Asunto Test Interno 1',
    'tipo_correspondencia' => 'Interna',
    'fecha_recepcion' => '2026-07-09',
    'area_origen_id' => 3, // Emisor diferente
    'municipio' => 'Pachuca',
]);

$oficio2 = Oficio::create([
    'numero_oficio' => 'TEST-INT-102/2026',
    'numero_oficio_dependencia' => 'ORIGEN-102',
    'remitente' => 'Emisor Test 2',
    'asunto' => 'Asunto Test Interno 2',
    'tipo_correspondencia' => 'Interna',
    'fecha_recepcion' => '2026-07-09',
    'area_origen_id' => 3, // Emisor diferente
    'municipio' => 'Pachuca',
]);

// Turn both oficios to Area 2
$pivotId1 = DB::table('area_oficio')->insertGetId([
    'area_id' => 2,
    'oficio_id' => $oficio1->id,
    'estatus' => 'Notificado',
    'instruccion' => 'Atender',
    'folio_interno' => 'TEST-INT-101/2026',
    'consecutivo' => 101,
    'anio' => 2026,
]);

$pivotId2 = DB::table('area_oficio')->insertGetId([
    'area_id' => 2,
    'oficio_id' => $oficio2->id,
    'estatus' => 'Notificado',
    'instruccion' => 'Atender',
    'folio_interno' => 'TEST-INT-102/2026',
    'consecutivo' => 102,
    'anio' => 2026,
]);

// Assign Oficio 1 to Operativo 1
DB::table('subarea_oficio')->insert([
    'area_oficio_id' => $pivotId1,
    'subarea_id' => null,
    'user_id' => $op1->id,
    'estatus' => 'Asignado',
    'instruccion' => 'Resolver esto por favor',
]);

// Assign Oficio 2 to Operativo 2
DB::table('subarea_oficio')->insert([
    'area_oficio_id' => $pivotId2,
    'subarea_id' => null,
    'user_id' => $op2->id,
    'estatus' => 'Asignado',
    'instruccion' => 'Resolver esto por favor',
]);

$controller = new \App\Http\Controllers\OficioController();

echo "\n=== Test 2: Testing Visibility Restrictions ===\n";

// A. Log in as Operativo 1 -> should only see Oficio 1
Auth::login($op1);
$request = new \Illuminate\Http\Request();
$request->merge(['tipo' => 'Recibidos']);
$view = $controller->internosIndex($request);
$oficiosVisible = $view->getData()['oficios'];

echo "Operativo 1 visible oficios count: " . count($oficiosVisible) . "\n";
$ids = $oficiosVisible->pluck('id')->toArray();
echo "Operativo 1 sees Oficio 1? " . (in_array($oficio1->id, $ids) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Operativo 1 sees Oficio 2? " . (!in_array($oficio2->id, $ids) ? 'SÍ ✅' : 'NO ❌') . "\n";

// B. Log in as Operativo 2 -> should only see Oficio 2
Auth::login($op2);
$view = $controller->internosIndex($request);
$oficiosVisible = $view->getData()['oficios'];

echo "\nOperativo 2 visible oficios count: " . count($oficiosVisible) . "\n";
$ids2 = $oficiosVisible->pluck('id')->toArray();
echo "Operativo 2 sees Oficio 1? " . (!in_array($oficio1->id, $ids2) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Operativo 2 sees Oficio 2? " . (in_array($oficio2->id, $ids2) ? 'SÍ ✅' : 'NO ❌') . "\n";

// C. Log in as Director -> should see both oficios
Auth::login($director);
$view = $controller->internosIndex($request);
$oficiosVisible = $view->getData()['oficios'];
$idsDir = $oficiosVisible->pluck('id')->toArray();
echo "\nDirector sees Oficio 1? " . (in_array($oficio1->id, $idsDir) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Director sees Oficio 2? " . (in_array($oficio2->id, $idsDir) ? 'SÍ ✅' : 'NO ❌') . "\n";

// D. Log in as Secretaria -> should see both oficios
Auth::login($secretaria);
$view = $controller->internosIndex($request);
$oficiosVisible = $view->getData()['oficios'];
$idsSec = $oficiosVisible->pluck('id')->toArray();
echo "\nSecretaria sees Oficio 1? " . (in_array($oficio1->id, $idsSec) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Secretaria sees Oficio 2? " . (in_array($oficio2->id, $idsSec) ? 'SÍ ✅' : 'NO ❌') . "\n";

// Clean up
DB::table('subarea_oficio')->where('area_oficio_id', $pivotId1)->delete();
DB::table('subarea_oficio')->where('area_oficio_id', $pivotId2)->delete();
DB::table('area_oficio')->where('id', $pivotId1)->delete();
DB::table('area_oficio')->where('id', $pivotId2)->delete();
$oficio1->delete();
$oficio2->delete();
$op1->forceDelete();
$op2->forceDelete();

echo "\nTest database cleaned up successfully!\n";
