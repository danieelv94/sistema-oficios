<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOficiosFirmaDgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oficios_firma_dg', function (Blueprint $table) {
            $table->id();
            $table->string('numero_oficio', 120)->nullable();
            $table->string('tipo_documento', 60)->default('Oficio'); // Oficio, Convenio, Tarjeta, Circular, etc.
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->foreignId('entregado_por_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('entregado_por_nombre', 180)->nullable();
            $table->string('destinatario', 255)->nullable();
            $table->text('asunto');
            $table->string('anexos', 255)->nullable();
            $table->string('prioridad', 30)->default('Normal'); // Normal, Urgente
            $table->dateTime('fecha_recepcion');
            $table->string('estatus', 40)->default('En Firma'); // En Firma, Firmado, Entregado, Devuelto
            $table->dateTime('fecha_firma')->nullable();
            $table->foreignId('recogido_por_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('recogido_por_nombre', 180)->nullable();
            $table->dateTime('fecha_entrega')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('entregado_en_dg_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oficios_firma_dg');
    }
}

