<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convocatoria extends Model
{
    use HasFactory;

    protected $table = 'convocatorias';
    public $timestamps = false;
    
    protected $fillable = [
        'codigo',
        'nombre',
        'logotipo',
        'descripcion',
        'enlace',
        'etiqueta',
        'noticiaplataforma'
    ];

    /**
     * Relación con la tabla de modulos.
     */
    public function modulos()
    {
        return $this->hasMany(Modulo::class, 'id_convocatoria');
    }

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,                  
            'convocatoria_x_usuarios',   
            'id_convocatoria',           
            'id_usuario'                
        )->withPivot('estado');
    }
    
}
