<?php 

namespace app\core;

use Telegram\Bot\Api;
use app\core\Monitor;

class Bot {
    private Api $telegram;
    private string $chatId; 
    private array $config = require __DIR__ . '/../config/config.php';

    public function __construct(){ 
        $this->telegram = new Api($this->config['telegram_bot_token']);
        $this->chatId = $this->config['chat_id'];
    }

    public function getConfig() :array{
        return $this->config;
    }

    public function sendMessage(string $message): void{
        $this->telegram->sendMessage([
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }

    public function receiveCommand(string $command): void{ 
        if ($command === '/botStatus'){
            $this->sendMessage('Bot funcionando, pronto para receber comandos');
        }
    }

    public function verifyChat(string $chatId): bool{

        if (is_string($chatId)){
            if (!in_array($chatId,  $this->config['chat_id']) ){
                return false;
            }
        }

        if ($this->config['chat_type'] !== 'group'){
            return false;
        }

        return true;
    }

}



?>