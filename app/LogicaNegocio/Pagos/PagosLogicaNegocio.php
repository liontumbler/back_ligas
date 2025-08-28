<?php
namespace App\LogicaNegocio\Pagos;

use App\LogicaNegocio\LogicaNegocio;
use App\Services\PagoService;

class PagosLogicaNegocio extends LogicaNegocio
{
    protected $reglaCrear = [
    'valor'      => 'required|numeric|min:0',
    'fecha_pago' => 'nullable|date',
    'liga_id'    => 'required|integer|exists:ligas,id',
    ];

    protected $reglaActualizar = [
    'valor'      => 'sometimes|required|numeric|min:0',
    'fecha_pago' => 'sometimes|nullable|date',
    'liga_id'    => 'sometimes|required|integer|exists:ligas,id',
    ];

    public function __construct()
    {
        parent::__construct(new PagoService(), $this->reglaCrear, $this->reglaActualizar);
    }
}