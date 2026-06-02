<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudGeneral extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_generales';
    protected $fillable = ['correo_solicitante', 'titulo_evento', 'estado_global'];

    // --- RELACIONES DE LA ARQUITECTURA ESTRELLA ---
    public function reservaFisica() {
        return $this->hasOne(ReservaFisica::class, 'solicitud_id');
    }

    public function transporte() {
        return $this->hasOne(SolicitudTransporte::class, 'solicitud_id');
    }

    public function restaurante() {
        return $this->hasOne(SolicitudRestaurante::class, 'solicitud_id');
    }
}