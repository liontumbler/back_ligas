<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Utils\Jwt;

use App\Utils\ManejoData;

class JwtMiddleware
{
    protected $jwt;

    public function __construct()
    {
        $this->jwt = new Jwt();
    }

    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('Authorization');

        if (!isset($authorization)) {
            $devolucion = ManejoData::armarDevolucion(401, false, 'No utorizado', null, 'token autenticacion');
            return response()->json($devolucion, $devolucion['code']);
        }
        
        $verificaJwt = $this->jwt->verificarToken($authorization, config('services.jwt_secret'));
        if (!$verificaJwt['success']) {
            return response()->json($verificaJwt, $verificaJwt['code']);
        }

        $request->merge(['usuario' => $verificaJwt['datos']]);

        return $next($request);
    }
}
