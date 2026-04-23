<?php

namespace App\Services;

use App\Repositories\VisitaRepository;
use App\Models\Visita;

class VisitaService
{
    protected VisitaRepository $visitaRepository;

    public function __construct(VisitaRepository $visitaRepository)
    {
        $this->visitaRepository = $visitaRepository;
    }

    /**
     * Suma una nueva visita del día actual.
     * Si no existe registro para hoy, lo crea en 0 y suma la primera visita.
     * Si ya existe, incrementa el total del día actual.
     */
    public function sumarVisita(string $tipo): Visita
    {
        return $this->visitaRepository->sumarVisita($tipo);
    }
}