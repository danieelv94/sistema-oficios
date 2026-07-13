<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOficioHistorialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oficio_historials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oficio_id')->constrained('oficios')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('subarea_id')->nullable();
            $table->string('accion'); // e.g. 'Turnado', 'Recibido', 'Delegado', 'Notificado', 'Solventado', 'Cancelado'
            $table->text('descripcion');
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
        Schema::dropIfExists('oficio_historials');
    }
}
