// database/migrations/YYYY_MM_DD_XXXXXX_remove_area_and_user_from_oficios_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('oficios', function (Blueprint $table) {
            // Primero removemos las llaves foráneas si existen
            $table->dropForeign(['area_id']);
            $table->dropForeign(['user_id']);

            // Ahora removemos las columnas
            $table->dropColumn(['area_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::table('oficios', function (Blueprint $table) {
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('area_id')->references('id')->on('areas');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};