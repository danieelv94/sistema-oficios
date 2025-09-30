<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() { Schema::create('comisions', function (Blueprint $table) { $table->id(); $table->string('oficio_numero')->unique(); $table->foreignId('user_id')->constrained()->onDelete('cascade'); $table->foreignId('jefe_area_id')->constrained('users')->onDelete('cascade'); $table->string('dias_comision'); $table->text('actividad'); $table->string('lugar'); $table->foreignId('vehiculo_id')->nullable()->constrained()->onDelete('set null'); $table->foreignId('proyecto_id')->nullable()->constrained()->onDelete('set null'); $table->timestamps(); }); }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comisions');
    }
}
