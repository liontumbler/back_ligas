<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ResourceStates
{
    private string $ruta = 'api/health';
    /**
     * Lista de servidores disponibles.
     */
    private array $servers = [
        'https://api1.midominio.com',
        'https://api2.midominio.com',
        'https://api3.midominio.com',
    ];
    //$servers = explode(',', env('SERVICES')),

    public function index()
    {
        // CPU — promedio de carga en 1 minuto
        $cpuLoad = sys_getloadavg()[0] ?? 0;

        // RAM
        $memInfo = $this->getMemoryUsage();

        // Disco
        $diskTotal = disk_total_space("/");
        $diskFree = disk_free_space("/");
        $diskUsage = 100 - (($diskFree / $diskTotal) * 100);

        return [
            'CPU' => round($cpuLoad, 2),
            'RAM' => $memInfo,
            'disk_usage' => round($diskUsage, 2)
        ];
    }

    private function getMemoryUsage(): array
    {
        $os = strtolower(PHP_OS_FAMILY);
        if ($os === 'linux') {
            // Linux: usa /proc/meminfo
            $memInfo = [];
            foreach (file('/proc/meminfo') as $line) {
                [$key, $val] = explode(':', $line);
                $memInfo[trim($key)] = trim($val);
            }

            $total = (int) filter_var($memInfo['MemTotal'], FILTER_SANITIZE_NUMBER_INT);
            $available = (int) filter_var($memInfo['MemAvailable'], FILTER_SANITIZE_NUMBER_INT);
            $used = $total - $available;
            $percent = ($used / $total) * 100;

            $memory = [
                'total_kb' => $total,
                'used_kb' => $used,
                'available_kb' => $available,
                'percent' => round($percent, 2)
            ];
        } elseif ($os === 'windows') {
            // Windows: usa WMIC (herramienta del sistema)
            $output = shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value');
            preg_match_all('/(\w+)=([0-9]+)/', $output, $matches);

            $values = array_combine($matches[1], $matches[2]);
            $total = (int) $values['TotalVisibleMemorySize'];
            $free = (int) $values['FreePhysicalMemory'];
            $used = $total - $free;
            $percent = ($used / $total) * 100;

            $memory = [
                'total_kb' => $total,
                'used_kb' => $used,
                'free_kb' => $free,
                'percent' => round($percent, 2)
            ];
        }
        return $memory;
    }

    public function getBestServer(): ?string
    {
        $bestServer = null;
        $lowestLoad = INF;

        foreach ($this->servers as $server) {
            try {
                $response = Http::timeout(2)->get("$server/$this->ruta");

                if ($response->ok()) {
                    $data = $response->json();

                    $cpu = $data['cpu_load'] ?? 0;
                    $ram = $data['memory']['used_percent'] ?? 0;
                    $disk = $data['disk_usage'] ?? 0;

                    // Cálculo ponderado (ajustable según tus prioridades)
                    $loadScore = ($cpu * 0.4) + ($ram * 0.5) + ($disk * 0.1);

                    if ($loadScore < $lowestLoad) {
                        $lowestLoad = $loadScore;
                        $bestServer = $server;
                    }
                }
            } catch (\Exception $e) {
                // Si un servidor no responde, se ignora
            }
        }

        return $bestServer;
    }
}