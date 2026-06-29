<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;
use App\Models\User;
use App\Models\Area;
use App\Models\Subarea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Find DGI area
$dgi = Area::find(2);
// Find a subarea of DGI
$subarea = Subarea::where('area_id', 2)->first();
if (!$subarea) {
    echo "No subarea of DGI found. Exiting.\n";
    exit(1);
}

// Find a subdirector of that subarea
$subdirector = User::where('role', 'subdirector')
    ->where('area_id', 2)
    ->where('subarea_id', $subarea->id)
    ->first();

if (!$subdirector) {
    echo "No subdirector found for DGI subarea {$subarea->name}. Exiting.\n";
    exit(1);
}

echo "=== Test: Setting up internal oficio captured by DGI ===\n";
// Create dummy internal oficio
$oficio = Oficio::create([
    'numero_oficio' => 'DGI-INT-TEST-99/2026',
    'remitente' => 'Director de Planeación',
    'municipio' => 'Pachuca de Soto',
    'localidad' => 'Pachuca de Soto',
    'asunto' => 'Oficio interno de prueba para validar asignación y solventación del subdirector.',
    'estatus' => 'Turnado',
    'fecha_recepcion' => date('Y-m-d'),
    'tipo_correspondencia' => 'Interna',
    'prioridad' => 'Ordinaria',
    'area_origen_id' => 3,
    'anio_origen' => 2026,
]);

// Insert receiving record in area_oficio
$pivotId = DB::table('area_oficio')->insertGetId([
    'oficio_id' => $oficio->id,
    'area_id' => 2, // DGI
    'instruccion' => 'Correspondencia Interna Recibida',
    'estatus' => 'Notificado',
    'folio_interno' => 'DGI-INT-TEST-99/2026',
    'consecutivo' => 99,
    'anio' => 2026,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Created internal oficio. ID: {$oficio->id}, Pivot ID: {$pivotId}\n";

// Assign to subarea
echo "Assigning to DGI subarea: {$subarea->name}\n";
DB::table('subarea_oficio')->insert([
    'area_oficio_id' => $pivotId,
    'subarea_id' => $subarea->id,
    'instruccion' => 'Atender de inmediato',
    'estatus' => 'Asignado',
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "\n=== Test 2: Log in as Subdirector {$subdirector->name} ===\n";
Auth::login($subdirector);

$controller = new \App\Http\Controllers\OficioController();
$request = new \Illuminate\Http\Request();
// Check default mode loading without passing mode parameter in request
$response = $controller->show($request, $oficio);
$viewData = $response->getData();

$turnosParaMostrar = $viewData['turnosParaMostrar'];
$mode = $viewData['mode'];
$subareaOficiosPorArea = $viewData['subareaOficiosPorArea'];
$personalPorSubarea = $viewData['personalPorSubarea'];

echo "Resolved view mode: {$mode}\n";
echo "Resolved mode is 'operativo'? " . ($mode === 'operativo' ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Turnos para mostrar count: " . count($turnosParaMostrar) . "\n";
echo "Turno is visible to subdirector? " . (count($turnosParaMostrar) > 0 ? 'SÍ ✅' : 'NO ❌') . "\n";

if (count($turnosParaMostrar) > 0) {
    $assignedSubareas = $subareaOficiosPorArea[$pivotId] ?? collect();
    echo "SubareaOficio record found for subdirector? " . ($assignedSubareas->isNotEmpty() ? 'SÍ ✅' : 'NO ❌') . "\n";
    if ($assignedSubareas->isNotEmpty()) {
        $soId = $assignedSubareas->first()->id;
        $personalCount = isset($personalPorSubarea[$soId]) ? count($personalPorSubarea[$soId]) : 0;
        echo "Operational staff available to delegate to: {$personalCount}\n";
        echo "Can delegate? " . ($personalCount > 0 ? 'SÍ ✅' : 'NO ❌') . "\n";
    }
}

// Cleanup database
DB::table('subarea_oficio')->where('area_oficio_id', $pivotId)->delete();
DB::table('area_oficio')->where('id', $pivotId)->delete();
$oficio->forceDelete();
echo "\nTest database cleaned up successfully!\n";
