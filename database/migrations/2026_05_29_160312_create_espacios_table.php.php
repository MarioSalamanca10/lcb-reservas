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
        Schema::create('espacios', function (Blueprint $table) {
    $table->id();
    $table->foreignId('torre_id')->constrained('torres')->onDelete('cascade');
    $table->string('nombre');
    $table->integer('capacidad_personas');
    $table->text('descripcion')->nullable();
    $table->string('imagen_url')->nullable();
    $table->tinyInteger('activo')->default(1); // 1: Activo, 0: En mantenimiento
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
