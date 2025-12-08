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
    <title>RepArte - Verificar Código</title>
</head>
<?php
session_start();

// Verificar se veio da recuperação de senha
if (!isset($_SESSION['email_recuperacao'])) {
    header("Location: emailesqueceu.php");
    exit();
}

$email = $_SESSION['email_recuperacao'];
$mensagem = $_SESSION['val_recuperacao'] ?? [];
unset($_SESSION['val_recuperacao']);
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
                <h1>Recuperação de Senha</h1>
                <p style="color: #ccc; margin-bottom: 20px; text-align: center;">
                    Enviamos um código de 6 dígitos para:<br>
                    <strong style="color: #ff6600;"><?= htmlspecialchars($email) ?></strong><br>
                    Digite o código abaixo para continuar.
                </p>
                <form id="verificarForm" method="post" action="php/verificar_codigo_senha.php">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                    <div class="textfield">
                        <label for="codigo"></label>
                        <input type="text" id="codigo" name="codigo" placeholder="000000" maxlength="6"
                            pattern="[0-9]{6}" required autocomplete="off"
                            style="text-align: center; font-size: 24px; letter-spacing: 5px;">
                    </div>

                    <!-- Área de mensagens -->
                    <div class="message-container">
                        <?php if (!empty($mensagem)): ?>
                            <div class="mensagem-sucesso">
                                <?php foreach ($mensagem as $msg): ?>
                                    <p><?= htmlspecialchars($msg); ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div id="mensagem-js"></div>
                    </div>

                    <button type="submit" class="btn-entrar">Verificar Código</button>
                    <button type="button" class="btn-registrarse" onclick="solicitarNovoCodigo()">Reenviar
                        Código</button>
                </form>

                <p style="color: #888; font-size: 14px; margin-top: 20px; text-align: center;">
                    Código válido por <strong>1 hora</strong> | <strong>5 tentativas</strong><br>
                    <a href="login1.php" style="color: #ff6600;">← Voltar ao login</a>
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

        // Função para reenviar código
        async function solicitarNovoCodigo() {
            const email = document.querySelector('input[name="email"]').value;
            const mensagemDiv = document.getElementById('mensagem-js');

            mensagemDiv.innerHTML = '<div class="mensagem-info">Enviando novo código...</div>';

            try {
                const response = await fetch('php/enviar_link_recuperacao.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'usuario_email=' + encodeURIComponent(email)
                });

                mensagemDiv.innerHTML = '<div class="mensagem-sucesso">Novo código enviado para seu email!</div>';

                setTimeout(() => {
                    mensagemDiv.innerHTML = '';
                }, 3000);
            } catch (error) {
                console.error('Erro:', error);
                mensagemDiv.innerHTML = '<div class="mensagem-erro">Erro ao enviar código. Tente novamente.</div>';
            }
        }
    </script>
</body>

</html>