<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. (AQUÍ VA LO QUE CONSTRUIMOS)
     */
    public function up(): void
    {
        Schema::create('solicitudes_generales', function (Blueprint $table) {
            $table->id();
            $table->string('correo_solicitante'); 
            $table->string('titulo_evento');
            $table->string('estado_global')->default('Pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations. (AQUÍ VA LA DESTRUCCIÓN)
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_generales');
    }
};