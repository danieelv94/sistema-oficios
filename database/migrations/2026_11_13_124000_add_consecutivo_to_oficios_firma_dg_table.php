<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddConsecutivoToOficiosFirmaDgTable extends Migration
{
    public function up()
    {
        Schema::table('oficios_firma_dg', function (Blueprint $table) {
            $table->unsignedInteger('consecutivo')->nullable()->after('id')->index();
        });

        // Re-numerar consecutivamente (1, 2, 3...) todos los registros existentes sin saltos
        $registros = DB::table('oficios_firma_dg')
            ->orderBy('fecha_recepcion', 'asc')
            ->orderBy('id', 'asc')
            ->get(['id']);

        $contador = 1;
        foreach ($registros as $reg) {
            DB::table('oficios_firma_dg')
                ->where('id', $reg->id)
                ->update(['consecutivo' => $contador]);
            $contador++;
        }
    }

    public function down()
    {
        Schema::table('oficios_firma_dg', function (Blueprint $table) {
            $table->dropColumn('consecutivo');
        });
    }
}

