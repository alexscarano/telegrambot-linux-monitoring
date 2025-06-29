<?php

require __DIR__ . '/../vendor/autoload.php';

use app\core\Bot;
use app\core\CommandHandler;

$input = file_get_contents("php://input");
$data = json_decode($input, true);
file_put_contents(__DIR__ . '/../log.txt', print_r($data, true));

if (isset($data['message']['text'])) {

        $message = $data['message'];
        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'] ?? null;

        if ($chatId && $message) {
                // Criar o bot e responder
                $bot = new CommandHandler();

                if (!$bot->verifyChat($chatId)){            
                        return;
                }

                $bot->handleTextCommand($text);

                // $bot->sendBotStatus($text); 
        }
}