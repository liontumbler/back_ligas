<?php

namespace App\Utils;

use App\Services\UsuarioService;
use App\Services\RefreshTokenService;
use App\Utils\ManejoData;

class Jwt
{
    protected $usuarioService;
    protected $refreshTokenService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->refreshTokenService = new RefreshTokenService();
    }

    public function getPayload ($authorization) {
        $accessToken = substr($authorization, 7);
        $parts = explode('.', $accessToken);
        [$headerB64, $payloadB64, $signatureB64] = $parts;

        return json_decode($this->base64url_decode($payloadB64), true);
    }

    public function verificarToken($authorization, $secret)
    {
        $devolucion = [];
        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            $devolucion = ManejoData::armarDevolucion(401, false, 'Token no proporcionado Bearer', null, 'token autenticacion');
        } else {
            $accessToken = substr($authorization, 7);
            if (!$accessToken) {
                $devolucion =  ManejoData::armarDevolucion(400, false, "Token inválido", null, 'access_token');
            } else {
                $token = $this->refreshTokenService->obtenerXAccessToken($accessToken);
                if (!$token) {
                    $devolucion =  ManejoData::armarDevolucion(404, false, "Token no encontrado", null, 'access_token');
                } else {
                    $parts = explode('.', $accessToken);
                    if (count($parts) !== 3) {
                        $devolucion = ManejoData::armarDevolucion(401, false, 'Token inválido', null, 'token autenticacion');
                    } else {
                        [$headerB64, $payloadB64, $signatureB64] = $parts;
                        
                        $payload = json_decode($this->base64url_decode($payloadB64), true);
                        $signature = hash_hmac('sha256', "$headerB64.$payloadB64", $secret, true);
                        $signatureCheckB64 = $this->base64url_encode($signature);

                        if (!hash_equals($signatureCheckB64, $signatureB64)) {
                            $devolucion = ManejoData::armarDevolucion(401, false, 'Firma inválida', null, 'token autenticacion');
                        } elseif (!$payload || $payload['exp'] < time()) {
                            $devolucion = ManejoData::armarDevolucion(401, false, 'Token expirado', null, 'token autenticacion');
                        } else {
                            $user = $this->usuarioService->obtenerXId($payload['sub']);
                            if (!$user) {
                                $devolucion = ManejoData::armarDevolucion(401, false, 'Usuario no encontrado', null, 'token autenticacion');
                            } else {
                                $devolucion = ManejoData::armarDevolucion(200, true, 'Usuario encontrado', $user);
                            }
                        }
                    }
                }
            }
        }
        return $devolucion;
    }

    public function generateJWT($payload, $secret)
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];

        $base64UrlHeader = $this->base64url_encode(json_encode($header));
        $base64UrlPayload = $this->base64url_encode(json_encode($payload));

        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret, true);
        $base64UrlSignature = $this->base64url_encode($signature);

        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }

    private function base64url_decode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // public function renovarToken($refreshToken)
    // {
    //     $verificaJwt = $this->verificarToken($refreshToken, config('services.jwt_secret_refresh'));
    //     if ($verificaJwt['success']) {
    //         return $this->generateToken($verificaJwt);
    //     }
    // }

    public function generateToken($verificaJwt)
    {
        $payload1 = ManejoData::generarPayload($verificaJwt, time() + (15 * 60));
        $payload2 = ManejoData::generarPayload($verificaJwt, time() + (7 * 24 * 60 * 60));
        return [
            'access_token' => $this->generateJWT($payload1, config('services.jwt_secret')),
            'refresh_token' => $this->generateJWT($payload2, config('services.jwt_secret_refresh'))
        ];
    }
}
