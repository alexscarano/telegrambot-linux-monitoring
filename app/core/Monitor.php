<?php

namespace app\core;

use Exception;

class Monitor {
    public static function checkServiceIsActive(string $service): string{
        
        if (is_string($service)){
            $output = trim(shell_exec("sudo systemctl is-active $service"));
        }
        
        if ($output === 'active') 
            return 'O serviço ' . $service . ' está ativo';
        
        return 'O serviço ' . $service . ' não está ativo ou falhou';
    } 

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
                return "O site está ativo e respondendo normalmente (Status " . $http_status . ").";
            case 400: // Bad Request
                return "O servidor não conseguiu processar a requisição (Status " . $http_status . ")."; 
            case 401: // Unauthorized
                return "Acesso não autorizado (Status " . $http_status . ").";
            case 403: // Forbidden
                return "O acesso ao site foi negado ou a requisição é inválida (Status " . $http_status . ").";
            case 404: // Not Found
                return "O site ou a página não foi encontrada (Status " . $http_status . ").";
            case 500: // Internal Server Error
                return "Erro interno no servidor (Status " . $http_status . ").";
            case 502: // Bad Gateway
                return "Resposta inválida (Status " . $http_status . ").";
            case 503: // Service Unavailable
                return "Serviço inativo (Status " . $http_status . ").";
            case 504: // Gateway Timeout
                return "Ocorreu um erro no servidor ou o serviço está indisponível (Status " . $http_status . ").";
            case 0: // Caso o cURL falhe completamente ou haja um timeout
                return "Não foi possível conectar ao site ou a requisição expirou. Verifique a URL ou a conexão.";
            default:
                return "O site retornou um status inesperado (" . $http_status . "). Sugiro verificar manualmente.";
        }

    }

}
