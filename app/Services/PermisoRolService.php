<?php

namespace App\Services;

use App\Models\Tablas\Permisos;

class PermisoService extends Service
{
    protected $allowedColumns = ['rol_id', 'permiso_id'];

    public function __construct() {
        parent::__construct(Clientes::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoCliente, $array) {
        isset($array['rol_id']) ? $objetoCliente->rol_id = $array['rol_id'] : null;
        isset($array['permiso_id']) ? $objetoCliente->permiso_id = $array['permiso_id'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoCliente = new Clientes();
        $this->armarCuerpo($objetoCliente, $array);
        isset($usuario) ? $objetoCliente->usuario_creacion = $usuario->id : null;
        $objetoCliente->save();

        return $objetoCliente;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoCliente = Clientes::find($id);
        $this->armarCuerpo($objetoCliente, $array);
        isset($usuario) ? $objetoCliente->usuario_modificacion = $usuario->id : null;
        $objetoCliente->save();

        return $objetoCliente;
    }

    public function crearPermiso(array $array, $usuario = null)
    {
        $objetoPermiso = new Permisos();
        $objetoPermiso->menu_id = $array['menu_id'];
        $objetoPermiso->action = $array['action'];

        isset($usuario) ? $objetoPermiso->usuario_creacion = $usuario->id : null;

        $objetoPermiso->save();

        return $objetoPermiso;
    }

    public function actualizarPermiso($id, array $array, $usuario = null)
    {
        $objetoPermiso = Permisos::find($id);
        isset($array['menu_id']) ? $objetoPermiso->menu_id = $array['menu_id'] : null;
        isset($array['action']) ? $objetoPermiso->action = $array['action'] : null;
        isset($usuario) ? $objetoPermiso->usuario_modificacion = $usuario->id : null;
        $objetoPermiso->save();

        return $objetoPermiso;
    }

    public function eliminarPermiso($id)
    {
        $objetoPermiso = Permisos::find($id);
        if ($objetoPermiso) {
            $objetoPermiso->delete();
        }

        return $objetoPermiso;
    }

    public function todo($ordenar, $tamaño = 0, $buscar = null)
    {
        $Permisos = Permisos::query();
        $allowedColumns = ['action'];

        if (!empty($buscar)) {
            $Permisos->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $Permisos->orderBy($column, $direction);
            }
        }

        return $Permisos->paginate($tamaño);
        //return Permisos::all();
    }

    public function obtenerXId($id)
    {
        return Permisos::find($id);
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
