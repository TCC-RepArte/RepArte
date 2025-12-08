<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login1.css">
    <link rel="stylesheet" href="css/mensagens.css">
    <title>RepArte - Verificação 2FA</title>
</head>
<?php

session_start();

// Verificar se tem dados pendentes de 2FA
if (!isset($_SESSION['2fa_pending_user'])) {
    header("Location: login1.php");
    exit();
}

$erros = $_SESSION['erros'] ?? [];
$val = $_SESSION['val'] ?? [];

unset($_SESSION['erros'], $_SESSION['val']);

?>

<body>
    <header>
        <div class="interface">
            <div class="logo">
                <a href="#">
                    <img src="images/logo.png" alt="Logo do site">
                </a>
            </div>
        </div>
    </header>
    <div class="main-login">
        <div class="right-login">
            <div class="card-login">
                <h1>Verificação 2FA</h1>
                <p style="color: #ccc; margin-bottom: 20px; text-align: center;">
                    Enviamos um código de 6 dígitos para seu email.<br>
                    Digite o código abaixo para continuar.
                </p>
                <form id="2faForm" method="post" action="php/verificar_codigo_2fa.php">
                    <div class="textfield">
                        <label for="codigo"></label>
                        <input type="text" id="codigo" name="codigo" placeholder="000000" maxlength="6"
                            pattern="[0-9]{6}" required
                            style="text-align: center; font-size: 24px; letter-spacing: 5px;">
                    </div>

                    <!-- Área de mensagens -->
                    <div class="message-container">
                        <?php if (!empty($erros)): ?>
                            <div class="mensagem-erro">
                                <?php foreach ($erros as $erro): ?>
                                    <p><?= htmlspecialchars($erro); ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($val)): ?>
                            <div class="mensagem-sucesso">
                                <?php foreach ($val as $validacao): ?>
                                    <p><?= htmlspecialchars($validacao); ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div id="mensagem"></div>
                    </div>

                    <button type="submit" class="btn-entrar">Verificar Código</button>
                    <a href="php/cancelar_2fa.php"><button type="button" class="btn-registrarse">Cancelar</button></a>
                </form>

                <p style="color: #888; font-size: 14px; margin-top: 20px; text-align: center;">
                    Não recebeu o código? <a href="php/reenviar_codigo_2fa.php" style="color: #ff6600;">Reenviar</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus no campo de código
        document.getElementById('codigo').focus();

        // Permite apenas números
        document.getElementById('codigo').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>