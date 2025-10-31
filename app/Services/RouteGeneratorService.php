<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class RouteGeneratorService
{
    public static function crearRutaYControlador(string $urlExterna, string $urlInterna, string $metodo, string $nombreControlador)
    {
        $controllerName = ucfirst($nombreControlador) . 'Controller';
        $controllerPath = app_path("Http/Controllers/ControllerGenerate/{$controllerName}.php");

        $metodo = strtoupper($metodo);
        $metodoMin = strtolower($metodo);
        $nombreMetodo = $metodoMin . 'Handler';

        $service = '';
        if (strtoupper($metodo) === 'GET') {
            // GET → enviar query params
            $service = 
<<<PHP
\$response = \$http->get('{$urlInterna}', \$request->query());
PHP;
        } else {
            // POST, PUT, PATCH, DELETE → enviar cuerpo tal cual
            $service = 
<<<PHP
\$response = \$http->withBody(
    \$request->getContent(),
    \$request->header('Content-Type', 'application/json')
)->send(\$metodo, '{$urlInterna}');
PHP;
        }

        // 🔧 Código del método dinámico que reenvía la petición tal cual
        $nuevoMetodo = 
<<<PHP
    public function {$nombreMetodo}(Request \$request)
    {
        \$http = Http::withHeaders([
            'Accept' => 'application/json',
        ]);

        {$service}

        return response(\$response->body(), \$response->status())
            ->header('Content-Type', \$response->header('Content-Type', 'application/json'));
    }
PHP;

        // 1️⃣ Si el controlador NO existe → crear clase completa
        if (!File::exists($controllerPath)) {
            $contenido = 
<<<PHP
<?php
namespace App\\Http\\Controllers\\ControllerGenerate;

use App\Http\Controllers\Controller;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Http;

class {$controllerName} extends Controller
{
    {$nuevoMetodo}
}
PHP;

            File::put($controllerPath, $contenido);
        } else {
            // 2️⃣ Si el controlador existe → solo agregar el método si no existe
            $contenido = File::get($controllerPath);

            if (!preg_match("/public function {$nombreMetodo}\s*\(/", $contenido)) {
                $contenido = preg_replace('/\}\s*$/', $nuevoMetodo . '
                }', $contenido);
                File::put($controllerPath, $contenido);
            }
        }

        // 3️⃣ Agregar la ruta correspondiente en routes/api.php (si no existe)
        $routeFile = app_path('routes/api.php');
        $routeFile = base_path('routes/api.php');
        //$routeDefinition = "\nRoute::" . $metodoMin . "('{$urlExterna}', [\\App\\Http\\Controllers\\ControllerGenerate\\{$controllerName}::class, '{$nombreMetodo}']);\n";
        $routeDefinition = "\nRoute::" . $metodoMin . "('{$urlExterna}', '\\App\\Http\\Controllers\\ControllerGenerate\\{$controllerName}@{$nombreMetodo}');\n";

        $rutas = File::get($routeFile);
        if (strpos($rutas, "Route::$metodoMin('$urlExterna'") === false) {
            File::append($routeFile, $routeDefinition);
        }

        return [
            'controlador' => $controllerPath,
            'ruta' => $routeFile,
            'mensaje' => "✅ Método {$nombreMetodo} agregado (si no existía) en {$controllerName} y ruta '{$urlExterna}' creada correctamente."
        ];
    }

    public static function eliminarRutaYControlador(string $metodo, string $nombreControlador)
    {
        $controllerName = ucfirst($nombreControlador) . 'Controller';
        $controllerPath = app_path("Http/Controllers/ControllerGenerate/{$controllerName}.php");
        $metodo = strtoupper($metodo);
        $nombreMetodo = strtolower($metodo) . 'Handler';

        // 1️⃣ Verificar que el controlador exista
        if (!File::exists($controllerPath)) {
            throw new \Exception("❌ El controlador {$controllerName} no existe.");
        }

        $contenido = File::get($controllerPath);

        // 2️⃣ Buscar el método dentro del controlador
        $pattern = "/public function {$nombreMetodo}\s*\(.*?\)\s*\{.*?\n\s*\}/s";

        if (!preg_match($pattern, $contenido)) {
            throw new \Exception("❌ El método {$nombreMetodo} no existe en {$controllerName}.");
        }

        // 3️⃣ Eliminar el método del controlador
        $nuevoContenido = preg_replace($pattern, '', $contenido);

        File::put($controllerPath, $nuevoContenido);

        // 4️⃣ Buscar y eliminar la ruta correspondiente en routes/api.php
        $routeFile = base_path('routes/api.php');
        if (!File::exists($routeFile)) {
            throw new \Exception("❌ El archivo de rutas 'routes/api.php' no existe.");
        }

        $rutas = File::get($routeFile);

        // Patrón para eliminar la ruta asociada a ese controlador/método
        $routePattern = "/Route::" . strtolower($metodo) . "\(.*{$controllerName}::class.*'{$nombreMetodo}'.*\);\s*/";

        if (!preg_match($routePattern, $rutas)) {
            throw new \Exception("❌ No se encontró ninguna ruta asociada a {$controllerName}::{$nombreMetodo}.");
        }

        $rutasActualizadas = preg_replace($routePattern, '', $rutas);

        File::put($routeFile, $rutasActualizadas);

        return [
            'controlador' => $controllerPath,
            'ruta' => $routeFile,
            'mensaje' => "✅ Se eliminó correctamente el método {$nombreMetodo} de {$controllerName} y su ruta asociada."
        ];
    }
}
