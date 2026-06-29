<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;
use App\Models\User;
use App\Models\SubareaOficio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Let's check what areas are turnados for oficio 29
$oficio = Oficio::find(29);
echo "=== Oficio 29 Turnos ===\n";
foreach ($oficio->areas as $area) {
    echo "Area ID: {$area->id}, Name: {$area->name}, Pivot ID: {$area->pivot->id}, Status: {$area->pivot->estatus}\n";
}

// Guillermo is in Area 2 (Dirección de Gestión Institucional)
// Let's create subarea assignments for subarea 2 and 3 for Oficio 29 to test the multi-subarea assignment rendering!
$pivotId = $oficio->areas->where('id', 2)->first()->pivot->id;

// Delete old ones to have a clean slate
SubareaOficio::where('area_oficio_id', $pivotId)->delete();

// Assigning to Subarea 2 (Subdirección de Informática y Transparencia)
$subarea2 = SubareaOficio::create([
    'area_oficio_id' => $pivotId,
    'subarea_id' => 2,
    'user_id' => null,
    'instruccion' => 'Atender urgente con el equipo de Informática',
    'estatus' => 'Asignado'
]);

// Assigning to Subarea 3 (Subdirección de Archivo Institucional)
$subarea3 = SubareaOficio::create([
    'area_oficio_id' => $pivotId,
    'subarea_id' => 3,
    'user_id' => null,
    'instruccion' => 'Digitalizar y archivar expediente completo',
    'estatus' => 'Asignado'
]);

echo "\nCreated subarea assignments for Informática (Subarea 2) and Archivo (Subarea 3).\n";

// Now, let's login as Guillermo (subdirector of Archivo, subarea_id = 3)
$guillermo = User::find(221);
Auth::login($guillermo);

echo "\n=== Simulating Guillermo (Subdirector of Archivo) viewing the Oficio ===\n";
$request = new \Illuminate\Http\Request();
$request->merge(['mode' => 'operativo']);

$controller = new \App\Http\Controllers\OficioController();
$response = $controller->show($request, $oficio);
$html = $response->render();

// Guillermo should see Archivo's subarea assignment!
$hasArchivo = strpos($html, 'Subdirección de Archivo Institucional') !== false;
$hasInformatica = strpos($html, 'Subdirección de Informática y Transparencia') !== false;
echo "Guillermo sees Archivo? " . ($hasArchivo ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "Guillermo sees Informática? " . ($hasInformatica ? 'SÍ ✅' : 'NO ❌') . " (Should be NO since he's only in Archivo)\n";
echo "Guillermo has delegation dropdown? " . (strpos($html, 'Delegar a personal...') !== false ? 'SÍ ✅' : 'NO ❌') . "\n";

// Let's login as a Director / Secretaria of Area 2 (e.g. Director id 204, or similar)
// Let's find Director of Area 2
$director = User::where('area_id', 2)->where('role', 'jefe_area')->first();
if ($director) {
    Auth::login($director);
    echo "\n=== Simulating Director ({$director->name}) viewing the Oficio in gestion mode ===\n";
    $request = new \Illuminate\Http\Request();
    $request->merge(['mode' => 'gestion']);
    $response = $controller->show($request, $oficio);
    $html = $response->render();

    echo "Director sees 'Subdirecciones Asignadas'? " . (strpos($html, 'Subdirecciones Asignadas:') !== false ? 'SÍ ✅' : 'NO ❌') . "\n";
    echo "Director sees Subdirección de Informática? " . (strpos($html, 'Subdirección de Informática y Transparencia') !== false ? 'SÍ ✅' : 'NO ❌') . "\n";
    echo "Director sees Subdirección de Archivo? " . (strpos($html, 'Subdirección de Archivo Institucional') !== false ? 'SÍ ✅' : 'NO ❌') . "\n";
} else {
    echo "\nNo area 2 director found for simulation.\n";
}

// === Simulating Operative User (María Magdalena) attending the Oficio ===
$maria = User::find(219);
Auth::login($maria);

// Assign the subarea assignment to Maria and set status to Notificado so she can attend
$subarea3->update([
    'user_id' => $maria->id,
    'estatus' => 'Notificado'
]);

echo "\n=== Simulating María Magdalena (Operative of Archivo) attending the Oficio ===\n";
$request = new \Illuminate\Http\Request();
$request->merge(['subarea_oficio_id' => $subarea3->id]);

$response = $controller->atender($request, $pivotId);
$html = $response->render();

echo "María sees the subarea instruction? " . (strpos($html, 'Digitalizar y archivar expediente completo') !== false ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "María sees the subarea name? " . (strpos($html, 'Subdirección de Archivo Institucional') !== false ? 'SÍ ✅' : 'NO ❌') . "\n";
echo "María has the subarea_oficio_id hidden input? " . (strpos($html, 'name="subarea_oficio_id" value="' . $subarea3->id . '"') !== false ? 'SÍ ✅' : 'NO ❌') . "\n";

// === Simulating Solventación of Archivo Subarea ===
echo "\n=== Simulating Solventación of Archivo Subarea ===\n";
$requestSolventar = new \Illuminate\Http\Request();
$requestSolventar->merge([
    'tipo_respuesta' => 'Solventacion',
    'mensaje' => 'Se digitalizó y archivó toda la documentación de soporte.',
    'subarea_oficio_id' => $subarea3->id
]);

// Run solventar method
$controller->solventar($requestSolventar, $pivotId);

// Reload subarea and area status
$subarea3 = SubareaOficio::find($subarea3->id);
$areaOficioStatus = DB::table('area_oficio')->where('id', $pivotId)->value('estatus');
$oficioStatus = Oficio::find(29)->estatus;

echo "1. Archivo subarea status is now Solventado? " . ($subarea3->estatus === 'Solventado' ? 'SÍ ✅' : 'NO ❌ (' . $subarea3->estatus . ')') . "\n";
echo "2. area_oficio status remains Notificado (because Informática is pending)? " . ($areaOficioStatus === 'Notificado' ? 'SÍ ✅' : 'NO ❌ (' . $areaOficioStatus . ')') . "\n";
echo "3. Oficio status remains En Proceso? " . ($oficioStatus === 'En Proceso' ? 'SÍ ✅' : 'NO ❌ (' . $oficioStatus . ')') . "\n";

// === Simulating Solventación of Informática Subarea ===
echo "\n=== Simulating Solventación of Informática Subarea ===\n";
$subarea2->update([
    'user_id' => 220, // Let's say user 220 or similar
    'estatus' => 'Notificado'
]);

$requestSolventar2 = new \Illuminate\Http\Request();
$requestSolventar2->merge([
    'tipo_respuesta' => 'Solventacion',
    'mensaje' => 'Se atendió el requerimiento de software y se actualizó el sistema.',
    'subarea_oficio_id' => $subarea2->id
]);

// Run solventar method for Informática
$controller->solventar($requestSolventar2, $pivotId);

// Reload subarea and area status
$subarea2 = SubareaOficio::find($subarea2->id);
$areaOficioStatus = DB::table('area_oficio')->where('id', $pivotId)->value('estatus');
$oficioStatus = Oficio::find(29)->estatus;

echo "4. Informática subarea status is now Solventado? " . ($subarea2->estatus === 'Solventado' ? 'SÍ ✅' : 'NO ❌ (' . $subarea2->estatus . ')') . "\n";
echo "5. area_oficio status is now Solventado (all subareas solved)? " . ($areaOficioStatus === 'Solventado' ? 'SÍ ✅' : 'NO ❌ (' . $areaOficioStatus . ')') . "\n";
echo "6. Oficio status is now Solventado? " . ($oficioStatus === 'Solventado' ? 'SÍ ✅' : 'NO ❌ (' . $oficioStatus . ')') . "\n";

// === Test Case: Assigning directly to Director ===
echo "\n=== Test Case: Assigning directly to Director ===\n";
// Delete old assignments to start fresh
SubareaOficio::where('area_oficio_id', $pivotId)->delete();

// Login as director to see available options in gestion mode
if ($director) {
    Auth::login($director);
    $requestShow = new \Illuminate\Http\Request();
    $requestShow->merge(['mode' => 'gestion']);
    $response = $controller->show($requestShow, $oficio);
    $html = $response->render();

    $hasDirectorCheckbox = strpos($html, 'value="director"') !== false;
    echo "1. Director checkbox appears in assignment form? " . ($hasDirectorCheckbox ? 'SÍ ✅' : 'NO ❌') . "\n";

    // Simulate form submission to assign to Director
    $requestAssign = new \Illuminate\Http\Request();
    $requestAssign->merge([
        'pivote_id' => $pivotId,
        'subarea_ids' => ['director']
    ]);

    $controller->asignar($requestAssign, $oficio);

    // Verify subarea_oficio record was created
    $dirAssignment = SubareaOficio::where('area_oficio_id', $pivotId)
        ->whereNull('subarea_id')
        ->first();

    echo "2. subarea_oficio record created with null subarea_id? " . ($dirAssignment ? 'SÍ ✅' : 'NO ❌') . "\n";
    echo "3. subarea_oficio user_id is the Director? " . ($dirAssignment && $dirAssignment->user_id == $director->id ? 'SÍ ✅' : 'NO ❌') . "\n";
    echo "4. subarea_oficio status is Asignado? " . ($dirAssignment && $dirAssignment->estatus == 'Asignado' ? 'SÍ ✅' : 'NO ❌') . "\n";

    // Now reload show in gestion mode to check if Director checkbox is gone and it lists Director as assigned
    $response = $controller->show($requestShow, $oficio);
    $html = $response->render();

    $hasDirectorCheckboxAfter = strpos($html, 'value="director"') !== false;
    $showsDirectorAssigned = strpos($html, 'Director (Jefe de Área)') !== false;

    echo "5. Director checkbox is gone after assignment? " . (!$hasDirectorCheckboxAfter ? 'SÍ ✅' : 'NO ❌') . "\n";
    echo "6. Director shows up under assigned list? " . ($showsDirectorAssigned ? 'SÍ ✅' : 'NO ❌') . "\n";
}



