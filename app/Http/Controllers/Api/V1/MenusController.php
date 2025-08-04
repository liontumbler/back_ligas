<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\LogicaNegocio\Menus\MenusLogicaNegocio;

class MenusController extends Controller
{
    public function __construct()
    {
        parent::__construct(new MenusLogicaNegocio());
    }
}