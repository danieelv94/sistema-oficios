<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Comision;

$jefes = User::where('area_id', 1)->where('role', 'jefe_area')->get();
echo "=== Jefes of Area 1 ===\n";
foreach ($jefes as $j) {
    echo "ID: {$j->id} | Name: {$j->name} | Role: {$j->role}\n";
}

$comisiones = Comision::whereHas('user', function($q) {
    $q->where('area_id', 1);
})->get();

echo "\n=== Commissions of Area 1 users ===\n";
foreach ($comisiones as $c) {
    echo "ID: {$c->id} | User: {$c->user->name} (ID: {$c->user_id}) | Jefe: " . ($c->jefeArea->name ?? 'None') . " (ID: " . ($c->jefe_area_id ?? 'None') . ")\n";
}
