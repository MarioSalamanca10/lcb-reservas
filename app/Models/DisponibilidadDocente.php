<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisponibilidadDocente extends Model
{
    use HasFactory;

    protected $table = 'disponibilidad_docente';

    protected $fillable = [
        'correo_docente',
        'dia_semana',
        'hora_inicio',
        'hora_fin'
    ];
}