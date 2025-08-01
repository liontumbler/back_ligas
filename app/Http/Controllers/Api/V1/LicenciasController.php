<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\LogicaNegocio\Licencias\LicenciasLogicaNegocio;

class LicenciasController extends Controller
{
    public function __construct()
    {
        parent::__construct(new LicenciasLogicaNegocio());
    }
}