<?php

namespace App\Services;

use App\Models\Tablas\RefreshTokens;

class RefreshTokenService extends Service
{
    protected $allowedColumns = ['continente', 'pais', 'ciudad',
            'latitud', 'longitud', 'usuario_id',
            'refresh_token', 'ip_address', 'usuario_agent',
            'revoked'];

    public function __construct() {
        parent::__construct(RefreshTokens::class, $this->allowedColumns);
    }

    public function armarCuerpo($objetoLicencia, $array) {
        isset($array['continente']) ? $objetoLicencia->continente = $array['continente'] : null;
        isset($array['pais']) ? $objetoLicencia->pais = $array['pais'] : null;
        isset($array['ciudad']) ? $objetoLicencia->ciudad = $array['ciudad'] : null;
        isset($array['latitud']) ? $objetoLicencia->latitud = $array['latitud'] : null;
        isset($array['longitud']) ? $objetoLicencia->longitud = $array['longitud'] : null;
        isset($array['usuario_id']) ? $objetoLicencia->usuario_id = $array['usuario_id'] : null;
        isset($array['refresh_token']) ? $objetoLicencia->refresh_token = $array['refresh_token'] : null;
        isset($array['ip_address']) ? $objetoLicencia->ip_address = $array['ip_address'] : null;
        isset($array['usuario_agent']) ? $objetoLicencia->usuario_agent = $array['usuario_agent'] : null;
        isset($array['revoked']) ? $objetoLicencia->revoked = $array['revoked'] : null;
    }

    public function crear(array $array)
    {
        $objetoRefreshToken = new RefreshTokens();
        $this->armarCuerpo($objetoRefreshToken, $array);
        $objetoRefreshToken->save();

        return $objetoRefreshToken;
    }

    public function actualizar($id, array $array)
    {
        $objetoRefreshToken = RefreshTokens::find($id);
        $this->armarCuerpo($objetoRefreshToken, $array);
        $objetoRefreshToken->save();

        return $objetoRefreshToken;
    }

    public function obtenerXRefreshToken($refreshToken)
    {
        return RefreshTokens::where('refresh_token', $refreshToken)->first();
    }

    public function revocarTodosLosRefreshTokens($id)
    {
        return RefreshTokens::where('usuario_id', $id)->where('revoked', false)->update(['revoked' => true]);
    }
}
