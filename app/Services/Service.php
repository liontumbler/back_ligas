<?php

namespace App\Services;

class Service
{
    protected $allowedColumns;
    protected $model;
    protected function __construct($modeloClase, $allowedColumns) {
        $this->model = app($modeloClase);
        $this->allowedColumns = $allowedColumns;
    }

    public function obtenerXId($id)
    {
        return $this->model::find($id);
    }

    public function eliminar($id)
    {
        $objetoLicencia = $this->model::find($id);
        if ($objetoLicencia) {
            $objetoLicencia->delete();
        }

        return $objetoLicencia;
    }

    public function todo($usuario, $ordenar, $tamaño = 0, $buscar = null)
    {
        $tabla = $this->model::where(function ($query) use ($usuario) {
            $query->where('usuario_creacion', $usuario->id)
                ->orWhere('usuario_modificacion', $usuario->id);
        });

        $allowedColumns = $this->allowedColumns;

        if (!empty($buscar)) {
            $tabla->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $tabla->orderBy($column, $direction);
            }
        }

        return $tabla->paginate($tamaño);
        // return $tamaño > 0
        //     ? $tabla->paginate($tamaño)
        //     : $tabla->get();
        //return tabla::all();
    }
}