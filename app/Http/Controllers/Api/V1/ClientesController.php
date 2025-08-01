<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\LogicaNegocio\Clientes\ClientesLogicaNegocio;

class ClientesController extends Controller
{
    public function __construct()
    {
        parent::__construct(new ClientesLogicaNegocio());
    }
}