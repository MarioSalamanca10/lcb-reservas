<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Torre extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    // Una torre tiene muchos espacios
    public function espacios()
    {
        return $this->hasMany(EspacioRecurso::class);
    }
}