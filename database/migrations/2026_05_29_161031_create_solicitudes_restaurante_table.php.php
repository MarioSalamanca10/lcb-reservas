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
        Schema::create('solicitudes_restaurante', function (Blueprint $table) {
    $table->id();
    $table->foreignId('solicitud_id')->constrained('solicitudes_generales')->onDelete('cascade');
    $table->dateTime('fecha_hora_evento');
    $table->integer('num_asistentes');
    $table->json('servicio_requerido'); // Guarda ["Almuerzo", "Estación de café"]
    $table->text('detalles_solicitud')->nullable();
    // Guarda el ID del administrador/superadmin asignado para darle el visto bueno
    $table->foreignId('aprobador_id')->nullable()->constrained('users')->onDelete('set null');
    $table->string('estado_restaurante')->default('Pendiente');
    $table->text('respuesta_cocina')->nullable();
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
