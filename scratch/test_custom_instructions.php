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

// Find Area 2 (Gestión Institucional)
$jefe = User::where('role', 'jefe_area')->where('area_id', 2)->first();
if (!$jefe) {
    echo "No jefe found for Area 2. Exiting.\n";
    exit(1);
}

// Find two subareas of Area 2
$subareas = Subarea::where('area_id', 2)->take(2)->get();
if ($subareas->count() < 2) {
    echo "Need at least 2 subareas in Area 2 for this test. Exiting.\n";
    exit(1);
}

$sub1 = $subareas[0];
$sub2 = $subareas[1];

echo "=== Test: Setting up Oficio ===\n";
$oficio = Oficio::create([
    'numero_oficio' => 'DGI-TEST-CUSTOM-INT/2026',
    'remitente' => 'Secretaría General',
    'municipio' => 'Pachuca de Soto',
    'localidad' => 'Pachuca de Soto',
    'asunto' => 'Prueba de asignación de instrucciones individualizadas a subdirecciones.',
    'estatus' => 'Turnado',
    'fecha_recepcion' => date('Y-m-d'),
    'tipo_correspondencia' => 'Externa',
    'prioridad' => 'Ordinaria',
]);

// Insert into area_oficio
$pivotId = DB::table('area_oficio')->insertGetId([
    'oficio_id' => $oficio->id,
    'area_id' => 2,
    'instruccion' => 'Instrucción general del oficio',
    'estatus' => 'Notificado',
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "Created Oficio: {$oficio->id}, Pivot ID: {$pivotId}\n";
echo "Subarea 1: {$sub1->name} (ID: {$sub1->id})\n";
echo "Subarea 2: {$sub2->name} (ID: {$sub2->id})\n";

// Login as Jefe
Auth::login($jefe);

$controller = new \App\Http\Controllers\OficioController();
$request = new \Illuminate\Http\Request();
$request->merge([
    'pivote_id' => $pivotId,
    'subarea_ids' => ['director', $sub1->id, $sub2->id],
    'instruccion_director' => 'Revisar personalmente y firmar',
    'instruccion_' . $sub1->id => 'Elaborar reporte técnico urgente',
    'instruccion_' . $sub2->id => 'Para su conocimiento y archivo',
]);

echo "\n=== Test 2: Executing asignar() method ===\n";
$response = $controller->asignar($request, $oficio);

// Query assignments in DB
$assignments = DB::table('subarea_oficio')
    ->where('area_oficio_id', $pivotId)
    ->get();

echo "Total subarea assignments created: " . $assignments->count() . "\n";
echo "Should have created 3 assignments? " . ($assignments->count() === 3 ? 'SÍ ✅' : 'NO ❌') . "\n";

foreach ($assignments as $assignment) {
    if (is_null($assignment->subarea_id)) {
        echo "- Assignment for Director - Instruction: '{$assignment->instruccion}'\n";
        echo "  Matches 'Revisar personalmente y firmar'? " . ($assignment->instruccion === 'Revisar personalmente y firmar' ? 'SÍ ✅' : 'NO ❌') . "\n";
    } elseif ($assignment->subarea_id == $sub1->id) {
        echo "- Assignment for Subarea 1 - Instruction: '{$assignment->instruccion}'\n";
        echo "  Matches 'Elaborar reporte técnico urgente'? " . ($assignment->instruccion === 'Elaborar reporte técnico urgente' ? 'SÍ ✅' : 'NO ❌') . "\n";
    } elseif ($assignment->subarea_id == $sub2->id) {
        echo "- Assignment for Subarea 2 - Instruction: '{$assignment->instruccion}'\n";
        echo "  Matches 'Para su conocimiento y archivo'? " . ($assignment->instruccion === 'Para su conocimiento y archivo' ? 'SÍ ✅' : 'NO ❌') . "\n";
    }
}

// Cleanup database
DB::table('subarea_oficio')->where('area_oficio_id', $pivotId)->delete();
DB::table('area_oficio')->where('id', $pivotId)->delete();
$oficio->forceDelete();
echo "\nTest database cleaned up successfully!\n";
