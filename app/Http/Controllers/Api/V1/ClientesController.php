<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Utils\ManejoData;

use App\Services\ClienteService;

class ClientesController extends Controller
{
    protected $clienteService;
    protected $arregloRetorno = [];

    protected $reglaCrear = [
        'nombres' => 'required|string|max:100',
        'apellidos' => 'required|string|max:100',
        'correo' => 'required|string|max:20',
        'telefono' => 'nullable|string|max:20',
        'liga_id' => 'required|string'
        
    ];

    protected $reglaActualizar = [
        'nombres' => 'sometimes|required|string|max:100',
        'apellidos' => 'sometimes|required|string|max:100',
        'correo' => 'sometimes|required|string|max:20',
        'telefono' => 'sometimes|nullable|string|max:20',
        'liga_id' => 'sometimes|nullable|string'
    ];

    public function __construct()
    {
        $this->clienteService = new ClienteService();
    }

    public function index(Request $request)
    {
        try {
            $size = $request->input('size', '0');
            $sort = $request->input('sort', 'id:asc');
            $filter = $request->input('filter', null);
            $clientes = $this->clienteService->todo($sort, $size, $filter);
            $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $clientes);
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
            $cliente = $this->clienteService->crearcliente($data, $request->usuario);

            $this->arregloRetorno = ManejoData::armarDevolucion(201, true, "Se creo exitosamente", $cliente);
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function show($id)
    {
        try {
            $cliente = $this->clienteService->obtenerXId($id);
            if (!$cliente) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $cliente);
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
            $cliente = $this->clienteService->obtenerXId($id);
            if (!$cliente) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $isPut = $request->method() === 'PUT';

                $rules = $isPut ? $this->reglaCrear : $this->reglaActualizar;
                $data = $request->validate($rules);
                $datos = $this->clienteService->actualizarcliente($id, $data, $request->usuario);

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
            $cliente = $this->clienteService->obtenerXId($id);
            if (!$cliente) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $datos = $this->clienteService->eliminarcliente($id);
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se elimina con exito", $datos);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }
}