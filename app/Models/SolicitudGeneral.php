<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudGeneral extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_generales';
    protected $guarded = ['id']; // Permite guardado masivo protegiendo el ID

    // --- RELACIONES CON LOS MÓDULOS HIJOS ---
    
    public function reservaFisica()
    {
        return $this->hasOne(ReservaFisica::class, 'solicitud_id');
    }

    public function transporte()
    {
        return $this->hasOne(SolicitudTransporte::class, 'solicitud_id');
    }

    public function restaurante()
    {
        return $this->hasOne(SolicitudRestaurante::class, 'solicitud_id');
    }
}