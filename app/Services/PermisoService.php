<?php

namespace App\Services;

use App\Models\Tablas\Permisos;

class PermisoService extends Service
{
    protected $allowedColumns = ['codigo', 'valor', 'fecha_inicio', 'fecha_fin', 'estado'];

    public function __construct() {
        parent::__construct(Permisos::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoLicencia, $array) {
        isset($array['menu_id']) ? $objetoLicencia->menu_id = $array['menu_id'] : null;
        isset($array['action']) ? $objetoLicencia->action = $array['action'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoPermiso = new Permisos();
        $this->armarCuerpo($objetoPermiso, $array);
        isset($usuario) ? $objetoPermiso->usuario_creacion = $usuario->id : null;

        $objetoPermiso->save();

        return $objetoPermiso;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoPermiso = Permisos::find($id);
        $this->armarCuerpo($objetoPermiso, $array);
        isset($usuario) ? $objetoPermiso->usuario_modificacion = $usuario->id : null;
        $objetoPermiso->save();

        return $objetoPermiso;
    }

    function permisosUsuario($usuario)
    {
        $permisos = $usuario->role
            ->permissions()
            ->with('menu.parent')
            ->get();

        $agrupados = [];
        foreach ($permisos as $permiso) {
            $menu = $permiso->menu;
            $padre = $menu->parent;

            if ($padre) {
                $clave = $padre->id;

                if (!isset($agrupados[$clave])) {
                    $agrupados[$clave] = [
                        'menu' => $padre->name,
                        'action' => null,
                        'parent_menu' => null,
                        'menus' => [],
                    ];
                }

                $agrupados[$clave]['menus'][] = [
                    'menu' => $menu->name,
                    'action' => $permiso->action,
                ];
            } else {
                $clave = $menu->id;

                if (!isset($agrupados[$clave])) {
                    $agrupados[$clave] = [
                        'menu' => $menu->name,
                        'action' => null,
                        'menus' => [],
                    ];
                }

                if ($agrupados[$clave]['action'] === null) {
                    $agrupados[$clave]['action'] = $permiso->action;
                }
            }
        }

        return $agrupados;
    }
}
