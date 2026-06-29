<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Oficio;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::find(221);
Auth::login($user);

echo "=== Prueba: Subdirector (Guillermo) accede a oficio 29 con mode=operativo ===\n";
echo "User: {$user->name}, Role: {$user->role}, Subarea: {$user->subarea_id}\n\n";

$oficio = Oficio::find(29);

$request = new \Illuminate\Http\Request();
$request->merge(['mode' => 'operativo']);

$controller = new \App\Http\Controllers\OficioController();
$response = $controller->show($request, $oficio);
$html = $response->render();

$hasAsignarForm = strpos($html, 'Asignar a personal...') !== false;
echo "1. ¿Tiene formulario 'Asignar a personal...'? " . ($hasAsignarForm ? 'SÍ ✅' : 'NO ❌') . "\n";

$hasAtender = strpos($html, '>Atender<') !== false;
echo "2. ¿Tiene botón 'Atender'? " . ($hasAtender ? 'SÍ ✅' : 'NO ❌') . "\n";

$hasNotificado = strpos($html, 'Notificado en el Sistema') !== false;
echo "3. ¿Tiene badge 'Notificado en el Sistema'? " . ($hasNotificado ? 'SÍ ✅' : 'NO ❌') . "\n";

$hasPersonal = strpos($html, 'María Magdalena Jaime Arroyo') !== false;
echo "4. ¿Aparece personal de su subdirección (María Magdalena)? " . ($hasPersonal ? 'SÍ ✅' : 'NO ❌') . "\n";

$hasHimself = strpos($html, 'Guillermo') !== false;
echo "5. ¿Aparece él mismo como opción? " . ($hasHimself ? 'SÍ ✅' : 'NO ❌') . "\n";

// Extraer la sección operativa del HTML
$startInstruccion = strpos($html, 'Instrucción para su Atención');
if ($startInstruccion !== false) {
    echo "\n--- Snippet de la sección operativa ---\n";
    echo substr($html, $startInstruccion, 3000) . "\n";
} else {
    echo "\n❌ No se encontró 'Instrucción para su Atención' en el HTML\n";
}
