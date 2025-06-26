<?php


require __DIR__ . '/../vendor/autoload.php';

use app\core\Bot;


file_put_contents(__DIR__ . '/../log.txt', date('c') . " - Webhook ativado\n", FILE_APPEND);

// Ler entrada do Telegram
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Verificar se    mensagem de texto
if (isset($data['message']['text'])) {
    $mensagem = $data['message']['text'];

    // Criar o bot e responder
    $bot = new Bot();
    $bot->receiveCommand($mensagem);
}