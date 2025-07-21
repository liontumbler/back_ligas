<?php

namespace App\Services;

use App\Models\Tablas\RefreshTokens;

class RefreshTokenService
{
    public function crearRefreshToken(array $array)
    {
        $objetoRefreshToken = new RefreshTokens();
        isset($array['usuario_id']) ? $objetoRefreshToken->usuario_id = $array['usuario_id'] : null;
        isset($array['refresh_token']) ? $objetoRefreshToken->refresh_token = $array['refresh_token'] : null;
        isset($array['ip_address']) ? $objetoRefreshToken->ip_address = $array['ip_address'] : null;
        isset($array['usuario_agent']) ? $objetoRefreshToken->usuario_agent = $array['usuario_agent'] : null;
        $objetoRefreshToken->save();

        return $objetoRefreshToken;
    }

    public function actualizarRefreshToken($id, array $array)
    {
        $objetoRefreshToken = RefreshTokens::find($id);
        isset($array['usuario_id']) ? $objetoRefreshToken->usuario_id = $array['usuario_id'] : null;
        isset($array['refresh_token']) ? $objetoRefreshToken->refresh_token = $array['refresh_token'] : null;
        isset($array['ip_address']) ? $objetoRefreshToken->ip_address = $array['ip_address'] : null;
        isset($array['usuario_agent']) ? $objetoRefreshToken->usuario_agent = $array['usuario_agent'] : null;
        isset($array['revoked']) ? $objetoRefreshToken->revoked = $array['revoked'] : null;
        $objetoRefreshToken->save();

        return $objetoRefreshToken;
    }

    public function eliminarRefreshToken($id)
    {
        $objetoRefreshToken = RefreshTokens::find($id);
        if ($objetoRefreshToken) {
            $objetoRefreshToken->delete();
        }
        return $objetoRefreshToken;
    }

    public function todo($ordenar, $tamaño = 0, $buscar = null)
    {
        $RefreshTokens = RefreshTokens::query();
        $allowedColumns = ['refresh_token', 'ip_address', 'usuario_agent'];

        if (!empty($buscar)) {
            $RefreshTokens->where(function ($q) use ($buscar, $allowedColumns) {
                foreach ($allowedColumns as $columna) {
                    $q->orWhere($columna, 'ILIKE', "%{$buscar}%");
                }
            });
        }

        $sorts = explode(',', $ordenar);
        foreach ($sorts as $sort) {
            [$column, $direction] = explode(':', $sort) + [null, 'asc'];
            if (in_array($column, $allowedColumns) && in_array(strtolower($direction), ['asc', 'desc'])) {
                $RefreshTokens->orderBy($column, $direction);
            }
        }

        return $RefreshTokens->paginate($tamaño);
        //return RefreshTokens::all();
    }

    public function obtenerXId($id)
    {
        return RefreshTokens::find($id);
    }

    public function obtenerXRefreshToken($refreshToken)
    {
        return RefreshTokens::where('refresh_token', $refreshToken)->first();
    }

    
}
