<?php
declare(strict_types=1);

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$recuperacaoId = (int)($_SESSION['recuperacao_verificada_id'] ?? 0);
$erro = '';

if ($recuperacaoId <= 0) {
    flash('erro', 'Verifique o código de recuperação primeiro.');
    redirecionar('recuperarSenha.php');
}

$stmt = $conn->prepare(
    'SELECT id, usuario_id, expira_em, usado FROM tokens_senha WHERE id = ? LIMIT 1'
);
$stmt->bind_param('i', $recuperacaoId);
$stmt->execute();
$recuperacao = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$recuperacao || (int)$recuperacao['usado'] === 1 || strtotime((string)$recuperacao['expira_em']) < time()) {
    unset($_SESSION['recuperacao_verificada_id']);
    flash('erro', 'A recuperação expirou. Solicite um novo código.');
    redirecionar('recuperarSenha.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf($_POST['csrf_token'] ?? null, 'novaSenha.php');

    $senha = (string)($_POST['senha'] ?? '');
    $confirmar = (string)($_POST['confirmar_senha'] ?? '');

    if (strlen($senha) < 8) {
        $erro = 'A senha precisa ter pelo menos 8 caracteres.';
    } elseif ($senha !== $confirmar) {
        $erro = 'As senhas não coincidem.';
    } else {
        $id = (int)$recuperacao['usuario_id'];
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
            $stmt->bind_param('si', $hash, $id);
            if (!$stmt->execute()) {
                throw new RuntimeException('Falha ao atualizar senha.');
            }
            $stmt->close();

            // Invalida o código utilizado e quaisquer outros códigos pendentes do usuário.
            $stmt = $conn->prepare('UPDATE tokens_senha SET usado = 1 WHERE usuario_id = ?');
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                throw new RuntimeException('Falha ao invalidar códigos.');
            }
            $stmt->close();

            $conn->commit();

            unset(
                $_SESSION['recuperacao_verificada_id'],
                $_SESSION['recuperacao_id'],
                $_SESSION['recuperacao_email']
            );

            flash('sucesso', 'Senha alterada com sucesso! Agora você pode entrar usando a nova senha.');
            redirecionar('../entrar.php');
        } catch (Throwable $e) {
            $conn->rollback();
            $erro = 'Não foi possível alterar a senha. Tente novamente.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../Css/design-system.css">
<link rel="stylesheet" href="../Css/login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<title>Nova senha | Blue Light</title>
</head>
<body class="bl-auth-body">
<main class="container">
  <div class="bl-auth-logo">
    <img src="../img/Logo Blue Light com contorno.png" alt="Logo Blue Light">
    <span>BLUE <b>LIGHT</b></span>
  </div>

  <h1>Crie sua nova senha</h1>
  <p class="bl-auth-sub">A senha que você definir aqui será usada no próximo login.</p>

  <?php if ($erro): ?>
    <div class="bl-flash erro msg" role="alert"><?= e($erro) ?></div>
  <?php endif; ?>

  <form method="post" data-bl-loading novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

    <div class="bl-field">
      <i class="fa-solid fa-lock bl-icon"></i>
      <input type="password" name="senha" placeholder="Nova senha (mín. 8 caracteres)" minlength="8" required autocomplete="new-password" aria-label="Nova senha">
      <button type="button" class="bl-toggle-pass" aria-label="Mostrar senha"><i class="fa-regular fa-eye"></i></button>
    </div>

    <div class="bl-field">
      <i class="fa-solid fa-lock bl-icon"></i>
      <input type="password" name="confirmar_senha" placeholder="Confirme a nova senha" minlength="8" required autocomplete="new-password" aria-label="Confirmar nova senha">
      <button type="button" class="bl-toggle-pass" aria-label="Mostrar senha"><i class="fa-regular fa-eye"></i></button>
    </div>

    <button type="submit" class="bl-btn bl-btn-primary bl-auth-submit">
      <span class="bl-spinner"></span><span class="bl-btn-label">Salvar nova senha</span>
    </button>

    <a href="../entrar.php" class="cadastro">Voltar ao login</a>
  </form>
</main>
<script src="../Js/app.js"></script>
</body>
</html>
