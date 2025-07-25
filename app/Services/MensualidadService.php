<?php

namespace App\Services;

use App\Models\Tablas\Mensualidades;

class MensualidadService extends Service
{
    protected $allowedColumns = ['nombre', 'direccion', 'telefono'];

    public function __construct() {
        parent::__construct(Mensualidades::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoMensualidade, $array) {
        isset($array['cliente_id']) ? $objetoMensualidade->cliente_id = $array['cliente_id'] : null;
        isset($array['fecha_inicio']) ? $objetoMensualidade->fecha_inicio = $array['fecha_inicio'] : null;
        isset($array['fecha_fin']) ? $objetoMensualidade->fecha_fin = $array['fecha_fin'] : null;
        isset($array['sesiones_disponibles']) ? $objetoMensualidade->sesiones_disponibles = $array['sesiones_disponibles'] : null;
        isset($array['sesiones_usadas']) ? $objetoMensualidade->sesiones_usadas = $array['sesiones_usadas'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoMensualidade = new Mensualidades();
        $this->armarCuerpo($objetoMensualidade, $array);
        isset($usuario) ? $objetoMensualidade->usuario_creacion = $usuario->id : null;
        $objetoMensualidade->save();

        return $objetoMensualidade;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoMensualidade = Mensualidades::find($id);
        $this->armarCuerpo($objetoMensualidade, $array);
        isset($usuario) ? $objetoMensualidade->usuario_modificacion = $usuario->id : null;
        $objetoMensualidade->save();

        return $objetoMensualidade;
    }
}
