<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\LogicaNegocio\Usuarios\UsuariosLogicaNegocio;

class UsuariosController extends Controller
{
    public function __construct()
    {
        parent::__construct(new UsuariosLogicaNegocio());
    }
}