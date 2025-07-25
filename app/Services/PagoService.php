<?php

namespace App\Services;

use App\Models\Tablas\Pagos;

class PagoService extends Service
{
    protected $allowedColumns = ['codigo', 'valor', 'fecha_inicio', 'fecha_fin', 'estado'];

    public function __construct() {
        parent::__construct(Pagos::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoLicencia, $array) {
        isset($array['cliente_id']) ? $objetoLicencia->cliente_id = $array['cliente_id'] : null;
        isset($array['tipo']) ? $objetoLicencia->tipo = $array['tipo'] : null;
        isset($array['valor']) ? $objetoLicencia->valor = $array['valor'] : null;
        isset($array['estado']) ? $objetoLicencia->estado = $array['estado'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoPago = new Pagos();
        $this->armarCuerpo($objetoPago, $array);
        isset($usuario) ? $objetoPago->usuario_creacion = $usuario->id : null;
        $objetoPago->save();

        return $objetoPago;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoPago = Pagos::find($id);
        $this->armarCuerpo($objetoPago, $array);
        isset($usuario) ? $objetoPago->usuario_modificacion = $usuario->id : null;
        $objetoPago->save();

        return $objetoPago;
    }
}
