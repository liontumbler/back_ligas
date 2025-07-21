<?php

namespace App\Services;

use App\Models\Tablas\Mensualidades;

class MensualidadService
{
    public function crearMensualidade(array $array, $usuario = null)
    {
        $objetoMensualidade = new Mensualidades();
        isset($array['cliente_id']) ? $objetoMensualidade->cliente_id = $array['cliente_id'] : null;
        isset($array['fecha_inicio']) ? $objetoMensualidade->fecha_inicio = $array['fecha_inicio'] : null;
        isset($array['fecha_fin']) ? $objetoMensualidade->fecha_fin = $array['fecha_fin'] : null;
        isset($array['sesiones_disponibles']) ? $objetoMensualidade->sesiones_disponibles = $array['sesiones_disponibles'] : null;
        isset($array['sesiones_usadas']) ? $objetoMensualidade->sesiones_usadas = $array['sesiones_usadas'] : null;
        isset($usuario) ? $objetoMensualidade->usuario_creacion = $usuario->id : null;
        $objetoMensualidade->save();

        return $objetoMensualidade;
    }

    public function actualizarMensualidade($id, array $array, $usuario = null)
    {
        $objetoMensualidade = Mensualidades::find($id);
        isset($array['cliente_id']) ? $objetoMensualidade->cliente_id = $array['cliente_id'] : null;
        isset($array['fecha_inicio']) ? $objetoMensualidade->fecha_inicio = $array['fecha_inicio'] : null;
        isset($array['fecha_fin']) ? $objetoMensualidade->fecha_fin = $array['fecha_fin'] : null;
        isset($array['sesiones_disponibles']) ? $objetoMensualidade->sesiones_disponibles = $array['sesiones_disponibles'] : null;
        isset($array['sesiones_usadas']) ? $objetoMensualidade->sesiones_usadas = $array['sesiones_usadas'] : null;
        isset($usuario) ? $objetoMensualidade->usuario_modificacion = $usuario->id : null;
        $objetoMensualidade->save();

        return $objetoMensualidade;
    }

    public function eliminarMensualidade($id)
    {
        $objetoMensualidade = Mensualidades::find($id);
        if ($objetoMensualidade) {
            $objetoMensualidade->delete();
        }

        return $objetoMensualidade;
    }

    public function todo($ordenar, $tamaño = 0, $buscar = null)
    {
        $mensualidades = Mensualidades::query();
        $allowedColumns = ['nombre', 'direccion', 'telefono'];

        if (!empty($buscar)) {
            $mensualidades->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $mensualidades->orderBy($column, $direction);
            }
        }

        return $mensualidades->paginate($tamaño);
        //return Mensualidades::all();
    }

    public function obtenerXId($id)
    {
        return Mensualidades::find($id);
    }
}
