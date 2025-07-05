<?php

namespace app\core;

use Exception;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Net\SSH2;

class SSH {
    private SSH2 $ssh;
    private string $hostname;
    private string $user;
    private ?string $port; 
    private ?string $password;
    private ?string $private_key_path;
    private array $config;

    /**
     * Inicializando o SSH com base no arquivo de configurações
     */
    public function __construct(){
        $this->config = require __DIR__ . '/../config/config.php';
        $this->hostname = $this->config['ssh_config']['hostname'];
        $this->user = $this->config['ssh_config']['user'];
        $this->port = $this->config['ssh_config']['port'];
        $this->password = $this->config['ssh_config']['password'];
        $this->private_key_path = $this->config['ssh_config']['private_key_path'];
        $this->ssh = new SSH2($this->hostname, $this?->port);
    }

    /**
     * Método para realizar a autenticação SSH
     * @throws \Exception
     * @return void
     */
    public function auth(): void{
        if (!empty($this?->private_key_path)){
            $key = PublicKeyLoader::load(file_get_contents($this?->private_key_path));
            if (!$this->ssh->login($this->user, $key)){
                throw new Exception("Falha em fazer login {$this->hostname}@{$this->user}");
            } 
        }
        
        if (!empty($this?->password)){
            if (!$this->ssh->login($this->user, $this->password)){
                throw new Exception("Falha em fazer login {$this->hostname}@{$this->user}");
            } 
        }
    }

    /**
     * Executar um comando remotamente
     * @param string $command
     * @return string
     */
    public function exec(string $command): string {
        return trim($this->ssh->exec($command));
    }

    /**
     * Método para verificar se há uma conexão SSH, não quer dizer que a conexão foi concluida
     * @return bool
     */
    public function isConnected(): bool{
        return $this->ssh->isConnected();
    }

    /**
     * Verificar se existe autenticação, se retornar true, quer dizer que ocorreu tudo corretamente
     * @return bool
     */
    public function isAuthenticated(): bool {
        return $this->ssh->isAuthenticated();
    }

    /**
     * Desconectar do SSH 
     * @return void
     */
    public function disconnect(): void{
        try{
            $this->ssh->disconnect();
        }
        catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Caso não seja fechada a conexão no próprio monitor
     */
    public function __destruct(){
        if ($this->isConnected()){
            $this->disconnect();
        }
    }

}