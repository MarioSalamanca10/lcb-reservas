<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudGeneral extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_generales';
    protected $fillable = ['correo_solicitante', 'titulo_evento', 'estado_global'];

   // LA QUE YA TENÍAS (1 a 1)
    public function reservaFisica()
    {
        return $this->hasOne(ReservaFisica::class, 'solicitud_id');
    }

    // --- NUEVA RELACIÓN (1 a Muchos) PARA MÚLTIPLES DÍAS ---
    public function reservasFisicas()
    {
        return $this->hasMany(ReservaFisica::class, 'solicitud_id');
    }
    public function transporte() {
        return $this->hasOne(SolicitudTransporte::class, 'solicitud_id');
    }

    public function restaurante() {
        return $this->hasOne(SolicitudRestaurante::class, 'solicitud_id');
    }
    // Trae la encuesta específica del módulo de espacios
    public function encuestaEspacio()
    {
        return $this->hasOne(EncuestaSatisfaccion::class, 'solicitud_id')
                    ->where('modulo_evaluado', 'Espacios');
    }
}