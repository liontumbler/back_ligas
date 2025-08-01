<?php
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LogicaNegocio\Usuarios\UsuariosLogicaNegocio;

class UsuariosController extends Controller
{
    public function __construct()
    {
        parent::__construct(new UsuariosLogicaNegocio());
    }

    public function login(Request $request)
    {
        return $this->logicaNegocio->login($request);
    }

    public function logout(Request $request)
    {
        return $this->logicaNegocio->logout($request);
    }

    public function refreshToken(Request $request)
    {
        return $this->logicaNegocio->refreshToken($request);
    }
}