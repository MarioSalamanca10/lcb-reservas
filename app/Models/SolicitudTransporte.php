<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudTransporte extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_transporte';

    // Lista VIP de campos permitidos
    protected $fillable = [
        'solicitud_id', 'nombre_responsable', 'celular_responsable',
        'area_solicitante', 'fecha_hora_servicio', 'direccion_recogida',
        'direccion_destino', 'direccion_regreso', 'fecha_hora_regreso',
        'num_estudiantes', 'num_adultos', 'necesidades_servicio',
        'estado_transporte', 'respuesta_coordinador',
        'observaciones'
    ];

    // Magia para convertir los checkboxes (JSON) a Arreglo de PHP
    protected $casts = [
        'necesidades_servicio' => 'array',
        'fecha_hora_servicio' => 'datetime',
        'fecha_hora_regreso' => 'datetime'
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudGeneral::class, 'solicitud_id');
    }
}