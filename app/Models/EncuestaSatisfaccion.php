<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaSatisfaccion extends Model
{
    protected $table = 'encuestas_satisfaccion';
    
    protected $guarded = [];

    // Esta es la magia: Laravel convertirá tu texto JSON en un array automáticamente
    protected $casts = [
        'respuestas_detalladas' => 'array',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudGeneral::class, 'solicitud_id');
    }

    public function encuestaTransporte()
    {
        return $this->hasOne(EncuestaSatisfaccion::class, 'solicitud_id')
                    ->where('modulo_evaluado', 'Transporte');
    }

    public function encuestaRestaurante()
    {
        return $this->hasOne(EncuestaSatisfaccion::class, 'solicitud_id')
                    ->where('modulo_evaluado', 'Restaurante');
    }
}