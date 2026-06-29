<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalFieldsToOficiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->unsignedBigInteger('area_origen_id')->nullable()->after('pdf_path');
            $table->integer('consecutivo_origen')->nullable()->after('area_origen_id');
            $table->integer('anio_origen')->nullable()->after('consecutivo_origen');

            $table->foreign('area_origen_id')->references('id')->on('areas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->dropForeign(['area_origen_id']);
            $table->dropColumn(['area_origen_id', 'consecutivo_origen', 'anio_origen']);
        });
    }
}
