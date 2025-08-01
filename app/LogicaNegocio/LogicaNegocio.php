<?php

namespace App\LogicaNegocio;

use Illuminate\Http\Request;

use Exception;
use App\Utils\ManejoData;

use App\Mail\ClaveCajaMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Enums\Alojar;

class LogicaNegocio
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

    public function index(Request $request)
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

    public function store(Request $request)
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

    public function show($id)
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

    public function update(Request $request, $id)
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

    public function destroy($id)
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

    public function mail($usuario)
    {
        try {
            Mail::to($usuario->correo)
                ->send(new ClaveCajaMail('xxxx', $usuario->nombres));
            return 'bien';
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo: ' . $e->getMessage());
            return 'mal';
        }
    }

    public function subirArchivo(Request $request, $guardar = false, Alojar $disk = Alojar::LOCAL)
    {
        $request->validate([
            'archivos' => 'required|array',
            'archivos.*' => 'file|max:10240', // máximo 10MB por archivo
        ]);

        $resultados = [];

        foreach ($request->file('archivos') as $archivo) {
            $contenido = file_get_contents($archivo);
            $extension = $archivo->getClientOriginalExtension();
            $mime = $archivo->getClientMimeType(); // MIME: image/png, application/pdf, etc.
            $base64 = base64_encode($contenido);
            $nombreArchivo = Str::uuid() . '.' . $extension;

            $ruta = 'archivos_guardados/' . $nombreArchivo;

            $resultados[] = [
                'nombre_original' => $archivo->getClientOriginalName(),
                'base64' => 'data:' . $mime . ';base64,' . $base64,
            ];

            if ($guardar) {
                $resultados['ruta_archivo'] = storage_path('app/' . $ruta);
                Storage::disk($disk)->put($ruta, $contenido);
            }
        }

        return response()->json($resultados);
    }
}
