<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Services\EntrenoService;

class EntrenosController extends Controller
{
    protected $reglaCrear = [
        'cliente_id'        => 'required|integer|exists:clientes,id',
        'tipo'              => 'required|in:individual,mensualidad,equipo',
        'pago_id'           => 'nullable|integer|exists:pagos,id',
        'mensualidad_id'    => 'nullable|integer|exists:mensualidades,id',
        'liga_id'           => 'required|integer|exists:ligas,id'
    ];

    protected $reglaActualizar = [
        'cliente_id'        => 'sometimes|required|integer|exists:clientes,id',
        'tipo'              => 'sometimes|required|in:individual,mensualidad,equipo',
        'pago_id'           => 'sometimes|nullable|integer|exists:pagos,id',
        'mensualidad_id'    => 'sometimes|nullable|integer|exists:mensualidades,id',
        'liga_id'           => 'sometimes|required|integer|exists:ligas,id'
    ];

    public function __construct()
    {
        parent::__construct(new EntrenoService(), $this->reglaCrear, $this->reglaActualizar);
    }
}
