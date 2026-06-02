<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudRestaurante extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_restaurante';

    protected $fillable = [
        'solicitud_id', 'fecha_hora_evento', 'num_asistentes',
        'servicio_requerido', 'detalles_solicitud', 'aprobador_id',
        'estado_restaurante', 'respuesta_cocina'
    ];

    protected $casts = [
        'servicio_requerido' => 'array',
        'fecha_hora_evento' => 'datetime'
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudGeneral::class, 'solicitud_id');
    }
}