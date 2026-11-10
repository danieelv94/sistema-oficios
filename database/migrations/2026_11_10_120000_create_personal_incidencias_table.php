<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalIncidenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'curp')) {
                $table->string('curp', 25)->nullable()->after('no_empleado');
            }
            if (!Schema::hasColumn('users', 'rfc')) {
                $table->string('rfc', 20)->nullable()->after('curp');
            }
            if (!Schema::hasColumn('users', 'nss')) {
                $table->string('nss', 30)->nullable()->after('rfc');
            }
            if (!Schema::hasColumn('users', 'tipo_sangre')) {
                $table->string('tipo_sangre', 15)->nullable()->after('nss');
            }
            if (!Schema::hasColumn('users', 'telefono')) {
                $table->string('telefono', 30)->nullable()->after('tipo_sangre');
            }
            if (!Schema::hasColumn('users', 'contacto_emergencia_nombre')) {
                $table->string('contacto_emergencia_nombre', 150)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('users', 'contacto_emergencia_telefono')) {
                $table->string('contacto_emergencia_telefono', 30)->nullable()->after('contacto_emergencia_nombre');
            }
            if (!Schema::hasColumn('users', 'contacto_emergencia_parentesco')) {
                $table->string('contacto_emergencia_parentesco', 60)->nullable()->after('contacto_emergencia_telefono');
            }
            if (!Schema::hasColumn('users', 'notas_rh')) {
                $table->text('notas_rh')->nullable()->after('contacto_emergencia_parentesco');
            }
        });

        Schema::create('personal_incidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('registrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->string('categoria', 30)->default('incapacidad'); // 'incapacidad' o 'incidencia'
            $table->string('tipo', 100); // Ej: Enfermedad General, Riesgo de Trabajo, Día Económico, Permiso, etc.
            $table->string('folio', 100)->nullable();
            $table->string('institucion_medica', 100)->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedInteger('dias')->default(1);
            $table->text('observaciones')->nullable();
            $table->string('archivo_path')->nullable();
            $table->string('estatus', 30)->default('Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_incidencias');

        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'curp',
                'rfc',
                'nss',
                'tipo_sangre',
                'telefono',
                'contacto_emergencia_nombre',
                'contacto_emergencia_telefono',
                'contacto_emergencia_parentesco',
                'notas_rh',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}

