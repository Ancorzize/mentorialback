<?php

namespace App\Services;

use App\Repositories\ModuloRepository;
use App\Repositories\RespuestaUsuarioRepository;

class ModuloService
{
    protected $moduloRepository;
    protected $respuestaUsuarioRepository;

    public function __construct(ModuloRepository $moduloRepository, RespuestaUsuarioRepository $respuestaUsuarioRepository)
    {
        $this->moduloRepository = $moduloRepository;
        $this->respuestaUsuarioRepository = $respuestaUsuarioRepository;
    }

    /**
     * Obtiene todos los modulos.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllModulos()
    {
        return $this->moduloRepository->all();
    }

    /**
     * Obtiene todos los modulos por id de convocatoria
     *
     */
    public function getModulosByIdConvocatoria($idConvocatoria, $id_usuario)
    {
        $aList = $this->moduloRepository->getModuloByIdConvocatoria($idConvocatoria);
        foreach($aList as $list)
        {
            $ultima_pregunta = $this->respuestaUsuarioRepository->getUltimaPreguntaRespondida($id_usuario, $idConvocatoria, $list->id);
            $list->ultima_pregunta = $ultima_pregunta;
        }

        return $aList;
    }

    /**
     * Crea un nuevo modulo.
     *
     * @param array $data
     * @return Modulo
     */
    public function createModulo(array $data)
    {
        return $this->moduloRepository->create($data);
    }
}
