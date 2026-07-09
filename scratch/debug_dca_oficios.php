<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$oficioIds = [398, 397, 396]; // DCA-INT-14, 13, 12
foreach ($oficioIds as $id) {
    $oficio = \App\Models\Oficio::find($id);
    if (!$oficio) continue;
    
    echo "=== Oficio: {$oficio->numero_oficio} ===\n";
    echo "Origin Area ID: {$oficio->area_origen_id}\n";
    echo "Estatus: {$oficio->estatus}\n";
    
    $pivots = DB::table('area_oficio')->where('oficio_id', $id)->get();
    foreach ($pivots as $p) {
        echo "  Pivot ID: {$p->id}, Area ID: {$p->area_id}, User ID: " . ($p->user_id ?? 'NULL') . ", Estatus: {$p->estatus}\n";
        
        $subareas = DB::table('subarea_oficio')->where('area_oficio_id', $p->id)->get();
        foreach ($subareas as $sa) {
            echo "    SubareaOficio ID: {$sa->id}, Subarea ID: " . ($sa->subarea_id ?? 'NULL') . ", User ID: " . ($sa->user_id ?? 'NULL') . ", Estatus: {$sa->estatus}\n";
        }
    }
}
