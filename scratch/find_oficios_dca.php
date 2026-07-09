<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$oficios = \App\Models\Oficio::where('numero_oficio', 'like', 'DCA-INT-%')->get();
foreach ($oficios as $o) {
    echo "ID: {$o->id}, Numero: {$o->numero_oficio}, Origin Area ID: {$o->area_origen_id}, Estatus: {$o->estatus}\n";
    $pivots = DB::table('area_oficio')->where('oficio_id', $o->id)->get();
    foreach ($pivots as $p) {
        echo "  Pivot ID: {$p->id}, Area ID: {$p->area_id}, User ID: " . ($p->user_id ?? 'NULL') . ", Estatus: {$p->estatus}\n";
        $subareas = DB::table('subarea_oficio')->where('area_oficio_id', $p->id)->get();
        foreach ($subareas as $sa) {
            $u = \App\Models\User::find($sa->user_id);
            echo "    SubareaOficio ID: {$sa->id}, Subarea ID: " . ($sa->subarea_id ?? 'NULL') . ", User ID: " . ($sa->user_id ?? 'NULL') . " (" . ($u ? $u->name : 'No User') . "), Estatus: {$sa->estatus}\n";
        }
    }
}
