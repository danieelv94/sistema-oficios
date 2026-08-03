<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // database/migrations/YYYY_MM_DD_XXXXXX_create_tickets_table.php
public function up()
{
    Schema::create('tickets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // El usuario que creó el ticket
        $table->string('subject'); // Asunto o título corto del problema
        $table->text('description'); // Descripción detallada
        $table->string('status')->default('Pendiente'); // Puede ser 'Pendiente' o 'Concluido'
        $table->text('resolution_notes')->nullable(); // Notas del admin sobre cómo se resolvió
        $table->string('evidence_path')->nullable(); // Ruta a la imagen de evidencia
        $table->timestamp('completed_at')->nullable(); // Fecha en que se concluyó
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
        Schema::dropIfExists('tickets');
    }
}
