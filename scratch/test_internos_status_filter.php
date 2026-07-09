<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Oficio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "=== Test: Setting up internal oficios with different statuses ===\n";

$director = User::where('area_id', 2)->where('role', 'jefe_area')->first();
if (!$director) {
    echo "ERROR: Director not found for Area 2! ❌\n";
    exit(1);
}

// Create 3 internal oficios
$oficio1 = Oficio::create([
    'numero_oficio' => 'TEST-STAT-101/2026',
    'numero_oficio_dependencia' => 'ORIG-101',
    'remitente' => 'Emisor Test 1',
    'asunto' => 'Asunto Test Interno 1',
    'tipo_correspondencia' => 'Interna',
    'fecha_recepcion' => '2026-07-09',
    'area_origen_id' => 3,
    'municipio' => 'Pachuca',
]);

$oficio2 = Oficio::create([
    'numero_oficio' => 'TEST-STAT-102/2026',
    'numero_oficio_dependencia' => 'ORIG-102',
    'remitente' => 'Emisor Test 2',
    'asunto' => 'Asunto Test Interno 2',
    'tipo_correspondencia' => 'Interna',
    'fecha_recepcion' => '2026-07-09',
    'area_origen_id' => 3,
    'municipio' => 'Pachuca',
]);

// Turn 1: Notificado
$pivotId1 = DB::table('area_oficio')->insertGetId([
    'area_id' => 2,
    'oficio_id' => $oficio1->id,
    'estatus' => 'Notificado',
    'instruccion' => 'Atender',
    'folio_interno' => 'TEST-STAT-101/2026',
    'consecutivo' => 101,
    'anio' => 2026,
]);

// Turn 2: Solventado
$pivotId2 = DB::table('area_oficio')->insertGetId([
    'area_id' => 2,
    'oficio_id' => $oficio2->id,
    'estatus' => 'Solventado',
    'instruccion' => 'Atender',
    'folio_interno' => 'TEST-STAT-102/2026',
    'consecutivo' => 102,
    'anio' => 2026,
]);

$controller = new \App\Http\Controllers\OficioController();
Auth::login($director);

// Case A: Filter = Todos
$request = new \Illuminate\Http\Request();
$request->merge(['estatus' => 'Todos']);
$view = $controller->internosIndex($request);
$oficios = $view->getData()['oficios'];
$ids = $oficios->pluck('id')->toArray();

echo "Filter 'Todos' sees Oficio 1? " . (in_array($oficio1->id, $ids) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Filter 'Todos' sees Oficio 2? " . (in_array($oficio2->id, $ids) ? 'SÍ ✅' : 'NO ❌') . "\n";

// Case B: Filter = Notificado
$request2 = new \Illuminate\Http\Request();
$request2->merge(['estatus' => 'Notificado']);
$view2 = $controller->internosIndex($request2);
$oficios2 = $view2->getData()['oficios'];
$ids2 = $oficios2->pluck('id')->toArray();

echo "Filter 'Notificado' sees Oficio 1? " . (in_array($oficio1->id, $ids2) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Filter 'Notificado' sees Oficio 2? " . (!in_array($oficio2->id, $ids2) ? 'SÍ ✅' : 'NO ❌') . "\n";

// Case C: Filter = Solventado
$request3 = new \Illuminate\Http\Request();
$request3->merge(['estatus' => 'Solventado']);
$view3 = $controller->internosIndex($request3);
$oficios3 = $view3->getData()['oficios'];
$ids3 = $oficios3->pluck('id')->toArray();

echo "Filter 'Solventado' sees Oficio 1? " . (!in_array($oficio1->id, $ids3) ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Filter 'Solventado' sees Oficio 2? " . (in_array($oficio2->id, $ids3) ? 'SÍ ✅' : 'NO ❌') . "\n";

// Clean up
DB::table('area_oficio')->where('id', $pivotId1)->delete();
DB::table('area_oficio')->where('id', $pivotId2)->delete();
$oficio1->delete();
$oficio2->delete();

echo "\nTest database cleaned up successfully!\n";
