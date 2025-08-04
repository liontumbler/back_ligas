<?php
namespace App\LogicaNegocio\Menus;

use App\LogicaNegocio\LogicaNegocio;
use App\Services\MenuService;

class MenusLogicaNegocio extends LogicaNegocio
{
    protected $reglaCrear = [
    'nombre'              => 'required|string|max:100',
    'parent_id'           => 'nullable|integer|exists:menus,id'
    ];

    protected $reglaActualizar = [
    'nombre'              => 'sometimes|required|string|max:100',
    'parent_id'           => 'sometimes|nullable|integer|exists:menus,id'
    ];

    public function __construct()
    {
        parent::__construct(new MenuService(), $this->reglaCrear, $this->reglaActualizar);
    }
}