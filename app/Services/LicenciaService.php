<?php

namespace App\Services;

use App\Models\Tablas\Licencias;

class LicenciaService
{
    public function crearLicencia(array $array, $usuario = null)
    {
        $objetoLicencia = new Licencias();
        isset($array['codigo']) ? $objetoLicencia->codigo = $array['codigo'] : null;
        isset($array['valor']) ? $objetoLicencia->valor = $array['valor'] : null;
        isset($array['fecha_inicio']) ? $objetoLicencia->fecha_inicio = $array['fecha_inicio'] : null;
        isset($array['fecha_fin']) ? $objetoLicencia->fecha_fin = $array['fecha_fin'] : null;
        isset($array['estado']) ? $objetoLicencia->estado = $array['estado'] : null;
        isset($usuario) ? $objetoLicencia->usuario_creacion = $usuario->id : null;
        $objetoLicencia->save();

        return $objetoLicencia;
    }

    public function actualizarLicencia($id, array $array, $usuario = null)
    {
        $objetoLicencia = Licencias::find($id);
        isset($array['codigo']) ? $objetoLicencia->codigo = $array['codigo'] : null;
        isset($array['valor']) ? $objetoLicencia->valor = $array['valor'] : null;
        isset($array['fecha_inicio']) ? $objetoLicencia->fecha_inicio = $array['fecha_inicio'] : null;
        isset($array['fecha_fin']) ? $objetoLicencia->fecha_fin = $array['fecha_fin'] : null;
        isset($array['estado']) ? $objetoLicencia->estado = $array['estado'] : null;
        isset($usuario) ? $objetoLicencia->usuario_modificacion = $usuario->id : null;
        $objetoLicencia->save();

        return $objetoLicencia;
    }

    public function eliminarLicencia($id)
    {
        $objetoLicencia = Licencias::find($id);
        $objetoLicencia->delete();
        return $objetoLicencia;
    }

    public function todo($ordenar, $tamaño = 0, $buscar = null)
    {
        $Licencias = Licencias::query();
        $allowedColumns = ['codigo', 'valor', 'fecha_inicio', 'fecha_fin', 'estado'];

        if (!empty($buscar)) {
            $Licencias->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $Licencias->orderBy($column, $direction);
            }
        }

        return $Licencias->paginate($tamaño);
        //return Licencias::all();
    }

    public function obtenerXId($id)
    {
        return Licencias::find($id);
    }
}
