<?php

require __DIR__ . '/../vendor/autoload.php';

use app\core\Bot;

// Ler entrada do Telegram
$input = file_get_contents("php://input");
$data = json_decode($input, true);
$config = require __DIR__ . '/../config/config.php';


if (isset($data['message']['text'])) {

    $message = $data['message']['text'] ?? '';

    $chatId = $message['chat']['id'] ?? null;

    if (isset($chatId) && isset($text)) {
        // Criar o bot e responder
        $bot = new Bot();

        // Se a requisição não vier do chat,invalida a requisição
        if (!$bot->verifyChat($chatId)){            
            return;
        }
        
        $bot->receiveCommand($message);
    }

}