<?php

namespace App\Services;

use App\Models\Tablas\Usuarios;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    public function crearUsuario(array $array, $usuario = null)
    {
        $objetoUsuario = new Usuarios();
        isset($array['nombres']) ? $objetoUsuario->nombres = $array['nombres'] : null;
        isset($array['apellidos']) ? $objetoUsuario->apellidos = $array['apellidos'] : null;
        isset($array['correo']) ? $objetoUsuario->correo = $array['correo'] : null;
        isset($array['password']) ? $objetoUsuario->password = Hash::make($array['password']) : null;
        isset($usuario) ? $objetoUsuario->usuario_creacion = $usuario->id : null;
        $objetoUsuario->save();

        return $objetoUsuario;
    }

    public function actualizarUsuario($id, array $array, $usuario = null)
    {
        $objetoUsuario = Usuarios::find($id);
        isset($array['nombres']) ? $objetoUsuario->nombres = $array['nombres'] : null;
        isset($array['apellidos']) ? $objetoUsuario->apellidos = $array['apellidos'] : null;
        isset($array['correo']) ? $objetoUsuario->correo = $array['correo'] : null;
        isset($array['password']) ? $objetoUsuario->password = Hash::make($array['password']) : null;
        isset($usuario) ? $objetoUsuario->usuario_modificacion = $usuario->id : null;
        $objetoUsuario->save();

        return $objetoUsuario;
    }

    public function eliminarUsuario($id)
    {
        $objetoUsuario = Usuarios::find($id);
        $objetoUsuario->delete();
        return $objetoUsuario;
    }

    
    public function todo($ordenar, $tamaño = 0, $buscar = null)
    {
        $usuarios = Usuarios::query();
        $allowedColumns = ['nombre', 'direccion', 'telefono'];

        if (!empty($buscar)) {
            $usuarios->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $usuarios->orderBy($column, $direction);
            }
        }

        return $usuarios->paginate($tamaño);
        //return Usuarios::all();
    }

    public function obtenerXId($id)
    {
        return Usuarios::find($id);
    }

    public function obtenerXcorreo($correo)
    {
        //return $correo;
        return Usuarios::where('correo', $correo)->first();
    }
}
