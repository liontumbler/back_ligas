<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Services\LicenciaService;

class LicenciasController extends Controller
{
    protected $reglaCrear = [
        'codigo'            => 'required|string|unique:licencias,codigo|max:255',
        'valor'             => 'required|numeric|min:0',
        'fecha_inicio'      => 'required|date',
        'fecha_fin'         => 'nullable|date|after_or_equal:fecha_inicio',
        'estado'            => 'required|in:activa,inactiva,vencida'
    ];

    protected $reglaActualizar = [
        'codigo'            => 'sometimes|required|string|unique:licencias,codigo|max:255',
        'valor'             => 'sometimes|required|numeric|min:0',
        'fecha_inicio'      => 'sometimes|required|date',
        'fecha_fin'         => 'sometimes|nullable|date|after_or_equal:fecha_inicio',
        'estado'            => 'sometimes|required|in:activa,inactiva,vencida'
    ];

    public function __construct()
    {
        parent::__construct(new LicenciaService(), $this->reglaCrear, $this->reglaActualizar);
    }
}
