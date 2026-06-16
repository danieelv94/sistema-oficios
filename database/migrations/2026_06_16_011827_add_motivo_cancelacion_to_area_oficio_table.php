<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMotivoCancelacionToAreaOficioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('area_oficio', function (Blueprint $table) {
            $table->text('motivo_cancelacion')->nullable()->after('anio');
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
            $table->dropColumn('motivo_cancelacion');
        });
    }
}
