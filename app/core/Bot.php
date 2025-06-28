<?php 

namespace app\core;

use Telegram\Bot\Api;
class Bot {
        protected Api $telegram;
        protected string $chatId; 
        protected array $config; 

        public function __construct(){ 
                $this->config = require __DIR__ . '/../config/config.php';
                $this->telegram = new Api($this->config['telegram_bot_token']);
                $this->chatId = $this->config['chat_id'];
        }
        protected function getConfig() :array{
                return $this->config;
        }
        public function sendMessage(string $message): void{
                $this->telegram->sendMessage([
                        'chat_id' => $this->chatId,
                        'text' => $message,
                        'parse_mode' => 'Markdown'
                ]);
        }
        public function sendBotStatus(string $command): void{ 
                if ($command === '/botStatus'){
                        $this->sendMessage('Bot funcionando, pronto para receber comandos');
                }
        }

        /* Troque pelo tipo de chat que vem no JSON, o meu será group,
        Porém também pode ser private, supergroup e etc... */
        public function verifyChat(string $chatId, string $chatType = 'group'): bool{ 
                
                if ($chatId !== $this->config['chat_id']) {
                        return false;
                }

                if ($this->config['chat_type'] !== $chatType){
                        return false;
                }

                return true;
        }

}