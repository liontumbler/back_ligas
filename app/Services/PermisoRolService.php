<?php

namespace App\Services;

use App\Models\Tablas\PermisoRol;
use App\Models\Tablas\Roles;

class PermisoRolService extends Service
{
    protected $allowedColumns = ['rol_id', 'permiso_id'];

    public function __construct() {
        parent::__construct(PermisoRol::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoCliente, $array) {
        isset($array['rol_id']) ? $objetoCliente->rol_id = $array['rol_id'] : null;
        isset($array['permiso_id']) ? $objetoCliente->permiso_id = $array['permiso_id'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoCliente = new PermisoRol();
        $this->armarCuerpo($objetoCliente, $array);
        isset($usuario) ? $objetoCliente->usuario_creacion = $usuario->id : null;
        $objetoCliente->save();

        return $objetoCliente;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoCliente = PermisoRol::find($id);
        $this->armarCuerpo($objetoCliente, $array);
        isset($usuario) ? $objetoCliente->usuario_modificacion = $usuario->id : null;
        $objetoCliente->save();

        return $objetoCliente;
    }

    private function buildMenuArray($menu, $permiso)
    {
        return [
            'id' => $menu->id,
            'menu' => $menu->nombre,
            'permisos' => [$permiso->action],
            'url' => $menu->url
        ];
    }


    function permisosUsuario($idRol)
    {
        $rol = Roles::find($idRol);
        $permisos = $rol->permisos;

        $agrupados = [];
        $menusById = [];

        // 1. Recolectar menús
        foreach ($permisos as $permiso) {
            $idMenu = $permiso->menu_id;

            if ($idMenu) {
                $menu = $permiso->menu;
                $menusById[$menu->id] = [
                    'id'        => $menu->id,
                    'nombre'    => $menu->nombre,
                    'orden'     => $menu->orden,
                    'url'       => $menu->url,
                    'parent_id' => $menu->parent_id,
                    'menus'     => []
                ];
            } else {
                $agrupados['permisos-globales'][] = $permiso->action;
            }
        }

        // 2. Construir árbol con helper
        $tree = $this->buildTree($menusById);

        // 3. Ordenar árbol completo
        $this->sortMenus($tree);

       // 4. Asignar permisos con el path completo
        foreach ($permisos as $permiso) {
            if ($permiso->menu_id && isset($menusById[$permiso->menu_id])) {
                $menuPath = $this->buildMenuPath($menusById[$permiso->menu_id], $menusById);
                $menusById[$permiso->menu_id]['permiso'] = $menuPath . '.' . $permiso->action;
            }
        }

        // 4. Asignar al resultado final
        $agrupados['menus'] = $tree;

        return $agrupados;
    }

    public function buildTree(array &$menusById): array
    {
        $tree = [];

        foreach ($menusById as $id => &$menu) {
            if ($menu['parent_id'] && isset($menusById[$menu['parent_id']])) {
                // Si tiene padre, se agrega como hijo
                $menusById[$menu['parent_id']]['menus'][] = &$menu;
            } else {
                // Si no tiene padre, es raíz
                $tree[] = &$menu;
            }
        }

        return $tree;
    }

    public function sortMenus(array &$menus): void
    {
        usort($menus, fn($a, $b) => $a['orden'] <=> $b['orden']);

        foreach ($menus as &$submenu) {
            if (!empty($submenu['menus'])) {
                self::sortMenus($submenu['menus']);
            }
        }
    }

    public function buildMenuPath($menu, $menusById): string
    {
        $path = [$menu['nombre']];
        $parentId = $menu['parent_id'];

        while ($parentId && isset($menusById[$parentId])) {
            array_unshift($path, $menusById[$parentId]['nombre']); // lo metemos al inicio
            $parentId = $menusById[$parentId]['parent_id'];
        }

        return implode('.', $path);
    }
}
