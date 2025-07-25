<?php

namespace App\Services;

use App\Models\Tablas\Ligas;

class LigaService extends Service
{
    protected $allowedColumns = ['nombre', 'direccion', 'telefono'];

    public function __construct() {
        parent::__construct(Ligas::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoLiga, $array) {
        isset($array['nombre']) ? $objetoLiga->nombre = $array['nombre'] : null;
        isset($array['direccion']) ? $objetoLiga->direccion = $array['direccion'] : null;
        isset($array['telefono']) ? $objetoLiga->telefono = $array['telefono'] : null;
    }

    public function crear(array $array, $usuario = null)
    {
        $objetoLiga = new Ligas();
        $this->armarCuerpo($objetoLiga, $array);
        $objetoLiga->save();

        return $objetoLiga;
    }

    public function actualizar($id, array $array, $usuario = null)
    {
        $objetoLiga = Ligas::find($id);
        $this->armarCuerpo($objetoLiga, $array);
        $objetoLiga->save();

        return $objetoLiga;
    }
}
