<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora_actividades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_nombre', 180)->nullable();
            $table->string('user_role', 60)->nullable();
            $table->string('area_nombre', 180)->nullable();

            $table->string('modulo', 80)->index(); // Ej: Firma DG, Oficios, Comisiones, Personal CEAA, Usuarios, Avisos, Tickets, Seguridad
            $table->string('accion', 60)->index(); // Ej: Creación, Actualización, Eliminación, Inicio de Sesión, Cierre de Sesión
            $table->string('descripcion', 500);

            $table->string('modelo_type', 120)->nullable();
            $table->unsignedBigInteger('modelo_id')->nullable();

            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora_actividades');
    }
};

