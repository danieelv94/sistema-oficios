<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToOficioRespuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oficio_respuestas', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('area_oficio_id');
            $table->string('tipo_respuesta')->after('user_id');
            $table->text('mensaje')->after('tipo_respuesta');
            $table->string('archivo_evidencia')->nullable()->after('mensaje');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oficio_respuestas', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'tipo_respuesta', 'mensaje', 'archivo_evidencia']);
        });
    }
}
