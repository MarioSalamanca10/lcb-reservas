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
        Schema::create('solicitudes_transporte', function (Blueprint $table) {
    $table->id();
    $table->foreignId('solicitud_id')->constrained('solicitudes_generales')->onDelete('cascade');
    $table->string('nombre_responsable');
    $table->string('celular_responsable');
    $table->string('area_solicitante');
    $table->dateTime('fecha_hora_servicio');
    $table->text('direccion_recogida');
    $table->text('direccion_destino');
    $table->text('direccion_regreso')->nullable();
    $table->dateTime('fecha_hora_regreso')->nullable();
    $table->integer('num_estudiantes')->default(0);
    $table->integer('num_adultos')->default(0);
    $table->json('necesidades_servicio'); // Guarda ["Ida y Vuelta", "Con Monitora"]
    $table->string('estado_transporte')->default('Pendiente');
    $table->text('observaciones')->nullable();
    $table->text('respuesta_coordinador')->nullable();
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
