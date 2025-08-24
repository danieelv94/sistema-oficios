<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToOficiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // database/migrations/YYYY_MM_DD_XXXXXX_add_details_to_oficios_table.php

        public function up()
        {
            Schema::table('oficios', function (Blueprint $table) {
                $table->string('tipo_correspondencia')->after('asunto')->nullable();
                $table->string('prioridad')->after('tipo_correspondencia')->nullable();
                $table->string('numero_oficio_dependencia')->after('prioridad')->nullable();
                $table->date('fecha_limite')->after('numero_oficio_dependencia')->nullable();
                $table->string('localidad')->after('municipio')->nullable();
                $table->text('observaciones')->after('localidad')->nullable();
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
            //
        });
    }
}
