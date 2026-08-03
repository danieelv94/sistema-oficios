<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitudes_vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('estatus')->default('Pendiente'); // Pendiente, Aprobado, Rechazado
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('solicitud_vacacion_fechas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_vacacion_id')->constrained('solicitudes_vacaciones')->onDelete('cascade');
            $table->date('fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_vacacion_fechas');
        Schema::dropIfExists('solicitudes_vacaciones');
    }
};
