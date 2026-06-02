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
        Schema::create('tickets_soporte', function (Blueprint $table) {
    $table->id();
    $table->foreignId('solicitud_id')->constrained('solicitudes_generales')->onDelete('cascade');
    
    // Clasificación del ticket
    $table->string('tipo_soporte'); // 'Sistemas' o 'Mantenimiento'
    $table->string('categoria'); // Ej: 'Hardware', 'Redes', 'Infraestructura', 'Plomería'
    $table->string('prioridad')->default('Media'); // Baja, Media, Alta, Crítica
    
    // Detalles del problema
    $table->text('descripcion_falla');
    $table->string('ubicacion_exacta'); // Ej: 'Salón 204', 'Oficina de Coordinación'
    
    // Gestión interna
    $table->string('estado_ticket')->default('Abierto'); // Abierto, En Proceso, Resuelto, Cerrado
    $table->foreignId('tecnico_asignado_id')->nullable()->constrained('users')->onDelete('set null');
    $table->text('notas_resolucion')->nullable();
    
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_soporte');
    }
};
