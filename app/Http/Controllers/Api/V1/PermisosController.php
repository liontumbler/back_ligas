<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Utils\ManejoData;

use App\Services\PermisoService;

class PermisosController extends Controller
{
    protected $PermisoService;
    protected $arregloRetorno = [];

    protected $reglaCrear = [
        'menu_id' => 'required|exists:menus,id',
        'action'  => 'required|in:view,create,update,delete',
    ];

    protected $reglaActualizar = [
        'menu_id' => 'sometimes|required|exists:menus,id',
        'action'  => 'sometimes|required|in:view,create,update,delete',
    ];

    public function __construct()
    {
        $this->PermisoService = new PermisoService();
    }

    public function index(Request $request)
    {
        try {
            $size = $request->input('size', '0');
            $sort = $request->input('sort', 'id:asc');
            $filter = $request->input('filter', null);
            $Permisos = $this->PermisoService->todo($sort, $size, $filter);
            $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $Permisos);
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
            $Permiso = $this->PermisoService->crearPermiso($data, $request->usuario);

            $this->arregloRetorno = ManejoData::armarDevolucion(201, true, "Se creo exitosamente", $Permiso);
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }

    public function show($id)
    {
        try {
            $Permiso = $this->PermisoService->obtenerXId($id);
            if (!$Permiso) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se muestra con exito", $Permiso);
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
            $Permiso = $this->PermisoService->obtenerXId($id);
            if (!$Permiso) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $isPut = $request->method() === 'PUT';

                $rules = $isPut ? $this->reglaCrear : $this->reglaActualizar;
                $data = $request->validate($rules);
                $datos = $this->PermisoService->actualizarPermiso($id, $data, $request->usuario);

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
            $Permiso = $this->PermisoService->obtenerXId($id);
            if (!$Permiso) {
                $this->arregloRetorno = ManejoData::armarDevolucion(404, true, "Valor no encontrado", []);
            } else {
                $datos = $this->PermisoService->eliminarPermiso($id);
                $this->arregloRetorno = ManejoData::armarDevolucion(200, true, "Se elimina con exito", $datos);
            }
        } catch (Exception $e) {
            $this->arregloRetorno = ManejoData::armarDevolucion(500, false, "Error inesperado", null,  ManejoData::verificarExcepciones($e));
        } finally {
            return response()->json($this->arregloRetorno, $this->arregloRetorno['code']);
        }
    }
}
