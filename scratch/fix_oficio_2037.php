<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;

$oficio = Oficio::where('id', 432)->first();
if ($oficio && $oficio->numero_oficio == '2037') {
    $oficio->update(['tipo_correspondencia' => 'Externa']);
    echo "SUCCESS: Oficio 2037 (ID 432) updated to 'Externa'! ✅\n";
} else {
    echo "ERROR: Oficio 2037 (ID 432) not found or has wrong number! ❌\n";
}
