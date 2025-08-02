<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\LogicaNegocio\Entrenos\EntrenosLogicaNegocio;

class EntrenosController extends Controller
{
    public function __construct()
    {
        parent::__construct(new EntrenosLogicaNegocio());
    }
}