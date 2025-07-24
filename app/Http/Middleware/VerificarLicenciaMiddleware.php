<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Utils\ManejoData;

use App\Models\Tablas\Licencias;

class VerificarLicenciaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $codigoLicencia = $request->header('License');

        if (!$codigoLicencia) {
            $devolucion = ManejoData::armarDevolucion(400, false, 'Licencia no proporcionada', null, 'Licencia');
            return response()->json($devolucion, $devolucion['code']);
        }

        $licencia = Licencias::where('codigo', $codigoLicencia)->first();
        if (!$licencia) {
            $devolucion = ManejoData::armarDevolucion(403, false, 'Licencia inválida o no registrada', null, 'Licencia');
            return response()->json($devolucion, $devolucion['code']);
        }

        if ($licencia->estado === 'inactiva') {
            $devolucion = ManejoData::armarDevolucion(403, false, 'La licencia está inactiva', null, 'Licencia');
            return response()->json($devolucion, $devolucion['code']);
        }

        if ($licencia->estado === 'vencida') {
            $devolucion = ManejoData::armarDevolucion(403, false, 'La licencia está marcada como vencida', null, 'Licencia');
            return response()->json($devolucion, $devolucion['code']);
        }

        if ($licencia->fecha_fin !== null && $licencia->fecha_fin < now()->toDateString()) {
            if ($licencia->estado !== 'vencida') {
                $licencia->estado = 'vencida';
                $licencia->save();
            }

            $devolucion = ManejoData::armarDevolucion(403, false, 'La licencia ha vencido', null, 'Licencia');
            return response()->json($devolucion, $devolucion['code']);
        }

        return $next($request);
    }
}
