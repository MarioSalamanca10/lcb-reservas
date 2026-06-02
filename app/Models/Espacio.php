<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espacio extends Model
{
    use HasFactory;

    protected $table = 'espacios';

    // Los campos que el formulario tiene permitido guardar
    protected $fillable = [
        'torre_id',
        'nombre',
        'capacidad_personas',
        'descripcion',
        'imagen_url',
        'activo'
    ];

    // Relación: Un espacio pertenece a una Torre
    public function torre()
    {
        return $this->belongsTo(Torre::class, 'torre_id');
    }
}