<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pivot = DB::table('area_oficio')->where('id', 624)->first();
echo "--- area_oficio ID 624 ---\n";
print_r((array)$pivot);

$subareas = DB::table('subarea_oficio')->where('area_oficio_id', 624)->get();
echo "\n--- subarea_oficio for area_oficio_id 624 ---\n";
foreach ($subareas as $sa) {
    print_r((array)$sa);
}
