<?php
namespace App\LogicaNegocio\PermisoRol;

use App\LogicaNegocio\LogicaNegocio;
use App\Services\PermisoRolService;

class PermisoRolLogicaNegocio extends LogicaNegocio
{
    protected $reglaCrear = [
        'rol_id' => 'required|integer|exists:roles,id',
        'permiso_id' => 'required|integer|exists:permisos,id',
    ];

    protected $reglaActualizar = [
        'rol_id' => 'sometimes|required|integer|exists:roles,id',
        'permiso_id' => 'sometimes|required|integer|exists:permisos,id',
    ];

    public function __construct()
    {
        parent::__construct(new PermisoRolService(), $this->reglaCrear, $this->reglaActualizar);
    }

    public function permisosUsuario($request, $idRol)
    {
        return $this->service->permisosUsuario($idRol);
    }
}