<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla principal: pnt_procedimientos
        Schema::create('pnt_procedimientos', function (Blueprint $table) {
            $table->id();
            
            // Sección 1: Licitaciones
            $table->integer('ejercicio');
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->string('tipo_procedimiento');
            $table->string('tipo_contratacion');
            $table->string('caracter_procedimiento');
            $table->string('numero_expediente');
            $table->string('declarado_desierto');
            $table->text('fundamentos_legales');
            $table->string('suficiencia_presupuestal_url')->nullable();
            $table->string('convocatoria_url')->nullable();
            $table->date('fecha_convocatoria')->nullable();
            $table->text('descripcion_bienes')->nullable();
            $table->date('fecha_junta_aclaraciones')->nullable();
            $table->string('acta_junta_url')->nullable();
            $table->string('acta_apertura_url')->nullable();
            $table->string('dictamen_fallo_url')->nullable();
            $table->string('acta_fallo_url')->nullable();
            $table->string('ganador_fisico_nombre')->nullable();
            $table->string('ganador_fisico_primer_apellido')->nullable();
            $table->string('ganador_fisico_segundo_apellido')->nullable();
            $table->string('ganador_fisico_sexo')->nullable();
            
            // Sección 2: Suministros / Recursos Materiales
            $table->string('proveedor_ganador_nombre')->nullable();
            $table->string('proveedor_ganador_rfc')->nullable();
            $table->text('proveedor_ganador_domicilio')->nullable();
            $table->decimal('monto_contrato_min', 15, 2)->nullable();
            $table->decimal('monto_contrato_max', 15, 2)->nullable();
            $table->date('fecha_inicio_contrato')->nullable();
            $table->date('fecha_fin_contrato')->nullable();
            $table->string('forma_pago')->nullable();
            $table->text('objeto_contrato')->nullable();
            $table->text('justificacion_adjudicacion')->nullable();
            $table->date('fecha_contrato')->nullable();
            $table->decimal('tipo_cambio', 15, 4)->nullable();
            $table->decimal('monto_garantias', 15, 2)->nullable();
            $table->string('contrato_url')->nullable();
            $table->string('comunicado_suspension_url')->nullable();
            
            // Sección 3: Infraestructura / Áreas Técnicas
            $table->string('ejecucion_obra')->nullable();
            $table->string('origen_recursos')->nullable();
            $table->string('fuente_financiamiento')->nullable();
            $table->string('tipo_fondo')->nullable();
            $table->string('lugar_ejecucion')->nullable();
            $table->text('descripcion_obra')->nullable();
            $table->string('impacto_ambiental_url')->nullable();
            $table->text('observaciones_obra')->nullable();
            $table->string('etapa_obra')->nullable();
            $table->text('mecanismos_vigilancia')->nullable();
            $table->string('informe_avances_fisicos_url')->nullable();
            $table->string('informe_avances_financieros_url')->nullable();
            $table->string('acta_recepcion_url')->nullable();
            $table->string('finiquito_url')->nullable();
            $table->string('factura_url')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });

        // 2. Tabla relacional: Licitantes (Tabla_579209)
        Schema::create('pnt_licitantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pnt_procedimiento_id')->constrained('pnt_procedimientos')->onDelete('cascade');
            $table->string('primer_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('sexo')->nullable(); // catálogo: Hombre, Mujer
            $table->string('razon_social')->nullable();
            $table->string('rfc')->nullable();
            $table->timestamps();
        });

        // 3. Tabla relacional: Ofertas (Tabla_579236)
        Schema::create('pnt_cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pnt_procedimiento_id')->constrained('pnt_procedimientos')->onDelete('cascade');
            $table->string('primer_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('sexo')->nullable(); // catálogo: Hombre, Mujer
            $table->string('razon_social')->nullable();
            $table->string('rfc')->nullable();
            $table->timestamps();
        });

        // 4. Tabla relacional: Participantes Junta (Tabla_579237)
        Schema::create('pnt_junta_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pnt_procedimiento_id')->constrained('pnt_procedimientos')->onDelete('cascade');
            $table->string('primer_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('sexo')->nullable(); // catálogo: Hombre, Mujer
            $table->string('razon_social')->nullable();
            $table->string('rfc')->nullable();
            $table->timestamps();
        });

        // 5. Tabla relacional: Servidores Junta (Tabla_579238)
        Schema::create('pnt_junta_servidores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pnt_procedimiento_id')->constrained('pnt_procedimientos')->onDelete('cascade');
            $table->string('primer_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('sexo')->nullable(); // catálogo: Hombre, Mujer
            $table->string('rfc')->nullable();
            $table->string('cargo')->nullable();
            $table->timestamps();
        });

        // 6. Tabla relacional: Beneficiarios (Tabla_579206)
        Schema::create('pnt_beneficiarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pnt_procedimiento_id')->constrained('pnt_procedimientos')->onDelete('cascade');
            $table->string('primer_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->timestamps();
        });

        // 7. Tabla relacional: Partidas (Tabla_579239)
        Schema::create('pnt_partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pnt_procedimiento_id')->constrained('pnt_procedimientos')->onDelete('cascade');
            $table->string('numero_partida');
            $table->timestamps();
        });

        // 8. Tabla relacional: Convenios (Tabla_579240)
        Schema::create('pnt_convenios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pnt_procedimiento_id')->constrained('pnt_procedimientos')->onDelete('cascade');
            $table->string('numero_convenio');
            $table->text('objeto');
            $table->decimal('monto_modificado', 15, 2);
            $table->date('fecha_firma');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pnt_convenios');
        Schema::dropIfExists('pnt_partidas');
        Schema::dropIfExists('pnt_beneficiarios');
        Schema::dropIfExists('pnt_junta_servidores');
        Schema::dropIfExists('pnt_junta_participantes');
        Schema::dropIfExists('pnt_cotizaciones');
        Schema::dropIfExists('pnt_licitantes');
        Schema::dropIfExists('pnt_procedimientos');
    }
};
