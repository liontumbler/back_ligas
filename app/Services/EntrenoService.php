<?php

namespace App\Services;

use App\Models\Tablas\Entrenos;

class EntrenoService extends Service
{
    protected $allowedColumns = ['cliente_id', 'tipo', 'pago_id', 'liga_id'];

    public function __construct() {
        parent::__construct(Entrenos::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoEntreno, $array) {
        isset($array['cliente_id']) ? $objetoEntreno->cliente_id = $array['cliente_id'] : null;
        isset($array['tipo']) ? $objetoEntreno->tipo = $array['tipo'] : null;
        isset($array['pago_id']) ? $objetoEntreno->pago_id = $array['pago_id'] : null;
        isset($array['liga_id']) ? $objetoEntreno->liga_id = $array['liga_id'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoEntreno = new Entrenos();
        $this->armarCuerpo($objetoEntreno, $array);
        isset($usuario) ? $objetoEntreno->usuario_creacion = $usuario->id : null;
        $objetoEntreno->save();

        return $objetoEntreno;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoEntreno = Entrenos::find($id);
        $this->armarCuerpo($objetoEntreno, $array);
        isset($usuario) ? $objetoEntreno->usuario_modificacion = $usuario->id : null;
        $objetoEntreno->save();

        return $objetoEntreno;
    }
}