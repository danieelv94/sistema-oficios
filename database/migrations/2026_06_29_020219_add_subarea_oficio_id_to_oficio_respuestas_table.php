<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubareaOficioIdToOficioRespuestasTable extends Migration
{
    public function up()
    {
        Schema::table('oficio_respuestas', function (Blueprint $table) {
            $table->unsignedBigInteger('subarea_oficio_id')->nullable()->after('area_oficio_id');
            $table->foreign('subarea_oficio_id')->references('id')->on('subarea_oficio')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('oficio_respuestas', function (Blueprint $table) {
            $table->dropForeign(['subarea_oficio_id']);
            $table->dropColumn('subarea_oficio_id');
        });
    }
}
