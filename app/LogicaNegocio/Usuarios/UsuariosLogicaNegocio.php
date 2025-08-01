<?php
namespace App\LogicaNegocio\Usuarios;

use App\LogicaNegocio\LogicaNegocio;

use Exception;

use App\Utils\ManejoData;
use App\Utils\Jwt;

use Illuminate\Support\Facades\Hash;

use App\Services\UsuarioService;
use App\Services\RefreshTokenService;

class UsuariosLogicaNegocio extends LogicaNegocio
{
    protected $refreshTokenService;
    protected $jwt;

    protected $reglaCrear = [
        'nombres' => 'required|string|max:100',
        'apellidos' => 'required|string|max:100',
        'correo' => 'required|string|email|max:100|unique:usuarios,correo',
        'password' => 'required|string|max:20'
    ];

    protected $reglaActualizar = [
        'nombres' => 'sometimes|required|string|max:100',
        'apellidos' => 'sometimes|required|string|max:100',
        'correo' => 'sometimes|required|string|email|max:20|unique:usuarios,correo',
        'password' => 'sometimes|required|string|max:20'
    ];

    public function __construct()
    {
        $this->refreshTokenService = new RefreshTokenService();
        $this->jwt = new Jwt();
        parent::__construct(new UsuarioService(), $this->reglaCrear, $this->reglaActualizar);
    }

    public function login(Request $request)
    {
        try {
            $data = $request->validate([
                'correo' => 'required|string|email|max:100',
                'password' => 'required|string|max:20'
            ]);

            $encrypted = $request->header('-----------');

            if (!$encrypted) {
                $this->arregloRetorno = ManejoData::armarDevolucion(400, false, "Refresh token encryp", null, 'token');
            } else {
                $usuario = $this->service->obtenerXcorreo($data['correo']);
                if ($usuario !== null) {
                    if (Hash::check($data['password'], $usuario['password'])) {
                        //$this->mail($usuario);

                        $desencryptado = $this->desencriptado($encrypted);
                        $desencriptado = json_decode($desencryptado->original['desencriptado']);

                        $this->arregloRetorno = $this->createTokenRefresh('Login ok', $usuario, $request->getClientIp(), $request->header('User-Agent'), $desencriptado->continente, $desencriptado->pais, $desencriptado->ciudad, $desencriptado->latitud, $desencriptado->longitud);
                    } else {
                        $this->arregloRetorno = ManejoData::armarDevolucion(400, true, "datos incorrectos", $usuario, 'datos incorrectos');
                    }
                } else {
                    $this->arregloRetorno = ManejoData::armarDevolucion(400, true, "datos incorrectos", null, 'datos incorrectos');
                }
            }


        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function logout(Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            $refreshToken = '';

            if ($authorization && str_starts_with($authorization, 'Bearer ')) {
                $refreshToken = substr($authorization, 7);
            }

            $token = $this->refreshTokenService->obtenerXRefreshToken($refreshToken);

            if (!$refreshToken) {
                $this->arregloRetorno = ManejoData::armarDevolucion(400, false, "Refresh token requerido", null, 'refresh_token');
            } elseif (!$token) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, false, "Token no encontrado", null, 'refresh_token');
            } else {
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Sesión cerrada correctamente", null);
                $token->revoked = true;
                $token->save();
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $authorization = $request->header('Authorization');
            $encrypted = $request->header('-----------');

            $refreshToken = '';
            if ($authorization && str_starts_with($authorization, 'Bearer ')) {
                $refreshToken = substr($authorization, 7);
            }

            if (!$encrypted) {
                $this->arregloRetorno = ManejoData::armarDevolucion(400, false, "Refresh token encryp", null, 'refresh_token');
            } elseif (!$refreshToken) {
                $this->arregloRetorno = ManejoData::armarDevolucion(400, false, "Refresh token requerido", null, 'refresh_token');
            } else {
                $desencryptado = $this->desencriptado($encrypted);
                $desencriptado = json_decode($desencryptado->original['desencriptado']);
                $token = $this->refreshTokenService->obtenerXRefreshToken($refreshToken);
                //$this->arregloRetorno = $token;
                if (!is_object($token)) {
                    $this->arregloRetorno = ManejoData::armarDevolucion(404, false, "Token no encontrado", null, 'refresh_token');
                } else {
                    $payload = $this->jwt->getPayload($token->refresh_token);
                    $usuario = $this->service->obtenerXId($payload['sub']);
                    $this->arregloRetorno = $this->createTokenRefresh('refresh ok', $usuario, $request->getClientIp(), $request->header('User-Agent'), $desencriptado->continente, $desencriptado->pais, $desencriptado->ciudad, $desencriptado->latitud, $desencriptado->longitud);
                }
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    private function createTokenRefresh($msg, $datos, $ip, $userAgent, $continente, $pais, $ciudad, $latitud, $longitud) {
        $token = $this->jwt->generateToken($datos);
        $tokenRefresh = [
            'continente' => $continente,
            'pais' => $pais,
            'ciudad' => $ciudad,
            'latitud' => $latitud,
            'longitud' => $longitud,
            'usuario_id' => $datos->id,
            'refresh_token' => $token['refresh_token'],
            'access_token' => $token['access_token'],
            'ip_address' => $ip, //$request->ip(),
            'usuario_agent' => $userAgent,
        ];
        $this->refreshTokenService->revocarTodosLosRefreshTokens($datos->id);
        $resToken = $this->refreshTokenService->crear($tokenRefresh);
        if ($resToken) {
            return ManejoData::armarDevolucion(200, true, $msg, $token);
        } else {
            return ManejoData::armarDevolucion(400, false, "error insercion token", null, 'token refresh');
        }
    }

    private function desencriptado($encrypted)
    {
        $key = '12345678901234567890123456789012';
        $iv = '1234567890123456';

        $cipher = 'AES-256-CBC';
        $decrypted = openssl_decrypt(
            base64_decode($encrypted),
            $cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return response()->json(['desencriptado' => $decrypted]);
    }
}