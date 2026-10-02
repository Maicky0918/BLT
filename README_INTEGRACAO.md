# Blue Light Team — PHP integrado

Esta versão mantém o site original e completa o backend PHP.

## Funcionalidades
- Cadastro com validação e senha com `password_hash`.
- Login/logout com sessão.
- Proteção de páginas privadas.
- Perfil do usuário.
- Assinatura com três planos e registro no MySQL.
- Biblioteca protegida por assinatura.
- Conquista de primeira assinatura e primeiro jogo.
- Recuperação/redefinição de senha com código de 6 dígitos por e-mail usando o PHPMailer + SMTP já configurado.
- Proteção CSRF nos formulários POST.
- Jogo protegido por assinatura através de `jogar.php`.
- Busca de jogos no site.

## Instalação no XAMPP
1. Coloque a pasta `BLT-gamer` diretamente em `C:\xampp\htdocs\`.
2. Inicie **Apache** e **MySQL** no XAMPP.
3. Abra `http://localhost/phpmyadmin`.
4. Importe `blt.sql`.
5. Abra `http://localhost/BlueLight/Index.php`.
6. Crie uma conta e teste login, assinatura, biblioteca e jogo.

## Importante sobre pagamento
O pagamento é **simulado**, adequado para demonstração/TCC. Nenhum cartão real é processado ou armazenado. Para cobrança real, o arquivo `php/assinatura.php` deve ser substituído por uma integração com um gateway e confirmação via webhook.

## Recuperação de senha
Em ambiente local, depois de informar o e-mail cadastrado, o sistema mostra o link de recuperação na tela para facilitar o teste. Em produção, esse link deve ser enviado por e-mail e não exibido ao usuário.


## Recuperação de senha por código

O fluxo agora é:
1. O usuário clica em "Esqueci minha senha".
2. Informa o e-mail.
3. O Blue Light gera um código aleatório de 6 dígitos.
4. O código é enviado pelo PHPMailer usando o SMTP já configurado.
5. O código vale por 10 minutos e há limite de 5 tentativas.
6. Depois da confirmação, o usuário cria uma nova senha.
7. A nova senha é salva com `password_hash()` no campo `usuarios.senha` e passa a ser a senha usada no login.
8. O código é invalidado após o uso.

### Banco já existente
Se você já possui o banco `blt` e não quer importá-lo novamente, execute:
`recuperacao_senha_migracao.sql`

Esse arquivo recria apenas a tabela `tokens_senha`; ele não apaga a tabela `usuarios` nem os demais dados.

### PHPMailer
A recuperação usa a mesma função `enviarEmailHtml()` e a mesma configuração SMTP de `php/config_email.php`. Não é necessário criar outro SMTP.

Antes de testar, confirme que:
- `vendor/autoload.php` existe (rode `composer install` na pasta do projeto se necessário);
- o `.env` contém as configurações `BLT_SMTP_*`;
- `BLT_URL_BASE` aponta para a pasta correta do projeto.
