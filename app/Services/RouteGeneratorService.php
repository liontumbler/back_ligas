<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class RouteGeneratorService
{
    protected $backupDir;
    protected $dir;

    public function __construct() {
        $this->backupDir = app_path('Http/Controllers/ControllerGenerate/backup');
        $this->dir = app_path('Http/Controllers/ControllerGenerate');
    }

    public function crearRutaYControlador(string $urlExterna, string $urlInterna, string $metodo, string $nombreControlador, array  $variables, array $headers = [])
    {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $nombreControlador)) {
            throw new \InvalidArgumentException("Nombre de controlador inválido");
        }
        
        if (!in_array(strtoupper($metodo), ['GET', 'POST', 'PUT', 'DELETE'])) {
            throw new \InvalidArgumentException("Método HTTP no permitido");
        }

        if (!$this->isSafeHttpUrl($urlExterna)) {
            throw new \InvalidArgumentException("URL externa inválida");
        }

        if (!File::exists($this->dir)) {
            File::makeDirectory($this->dir, 0755, true);
        }

        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
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
            File::put($controllerPath, $contenido);
        } else {
            File::copy($controllerPath, $controllerPath . '.bak.' . time());
            // 2️⃣ Si el controlador existe → solo agregar el método si no existe
            $contenido = File::get($controllerPath);

            if (!preg_match("/public function {$nombreMetodo}\s*\(/", $contenido)) {
                $contenido = preg_replace('/\}\s*$/', $nuevoMetodo . '
                }', $contenido);
                File::put($controllerPath, $contenido);
            }
        }

        //$routeDefinition = "\nRoute::" . $metodoMin . "('{$urlExterna}', [\\App\\Http\\Controllers\\ControllerGenerate\\{$controllerName}::class, '{$nombreMetodo}']);\n";
        $routeDefinition = "\nRoute::" . $metodoMin . "('{$urlExterna}', '\\App\\Http\\Controllers\\ControllerGenerate\\{$controllerName}@{$nombreMetodo}');\n";

        $routeFile = base_path('routes/api.php');
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
        $variablesCode = var_export($variables, true);
        return 
<<<PHP
    public function {$nombreMetodo}(Request \$request)
    {
        \$http = Http::withHeaders(\$headers);

        {$service}

        // Decodificar el cuerpo de la respuesta
        \$data = json_decode(\$response->body(), true);
        \$variables = {$variablesCode};

        // Crear un arreglo filtrado solo con las variables especificadas
        \$filteredData = [];
        foreach (\$variables as \$var) {
            \$filteredData[\$var] = \$data[\$var] ?? null;
        }
        
        return response()->json(\$filteredData, \$response->status());

        // return response(\$response->body(), \$response->status())
        //     ->header('Content-Type', \$response->header('Content-Type', 'application/json'));
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

class {$controllerName} extends Controller
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

\$response = \$http->get('{$urlInterna}', \$request->query());
PHP;
        } else {
            // POST, PUT, PATCH, DELETE → enviar cuerpo tal cual
            $service = 
<<<PHP
\$headers = {$headers}

\$response = \$http->withBody(
    \$request->getContent(),
    \$request->header('Content-Type', 'application/json')
)->send(\$metodo, '{$urlInterna}');
PHP;
        }

        return $service;
    }

    public function eliminarRutaYControlador(string $metodo, string $nombreControlador)
    {
        $controllerName = ucfirst($nombreControlador) . 'Controller';
        $controllerPath = app_path("{$this->dir}/{$controllerName}.php");
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
        if (!$this->isValidUrl($url)) return false;
        $parts = parse_url($url);
        return in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https']);
    }
}
