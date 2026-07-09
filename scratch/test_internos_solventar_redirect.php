<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Oficio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "=== Test: Setting up internal and external oficios for redirection test ===\n";

$director = User::where('area_id', 2)->where('role', 'jefe_area')->first();
if (!$director) {
    echo "ERROR: Director not found for Area 2! ❌\n";
    exit(1);
}

// 1. External Oficio
$extOficio = Oficio::create([
    'numero_oficio' => 'TEST-RED-EXT/2026',
    'numero_oficio_dependencia' => 'EXT-101',
    'remitente' => 'Emisor Externo',
    'asunto' => 'Asunto Externo',
    'tipo_correspondencia' => 'Externa',
    'fecha_recepcion' => '2026-07-09',
    'area_origen_id' => 3,
    'municipio' => 'Pachuca',
]);

$pivotExt = DB::table('area_oficio')->insertGetId([
    'area_id' => 2,
    'oficio_id' => $extOficio->id,
    'estatus' => 'Notificado',
    'instruccion' => 'Atender',
    'folio_interno' => 'TEST-RED-EXT/2026',
    'consecutivo' => 201,
    'anio' => 2026,
]);

// 2. Internal Oficio
$intOficio = Oficio::create([
    'numero_oficio' => 'TEST-RED-INT/2026',
    'numero_oficio_dependencia' => 'INT-101',
    'remitente' => 'Emisor Interno',
    'asunto' => 'Asunto Interno',
    'tipo_correspondencia' => 'Interna',
    'fecha_recepcion' => '2026-07-09',
    'area_origen_id' => 3,
    'municipio' => 'Pachuca',
]);

$pivotInt = DB::table('area_oficio')->insertGetId([
    'area_id' => 2,
    'oficio_id' => $intOficio->id,
    'estatus' => 'Notificado',
    'instruccion' => 'Atender',
    'folio_interno' => 'TEST-RED-INT/2026',
    'consecutivo' => 202,
    'anio' => 2026,
]);

$controller = new \App\Http\Controllers\OficioController();
Auth::login($director);

// Case A: Solventar External Oficio
echo "\nTesting redirection when solving an External Oficio...\n";
$requestExt = new \Illuminate\Http\Request();
$requestExt->merge([
    'tipo_respuesta' => 'Conocimiento',
    'mensaje' => 'Se da por enterado del oficio externo.',
]);
$responseExt = $controller->solventar($requestExt, $pivotExt);

echo "Is redirect? " . ($responseExt instanceof \Illuminate\Http\RedirectResponse ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Redirect Target URL matches oficios.gestion? " . (str_contains($responseExt->getTargetUrl(), 'oficios/gestion') ? 'SÍ ✅' : 'NO ❌') . "\n";

// Case B: Solventar Internal Oficio
echo "\nTesting redirection when solving an Internal Oficio...\n";
$requestInt = new \Illuminate\Http\Request();
$requestInt->merge([
    'tipo_respuesta' => 'Conocimiento',
    'mensaje' => 'Se da por enterado del oficio interno.',
]);
$responseInt = $controller->solventar($requestInt, $pivotInt);

echo "Is redirect? " . ($responseInt instanceof \Illuminate\Http\RedirectResponse ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Redirect Target URL matches principal? " . (str_contains($responseInt->getTargetUrl(), 'principal') ? 'SÍ ✅' : 'NO ❌') . "\n";

// Clean up
DB::table('area_oficio')->where('id', $pivotExt)->delete();
DB::table('area_oficio')->where('id', $pivotInt)->delete();
DB::table('oficio_respuestas')->where('area_oficio_id', $pivotExt)->delete();
DB::table('oficio_respuestas')->where('area_oficio_id', $pivotInt)->delete();
$extOficio->delete();
$intOficio->delete();

echo "\nTest database cleaned up successfully!\n";
