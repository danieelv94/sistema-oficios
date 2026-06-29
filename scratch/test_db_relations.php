<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Area;
use App\Models\Subarea;
use App\Models\User;

echo "--- VERIFYING SUBAREAS RELATIONSHIPS ---\n";
$areas = Area::with('subareas')->get();
foreach ($areas as $area) {
    if ($area->subareas->count() > 0) {
        echo "Area: {$area->name} has subareas:\n";
        foreach ($area->subareas as $sub) {
            echo "  - {$sub->name} (prefijo: {$sub->prefijo})\n";
        }
    }
}

echo "\n--- VERIFYING USER SUBAREA ATTACHMENT ---\n";
// Let's find a user, e.g. User with ID 1
$user1 = User::find(1);
if ($user1) {
    echo "User 1: {$user1->name}, Role: {$user1->role}, Area ID: {$user1->area_id}, Subarea ID: " . ($user1->subarea_id ?? 'None') . "\n";
    
    // Assign Subarea with ID 2 (which is Subdirección de Informática y Transparencia under Area 2)
    $subarea = Subarea::find(2);
    if ($subarea) {
        echo "Updating User 1 subarea to Subarea ID 2...\n";
        $user1->subarea_id = 2;
        $user1->save();
        
        $user1 = User::find(1);
        echo "User 1 Subarea after save: " . ($user1->subarea->name ?? 'None') . "\n";
    }
}
