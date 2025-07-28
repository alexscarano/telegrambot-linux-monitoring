<?php 

namespace app\core;

use app\support\Wol;

class CommandHandler extends Bot{
 
    private const DEFAULT_RESPONSE = 'Este comando não existe ou foi digitado incorretamente, tente novamente';
    public function __construct(){
        parent::__construct();
    }

    /**
     * Envia a mensagem padrão
     * @return void
     */
    private function sendDefaultResponse(): void{
        $this->sendMessage($this::DEFAULT_RESPONSE);     
    } 

    /**
     * Envia uma mensagem para verificar se o bot está funcionando
     * @return void
     */
    protected function sendBotStatus(): void {
            $this->sendMessage('Bot funcionando, pronto para receber comandos');
    }
 
    /**
     * Verifica se é um comando (não verifica se é valido, somente se tem '/' no começo do texto)
     * @param string $command
     * @return bool|int
     */
    private static function IsCommand(string $command): bool{
        return preg_match('/^\/\w+/', $command);
    }
 
    /**
     *Método utilizado para processar argumentos do comando
     * @param mixed $method
     * @param array $args
     * @return void
     */
    private function argsProcessor($method, array $args): void{
        if (!empty($args)){
            foreach ($args as $arg){
                $message = call_user_func($method, $arg);
                $this->sendMessage($message);  
            }
        }
        else {
            $this->sendMessage('É necessário argumentos para executar este comando');
        }

    }

    public function handleTextCommand(string $text): void{

        $partsArray = explode(' ', trim($text));
        $command = $partsArray[0] ?? '';
        $args = array_slice($partsArray, 1);

        if (!$this::IsCommand($command)) return;

        // Cada case é um comando a ser processado
        switch ((string)$command){
            // Checa se um serviço dentro do node está ativo
            case '/checkService':
               $this->argsProcessor(['app\core\Monitor', 'checkServiceIsActive'], $args); 
               break;
            
            // Envia o status do bot, se ele estiver on  
            case '/checkSite':
                $this->argsProcessor(['app\core\Monitor', 'checkWebsiteIsActive'], $args); 
                break;

            case '/botStatus':
                $this->sendBotStatus();
                break;

            case '/checkUpDevices':
                $devices = Monitor::scanNetwork('vmbr0');
                if (empty($devices)) {
                    $this->sendMessage("Nenhum dispositivo encontrado na rede.");
                } else {
                    $message = "🖥️ *Dispositivos encontrados:*\n";
                    foreach ($devices as $d) {
                        $message .= "``` {$d['ip']} - {$d['mac']} - *{$d['vendor']}*\n```";
                    }
                    $message .=  PHP_EOL .  "⚡ *Quantidade de hosts ativos: {$d['device_count']}*";
                    $this->sendMessage($message);
                }
                break;

            case '/status':
                $response = Monitor::generalStatus();

                if (empty($response)) {
                    return;
                }
                
                $message = 
                "```
                🖥️ Máquina: {$response['hostname']}
                ⏱️ Uptime: {$response['uptime']}
                💽 Espaço em disco: {$response['diskUsage']}
                🧠 Memória usada: {$response['memoryUsage']}
                📡 IP local: {$response['localIp']}
                🌍 IP público: {$response['publicIp']}
                🌡️ Temp. da CPU: {$response['cpuTemp']}
                ```";

                $this->sendMessage($message); 
                break;
            
            default:
                $this->sendDefaultResponse();
                break;
        }

    }



}    



