<?php 

namespace app\core;
use app\core\Monitor;
use Exception;

class CommandHandler extends Bot{
    private const DEFAULT_RESPONSE = 'Este comando não existe ou foi digitado incorretamente, tente novamente';
    public function __construct(){
        parent::__construct();
    }

    private function sendDefaultResponse(): void{
        $this->sendMessage($this::DEFAULT_RESPONSE);
    } 

    private function sendServiceIsActive(string $service): void{
            try {
                Monitor::checkServiceIsActive($service) 
                ? $this->sendMessage('O serviço ' . $service . ' está ativo')
                :
                  $this->sendMessage('O serviço ' . $service . ' não está ativo');
            }
            catch (Exception $e){
                $this->sendMessage('Ocorreu algum erro na execução do comando'); // Debug
            }
    }

    protected function sendBotStatus(): void {
            $this->sendMessage('Bot funcionando, pronto para receber comandos');
    }

    // Verifica se é um comando (não verifica se é valido, somente se tem '/' no começo do texto)
    private static function IsCommand(string $command): bool{
        return preg_match('/^\/\w+/', $command);
    }

    public function handleTextCommand(string $text): void{

        $partsArray = explode(' ', trim($text));
        $command = $partsArray[0] ?? '';
        $args = array_slice($partsArray, 1);

        if (!$this::IsCommand($command)) return;
     
        if ($command === '/checkService'){
            if (!empty($args)){
                foreach ($args as $arg){
                    $this->sendServiceIsActive($arg);
                }
                return;
            }
            else {
                $this->sendMessage('Por favor forneça argumentos para o comando: exemplo apache2, mysql, nginx...');
                return;
            }
        }

        if ($command === '/botStatus'){
            $this->sendBotStatus();
            return;
        }
        
        $this->sendDefaultResponse();
        
    }



}    



