<?php

namespace App\Services;

use App\Models\Tablas\Clientes;

class ClienteService extends Service
{
    protected $allowedColumns = ['nombre', 'apellidos', 'correo', 'telefono'];

    public function __construct() {
        parent::__construct(Clientes::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoCliente, $array) {
        isset($array['nombres']) ? $objetoCliente->nombres = $array['nombres'] : null;
        isset($array['apellidos']) ? $objetoCliente->apellidos = $array['apellidos'] : null;
        isset($array['correo']) ? $objetoCliente->correo = $array['correo'] : null;
        isset($array['telefono']) ? $objetoCliente->telefono = $array['telefono'] : null;
        isset($array['liga_id']) ? $objetoCliente->liga_id = $array['liga_id'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoCliente = new Clientes();
        $this->armarCuerpo($objetoCliente, $array);
        isset($usuario) ? $objetoCliente->usuario_creacion = $usuario->id : null;
        $objetoCliente->save();

        return $objetoCliente;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoCliente = Clientes::find($id);
        $this->armarCuerpo($objetoCliente, $array);
        isset($usuario) ? $objetoCliente->usuario_modificacion = $usuario->id : null;
        $objetoCliente->save();

        return $objetoCliente;
    }
}
