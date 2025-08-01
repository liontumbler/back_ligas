<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Services\LigaService;

class LigasController extends Controller
{
    protected $reglaCrear = [
        'nombre'            => 'required|string|max:100',
        'direccion'         => 'nullable|string|max:100',
        'telefono'          => 'nullable|string|max:20'
    ];

    protected $reglaActualizar = [
        'nombre'            => 'sometimes|required|string|max:100',
        'direccion'         => 'sometimes|nullable|string|max:100',
        'telefono'          => 'sometimes|nullable|string|max:20'
    ];

    public function __construct()
    {
        parent::__construct(new LigaService(), $this->reglaCrear, $this->reglaActualizar);
    }
}
