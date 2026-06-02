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
        Schema::create('reservas_fisicas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('solicitud_id')->constrained('solicitudes_generales')->onDelete('cascade');
    $table->foreignId('espacio_id')->constrained('espacios')->onDelete('cascade');
    $table->date('fecha_inicio');
    $table->date('fecha_fin')->nullable();
    $table->time('hora_inicio');
    $table->time('hora_fin');
    $table->json('recursos_adicionales')->nullable();
    $table->text('observaciones')->nullable();
    $table->tinyInteger('encuesta_completada')->default(0);
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
