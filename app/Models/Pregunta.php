<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;

    protected $table = 'preguntas';
    public $timestamps = false;

    protected $fillable = [
        'id_encabezado',
        'pregunta',
    ];

    public function encabezado()
    {
        return $this->belongsTo(Encabezado::class, 'id_encabezado');
    }

    public function opciones()
    {
        return $this->hasMany(Opcion::class, 'id_pregunta');
    }

    
}
