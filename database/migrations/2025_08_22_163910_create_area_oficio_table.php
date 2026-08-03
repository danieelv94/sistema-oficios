// database/migrations/YYYY_MM_DD_XXXXXX_create_area_oficio_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('area_oficio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oficio_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Persona asignada
            $table->string('instruccion'); // Aquí guardaremos la instrucción
            $table->string('estatus')->default('Turnado'); // Turnado, Asignado, Finalizado
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('area_oficio');
    }
};