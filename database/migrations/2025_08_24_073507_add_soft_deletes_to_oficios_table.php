<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->softDeletes(); // Esto añade la columna deleted_at
        });
    }

    public function down()
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};