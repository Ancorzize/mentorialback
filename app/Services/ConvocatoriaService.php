<?php

namespace App\Services;

use App\Repositories\ConvocatoriaRepository;
use App\Repositories\RespuestaUsuarioRepository;
use App\Repositories\ModuloRepository;

class ConvocatoriaService
{
    protected $convocatoriaRepository;
    protected $respuestaUsuarioRepository;
    protected $moduloRepository;

    public function __construct(ConvocatoriaRepository $convocatoriaRepository, 
                                RespuestaUsuarioRepository $respuestaUsuarioRepository,
                                ModuloRepository $moduloRepository
    )
    {
        $this->convocatoriaRepository = $convocatoriaRepository;
        $this->respuestaUsuarioRepository = $respuestaUsuarioRepository;
        $this->moduloRepository = $moduloRepository;
    }

    /**
     * Obtiene todas las convocatorias.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllConvocatorias()
    {
        return $this->convocatoriaRepository->all();
    }

    /**
     * Crea una nueva convocatoria.
     *
     * @param array $data
     * @return Convocatoria
     */
    public function createConvocatoria(array $data)
    {
        return $this->convocatoriaRepository->create($data);
    }

     /**
     * Lista todas las convocatorias, con opción de búsqueda por nombre.
     *
     * @param string $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list(string $query = '', $id_usuario)
    {
        $aList = $this->convocatoriaRepository->list($query);
        return $aList;
    }

    public function obtenerEstadisticasPorUsuario(int $userId) {
        $convocatoriaActiva =  $this->convocatoriaRepository->getConvocatoriasByUsuarioActivas($userId);
        $convocatoriaData = array();    

        foreach($convocatoriaActiva as $convocatoria)
        {
            $modulos = $this->moduloRepository->getModuloByIdConvocatoria($convocatoria->id_convocatoria);
            $moduloData = array();

            foreach($modulos as $modulo)
            {
                $moduloData[] = [
                    "id_modulo" => $modulo->id,
                    "nombre_modulo" => $modulo->nombre
                ];
            }

            $respuestaUsuario = $this->respuestaUsuarioRepository->obtenerEstadisticas($userId, $convocatoria->id_convocatoria);

            $convocatoriaData[] = [
                "id_convocatoria" => $convocatoria->id_convocatoria,
                "id_usuario" => $userId,
                "codigo_convocatoria" => $convocatoria->codigo_convocatoria ,
                "nombre_convocatoria" => $convocatoria->nombre_convocatoria,
                "modulo" => $moduloData,
                "avance" => $respuestaUsuario
            ];
        }
        
        return $convocatoriaData;
        
    }

    public function getConvocatoriasByUsuario(int $userId) {
        return $this->convocatoriaRepository->getConvocatoriasByUsuario($userId);
    }

    
    public function getRespuestasByConvocatoria(int $userId, int $convocatoriaId) {
        return $this->convocatoriaRepository->getRespuestasByConvocatoria($userId, $convocatoriaId);
    }

    /**
     * Lista todas las convocatorias
     *
     * @param string $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listAll()
    {
       return $this->convocatoriaRepository->getConvocatoriasPlataforma();
    }
}
