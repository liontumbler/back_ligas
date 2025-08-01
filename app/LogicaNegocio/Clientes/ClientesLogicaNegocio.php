<?php
namespace App\LogicaNegocio\Clientes;

use App\LogicaNegocio\LogicaNegocio;
use App\Services\EntrenoService;

class ClientesLogicaNegocio extends LogicaNegocio
{
    protected $reglaCrear = [
        'nombres' => 'required|string|max:100',
        'apellidos' => 'required|string|max:100',
        'correo' => 'required|string|email|max:100|unique:clientes,correo',
        'telefono' => 'nullable|string|max:20',
        'equipo_id' => 'nullable|exists:equipos,id',
        'plan_id' => 'nullable|exists:planes,id',
        'liga_id' => 'required|exists:ligas,id',
    ];

    protected $reglaActualizar = [
        'nombres' => 'sometimes|required|string|max:100',
        'apellidos' => 'sometimes|required|string|max:100',
        'correo' => 'sometimes|required|string|email|max:100|unique:clientes,correo',
        'telefono' => 'sometimes|nullable|string|max:20',
        'equipo_id' => 'sometimes|nullable|exists:equipos,id',
        'plan_id' => 'sometimes|nullable|exists:planes,id',
        'liga_id' => 'sometimes|required|exists:ligas,id',
    ];

    public function __construct()
    {
        parent::__construct(new ClienteService(), $this->reglaCrear, $this->reglaActualizar);
    }
}