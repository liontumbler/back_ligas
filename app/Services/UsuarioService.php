<?php

namespace App\Services;

use App\Models\Tablas\Usuarios;
use Illuminate\Support\Facades\Hash;

class UsuarioService extends Service
{
    protected $allowedColumns = ['continente', 'pais', 'ciudad',
            'latitud', 'longitud', 'usuario_id',
            'refresh_token', 'ip_address', 'usuario_agent',
            'revoked'];

    public function __construct() {
        parent::__construct(Usuarios::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoUsuario, $array) {
        isset($array['nombres']) ? $objetoUsuario->nombres = $array['nombres'] : null;
        isset($array['apellidos']) ? $objetoUsuario->apellidos = $array['apellidos'] : null;
        isset($array['correo']) ? $objetoUsuario->correo = $array['correo'] : null;
        isset($array['password']) ? $objetoUsuario->password = Hash::make($array['password']) : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoUsuario = new Usuarios();
        $this->armarCuerpo($objetoUsuario, $array);
        isset($usuario) ? $objetoUsuario->usuario_creacion = $usuario->id : null;
        $objetoUsuario->save();

        return $objetoUsuario;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoUsuario = Usuarios::find($id);
        $this->armarCuerpo($objetoUsuario, $array);
        isset($usuario) ? $objetoUsuario->usuario_modificacion = $usuario->id : null;
        $objetoUsuario->save();

        return $objetoUsuario;
    }

    public function obtenerXcorreo($correo)
    {
        return Usuarios::where('correo', $correo)->first();
    }
}
