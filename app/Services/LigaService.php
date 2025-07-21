<?php

namespace App\Services;

use App\Models\Tablas\Ligas;

class LigaService
{
    public function crearLiga(array $array, $usuario = null)
    {
        $objetoLiga = new Ligas();
        isset($array['nombre']) ? $objetoLiga->nombre = $array['nombre'] : null;
        isset($array['direccion']) ? $objetoLiga->direccion = $array['direccion'] : null;
        isset($array['telefono']) ? $objetoLiga->telefono = $array['telefono'] : null;
        isset($usuario) ? $objetoLiga->usuario_creacion = $usuario->id : null;
        $objetoLiga->save();

        return $objetoLiga;
    }

    public function actualizarLiga($id, array $array, $usuario = null)
    {
        $objetoLiga = Ligas::find($id);
        isset($array['nombre']) ? $objetoLiga->nombre = $array['nombre'] : null;
        isset($array['direccion']) ? $objetoLiga->direccion = $array['direccion'] : null;
        isset($array['telefono']) ? $objetoLiga->telefono = $array['telefono'] : null;
        isset($usuario) ? $objetoLiga->usuario_modificacion = $usuario->id : null;
        $objetoLiga->save();

        return $objetoLiga;
    }

    public function eliminarLiga($id)
    {
        $objetoLiga = Ligas::find($id);
        if ($objetoLiga) {
            $objetoLiga->delete();
        }

        return $objetoLiga;
    }

    public function todo($ordenar, $tamaño = 0, $buscar = null)
    {
        $ligas = Ligas::query();
        $allowedColumns = ['nombre', 'direccion', 'telefono'];

        if (!empty($buscar)) {
            $ligas->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $ligas->orderBy($column, $direction);
            }
        }

        return $ligas->paginate($tamaño);
        //return Ligas::all();
    }

    public function obtenerXId($id)
    {
        return Ligas::find($id);
    }
}
