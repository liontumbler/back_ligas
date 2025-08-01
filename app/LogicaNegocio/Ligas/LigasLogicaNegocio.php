<?php
namespace App\LogicaNegocio\Ligas;

use App\LogicaNegocio\LogicaNegocio;
use App\Services\LigaService;

class LigasLogicaNegocio extends LogicaNegocio
{
    protected $reglaCrear = [
        'nombre' => 'required|string|max:100',
        'direccion' => 'nullable|string|max:100',
        'telefono' => 'nullable|string|max:20',
    ];

    protected $reglaActualizar = [
        'nombre' => 'sometimes|required|string|max:100',
        'direccion' => 'sometimes|nullable|string|max:100',
        'telefono' => 'sometimes|nullable|string|max:20'
    ];

    public function __construct()
    {
        parent::__construct(new LigaService(), $this->reglaCrear, $this->reglaActualizar);
    }
}