<?php

namespace App\Services;

use App\Models\Tablas\Pagos;

class PagoService extends Service
{
    protected $allowedColumns = ['valor', 'fecha_pago', 'liga_id'];

    public function __construct() {
        parent::__construct(Pagos::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoPago, $array) {
        isset($array['valor']) ? $objetoPago->valor = $array['valor'] : null;
        isset($array['fecha_pago']) ? $objetoPago->fecha_pago = $array['fecha_pago'] : null;
        isset($array['liga_id']) ? $objetoPago->liga_id = $array['liga_id'] : null;
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