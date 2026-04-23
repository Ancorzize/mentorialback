<?php

namespace App\Http\Controllers;

use App\Services\VisitaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class VisitaController extends Controller
{
    protected $visitaService;

    public function __construct(VisitaService $visitaService)
    {
        $this->visitaService = $visitaService;
    }

     /**
     * registrar nueva visita
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sumarVisita(Request $request)
    {
        try {
            $tipovisita = $request->input('tipo');

            $visita = $this->visitaService->sumarVisita($tipovisita);
            return response()->json($visita, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al registrar visita'], 500);
        }
    }

}