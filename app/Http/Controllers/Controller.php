<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Utils\ManejoData;

use App\Mail\ClaveCajaMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    protected $arregloRetorno = [];
    protected $service;
    protected $reglaCrear;
    protected $reglaActualizar;

    protected function __construct($service, $reglaCrear, $reglaActualizar)
    {
        $this->service = $service;
        $this->reglaCrear = $reglaCrear;
        $this->reglaActualizar = $reglaActualizar;
    }

    protected function index(Request $request)
    {
        try {
            $size = $request->input('size', '0');
            $sort = $request->input('sort', 'id:asc');
            $filter = $request->input('filter', null);
            $licencias = $this->service->todo($request->usuario, $sort, $size, $filter);
            $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $licencias);
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null, ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    protected function store(Request $request)
    {
        try {
            $data = $request->validate($this->reglaCrear);
            $licencia = $this->service->crear($data, $request->usuario);

            $this->arregloRetorno = ManejoData::armarDevolucion(201, true, "Se creo exitosamente", $licencia);
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    protected function show($id)
    {
        try {
            $licencia = $this->service->obtenerXId($id);
            if (!$licencia) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $licencia);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    protected function update(Request $request, $id)
    {
        try {
            $licencia = $this->service->obtenerXId($id);
            if (!$licencia) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $isPut = $request->method() === 'PUT';

                $rules = $isPut ? $this->reglaCrear : $this->reglaActualizar;
                $data = $request->validate($rules);
                $datos = $this->service->actualizar($id, $data, $request->usuario);

                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se actualiza con exito", $datos);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    protected function destroy($id)
    {
        try {
            $licencia = $this->service->obtenerXId($id);
            if (!$licencia) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $datos = $this->service->eliminar($id);
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se elimina con exito", $datos);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    protected function mail($usuario) {
        try {
            Mail::to($usuario->correo)
            ->send(new ClaveCajaMail('xxxx', $usuario->nombres));
            return 'bien';
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo: ' . $e->getMessage());
            return 'mal';
        }
    }
}
