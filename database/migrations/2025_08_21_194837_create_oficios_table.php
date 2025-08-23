<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOficiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // database/migrations/YYYY_MM_DD_XXXXXX_create_oficios_table.php
        Schema::create('oficios', function (Blueprint $table) {
            $table->id();
            $table->string('numero_oficio');
            $table->string('remitente');
            $table->string('municipio');
            $table->text('asunto');
            $table->date('fecha_recepcion');
            $table->string('estatus')->default('Recibido'); // Recibido, Turnado, Asignado, Finalizado
            $table->unsignedBigInteger('area_id')->nullable(); // Área a la que se turnó
            $table->unsignedBigInteger('user_id')->nullable(); // Persona a la que se asignó
            $table->timestamps();

            $table->foreign('area_id')->references('id')->on('areas')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oficios');
    }
}
