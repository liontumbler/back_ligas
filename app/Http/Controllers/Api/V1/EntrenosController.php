<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Utils\ManejoData;

use App\Services\EntrenoService;

class EntrenosController extends Controller
{
    protected $entrenoService;
    protected $arregloRetorno = [];

    protected $reglaCrear = [
        'nombre' => 'required|string|max:100',
        'direccion' => 'nullable|string|max:100',
        'telefono' => 'nullable|string|max:20'
    ];

    protected $reglaActualizar = [
        'cliente_id'    => 'sometimes|required|string|max:100',
        'fecha'> 'sometimes|nullable|string|max:100',
        'tipo'=> 'sometimes|nullable|string|max:20',
        'pago_id'=> 'sometimes|nullable|string|max:20',
        'mensualidad_id'=> 'sometimes|nullable|string|max:20'
    ];

    public function __construct()
    {
        $this->entrenoService = new EntrenoService();
    }

    public function index(Request $request)
    {
        try {
            $size = $request->input('size', '0');
            $sort = $request->input('sort', 'id:asc');
            $filter = $request->input('filter', null);
            $entrenos = $this->entrenoService->todo($sort, $size, $filter);
            $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $entrenos);
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
            $entreno = $this->entrenoService->crearEntreno($data, $request->usuario);

            $this->arregloRetorno = ManejoData::armarDevolucion(201, true, "Se creo exitosamente", $entreno);
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function show($id)
    {
        try {
            $entreno = $this->entrenoService->obtenerXId($id);
            if (!$entreno) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $entreno);
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
            $entreno = $this->entrenoService->obtenerXId($id);
            if (!$entreno) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $isPut = $request->method() === 'PUT';

                $rules = $isPut ? $this->reglaCrear : $this->reglaActualizar;
                $data = $request->validate($rules);
                $datos = $this->entrenoService->actualizarEntreno($id, $data, $request->usuario);

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
            $entreno = $this->entrenoService->obtenerXId($id);
            if (!$entreno) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $datos = $this->entrenoService->eliminarEntreno($id);
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se elimina con exito", $datos);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }
}
