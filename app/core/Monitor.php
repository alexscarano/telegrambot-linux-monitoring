<?php

namespace app\core;

use Exception;
use app\core\SSH;

class Monitor {
    
    /**
     * Checa se um serviço específico está rodando utilizando o systemctl (systemd)
     * @param string $service
     * @return string|null
     */
    public static function checkServiceIsActive(string $service): string|null{
        
        $ssh = new SSH();
        $ssh->auth();

        if ($ssh->isAuthenticated()){
            $output = $ssh->exec("sudo /usr/bin/systemctl is-active {$service}");
            $ssh->disconnect();
        }

        if (empty($output)) return null;

        return $output === 'active' ?
        "O serviço {$service} está ativo"
        : "O serviço {$service} não está ativo ou falhou";
    } 

    /**
     * Faz uma requisição HTTP para verificar se um website está ativo
     * @param string $url
     * @throws \Exception
     * @return string
     */
    public static function checkWebsiteIsActive(string $url): string{

        if (!function_exists('curl_init')) {
            throw new Exception("O módulo do curl não existe no servidor instale o php-curl", 1); 
        }

        try {
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); // Define a URL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
            curl_setopt($ch, CURLOPT_HEADER, false); // Não inclui os cabeçalhos na saída
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-App/1.0'); // Define o User-Agent
            
            $http_status = 0;
            $request = curl_exec($ch);
            $request === FALSE ? curl_error($ch) : $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        catch (Exception $e){
           throw $e;
        }
        finally {
            if (isset($ch) && is_resource($ch))
                curl_close($ch); 
        }

        switch ((int) $http_status) {
            case 200:
                return "O site está ativo e respondendo normalmente (Status:  {$http_status}).";
            case 400: // Bad Request
                return "O servidor não conseguiu processar a requisição (Status:  {$http_status})."; 
            case 401: // Unauthorized
                return "Acesso não autorizado (Status: {$http_status}).";
            case 403: // Forbidden
                return "O acesso ao site foi negado ou a requisição é inválida (Status: {$http_status}).";
            case 404: // Not Found
                return "O site ou a página não foi encontrada (Status: {$http_status}).";
            case 500: // Internal Server Error
                return "Erro interno no servidor (Status: {$http_status}).";
            case 502: // Bad Gateway
                return "Resposta inválida (Status: {$http_status}).";
            case 503: // Service Unavailable
                return "Serviço inativo (Status: {$http_status}).";
            case 504: // Gateway Timeout
                return "Ocorreu um erro no servidor ou o serviço está indisponível (Status: {$http_status}).";
            case 0: // Caso o cURL falhe completamente ou haja um timeout
                return "Não foi possível conectar ao site ou a requisição expirou. Verifique a URL ou a conexão.";
            default:
                return "O site retornou um status inesperado (Status: {$http_status}). Sugiro verificar manualmente.";
        }

    }

    /**
     * Retorna status geral, uso de disco, memória, ip local, ip público, temperatura de cpu e etc.
     * @return array|array{cpuTemp: string, diskUsage: string, localIp: string, memoryUsage: string, publicIp: string, uptime: string}
     */
    public static function generalStatus(): array{

        $ssh = new SSH();
        $ssh->auth();

        if ($ssh->isAuthenticated()){

            $hostname = $ssh->exec("hostname");
            $uptime = $ssh->exec("uptime -p | sed 's/up //'");
            $diskUsage = $ssh->exec('df -h / | awk \'NR==2 {print $4 " livre de " $2}\'');
            $memoryUsage = $ssh->exec('free -m | awk \'/^Mem:/ {print $3 " MB usados de " $2 " MB"}\'');
            $localIp = $ssh->exec('hostname -I | awk \'{print $2}\'');
            $publicIp = $ssh->exec('curl -s ifconfig.me || echo "indisponível"');
            $cpuTemp = $ssh->exec('command -v sensors >/dev/null && sensors | grep -m1 \'Package id 0:\' | awk \'{print $4}\' || echo "indisponível"');
                        
            $responses = 
            [
                'hostname' => $hostname,
                'uptime' => $uptime,
                'diskUsage' => $diskUsage,
                'memoryUsage' => $memoryUsage,
                'localIp' => $localIp,
                'publicIp' => $publicIp,
                'cpuTemp' => $cpuTemp
            ];

            $ssh->disconnect();
            
            if (!empty($responses)){
                return $responses;
            }
            
        }

        return [];

    }

    public static function scanNetwork(string $interface = 'vmbr0'): array {
        $ssh = new SSH();
        $ssh->auth();

        $output = $ssh->exec("sudo /usr/sbin/arp-scan --interface={$interface} --localnet");
 
        if (empty($output)) {
            return [];
        }

        $devices = [];
        $devicesCount = 1;
        $lines = explode("\n", $output);
 
        foreach ($lines as $line) {
        // ignora cabeçalhos ou linhas em branco
        if (preg_match('/^([\d\.]+)\s+([0-9A-Fa-f:]{17})\s+(.+)$/', $line, $matches)) {
            $devices[] = [
                'ip' => $matches[1],
                'mac' => $matches[2],
                'vendor' => trim($matches[3]),
                'device_count' => $devicesCount
                ];
            ++$devicesCount;
            }
        }

        return $devices;
    }





}
