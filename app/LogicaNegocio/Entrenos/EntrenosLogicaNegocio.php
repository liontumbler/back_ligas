<?php
namespace App\LogicaNegocio\Entrenos;

use App\LogicaNegocio\LogicaNegocio;
use App\Services\EntrenoService;

class EntrenosLogicaNegocio extends LogicaNegocio
{
        protected $reglaCrear = [
        'cliente_id'        => 'nullable|integer|exists:clientes,id',
        'tipo'              => 'required|in:individual,mensualidad,equipo',
        'pago_id'           => 'nullable|integer|exists:pagos,id',
        'liga_id'           => 'required|integer|exists:ligas,id'
    ];

    protected $reglaActualizar = [
        'cliente_id'        => 'sometimes|nullable|integer|exists:clientes,id',
        'tipo'              => 'sometimes|required|in:individual,mensualidad,equipo',
        'pago_id'           => 'sometimes|nullable|integer|exists:pagos,id',
        'liga_id'           => 'sometimes|required|integer|exists:ligas,id'
    ];
        public function __construct()
    {
        parent::__construct(new EntrenoService(), $this->reglaCrear, $this->reglaActualizar);
    }
}
