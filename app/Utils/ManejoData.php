<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;

class ManejoData
{
    public static function armarDevolucion($codigo, $paso, $mensaje, $datos = [], $error = null)
    {
        return [
            'code'  =>  $codigo,
            'success' => $paso,
            'message' => $mensaje,
            'datos' => $datos,
            'errores' => $error,
        ];
    }

    public static function generarPayload($verificaJwt, $expiracion)
    {
        return [
            'sub' => $verificaJwt['id'],
            'exp' => $expiracion
        ];
    }

    public static function mensajeQueryException(QueryException $exception)
    {
        $mensajeExcepcion       =   "";
        switch ($exception->getCode()) {
            case 23503:
                $mensajeExcepcion   =   "La información contiene datos relacionados, debe eliminar dichos datos para continuar";
                break;

            default:
                $finalError         =   strpos(trim($exception->getMessage()), '(SQL');
                $messageError       =   substr($exception->getMessage(), 0,      $finalError);
                $messageError       =   str_replace('SQLSTATE', 'Incidencia', $messageError);

                $mensajeExcepcion   =   "La información presenta inconsistencia en los datos " . $messageError .
                    " por favor corregir, en caso de persistir, contacte al Administrador";
                break;
        }
        return $mensajeExcepcion;
    }

    public static function verificarExcepciones(Exception $exception)
    {
        $mensajeExcepcion   =   "";

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $mensajeExcepcion   =   $exception->errors();
        } else if ($exception instanceof QueryException) {
            $mensajeExcepcion   =   self::mensajeQueryException($exception);
        } else {
            $mensajeExcepcion   =   "Error en el sistema " . $exception->getMessage() . " contacte al Administrador";
        }

        Log::error($exception);
        return $mensajeExcepcion;
    }
}
