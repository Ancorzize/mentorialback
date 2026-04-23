<?php

namespace App\Repositories;

use App\Models\Visita;
use Carbon\Carbon;

class VisitaRepository
{
    public function sumarVisita(string $tipo): Visita
    {
        $hoy = Carbon::today()->toDateString();

        $visita = Visita::firstOrCreate(
            [
                'tipo' => $tipo,
                'fecha' => $hoy,
            ],
            [
                'total' => 0,
            ]
        );

        $visita->increment('total');
        $visita->refresh();

        return $visita;
    }
}