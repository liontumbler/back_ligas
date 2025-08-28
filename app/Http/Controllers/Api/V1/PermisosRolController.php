<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Utils\ManejoData;

use App\LogicaNegocio\PermisoRol\PermisoRolLogicaNegocio;

class PermisosRolController extends Controller
{
    public function __construct()
    {
        parent::__construct(new PermisoRolLogicaNegocio());
    }
}