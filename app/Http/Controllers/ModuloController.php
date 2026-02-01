<?php

namespace App\Http\Controllers;

use App\Services\ModuloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
class ModuloController extends Controller
{
    protected $moduloService;

    public function __construct(ModuloService $moduloService)
    {
        $this->moduloService = $moduloService;
    }

    /**
     * Lista todos los modulos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $modulos = $this->moduloService->getAllModulos();
        return response()->json($modulos);
    }

     /**
     * Lista todas las convocatorias, con opción de búsqueda.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModulos()
    {
        try {
            $idconvocatoria = request()->query('id_convocatoria'); 

            $convocatorias = $this->moduloService->getModulosByIdConvocatoria($idconvocatoria);
            return response()->json($convocatorias, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al obtener los modulos de la convocatoria.'], 500);
        }
    }

    /**
     * Registra un nuevo modulo.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_convocatoria' => 'required|exists:convocatorias,id',
            'nombre' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $modulo = $this->moduloService->createModulo($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Módulo registrado exitosamente.',
            'modulo' => $modulo,
        ], 201);
    }
}