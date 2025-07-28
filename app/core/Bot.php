<?php 

namespace app\core;

use Telegram\Bot\Api;


/*
 CLASSE PARA AS FUNÇÕES BÁSICAS DE BOT 
  A CLASSE DE OPERAÇÕES DE COMANDOS É A CommandHandler, 
 */
class Bot {
        protected Api $telegram;
        protected readonly string $chatId; 
        protected readonly array $config; 

        protected function __construct(){ 
                $this->config = require __DIR__ . '/../config/config.php';
                $this->telegram = new Api($this->config['telegram_bot_token']);
                $this->chatId = $this->config['chat_id'];
        }
        protected function getConfig() :array{
                return $this->config;
        }

        /**
         * Função básica do bot de enviar mensagens
         * @param string $message
         * @return void
         */
        protected function sendMessage(string $message): void{
                $this->telegram->sendMessage([
                        'chat_id' => $this->chatId,
                        'text' => $message,
                        'parse_mode' => 'Markdown'
                ]);
        }
  
        /* Troque pelo tipo de chat que vem no JSON, o meu será group,
        Porém também pode ser private, supergroup e etc... */
        /**
         * Verifica o chat no qual a requisição vem.
         * @param string $chatId
         * @param string $chatType
         * @return bool
         */
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