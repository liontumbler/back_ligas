<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\LogicaNegocio\Pagos\PagosLogicaNegocio;

class PagosController extends Controller
{
    public function __construct()
    {
        parent::__construct(new PagosLogicaNegocio());
    }
}