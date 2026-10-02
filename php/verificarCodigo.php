<?php
declare(strict_types=1);

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

$recuperacaoId = (int)($_SESSION['recuperacao_id'] ?? 0);
$erro = '';
$sucesso = null;

if ($recuperacaoId <= 0) {
    flash('erro', 'Solicite um novo código de recuperação.');
    redirecionar('recuperarSenha.php');
}

$stmt = $conn->prepare(
    'SELECT id, usuario_id, expira_em, usado, tentativas
     FROM tokens_senha WHERE id = ? LIMIT 1'
);
$stmt->bind_param('i', $recuperacaoId);
$stmt->execute();
$recuperacao = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$recuperacao || (int)$recuperacao['usado'] === 1 || strtotime((string)$recuperacao['expira_em']) < time()) {
    unset($_SESSION['recuperacao_id'], $_SESSION['recuperacao_email']);
    $erro = 'Este código expirou ou já foi utilizado. Solicite um novo código.';
} elseif ((int)$recuperacao['tentativas'] >= 5) {
    $erro = 'Limite de tentativas atingido. Solicite um novo código.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $erro === '') {
    exigirCsrf($_POST['csrf_token'] ?? null, 'verificarCodigo.php');

    $codigo = trim((string)($_POST['codigo'] ?? ''));

    if (!preg_match('/^\d{6}$/', $codigo)) {
        $erro = 'Digite o código de 6 dígitos recebido por e-mail.';
    } else {
        $stmt = $conn->prepare('SELECT codigo_hash, tentativas, expira_em, usado FROM tokens_senha WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $recuperacaoId);
        $stmt->execute();
        $atual = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $valido = $atual
            && (int)$atual['usado'] === 0
            && strtotime((string)$atual['expira_em']) >= time()
            && (int)$atual['tentativas'] < 5
            && password_verify($codigo, (string)$atual['codigo_hash']);

        if (!$valido) {
            $stmt = $conn->prepare('UPDATE tokens_senha SET tentativas = tentativas + 1 WHERE id = ? AND usado = 0');
            $stmt->bind_param('i', $recuperacaoId);
            $stmt->execute();
            $stmt->close();

            $erro = 'Código incorreto ou expirado.';
        } else {
            $_SESSION['recuperacao_verificada_id'] = $recuperacaoId;
            unset($_SESSION['recuperacao_id']);
            redirecionar('novaSenha.php');
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
<title>Verificar código | Blue Light</title>
</head>
<body class="bl-auth-body">
<main class="container">
  <div class="bl-auth-logo">
    <img src="../img/Logo Blue Light com contorno.png" alt="Logo Blue Light">
    <span>BLUE <b>LIGHT</b></span>
  </div>

  <h1>Verifique seu e-mail</h1>
  <p class="bl-auth-sub">Digite o código de 6 dígitos que enviamos para seu e-mail.</p>

  <?php if ($erro): ?>
    <div class="bl-flash erro msg" role="alert"><?= e($erro) ?></div>
  <?php endif; ?>

  <form method="post" data-bl-loading novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

    <div class="bl-field">
      <i class="fa-solid fa-shield-halved bl-icon"></i>
      <input
        type="text"
        name="codigo"
        inputmode="numeric"
        pattern="[0-9]{6}"
        maxlength="6"
        minlength="6"
        autocomplete="one-time-code"
        placeholder="Código de 6 dígitos"
        required
        aria-label="Código de verificação"
      >
    </div>

    <button type="submit" class="bl-btn bl-btn-primary bl-auth-submit">
      <span class="bl-spinner"></span><span class="bl-btn-label">Verificar código</span>
    </button>

    <a href="recuperarSenha.php" class="cadastro">Enviar outro código</a>
    <a href="../entrar.php" class="cadastro">Voltar ao login</a>
  </form>
</main>
<script src="../Js/app.js"></script>
</body>
</html>
