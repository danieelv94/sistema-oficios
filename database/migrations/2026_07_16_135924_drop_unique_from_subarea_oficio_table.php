<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueFromSubareaOficioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subarea_oficio', function (Blueprint $table) {
            $table->dropForeign('subarea_oficio_area_oficio_id_foreign');
            $table->dropUnique('subarea_oficio_area_oficio_id_subarea_id_unique');
            
            $table->unique(['area_oficio_id', 'subarea_id', 'user_id'], 'subarea_oficio_area_subarea_user_unique');
            $table->foreign('area_oficio_id')->references('id')->on('area_oficio')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subarea_oficio', function (Blueprint $table) {
            $table->dropForeign('subarea_oficio_area_oficio_id_foreign');
            $table->dropUnique('subarea_oficio_area_subarea_user_unique');
            
            $table->unique(['area_oficio_id', 'subarea_id'], 'subarea_oficio_area_oficio_id_subarea_id_unique');
            $table->foreign('area_oficio_id')->references('id')->on('area_oficio')->onDelete('cascade');
        });
    }
}
