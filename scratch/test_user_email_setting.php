<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

echo "=== Test 1: User Model default setting ===\n";
// Create test user without setting receiving emails
$user = User::create([
    'name' => 'Test Email Pref User',
    'email' => 'test_pref_email_' . uniqid() . '@ceaa.gob.mx',
    'password' => bcrypt('password'),
    'role' => 'user',
    'area_id' => 2,
]);

echo "1. Default value of recibir_correos is boolean true? " . ($user->recibir_correos === true ? 'SÍ ✅' : 'NO ❌') . "\n";

// Disable email notification setting
$user->update(['recibir_correos' => false]);
$user->refresh();
echo "2. Disabling setting updates to boolean false? " . ($user->recibir_correos === false ? 'SÍ ✅' : 'NO ❌') . "\n";

echo "\n=== Test 2: Mail Interception check ===\n";
// Mock Mail facade to count sent messages
Mail::fake();

$oficio = \App\Models\Oficio::create([
    'numero_oficio' => 'TEST-MAIL-1',
    'remitente' => 'Test Remitente',
    'municipio' => 'Pachuca',
    'localidad' => 'Pachuca',
    'asunto' => 'Test Asunto',
    'tipo_correspondencia' => 'Externa',
    'fecha_recepcion' => date('Y-m-d'),
    'prioridad' => 'Ordinaria',
    'numero_oficio_dependencia' => 'DEP-MAIL-1',
]);

$pivotId = DB::table('area_oficio')->insertGetId([
    'oficio_id' => $oficio->id,
    'area_id' => 2,
    'estatus' => 'Turnado',
    'instruccion' => 'Atender',
    'consecutivo' => 999,
    'anio' => 2026,
]);

// Turn / Assign the oficio to this user whose emails are disabled
$controller = new \App\Http\Controllers\OficioController();
$request = new \Illuminate\Http\Request();
$request->merge([
    'pivote_id' => $pivotId,
    'subarea_ids' => ['user_' . $user->id],
    'instruccion_user_' . $user->id => 'Atender de inmediato'
]);

$controller->asignar($request, $oficio);

// Assert NO mail was sent to this user
Mail::assertNotSent(\App\Mail\OficioAsignado::class); // using facade mock check
// Let's also check with custom Mail::fake assertNotSent function
Mail::assertNothingSent();
echo "3. Mail was NOT sent because user has notifications disabled? SÍ ✅\n";

// Clean up
$user->forceDelete();
$oficio->delete();
DB::table('area_oficio')->where('id', $pivotId)->delete();
echo "\nTest database cleaned up successfully!\n";
