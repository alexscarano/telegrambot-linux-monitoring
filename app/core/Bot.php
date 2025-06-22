<?php 

namespace app\core;

use Telegram\Bot\Api;
use app\core\Monitor;

class Bot {

    private Api $telegram;
    private string $chatId;

    public function __construct(){
        $config = require __DIR__ . '/../config/config.php';
        $this->telegram = new Api($config['telegram_bot_token']);
        $this->chatId = $config['chat_id'];
    }

    public function sendMessage(string $message): void{
        $this->telegram->sendMessage([
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }

    public function receiveCommand(string $command){
        
        if ($command === '/botStatus'){
            $this->sendMessage('Bot funcionando, pronto para receber comandos');
        }
    }

}



?>