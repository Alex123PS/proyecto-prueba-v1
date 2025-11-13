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
        Schema::create('constancias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('dni', 15);
            $table->string('nombres', 100);
            $table->string('ap_paterno', 100);
            $table->string('ap_materno', 100);
            $table->string('ciclo', 100)->nullable(); // Ej. "Ciclo Verano 2025"
            $table->string('sede', 100)->nullable(); // Ej. "Sede Central Puno"
            $table->string('area', 100)->nullable(); // Ej. "Ciencias"
            $table->string('curso', 100)->nullable(); // Ej. "Matemática Básica"
            $table->integer('cantidad_horas')->default(0);
            $table->date('fecha_emision')->nullable();
            $table->enum('estado', ['Emitida', 'Anulada'])->default('Emitida');
            $table->string('emitido_por', 100)->nullable(); // nombre del usuario o autoridad
            $table->string('path_pdf')->nullable(); // ruta del archivo generado
            $table->text('observaciones')->nullable(); // comentarios
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constancias');
    }
};
