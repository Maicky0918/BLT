<?php
declare(strict_types=1);
require_once __DIR__ . '/funcoes.php';

// Links antigos de recuperação não são mais válidos. O novo fluxo usa código de 6 dígitos.
flash('erro', 'Este link de recuperação não é mais válido. Solicite um novo código.');
redirecionar('recuperarSenha.php');
