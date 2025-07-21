<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tablas\RefreshTokens;
use Illuminate\Http\Request;
use Exception;
use App\Utils\ManejoData;
use App\Utils\Jwt;
use Illuminate\Support\Facades\Hash;

use App\Services\UsuarioService;
use App\Services\RefreshTokenService;

class UsuariosController extends Controller
{
    protected $usuarioService;
    protected $refreshTokenService;
    protected $jwt;
    protected $arregloRetorno = [];

    protected $reglaCrear = [
        'nombres' => 'required|string|max:100',
        'apellidos' => 'required|string|max:100',
        'correo' => 'required|string|email|max:20|unique:usuarios,correo',
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
        $this->usuarioService = new UsuarioService();
        $this->refreshTokenService = new RefreshTokenService();
        $this->jwt = new Jwt();
    }

    public function index(Request $request)
    {
        try {
            $size = $request->input('size', '0');
            $sort = $request->input('sort', 'id:asc');
            $filter = $request->input('filter', null);
            $Usuarios = $this->usuarioService->todo($sort, $size, $filter);
            $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $Usuarios);
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null, ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate($this->reglaCrear);
            $Usuario = $this->usuarioService->crearUsuario($data, $request->usuario);

            $this->arregloRetorno = ManejoData::armarDevolucion(201, true, "Se creo exitosamente", $Usuario);
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function show($id)
    {
        try {
            $Usuario = $this->usuarioService->obtenerXId($id);
            if (!$Usuario) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $Usuario);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $Usuario = $this->usuarioService->obtenerXId($id);
            if (!$Usuario) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $isPut = $request->method() === 'PUT';
                $this->reglaActualizar['correo'] = $this->reglaActualizar['correo'] . $request->usuario->id;
                $rules = $isPut ? $this->reglaCrear : $this->reglaActualizar;
                $data = $request->validate($rules);
                $datos = $this->usuarioService->actualizarUsuario($id, $data, $request->usuario);

                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se actualiza con exito", $datos);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function destroy($id)
    {
        try {
            $Usuario = $this->usuarioService->obtenerXId($id);
            if (!$Usuario) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $datos = $this->usuarioService->eliminarUsuario($id);
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se elimina con exito", $datos);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function login(Request $request)
    {
        try {
            $data = $request->validate([
                'correo' => 'required|string|email|max:20',
                'password' => 'required|string|max:20'
            ]);

            $datos = $this->usuarioService->obtenerXcorreo($data['correo']);
            if ($datos !== null) {
                if (Hash::check($data['password'], $datos['password'])) {
                    $token = $this->jwt->generateToken($datos);
                    $tokenRefresh = [
                        'usuario_id' => $datos->id,
                        'refresh_token' => $token['refresh_token'],
                        'ip_address' => $request->getClientIp(),//$request->ip(),
                        'usuario_agent' => $request->header('User-Agent'),
                    ];
                    $resToken = $this->refreshTokenService->crearRefreshToken($tokenRefresh);
                    if ($resToken) {
                        $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Login ok", $token);
                    } else {
                        $this->arregloRetorno = ManejoData::armarDevolucion(400, false, "error insercion token", null, 'token refresh');
                    }
                } else {
                    $this->arregloRetorno = ManejoData::armarDevolucion(400, true, "datos incorrectos", null, 'datos incorrectos');
                }
            } else {
                $this->arregloRetorno = ManejoData::armarDevolucion(400, true, "datos incorrectos", null, 'datos incorrectos');
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
}
