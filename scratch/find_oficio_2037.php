<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;
use Illuminate\Support\Facades\DB;

$oficio = Oficio::where('numero_oficio', 'like', '%2037%')
    ->orWhere('id', 2037)
    ->with('areas', 'areaOrigen')
    ->first();

if (!$oficio) {
    echo "No oficio found with number/id containing 2037\n";
    // Let's print the last 10 internal oficios
    $lastInternals = Oficio::where('tipo_correspondencia', 'Interna')->orderBy('id', 'desc')->limit(10)->get();
    echo "\nLast 10 internal oficios:\n";
    foreach ($lastInternals as $o) {
        echo "ID: {$o->id}, Num: {$o->numero_oficio}, Tipo: {$o->tipo_correspondencia}, Emisor: {$o->numero_oficio_dependencia}, Remitente: {$o->remitente}\n";
    }
} else {
    echo "Found Oficio details:\n";
    echo "ID: {$oficio->id}\n";
    echo "Numero Oficio (Folio): {$oficio->numero_oficio}\n";
    echo "Tipo Correspondencia: {$oficio->tipo_correspondencia}\n";
    echo "Remitente: {$oficio->remitente}\n";
    echo "Asunto: {$oficio->asunto}\n";
    echo "Area Origen ID: {$oficio->area_origen_id} (" . ($oficio->areaOrigen->name ?? 'None') . ")\n";
    echo "Numero Oficio Dependencia: {$oficio->numero_oficio_dependencia}\n";
    echo "Created At: {$oficio->created_at}\n";
    echo "Turned Areas:\n";
    foreach ($oficio->areas as $area) {
        echo "  - Area ID: {$area->id}, Name: {$area->name}, Pivot Estatus: {$area->pivot->estatus}, Folio Interno: {$area->pivot->folio_interno}\n";
    }
}
