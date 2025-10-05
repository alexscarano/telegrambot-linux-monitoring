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
