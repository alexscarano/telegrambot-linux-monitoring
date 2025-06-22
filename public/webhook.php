<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\core\Bot;
use Telegram\Bot\Api;

// Configuração
$config = require __DIR__ . '../app/config/config.php';
$telegram = new Api($config['telegram_bot_token']);

// Verifica se é uma requisição válida
$update = $telegram->getWebhookUpdate();
$message = $update->getMessage();

if ($message) {
    $bot = new Bot();
    $bot->receiveCommand($message->getText());
}
