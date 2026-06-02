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
        Schema::create('encuestas_satisfaccion', function (Blueprint $table) {
    $table->id();
    $table->foreignId('solicitud_id')->constrained('solicitudes_generales')->onDelete('cascade');
    $table->string('modulo_evaluado'); // "Espacios", "Transporte" o "Restaurante"
    $table->integer('calificacion_general'); // 1 al 5
    $table->json('respuestas_detalladas'); // Guarda calificaciones dinámicas
    $table->text('observaciones')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
