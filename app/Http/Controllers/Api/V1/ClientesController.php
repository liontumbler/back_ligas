<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Services\ClienteService;

class ClientesController extends Controller
{
    protected $reglaCrear = [
        'nombres' => 'required|string|max:100',
        'apellidos' => 'required|string|max:100',
        'correo' => 'required|string|max:20',
        'telefono' => 'nullable|string|max:20',
        'liga_id' => 'required|string'
        
    ];

    protected $reglaActualizar = [
        'nombres' => 'sometimes|required|string|max:100',
        'apellidos' => 'sometimes|required|string|max:100',
        'correo' => 'sometimes|required|string|max:20',
        'telefono' => 'sometimes|nullable|string|max:20',
        'liga_id' => 'sometimes|nullable|string'
    ];

    public function __construct()
    {
        parent::__construct(new ClienteService(), $this->reglaCrear, $this->reglaActualizar);
    }
}