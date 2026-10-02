<?php
declare(strict_types=1);

require_once __DIR__ . '/php/funcoes.php';

if (usuarioLogado()) {
    redirecionar('php/perfil.php');
}

$flash = obterFlash();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<link
    rel="stylesheet"
    href="Css/design-system.css"
>

<link
    rel="stylesheet"
    href="Css/login.css"
>

<link
    rel="stylesheet"
    href="https://cloudflare.com"
>

<title>Cadastro | Blue Light</title>


<style>

/* =========================================================
   BOTÃO VOLTAR AO INÍCIO
   ========================================================= */

.bl-btn-home {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    width: 100%;

    height: 52px;

    margin-top: 14px;

    border: 1px solid #33415f;

    border-radius: 15px;

    background: transparent;

    color: #91a3c5;

    text-decoration: none;

    font-size: 15px;

    font-weight: 600;

    transition: all 0.2s ease;

}

.bl-btn-home:hover {

    color: #ffffff;

    border-color: #3d8fff;

    background: rgba(61, 143, 255, 0.08);

    transform: translateY(-1px);

}

.bl-btn-home i {

    font-size: 14px;

}


/* =========================================================
   BOTÃO ENTRAR (mesmo visual do Criar conta)
   ========================================================= */

.btn-entrar {

    display: flex;

    align-items: center;

    justify-content: center;

    margin-top: 16px;

    text-decoration: none;

}

</style>

</head>


<body class="bl-auth-body">


<main class="container">


    <!-- =====================================================
         LOGO
         ===================================================== -->

    <div class="bl-auth-logo">

        <img
            src="img/Logo Blue Light com contorno.png"
            alt="Logo Blue Light"
        >

        <span>
            BLUE<b>LIGHT</b>
        </span>

    </div>


    <!-- =====================================================
         TÍTULO
         ===================================================== -->

    <h1>
        Criar conta
    </h1>


    <p class="bl-auth-sub">
        Comece a jogar em poucos segundos.
    </p>


    <!-- =====================================================
         MENSAGEM
         ===================================================== -->

    <?php if ($flash): ?>

        <div
            class="bl-flash <?= e($flash['tipo']) ?> msg"
            role="status"
        >
            <?= e($flash['mensagem']) ?>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         FORMULÁRIO
         ===================================================== -->

    <form
        action="php/cadastro.php"
        method="POST"
        data-bl-loading
        novalidate
    >


        <!-- CSRF -->

        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrfToken()) ?>"
        >


        <!-- =================================================
             NOME
             ================================================= -->

        <div class="bl-field">

            <i class="fa-regular fa-user bl-icon"></i>

            <input
                placeholder="Seu nome"
                type="text"
                name="nome"
                minlength="2"
                maxlength="120"
                required
                aria-label="Seu nome"
            >

        </div>


        <!-- =================================================
             E-MAIL
             ================================================= -->

        <div class="bl-field">

            <i class="fa-regular fa-envelope bl-icon"></i>

            <input
                placeholder="Informe seu e-mail"
                type="email"
                name="email"
                required
                aria-label="E-mail"
            >

        </div>


        <!-- =================================================
             SENHA
             ================================================= -->

        <div class="bl-field">

            <i class="fa-solid fa-lock bl-icon"></i>

            <input
                placeholder="Crie sua senha (mín. 8 caracteres)"
                type="password"
                name="senha"
                minlength="8"
                required
                aria-label="Senha"
            >

            <button
                type="button"
                class="bl-toggle-pass"
                aria-label="Mostrar senha"
            >

                <i class="fa-regular fa-eye"></i>

            </button>

        </div>


        <!-- =================================================
             CONFIRMAR SENHA
             ================================================= -->

        <div class="bl-field">

            <i class="fa-solid fa-lock bl-icon"></i>

            <input
                placeholder="Confirme sua senha"
                type="password"
                name="confirmar_senha"
                minlength="8"
                required
                aria-label="Confirmar senha"
            >

            <button
                type="button"
                class="bl-toggle-pass"
                aria-label="Mostrar senha"
            >

                <i class="fa-regular fa-eye"></i>

            </button>

        </div>


        <!-- =================================================
             TERMOS
             ================================================= -->

        <label class="termos">

            <input
                type="checkbox"
                name="termos"
                required
            >

            <span>

                Li e aceito os

                <a
                    href="termos.html"
                    target="_blank"
                >
                    Termos de Uso
                </a>.

            </span>

        </label>


        <!-- =================================================
             CRIAR CONTA
             ================================================= -->

        <button
            type="submit"
            class="bl-btn bl-btn-primary bl-auth-submit"
        >

            <span class="bl-spinner"></span>

            <span class="bl-btn-label">
                Criar conta
            </span>

        </button>


        <!-- =================================================
             ENTRAR (igual ao Criar conta, separado)
             ================================================= -->

        <a
            href="entrar.php"
            class="bl-btn bl-btn-primary bl-auth-submit btn-entrar"
        >
            <span class="bl-btn-label">
                Entrar
            </span>
        </a>


        <!-- =================================================
             JÁ TENHO UMA CONTA
             ================================================= -->

        <a
            href="entrar.php"
            class="cadastro"
        >
            Já tenho uma conta
        </a>


        <!-- =================================================
             VOLTAR AO INÍCIO
             ================================================= -->

        <a
            href="index.php"
            class="bl-btn-home"
        >

            <i class="fa-solid fa-house"></i>

            Voltar ao início

        </a>


    </form>


</main>


<script src="Js/app.js"></script>

</body>

</html>