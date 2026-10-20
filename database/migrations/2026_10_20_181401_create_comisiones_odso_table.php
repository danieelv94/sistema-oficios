<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComisionesOdsoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comisiones_odso', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('consecutivo');
            $table->year('anio');
            $table->string('oficio_numero')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('jefe_area_id')->constrained('users')->onDelete('cascade');
            $table->string('dias_comision');
            $table->string('hora_inicio')->nullable();
            $table->string('hora_fin')->nullable();
            $table->text('actividad');
            $table->string('lugar');
            $table->foreignId('vehiculo_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('proyecto_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('unidad_administrativa_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('Autorizado');
            $table->boolean('entregado_acuse')->default(false);
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
        Schema::dropIfExists('comisiones_odso');
    }
}
