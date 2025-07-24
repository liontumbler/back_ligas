<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Utils\ManejoData;

use App\Services\LicenciaService;

class LicenciasController extends Controller
{
    protected $licenciaService;
    protected $arregloRetorno = [];

    protected $reglaCrear = [
        'codigo'            => 'required|string|unique:licencias,codigo|max:255',
        'valor'             => 'required|numeric|min:0',
        'fecha_inicio'      => 'required|date',
        'fecha_fin'         => 'nullable|date|after_or_equal:fecha_inicio',
        'estado'            => 'required|in:activa,inactiva,vencida'
    ];

    protected $reglaActualizar = [
        'codigo'            => 'sometimes|required|string|unique:licencias,codigo|max:255',
        'valor'             => 'sometimes|required|numeric|min:0',
        'fecha_inicio'      => 'sometimes|required|date',
        'fecha_fin'         => 'sometimes|nullable|date|after_or_equal:fecha_inicio',
        'estado'            => 'sometimes|required|in:activa,inactiva,vencida'
    ];

    public function __construct()
    {
        $this->licenciaService = new LicenciaService();
    }

    public function index(Request $request)
    {
        try {
            $size = $request->input('size', '0');
            $sort = $request->input('sort', 'id:asc');
            $filter = $request->input('filter', null);
            $licencias = $this->licenciaService->todo($request->usuario, $sort, $size, $filter);
            $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $licencias);
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
            $licencia = $this->licenciaService->crearLicencia($data, $request->usuario);

            $this->arregloRetorno = ManejoData::armarDevolucion(201, true, "Se creo exitosamente", $licencia);
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function show($id)
    {
        try {
            $licencia = $this->licenciaService->obtenerXId($id);
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

    public function update(Request $request, $id)
    {
        try {
            $licencia = $this->licenciaService->obtenerXId($id);
            if (!$licencia) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $isPut = $request->method() === 'PUT';

                $rules = $isPut ? $this->reglaCrear : $this->reglaActualizar;
                $data = $request->validate($rules);
                $datos = $this->licenciaService->actualizarLicencia($id, $data, $request->usuario);

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
            $licencia = $this->licenciaService->obtenerXId($id);
            if (!$licencia) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $datos = $this->licenciaService->eliminarLicencia($id);
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se elimina con exito", $datos);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }
}
