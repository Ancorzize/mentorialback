<?php

namespace App\Repositories;

use App\Models\RespuestaUsuario;
use Illuminate\Support\Facades\DB;
class RespuestaUsuarioRepository 
{
    /**
     * @param array $data
     * @return RespuestaUsuario
     */
    public function create(array $data): RespuestaUsuario
    {
        return RespuestaUsuario::create($data);
    }

    public function getUltimaPreguntaRespondida(int $idUsuario, int $idConvocatoria, int $idModulo): ?int
    {
        return RespuestaUsuario::ultimaPreguntaRespondida($idUsuario, $idConvocatoria, $idModulo)
            ->value('id_pregunta');
    }

    public function deleteHistorial($id_usuario, $id_convocatoria)
    {
        return RespuestaUsuario::where('id_usuario', $id_usuario)
            ->whereIn('id_pregunta', function($query) use ($id_convocatoria) {
                $query->select('r.id_pregunta')
                      ->from('respuesta_usuarios as r')
                      ->join('preguntas as p', 'p.id', '=', 'r.id_pregunta')
                      ->join('encabezados as e', 'e.id', '=', 'p.id_encabezado')
                      ->join('modulos as m', 'm.id', '=', 'e.id_modulo')
                      ->where('m.id_convocatoria', $id_convocatoria);
            })
            ->delete();
    }

    public function obtenerEstadisticas($idUsuario, $idConvocatoria)
    {
        $resultado = DB::table('preguntas as p')
            ->join('encabezados as e', 'e.id', '=', 'p.id_encabezado')
            ->join('modulos as m', 'm.id', '=', 'e.id_modulo')
            ->leftJoin('respuesta_usuarios as r', function ($join) use ($idUsuario) {
                $join->on('r.id_pregunta', '=', 'p.id')
                     ->where('r.id_usuario', '=', $idUsuario);
            })
            ->where('m.id_convocatoria', $idConvocatoria)
            ->selectRaw("
                COUNT(DISTINCT p.id) FILTER (WHERE r.id_usuario = ?) AS total_contestadas,
                COUNT(DISTINCT p.id) FILTER (WHERE r.id_usuario = ? AND r.correcta = true) AS total_correctas,
                COUNT(DISTINCT p.id) FILTER (WHERE r.id_usuario = ? AND r.correcta = false) AS total_incorrectas,
                COUNT(DISTINCT p.id) AS total_preguntas
            ", [$idUsuario, $idUsuario, $idUsuario])
            ->first();

        return $resultado;
    }
}
