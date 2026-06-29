<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;
use Illuminate\Support\Facades\DB;

echo "=== Inspecting Oficio 1938 ===\n";

// Search by ID first
$oficioById = Oficio::find(1938);
if ($oficioById) {
    echo "Found by ID 1938:\n";
    print_oficio_details($oficioById);
} else {
    echo "No oficio found with ID = 1938.\n";
}

// Search by fields like numero_oficio or numero_oficio_dependencia matching 1938
$oficiosByNum = Oficio::where('numero_oficio', 'like', '%1938%')
    ->orWhere('numero_oficio_dependencia', 'like', '%1938%')
    ->get();

if ($oficiosByNum->isNotEmpty()) {
    echo "\nFound by number matching '1938' (" . $oficiosByNum->count() . " records):\n";
    foreach ($oficiosByNum as $o) {
        print_oficio_details($o);
        echo "---------------------------------\n";
    }
} else {
    echo "\nNo oficios found with number/dependencia matching '1938'.\n";
}

function print_oficio_details($oficio) {
    echo "  ID: {$oficio->id}\n";
    echo "  Número de Oficio: {$oficio->numero_oficio}\n";
    echo "  Número Oficio Dependencia: {$oficio->numero_oficio_dependencia}\n";
    echo "  Tipo Correspondencia: {$oficio->tipo_correspondencia}\n";
    echo "  Remitente: {$oficio->remitente}\n";
    echo "  Estatus: {$oficio->estatus}\n";
    echo "  Fecha Recepción: {$oficio->fecha_recepcion}\n";
    echo "  Creado en: {$oficio->created_at}\n";
    
    // Check pivot area_oficio
    $areas = DB::table('area_oficio')->where('oficio_id', $oficio->id)->get();
    echo "  Pivot areas count: " . $areas->count() . "\n";
    foreach ($areas as $a) {
        echo "    - Area ID: {$a->area_id}, Estatus: {$a->estatus}, Folio Interno: {$a->folio_interno}\n";
    }
}
