<?php

require __DIR__ . '/../vendor/autoload.php';

use app\core\Bot;

// Ler entrada do Telegram
$input = file_get_contents("php://input");
$data = json_decode($input, true);
file_put_contents(__DIR__ . '/../log.txt', print_r($data, true));

if (isset($data['message']['text'])) {

    $message = $data['message'];
    $text = $message['text'] ?? '';
    $chatId = $message['chat']['id'] ?? null;

    if ($chatId && $message) {
        // Criar o bot e responder
        $bot = new Bot();

        // Se a requisição não vier do chat,invalida a requisição
        if (!$bot->verifyChat($chatId)){            
            $bot->sendMessage("Chat não autorizado."); // debug
            return;
        }
        
        $bot->receiveCommand($text);
    }

}