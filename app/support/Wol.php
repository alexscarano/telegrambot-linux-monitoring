<?php

namespace app\support;

use app\core\SSH;

class Wol
{
    /**
     * Envia um pacote WOL para o MAC informado
     * @param string $ip Endereço de broadcast (ex: 192.168.1.255)
     * @param string $mac Endereço MAC do dispositivo
     * @param int $port Porta UDP (geralmente 7 ou 9)
     * @return bool
     */
    public static function wake(string $ip, string $mac, int $port = 7): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new \Exception("IP inválido.");
        }

        if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac)) {
            throw new \Exception("MAC inválido.");
        }

        $macBin = '';
        foreach (explode(':', str_replace('-', ':', $mac)) as $hex) {
            $macBin .= chr(hexdec($hex));
        }

        $packet = str_repeat(chr(0xFF), 6) . str_repeat($macBin, 16);

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            throw new \Exception('Erro ao criar socket: ' . socket_strerror(socket_last_error()));
        }

        // Permite envio para broadcast
        socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, true);

        $sent = socket_sendto($socket, $packet, strlen($packet), 0, $ip, $port);

        socket_close($socket);

        return $sent === strlen($packet);
    }

    public static function scanNetwork(string $interface = 'vmbr0'): array {

        $ssh = new SSH();
        $ssh->auth();

        $output = $ssh->exec("sudo /usr/sbin/arp-scan --interface={$interface} --localnet");
 
        if (empty($output)) {
            return [];
        }

        $devices = [];
        $devicesCount = 1;
        $lines = explode("\n", $output);
 
        foreach ($lines as $line) {
        // ignora cabeçalhos ou linhas em branco
        if (preg_match('/^([\d\.]+)\s+([0-9A-Fa-f:]{17})\s+(.+)$/', $line, $matches)) {
            $devices[] = [
                'ip' => $matches[1],
                'mac' => $matches[2],
                'vendor' => trim($matches[3]),
                'device_count' => $devicesCount
                ];
            ++$devicesCount;
            }
        }

        return $devices;
}

 
}
