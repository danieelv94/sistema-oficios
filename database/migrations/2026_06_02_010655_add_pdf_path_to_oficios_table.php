<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfPathToOficiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->string('pdf_path')->nullable();
        });
    }

    public function down()
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }
}
