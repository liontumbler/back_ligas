<?php

namespace App\Services;

use App\Models\Tablas\Menus;

class MenuService extends Service
{
    protected $allowedColumns = ['nombre', 'parent_id'];

    public function __construct() {
        parent::__construct(Menus::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoMenu, $array) {
        isset($array['nombre']) ? $objetoMenu->nombre = $array['nombre'] : null;
        isset($array['parent_id']) ? $objetoMenu->parent_id = $array['parent_id'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoMenu = new Menus();
        $this->armarCuerpo($objetoMenu, $array);
        isset($usuario) ? $objetoMenu->usuario_creacion = $usuario->id : null;
        $objetoMenu->save();

        return $objetoMenu;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoMenu = Menus::find($id);
        $this->armarCuerpo($objetoMenu, $array);
        isset($usuario) ? $objetoMenu->usuario_modificacion = $usuario->id : null;
        $objetoMenu->save();

        return $objetoMenu;
    }
}