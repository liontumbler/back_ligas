<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class RouteGeneratorService
{
    protected $backupDir;
    protected $dir;

    public function __construct() {
        $this->backupDir = 'Http/Controllers/ControllerGenerate/backup';
        $this->dir = 'Http/Controllers/ControllerGenerate';
    }

    public function crearRutaYControlador(string $urlExterna, string $urlInterna, string $metodo, string $nombreControlador, array  $variables = [], array $headers = [])
    {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $nombreControlador)) {
            throw new \Exception("Nombre de controlador inválido");
        }
        
        if (!in_array(strtoupper($metodo), ['GET', 'POST', 'PUT', 'DELETE'])) {
            throw new \Exception("Método HTTP no permitido");
        }

        // if (!$this->isSafeHttpUrl($urlExterna)) {
        //     throw new \Exception("URL externa inválida");
        // }

        if (!File::exists(app_path($this->dir))) {
            File::makeDirectory(app_path($this->dir), 0755, true);
        }

        if (!File::exists(app_path($this->backupDir))) {
            File::makeDirectory(app_path($this->backupDir), 0755, true);
        }

        $metodo = strtoupper($metodo);
        $metodoMin = strtolower($metodo);
        $nombreMetodo = $metodoMin . 'Handler';

        $headersCode = $this->generateHeaders($headers);
        $service = $this->generateService($metodo, $urlInterna, $headersCode);
        $nuevoMetodo = $this->generateFunction($nombreMetodo, $service, $variables);

        $controllerName = ucfirst($nombreControlador) . 'Controller';
        $controllerPath = app_path("{$this->dir}/{$controllerName}.php");
        
        // 1️⃣ Si el controlador NO existe → crear clase completa
        if (!File::exists($controllerPath)) {
            $contenido = $this->generateController($controllerName, $nuevoMetodo);
            //return "{$this->dir}/{$controllerName}.php";
            File::put($controllerPath, $contenido);
        } else {
            File::copy($controllerPath, app_path($this->backupDir . "/{$controllerName}.php" . '.bak.' . time()));
            // 2️⃣ Si el controlador existe → solo agregar el método si no existe
            $contenido = File::get($controllerPath);

            if (!preg_match("/public function {$nombreMetodo}\s*\(/", $contenido)) {
                $contenido = preg_replace('/\}\s*$/', $nuevoMetodo . '
                }', $contenido);
                File::put($controllerPath, $contenido);
            }
        }

        $routeBaseFolder = base_path('routes');
        $controllerRouteFolder = "{$routeBaseFolder}/{$controllerName}";
        if (!File::exists($controllerRouteFolder)) {
            File::makeDirectory($controllerRouteFolder, 0755, true);
        }

        $routeDefinition = "\nRoute::" . $metodoMin . "('{$urlExterna}', [\\App\\Http\\Controllers\\ControllerGenerate\\{$controllerName}::class, '{$nombreMetodo}']);\n";
        $routeFile = "{$controllerRouteFolder}/{$controllerName}Route.php";
        if (!File::exists($routeFile)) {
            $contenido = "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n";
            $contenido .= $routeDefinition;
            File::put($routeFile, $contenido);
        } else {
            // Agregar la ruta solo si no existe
            $rutas = File::get($routeFile);
            if (strpos($rutas, "Route::{$metodoMin}('{$urlExterna}'") === false) {
                File::append($routeFile, $routeDefinition);
            }
        }

        $serviceProviderPath = app_path('Providers/AppServiceProvider.php');

        // Verificar que exista
        if (!File::exists($serviceProviderPath)) {
            throw new \Exception("No se encontró AppServiceProvider.php en app/Providers");
        }

        $contenido = File::get($serviceProviderPath);
        $nuevaLinea = "\n        \$this->loadRoutesFrom(base_path('routes/{$controllerName}/{$controllerName}Route.php'));";

        // Solo agregamos si no existe aún
        if (!str_contains($contenido, "routes/{$controllerName}/{$controllerName}Route.php")) {
            // Asegurar que tenga método boot()
            if (!preg_match('/public function boot\s*\(\)\s*:\s*void\s*\{/', $contenido)) {
                throw new \Exception("El método boot() no fue encontrado en AppServiceProvider");
            }

            // Insertar antes de la última llave del método boot()
            $contenido = preg_replace(
                '/(public function boot\s*\(\)\s*:\s*void\s*\{)(.*?)(\n\s*\})/s',
                '$1$2' . $nuevaLinea . '$3',
                $contenido
            );

            File::put($serviceProviderPath, $contenido);
        }

        return [
            'controlador' => $controllerPath,
            'ruta' => $routeFile,
            'mensaje' => "✅ Método {$nombreMetodo} agregado (si no existía) en {$controllerName} y ruta '{$urlExterna}' creada correctamente."
        ];
    }

    function generateHeaders($headers) {
        $headersCode = '';
        if (count($headers) > 0) {
            $headersCode = var_export($headers, true) . ';';
        } else {
            // Si no se pasan headers, generamos el código para tomarlos del request
            $headersCode = 
<<<PHP
collect(\$request->headers->all())
        // ->reject(fn(\$v, \$k) => in_array(strtolower(\$k), ['host', 'content-length'])) // opcional: filtrar
        ->mapWithKeys(fn(\$v, \$k) => [\$k => is_array(\$v) ? implode(', ', \$v) : \$v])
        ->toArray();
PHP;
        }
        return $headersCode;
    }

    private function generateFunction($nombreMetodo, $service, array $variables) {
        $validate = 
<<<PHP
return response()->json(json_decode(\$response->body()), \$response->status());
PHP;
        if (count($variables)) {
            $variablesCode = var_export($variables, true);
            $validate = 
<<<PHP
// Decodificar el cuerpo de la respuesta
    \$data = json_decode(\$response->body(), true);
    \$variables = {$variablesCode};

    // Crear un arreglo filtrado solo con las variables especificadas
    \$filteredData = [];
    foreach (\$variables as \$var) {
        \$filteredData[\$var] = \$data[\$var] ?? null;
    }

    return response()->json(json_decode(\$filteredData), \$response->status());
PHP;
        }
        
        return 
<<<PHP
public function {$nombreMetodo}(Request \$request)
    {
        {$service}

        {$validate}
    }
PHP;
    }

    private function generateController($controllerName, $nuevoMetodo) {
        return 
<<<PHP
<?php
namespace App\\Http\\Controllers\\ControllerGenerate;

use App\Http\Controllers\Controller;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Http;

class {$controllerName} //extends Controller
{
    {$nuevoMetodo}
}
PHP;
    }

    private function generateService($metodo, $urlInterna, $headers) {
        $service = '';
        if (strtoupper($metodo) === 'GET') {
            // GET → enviar query params
            $service = 
<<<PHP
\$headers = {$headers}
        \$http = Http::withHeaders(\$headers);
        \$response = \$http->get('{$urlInterna}', \$request->query());
PHP;
        } else {
            // POST, PUT, PATCH, DELETE → enviar cuerpo tal cual
            $service = 
<<<PHP
\$headers = {$headers}
        \$http = Http::withHeaders(\$headers);
        \$response = \$http->withBody(
            \$request->getContent(),
            \$request->header('Content-Type', 'application/json')
        )->send(\$metodo, '{$urlInterna}');
PHP;
        }

        return $service;
    }

    public function eliminarControladorYRuta(string $nombreControlador, string $metodo)
    {
        $nombreMetodo = strtolower($metodo) . 'Handler';
        $controllerName = ucfirst($nombreControlador) . 'Controller';
        $controllerPath = app_path("{$this->dir}/{$controllerName}.php");
        $controllerRouteFolder = base_path("routes/{$controllerName}");
        $routeFile = "{$controllerRouteFolder}/{$controllerName}Route.php";
        $providerPath = app_path('Providers/AppServiceProvider.php');

        $result = [
            'controlador' => false,
            'ruta' => false,
            'provider' => false,
            'mensaje' => [],
        ];

        // 🧹 1️⃣ Eliminar método del controlador (o el archivo si no queda nada)
        if (File::exists($controllerPath)) {
            $contenido = File::get($controllerPath);
            $pattern = "/public function {$nombreMetodo}\s*\([^}]*\}\s*/s";

            if (preg_match($pattern, $contenido)) {
                $nuevoContenido = preg_replace($pattern, '', $contenido);
                // Si el archivo queda sin métodos propios → eliminar todo el controlador
                if (!preg_match('/public function\s+\w+\s*\(/', $nuevoContenido)) {
                    File::delete($controllerPath);
                    $result['mensaje'][] = "🗑 Controlador {$controllerName} eliminado (sin métodos restantes).";
                } else {
                    File::put($controllerPath, $nuevoContenido);
                    $result['mensaje'][] = "🧹 Método {$nombreMetodo} eliminado de {$controllerName}.";
                }
                $result['controlador'] = true;
            }
        }

        // 🧭 2️⃣ Eliminar la ruta asociada
        if (File::exists($routeFile)) {
            $rutas = File::get($routeFile);
            $patternRuta = "/Route::[a-z]+\('.*', \[.*{$nombreMetodo}.*\]\);\n?/";
            $nuevoRutas = preg_replace($patternRuta, '', $rutas);

            // Si el archivo queda vacío (solo cabecera), eliminarlo todo
            if (trim($nuevoRutas) === "<?php\n\nuse Illuminate\Support\Facades\Route;") {
                File::delete($routeFile);
                File::deleteDirectory($controllerRouteFolder);
                $result['mensaje'][] = "🗑 Archivo de ruta {$routeFile} eliminado (sin rutas restantes).";
            } else {
                File::put($routeFile, $nuevoRutas);
                $result['mensaje'][] = "🧹 Ruta eliminada de {$routeFile}.";
            }
            $result['ruta'] = true;
        }

        // 🧩 3️⃣ Quitar la línea del provider
        if (File::exists($providerPath)) {
            
            $contenidoProvider = File::get($providerPath);
            //return $providerPath;
            $patternProvider = '/\$this->loadRoutesFrom\s*\(\s*base_path\(["\']routes\/' . $controllerName . '\/' . $controllerName . 'Route\.php["\']\)\s*\)\s*;/';
            if (preg_match($patternProvider, $contenidoProvider)) {
                $nuevoProvider = preg_replace($patternProvider, '', $contenidoProvider);
                File::put($providerPath, $nuevoProvider);
                $result['mensaje'][] = "🧩 Línea del provider para {$controllerName} eliminada.";
                $result['provider'] = true;
            }
        }

        if (empty($result['mensaje'])) {
            $result['mensaje'][] = "⚠️ No se encontró nada que eliminar para {$controllerName}::{$nombreMetodo}.";
        }

        return $result;
    }

    public function findMethodsInGeneratedControllers(string $filter = ''): array {
        $results = [];
        foreach (File::files($this->dir) as $file) {
            $content = File::get($file->getPathname());
            preg_match_all('/public function\s+([a-zA-Z0-9_]+)\s*\(/', $content, $matches);
            $methods = $matches[1] ?? [];
            if ($filter) {
                $methods = array_filter($methods, fn($m) => stripos($m, $filter) !== false);
                if (empty($methods)) continue;
            }
            $results[] = [
                'file' => $file->getFilename(),
                'methods' => array_values($methods),
            ];
        }
        return $results;
    }

    public function listGeneratedControllers(): array {
        if (!File::exists($this->dir)) return [];

        $files = File::files($this->dir);
        $result = [];
        foreach ($files as $file) {
            $content = File::get($file->getPathname());
            // Extraer nombre de la clase con regex (básico)
            if (preg_match('/class\s+([A-Za-z0-9_]+)\s+extends/', $content, $m)) {
                $result[] = [
                    'file' => $file->getFilename(),
                    'class' => $m[1],
                ];
            }
        }
        return $result;
    }

    public function restaurarBackup(string $nombreControlador, ?string $timestamp = null)
    {
        $controllerName = ucfirst($nombreControlador) . 'Controller';

        $controllerPath = "{$this->backupDir}/{$controllerName}.php";

        // Buscar todos los backups disponibles
        $backups = glob("{$controllerPath}.bak.*");

        if (empty($backups)) {
            return "⚠️ No se encontraron backups para {$controllerName}";
        }

        // Si no se especifica timestamp, usar el más reciente
        if (!$timestamp) {
            $backup = collect($backups)->sortDesc()->first();
        } else {
            $backup = "{$controllerPath}.bak.{$timestamp}";
        }

        if (!file_exists($backup)) {
            return "❌ El backup especificado no existe: {$backup}";
        }

        // Restaurar el backup
        copy($backup, $controllerPath);

        return "✅ Backup restaurado desde: {$backup}";
    }

    private function isValidUrl(string $url): bool {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    private function isSafeHttpUrl(string $url): bool {
        //if (!$this->isValidUrl($url)) return false;
        $parts = parse_url($url);
        return in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https']);
    }
}
