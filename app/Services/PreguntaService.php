<?php

namespace App\Services;

use App\Repositories\ConvocatoriaRepository;
use App\Repositories\ModuloRepository;
use App\Repositories\PreguntaRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;

class PreguntaService
{
    protected $preguntaRepository;
    protected $convocatoriaRepository;
    protected $moduloRepository;

    public function __construct(PreguntaRepository $preguntaRepository, ConvocatoriaRepository $convocatoriaRepository, ModuloRepository $moduloRepository)
    {
        $this->preguntaRepository = $preguntaRepository;
        $this->convocatoriaRepository = $convocatoriaRepository;
        $this->moduloRepository = $moduloRepository;
    }

    /**
     * Registra una nueva pregunta.
     *
     * @param array $data
     * @return \App\Models\Pregunta
     */
    public function createPregunta(array $data)
    {
        $validator = Validator::make($data, [
            'id_encabezado' => 'required|exists:encabezados,id',
            'pregunta' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->preguntaRepository->create($data);
    }

    /**
     * Obtiene y formatea las preguntas para un módulo y usuario específicos.
     *
     * @param array $data
     * @return array
     */
    public function getFormattedPreguntas(array $data)
    {
        $validator = Validator::make($data, [
            'id_convocatoria' => 'required|integer',
            'id_usuario' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $preguntas_flat = $this->preguntaRepository->getPreguntasByModuloAndUser(
            $data['id_convocatoria'],
            $data['id_usuario']
        );

        // Si no se encuentran preguntas, se devuelve un array vacío
        if ($preguntas_flat->isEmpty()) {
            return [
                'convocatoria' => null,
                'modulo' => null,
                'data' => [],
            ];
        }

        $firstItem = $preguntas_flat->first();

        $formattedData = [
            'convocatoria' => [
                'id_convocatoria' => $firstItem->id_convocatoria,
                'nombre' => $firstItem->convocatoria_nombre,
            ],
            'modulo' => [
                'id_modulo' => $firstItem->id_modulo,
                'nombre' => $firstItem->modulo_nombre,
            ],
            'data' => []
        ];

        // Agrupa las preguntas y opciones
        $preguntasAgrupadas = $preguntas_flat->groupBy('id_pregunta');

        foreach ($preguntasAgrupadas as $preguntaId => $opciones) {
            $primeraOpcion = $opciones->first();
            $preguntaData = [
                'encabezado' => [
                    'id_encabezado' => $primeraOpcion->id_encabezado,
                    'encabezado' => $primeraOpcion->encabezado,
                ],
                'preguntas' => [
                    'id_pregunta' => $primeraOpcion->id_pregunta,
                    'pregunta' => $primeraOpcion->pregunta,
                ],
                'opciones' => $opciones->map(function ($opcion) {
                    return [
                        'opcion' => $opcion->opcion,
                        'descripcion_opcion' => $opcion->descripcion_opcion,
                        'correcta' => (bool)$opcion->correcta,
                    ];
                })->values()->all(),
            ];
            $formattedData['data'][] = $preguntaData;
        }

        return $formattedData;
    }

    public function createBulk(array $data): array
    {
        $validator = Validator::make($data, [
            '*.encabezados' => 'required|string',
            '*.data' => 'required|array',
            '*.data.*.preguntas' => 'required|string',
            '*.data.*.opciones' => 'required|array|min:1',
            '*.data.*.opciones.*.opcion' => 'required|string',
            '*.data.*.opciones.*.descripcion_opcion' => 'required|string',
            '*.data.*.opciones.*.correcta' => 'required|boolean',
            //'*.data.*.opciones.*.retroalimentacion' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'message' => ['success' => false, 'message' => $validator->errors()],
                'status' => 400
            ];
        }

        try {
            $this->preguntaRepository->createBulk($data);
            return [
                'message' => ['success' => true, 'message' => 'Preguntas registradas masivamente con éxito.'],
                'status' => 201
            ];
        } catch (\Exception $e) {
            return [
                'message' => ['success' => false, 'message' => 'Error al registrar las preguntas: ' . $e->getMessage()],
                'status' => 500
            ];
        }
    }

    public function getPreguntasByConvocatoria(
        int $convocatoriaId,
        int $userId,
        ?int $moduloId = null,
        int $idUltimaPregunta = 0
    ) {
        $registro = $this->convocatoriaRepository->validarActivacionConvocatoria($convocatoriaId, $userId);
        if (!$registro) {
            $this->convocatoriaRepository->registrarUsuarioConvocatoria($convocatoriaId, $userId);
            $registro = $this->convocatoriaRepository->validarActivacionConvocatoria($convocatoriaId, $userId);
        }

        $limitFree = 18;
        $limit = 9;
        if ($registro->estado == false) {
            $respuestasCount = $this->convocatoriaRepository->contarRespuestasPorConvocatoria($convocatoriaId, $userId);
            
            $remaining = $limitFree - $respuestasCount;
            if ($remaining <= 0) {
                return response()->json([
                    "message" => "Ya ha respondido 18 preguntas gratuitas. Debe solicitar la activación de la convocatoria.",
                    "data"    => null,
                    "inactivo" => true
                ], 200);
            }
            $limit = min(9, $remaining);
        }

        $convocatoria = $this->convocatoriaRepository->getConvocatoriaByUser($convocatoriaId, $userId);

        $moduloQuery = $this->moduloRepository->getModuloByIdConvocatoriaCollection($convocatoriaId);
        if ($moduloId) {
            $moduloQuery->where('id', $moduloId);
        }
        $modulo = $moduloQuery->select('id','nombre','id_convocatoria')->firstOrFail();


        $encabezados = \App\Models\Encabezado::where('id_modulo', $modulo->id)
            ->select('id','texto','id_modulo')
            ->orderBy('id')
            ->get();


        $encabezadoIds = $encabezados->pluck('id')->toArray();

        $preguntas = \App\Models\Pregunta::from('preguntas as p')
            ->join('encabezados as e', 'e.id', '=', 'p.id_encabezado')
            ->with(['opciones' => function ($q) {
                $q->select('id', 'id_pregunta', 'opcion', 'descripcion_opcion', 'correcta', 'retroalimentacion')
                ->orderBy('opcion');
            }])
            ->select('p.*', 'e.id_modulo')
            ->whereIn('p.id_encabezado', $encabezadoIds)
            ->where('e.id_modulo', $moduloId)
            ->when($idUltimaPregunta > 0, function ($q) use ($idUltimaPregunta) {
                $q->where('p.id', '>', $idUltimaPregunta);
            })
            ->orderBy('p.id')
            ->limit($limit)
            ->get();

        if ($preguntas->isEmpty()) {
            return response()->json([
                "message" => "No hay más preguntas disponibles.",
                "data"    => null
            ], 200);
        }

        $preguntasPorEncabezado = $preguntas->groupBy('id_encabezado');

        $encabezadosConPreguntas = $encabezados->filter(function($en) use ($preguntasPorEncabezado) {
            return $preguntasPorEncabezado->has($en->id) && $preguntasPorEncabezado->get($en->id)->isNotEmpty();
        });

        $dataEncabezados = $encabezadosConPreguntas->map(function ($en) use ($preguntasPorEncabezado, $modulo) {
            $pregs = $preguntasPorEncabezado->get($en->id);

            $pregsFormateadas = $pregs->map(function ($pr) {
                return [
                    "pregunta" => [
                        "id_pregunta" => $pr->id,
                        "pregunta"    => $pr->pregunta,
                    ],
                    "opciones" => $pr->opciones->map(function ($op) {
                        return [
                            "id"                => $op->id,
                            "opcion"             => $op->opcion,
                            "descripcion_opcion" => $op->descripcion_opcion,
                            "correcta"           => (bool) $op->correcta,
                            "retroalimentacion" => $op->retroalimentacion
                        ];
                    })->values(),
                ];
            })->values();

            return [
                "encabezado" => [
                    "id_encabezado" => $en->id,
                    "encabezado"    => $en->texto,
                ],
                "modulo" => [
                    "id_modulo" => $modulo->id,
                    "nombre"    => $modulo->nombre,
                ],
                "preguntas" => $pregsFormateadas,
            ];
        })->values();

        $ultimaPreguntaEnviada = $preguntas->last()->id ?? $idUltimaPregunta;

        $payload = [
            "message" => "Preguntas obtenidas exitosamente.",
            "data" => [
                "convocatoria" => [
                    "id_convocatoria" => $convocatoria->id,
                    "nombre"          => $convocatoria->nombre,
                ],
                "modulo" => [
                    "id_modulo" => $modulo->id,
                    "nombre"    => $modulo->nombre,
                ],
                "data" => $dataEncabezados,
              
                "ultima_pregunta_enviada" => $ultimaPreguntaEnviada,
            ],
        ];

        return response()->json($payload);
    }

}