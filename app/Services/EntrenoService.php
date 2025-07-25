<?php

namespace App\Services;

use App\Models\Tablas\Entrenos;

class EntrenoService
{
    public function crearEntreno(array $array, $usuario = null)
    {
        $objetoEntreno = new Entrenos();
        isset($array['nombre']) ? $objetoEntreno->nombre = $array['nombre'] : null;
        isset($array['direccion']) ? $objetoEntreno->direccion = $array['direccion'] : null;
        isset($array['telefono']) ? $objetoEntreno->telefono = $array['telefono'] : null;
        $objetoEntreno->save();

        return $objetoEntreno;
    }

    public function actualizarEntreno($id, array $array, $usuario = null)
    {
        $objetoEntreno = Entrenos::find($id);
        isset($array['nombre']) ? $objetoEntreno->nombre = $array['nombre'] : null;
        isset($array['direccion']) ? $objetoEntreno->direccion = $array['direccion'] : null;
        isset($array['telefono']) ? $objetoEntreno->telefono = $array['telefono'] : null;
        $objetoEntreno->save();

        return $objetoEntreno;
    }

    public function eliminarEntreno($id)
    {
        $objetoEntreno = Entrenos::find($id);
        if ($objetoEntreno) {
            $objetoEntreno->delete();
        }

        return $objetoEntreno;
    }

    public function todo($ordenar, $tamaño = 0, $buscar = null)
    {
        $entreno = Entrenos::query();
        $allowedColumns = ['nombre', 'direccion', 'telefono'];

        if (!empty($buscar)) {
            $entreno->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $entreno->orderBy($column, $direction);
            }
        }

        return $entreno->paginate($tamaño);
        //return Entrenos::all();
    }

    public function obtenerXId($id)
    {
        return Entrenos::find($id);
    }
}
