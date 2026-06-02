<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaFisica extends Model
{
    use HasFactory;

    protected $table = 'reservas_fisicas';

    // La "Lista VIP" actualizada a nuestra nueva base de datos
    protected $fillable = [
        'solicitud_id',
        'espacio_id',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'recursos_adicionales',
        'observaciones',
        'encuesta_completada'
    ];

    // Magia de Laravel para convertir el JSON en Arreglo y el 1/0 en True/False
    protected $casts = [
        'recursos_adicionales' => 'array',
        'encuesta_completada' => 'boolean',
    ];

    // --- RELACIONES ---

    public function solicitud()
    {
        return $this->belongsTo(SolicitudGeneral::class, 'solicitud_id');
    }

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'espacio_id');
    }
}