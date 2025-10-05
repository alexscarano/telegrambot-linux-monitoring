# Telegram Bot Linux Monitoring

Um bot em **PHP** para monitorar e gerenciar servidores Linux remotamente (home, produção ou VPS) através do **Telegram**.  
Ele conecta via **SSH**, executa comandos de verificação e envia alertas ou saídas diretamente para o seu chat no Telegram.

---

## Recursos
-  Execução de comandos remotos via SSH  
- Verificações periódicas de **CPU, memória, disco e serviços**  
- Comandos sob demanda enviados pelo Telegram  
- Código simples em PHP com gerenciamento de dependências via Composer  

---

## Requisitos
- **PHP 7.4+** com CLI  
- Extensão **ssh2** ou cliente SSH disponível no sistema  
- **Composer** para dependências  
- **Token do bot** (criado no [BotFather](https://core.telegram.org/bots#botfather))  
- Acesso SSH aos servidores que deseja monitorar  

---

## Instalação
1. Clone o repositório:
   ```bash
   git clone https://github.com/alexscarano/telegrambot-linux-monitoring.git
   cd telegrambot-linux-monitoring


-----

## ▶️ Como usar

### 1\. Executando o bot

  * Inicie o bot **manualmente**:

    ```bash
    php app/bot.php
    ```

  * Ou configure um serviço **systemd** / **supervisord** para manter o bot rodando em *background*.

### 2\. Modos de operação

  * **Polling**: o bot verifica periodicamente novas mensagens.

  * **Webhook**: configure um endpoint público e associe ao bot no Telegram:

    ```bash
    https://api.telegram.org/bot<SEU_TOKEN>/setWebhook?url=https://seuservidor.com/bot.php
    ```

### 3\. Exemplos de comandos no Telegram

  * **/status** $\rightarrow$ resumo rápido do sistema
  * **/disk** $\rightarrow$ uso de disco
  * **/top** $\rightarrow$ processos que mais consomem CPU
  * **/restart nginx** $\rightarrow$ reinicia um serviço (requer permissões adequadas)

-----

## 🔒 Segurança

  * Prefira **autenticação por chave SSH** em vez de senha
  * Restrinja **permissões** do usuário SSH
  * Armazene tokens e credenciais em **variáveis de ambiente**, nunca em código versionado
  * Execute o bot com um **usuário dedicado** e privilégios mínimos

-----

## 🛠️ Contribuindo

1.  Faça um **fork**
2.  Crie uma **branch** (`git checkout -b feature/nova-feature`)
3.  **Commit** suas alterações (`git commit -m 'Adiciona nova feature'`)
4.  **Push** para o repositório (`git push origin feature/nova-feature`)
5.  Abra um **Pull Request**

-----
