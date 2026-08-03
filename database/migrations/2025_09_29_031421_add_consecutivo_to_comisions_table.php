<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConsecutivoToComisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('comisions', function (Blueprint $table) {
        $table->unsignedInteger('consecutivo')->after('id');
        $table->year('anio')->after('consecutivo');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comisions', function (Blueprint $table) {
            //
        });
    }
}
