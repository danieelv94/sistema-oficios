<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFolioColumnsToAreaOficioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('area_oficio', function (Blueprint $table) {
            $table->string('folio_interno')->nullable()->after('estatus');
            $table->integer('consecutivo')->nullable()->after('folio_interno');
            $table->integer('anio')->nullable()->after('consecutivo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('area_oficio', function (Blueprint $table) {
            $table->dropColumn(['folio_interno', 'consecutivo', 'anio']);
        });
    }
}
