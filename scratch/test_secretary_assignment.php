<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;
use App\Models\User;
use App\Models\SubareaOficio;
use Illuminate\Support\Facades\DB;

echo "=== Test: Setting up Oficio ===\n";
// Create test oficio
$oficio = Oficio::create([
    'numero_oficio' => 'TEST-SEC-123',
    'remitente' => 'Test Remitente',
    'municipio' => 'Pachuca',
    'localidad' => 'Pachuca',
    'asunto' => 'Test Asunto para asignación de secretaria',
    'tipo_correspondencia' => 'Externa',
    'fecha_recepcion' => date('Y-m-d'),
    'prioridad' => 'Ordinaria',
    'numero_oficio_dependencia' => 'DEP-SEC-123',
]);

// Turn to Area 2 (DGI)
$pivotId = DB::table('area_oficio')->insertGetId([
    'oficio_id' => $oficio->id,
    'area_id' => 2,
    'estatus' => 'Turnado',
    'instruccion' => 'Atender',
    'consecutivo' => 999,
    'anio' => 2026,
]);

// Force create a temp secretary with subarea_id = null to test direct assignment listing
$secretary = User::create([
    'name' => 'Temp Secretary DGI',
    'email' => 'temp_sec_dgi_' . uniqid() . '@ceaa.gob.mx',
    'password' => bcrypt('password'),
    'role' => 'secretaria_area',
    'area_id' => 2,
    'subarea_id' => null
]);
$tempSecretary = true;

echo "Secretary details: ID={$secretary->id}, Name={$secretary->name}, Area={$secretary->area_id}, Subarea=" . ($secretary->subarea_id ?? 'NULL') . ", Role={$secretary->role}\n";

// Find director of Area 2
$director = User::where('area_id', 2)->where('role', 'jefe_area')->first();
\Illuminate\Support\Facades\Auth::login($director);
echo "=== Test 2: Checking if secretary is loaded in available direct staff ===\n";
$controller = new \App\Http\Controllers\OficioController();
$request = new \Illuminate\Http\Request();
$response = $controller->show($request, $oficio);
$viewData = $response->getData();

$personalDirecto = $viewData['personalDirectoDisponiblesPorArea'][$pivotId] ?? collect();
$hasSecretary = $personalDirecto->contains('id', $secretary->id);
echo "Secretary is in the list of direct available staff? " . ($hasSecretary ? "SÍ ✅" : "NO ❌") . "\n";

echo "=== Test 3: Assigning to secretary ===\n";
$requestAssign = new \Illuminate\Http\Request();
$requestAssign->merge([
    'pivote_id' => $pivotId,
    'subarea_ids' => ['user_' . $secretary->id],
    'instruccion_user_' . $secretary->id => 'Elaborar minuta de acuerdos'
]);

$controller->asignar($requestAssign, $oficio);

// Verify SubareaOficio record was created
$assignment = SubareaOficio::where('area_oficio_id', $pivotId)
    ->whereNull('subarea_id')
    ->where('user_id', $secretary->id)
    ->first();

if ($assignment) {
    echo "1. Assignment record created successfully? SÍ ✅\n";
    echo "   User ID: {$assignment->user_id}\n";
    echo "   Instruction: {$assignment->instruccion}\n";
    echo "   Estatus: {$assignment->estatus}\n";
} else {
    echo "1. Assignment record created successfully? NO ❌\n";
}

// Clean up
SubareaOficio::where('area_oficio_id', $pivotId)->delete();
DB::table('area_oficio')->where('id', $pivotId)->delete();
$oficio->delete();
if ($tempSecretary) {
    $secretary->delete();
}
echo "\nTest database cleaned up successfully!\n";
