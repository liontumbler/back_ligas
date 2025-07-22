<?php

namespace App\Services;

use App\Models\Tablas\Clientes;

class ClienteService
{
    public function crearCliente(array $array, $usuario = null)
    {



        $objetoCliente = new Clientes();
        isset($array['nombres']) ? $objetoCliente->nombres = $array['nombres'] : null;
        isset($array['apellidos']) ? $objetoCliente->apellidos = $array['apellidos'] : null;
        isset($array['correo']) ? $objetoCliente->correo = $array['correo'] : null;
        isset($array['telefono']) ? $objetoCliente->telefono = $array['telefono'] : null;
        isset($array['liga_id']) ? $objetoCliente->liga_id = $array['liga_id'] : null;
        isset($usuario) ? $objetoCliente->usuario_creacion = $usuario->id : null;
        $objetoCliente->save();

        return $objetoCliente;
    }

    public function actualizarCliente($id, array $array, $usuario = null)
    {
        $objetoCliente = Clientes::find($id);
        isset($array['nombres']) ? $objetoCliente->nombres = $array['nombres'] : null;
        isset($array['apellidos']) ? $objetoCliente->apellidos = $array['apellidos'] : null;
        isset($array['correo']) ? $objetoCliente->correo = $array['correo'] : null;
        isset($array['telefono']) ? $objetoCliente->telefono = $array['telefono'] : null;
        isset($array['liga_id']) ? $objetoCliente->liga_id = $array['liga_id'] : null;
        isset($usuario) ? $objetoCliente->usuario_modificacion = $usuario->id : null;
        $objetoCliente->save();

        return $objetoCliente;
    }

    public function eliminarCliente($id)
    {
        $objetoCliente = Clientes::find($id);
        if ($objetoCliente) {
            $objetoCliente->delete();
        }

        return $objetoCliente;
    }

    public function todo($ordenar, $tamaño = 0, $buscar = null)
    {
        $Clientes = Clientes::query();
        $allowedColumns = ['nombre', 'apellidos', 'correo', 'telefono'];

        if (!empty($buscar)) {
            $Clientes->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $Clientes->orderBy($column, $direction);
            }
        }

        return $Clientes->paginate($tamaño);
        //return Clientes::all();
    }

    public function obtenerXId($id)
    {
        return Clientes::find($id);
    }
}
