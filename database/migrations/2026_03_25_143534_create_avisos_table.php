<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvisosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Tabla principal de las Circulares
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Quién lo crea (Admin/Secretaria)
            $table->string('titulo');
            $table->text('mensaje');
            $table->enum('prioridad', ['Normal', 'Urgente'])->default('Normal');
            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null'); // Destino específico
            $table->timestamps();
        });

        // 2. Tabla Pivote para los Acuses de Recibo (Relación con Usuarios)
        Schema::create('aviso_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aviso_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('leido_at')->nullable(); // Aquí se guarda la fecha de lectura
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aviso_user');
        Schema::dropIfExists('avisos');
    }
}
