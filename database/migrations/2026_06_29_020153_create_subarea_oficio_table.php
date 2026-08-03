<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubareaOficioTable extends Migration
{
    public function up()
    {
        Schema::create('subarea_oficio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_oficio_id');
            $table->unsignedBigInteger('subarea_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Personal asignado por el subdirector
            $table->text('instruccion')->nullable(); // Instrucción específica para esta subdirección
            $table->string('estatus')->default('Asignado'); // Asignado, Notificado, Solventado
            $table->timestamps();

            $table->foreign('area_oficio_id')->references('id')->on('area_oficio')->onDelete('cascade');
            $table->foreign('subarea_id')->references('id')->on('subareas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Un oficio solo puede asignarse una vez a cada subdirección dentro del mismo turno
            $table->unique(['area_oficio_id', 'subarea_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('subarea_oficio');
    }
}
