<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Services\PermisoService;

class PermisosController extends Controller
{
    protected $reglaCrear = [
        'menu_id' => 'required|exists:menus,id',
        'action'  => 'required|in:view,create,update,delete',
    ];

    protected $reglaActualizar = [
        'menu_id' => 'sometimes|required|exists:menus,id',
        'action'  => 'sometimes|required|in:view,create,update,delete',
    ];

    public function __construct()
    {
        parent::__construct(new PermisoService(), $this->reglaCrear, $this->reglaActualizar);
    }
}
