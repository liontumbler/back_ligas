<?php

namespace App\Services;

use App\Models\Tablas\Licencias;

class LicenciaService extends Service
{
    protected $allowedColumns = ['codigo', 'valor', 'fecha_inicio', 'fecha_fin', 'estado'];

    public function __construct() {
        parent::__construct(Licencias::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoLicencia, $array) {
        isset($array['codigo']) ? $objetoLicencia->codigo = $array['codigo'] : null;
        isset($array['valor']) ? $objetoLicencia->valor = $array['valor'] : null;
        isset($array['fecha_inicio']) ? $objetoLicencia->fecha_inicio = $array['fecha_inicio'] : null;
        isset($array['fecha_fin']) ? $objetoLicencia->fecha_fin = $array['fecha_fin'] : null;
        isset($array['estado']) ? $objetoLicencia->estado = $array['estado'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoLicencia = new Licencias();
        $this->armarCuerpo($objetoLicencia, $array);
        isset($usuario) ? $objetoLicencia->usuario_creacion = $usuario->id : null;
        $objetoLicencia->save();

        return $objetoLicencia;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoLicencia = Licencias::find($id);
        $this->armarCuerpo($objetoLicencia, $array);
        isset($usuario) ? $objetoLicencia->usuario_modificacion = $usuario->id : null;
        $objetoLicencia->save();

        return $objetoLicencia;
    }
}
