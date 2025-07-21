<?php

namespace App\Services;

use App\Models\Tablas\Pagos;

class PagoService
{
    public function crearPago(array $array, $usuario = null)
    {
        $objetoPago = new Pagos();
        isset($array['cliente_id']) ? $objetoPago->cliente_id = $array['cliente_id'] : null;
        isset($array['tipo']) ? $objetoPago->tipo = $array['tipo'] : null;
        isset($array['valor']) ? $objetoPago->valor = $array['valor'] : null;
        isset($array['estado']) ? $objetoPago->estado = $array['estado'] : null;
        isset($usuario) ? $objetoPago->usuario_creacion = $usuario->id : null;
        $objetoPago->save();

        return $objetoPago;
    }

    public function actualizarPago($id, array $array, $usuario = null)
    {
        $objetoPago = Pagos::find($id);
        isset($array['cliente_id']) ? $objetoPago->cliente_id = $array['cliente_id'] : null;
        isset($array['tipo']) ? $objetoPago->tipo = $array['tipo'] : null;
        isset($array['valor']) ? $objetoPago->valor = $array['valor'] : null;
        isset($array['estado']) ? $objetoPago->estado = $array['estado'] : null;
        isset($usuario) ? $objetoPago->usuario_modificacion = $usuario->id : null;
        $objetoPago->save();

        return $objetoPago;
    }

    public function eliminarPago($id)
    {
        $objetoPago = Pagos::find($id);
        if ($objetoPago) {
            $objetoPago->delete();
        }

        return $objetoPago;
    }

    public function todo($ordenar, $tamaño = 0, $buscar = null)
    {
        $pagos = Pagos::query();
        $allowedColumns = ['nombre', 'direccion', 'telefono'];

        if (!empty($buscar)) {
            $pagos->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $pagos->orderBy($column, $direction);
            }
        }

        return $pagos->paginate($tamaño);
        //return Pagos::all();
    }

    public function obtenerXId($id)
    {
        return Pagos::find($id);
    }
}
