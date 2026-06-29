<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;
use App\Models\User;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

// 1. Find a director user (e.g. Jefe de Área of Gestión Institucional - Area 2)
$director = User::where('area_id', 2)->where('role', 'jefe_area')->first();
if (!$director) {
    echo "No director found in database. Exiting.\n";
    exit(1);
}

echo "=== Test 1: Log in as Director {$director->name} (Area 2) ===\n";
Auth::login($director);

// Instance controller
$controller = new \App\Http\Controllers\OficioController();

// 2. Fetch the create page to check next available folio
echo "\n=== Test 2: Calculate next internal folio ===\n";
$responseCreate = $controller->internosCreate();
$viewData = $responseCreate->getData();

$areaCaptura = $viewData['areaCaptura'];
$siguienteFolio = $viewData['siguienteFolio'];

echo "Area captura/receptora: {$areaCaptura->name}\n";
echo "Siguiente folio generado para receptor: {$siguienteFolio}\n";
echo "Folio precalculado matches DGI? " . (strpos($siguienteFolio, 'DGI-INT-') !== false ? 'SÍ ✅' : 'NO ❌') . "\n";

// 3. Register a new internal oficio (received from Area 3 - Planeación)
echo "\n=== Test 3: Store internal oficio ===\n";

// Create temp PDF file using Laravel storage helper
Storage::fake('public');
$file = UploadedFile::fake()->create('oficio_interno_test.pdf', 100, 'application/pdf');

$request = new \Illuminate\Http\Request();
$request->merge([
    'area_origen_id' => 3, // Sent by Area 3 (Planeación)
    'asunto' => 'Asunto de prueba de correspondencia interna entre áreas.',
    'fecha_recepcion' => date('Y-m-d'),
    'prioridad' => 'Ordinaria',
    'archivo_pdf' => $file,
    'remitente' => 'Director de Planeación',
    'observaciones' => 'Nota de prueba interna'
]);

$responseStore = $controller->internosStore($request);

// Verify DB entries
$createdOficio = Oficio::where('tipo_correspondencia', 'Interna')
    ->where('area_origen_id', 3)
    ->orderBy('id', 'desc')
    ->first();

if ($createdOficio) {
    echo "1. Oficio record created successfully? SÍ ✅\n";
    echo "   Folio / Numero Oficio: {$createdOficio->numero_oficio}\n";
    echo "   Remitente: {$createdOficio->remitente}\n";
    echo "   Tipo: {$createdOficio->tipo_correspondencia}\n";

    // Verify pivot area_oficio
    $pivot = DB::table('area_oficio')
        ->where('oficio_id', $createdOficio->id)
        ->where('area_id', 2) // Capturing area (DGI)
        ->first();

    if ($pivot) {
        echo "2. Pivot entry created for receiving Area 2 (DGI)? SÍ ✅\n";
        echo "   Status: {$pivot->estatus}\n";
        echo "   Instrucción: {$pivot->instruccion}\n";
        echo "   Folio interno: {$pivot->folio_interno}\n";
    } else {
        echo "2. Pivot entry created for receiving Area 2 (DGI)? NO ❌\n";
    }

    // Cleanup database
    DB::table('area_oficio')->where('oficio_id', $createdOficio->id)->delete();
    $createdOficio->forceDelete();
    echo "\nTest data cleaned up successfully!\n";
} else {
    echo "1. Oficio record created successfully? NO ❌\n";
}
