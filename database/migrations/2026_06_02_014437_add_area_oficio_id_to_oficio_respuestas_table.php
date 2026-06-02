<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaOficioIdToOficioRespuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oficio_respuestas', function (Blueprint $table) {
            // Asegúrate de que el tipo de dato coincida con tu llave primaria en 'area_oficio' (usualmente unsignedBigInteger)
            $table->unsignedBigInteger('area_oficio_id')->after('id');
        });
    }

    public function down()
    {
        Schema::table('oficio_respuestas', function (Blueprint $table) {
            $table->dropColumn('area_oficio_id');
        });
    }
}
