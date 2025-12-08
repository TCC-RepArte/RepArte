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
    <title>RepArte - Verificação de Código</title>
</head>
<?php
session_start();

// Verificar se veio do cadastro
if (!isset($_SESSION['dados_cadastro'])) {
    header("Location: cadastro.php");
    exit();
}

$dados = $_SESSION['dados_cadastro'];
$email = $dados['email'] ?? '';
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
                <h1>Verificação de Código</h1>
                <p style="color: #ccc; margin-bottom: 20px; text-align: center;">
                    Enviamos um código de 6 dígitos para:<br>
                    <strong style="color: #ff6600;"><?= htmlspecialchars($email) ?></strong><br>
                    Digite o código abaixo para continuar.
                </p>
                <form id="verificarForm">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                    <div class="textfield">
                        <label for="codigo"></label>
                        <input type="text" id="codigo" name="codigo" placeholder="000000" maxlength="6"
                            pattern="[0-9]{6}" required autocomplete="off"
                            style="text-align: center; font-size: 24px; letter-spacing: 5px;">
                    </div>

                    <!-- Área de mensagens -->
                    <div class="message-container">
                        <div id="mensagem"></div>
                    </div>

                    <button type="submit" class="btn-entrar">Verificar Código</button>
                    <button type="button" class="btn-registrarse" onclick="solicitarNovoCodigo()">Reenviar
                        Código</button>
                </form>

                <p style="color: #888; font-size: 14px; margin-top: 20px; text-align: center;">
                    Não recebeu o código? Verifique sua caixa de spam.<br>
                    <a href="cadastro.php" style="color: #ff6600;">← Voltar ao cadastro</a>
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

        // Solicitar código ao carregar a página
        window.addEventListener('load', function () {
            solicitarNovoCodigo(true);
        });

        // Função para solicitar novo código
        async function solicitarNovoCodigo(inicial = false) {
            const email = document.querySelector('input[name="email"]').value;
            const mensagemDiv = document.getElementById('mensagem');

            if (!inicial) {
                mensagemDiv.innerHTML = '<div class="mensagem-info">Enviando novo código...</div>';
            }

            try {
                const response = await fetch('php/enviar_codigo_cadastro.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'email=' + encodeURIComponent(email)
                });

                const data = await response.json();

                if (data.success) {
                    if (!inicial) {
                        mensagemDiv.innerHTML = '<div class="mensagem-sucesso">' + data.message + '</div>';
                    }
                } else {
                    mensagemDiv.innerHTML = '<div class="mensagem-erro">' + data.message + '</div>';
                }
            } catch (error) {
                console.error('Erro:', error);
                mensagemDiv.innerHTML = '<div class="mensagem-erro">Erro ao enviar código. Tente novamente.</div>';
            }
        }

        // Formulário de verificação
        document.getElementById('verificarForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const mensagemDiv = document.getElementById('mensagem');

            mensagemDiv.innerHTML = '<div class="mensagem-info">Verificando...</div>';

            try {
                const response = await fetch('php/verificar_codigo_cadastro.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    mensagemDiv.innerHTML = '<div class="mensagem-sucesso">' + data.message + '</div>';

                    setTimeout(() => {
                        window.location.href = 'php/signup.php';
                    }, 1000);
                } else {
                    mensagemDiv.innerHTML = '<div class="mensagem-erro">' + data.message + '</div>';

                    if (data.bloqueado || data.expirado) {
                        document.getElementById('codigo').value = '';
                    }
                }
            } catch (error) {
                console.error('Erro:', error);
                mensagemDiv.innerHTML = '<div class="mensagem-erro">Erro ao verificar código. Tente novamente.</div>';
            }
        });
    </script>
</body>

</html>