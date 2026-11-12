<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelacionToOficiosFirmaDgTable extends Migration
{
    public function up()
    {
        Schema::table('oficios_firma_dg', function (Blueprint $table) {
            $table->text('motivo_cancelacion')->nullable()->after('observaciones');
            $table->dateTime('fecha_cancelacion')->nullable()->after('motivo_cancelacion');
            $table->foreignId('cancelado_por')->nullable()->after('entregado_en_dg_por')->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('oficios_firma_dg', function (Blueprint $table) {
            $table->dropForeign(['cancelado_por']);
            $table->dropColumn(['motivo_cancelacion', 'fecha_cancelacion', 'cancelado_por']);
        });
    }
}

