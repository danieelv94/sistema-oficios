<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;

$internalOficios = Oficio::where('tipo_correspondencia', 'Interna')->get();
echo "=== Internal Oficios (total " . $internalOficios->count() . ") ===\n";
foreach ($internalOficios as $o) {
    echo "ID: {$o->id}, Numero: {$o->numero_oficio}, Numero Dependencia (Origen): {$o->numero_oficio_dependencia}, Area Origen: {$o->area_origen_id}, Consecutivo Origen: {$o->consecutivo_origen}, Anio Origen: {$o->anio_origen}\n";
}
