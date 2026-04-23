<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaUsuario extends Model
{
    use HasFactory;

    protected $table = 'respuesta_usuarios';
    public $timestamps = false;
    
    protected $fillable = [
        'id_pregunta',
        'id_usuario',
        'respuesta_usuario',
        'correcta',
        'id_modulo'
    ];

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'id_pregunta');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function scopeUltimaPreguntaRespondida($query, int $idUsuario, int $idConvocatoria, int $idModulo)
    {
        return $query->where('id_usuario', $idUsuario)
            ->where('id_modulo', $idModulo)
            ->whereHas('pregunta.encabezado.modulo', function ($q) use ($idConvocatoria) {
                $q->where('id_convocatoria', $idConvocatoria);
            })
            ->orderByDesc('id_pregunta')
            ->limit(1);
    }
}