<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Services\LigaService;

class LigasController extends Controller
{
    protected $reglaCrear = [
        'cliente_id'            => 'required|integer|exists:clientes,id',
        'fecha_inicio'          => 'required|date',
        'fecha_fin'             => 'required|date|after_or_equal:fecha_inicio',
        'sesiones_disponibles'  => 'required|integer|min:0',
        'sesiones_usadas'       => 'nullable|integer|min:0',
        'liga_id'               => 'required|integer|exists:ligas,id'
    ];

    protected $reglaActualizar = [
        'cliente_id'            => 'sometimes|required|integer|exists:clientes,id',
        'fecha_inicio'          => 'sometimes|required|date',
        'fecha_fin'             => 'sometimes|required|date|after_or_equal:fecha_inicio',
        'sesiones_disponibles'  => 'sometimes|required|integer|min:0',
        'sesiones_usadas'       => 'sometimes|nullable|integer|min:0',
        'liga_id'               => 'sometimes|required|integer|exists:ligas,id'
    ];

    public function __construct()
    {
        parent::__construct(new LicenciaService(), $this->reglaCrear, $this->reglaActualizar);
    }
}
