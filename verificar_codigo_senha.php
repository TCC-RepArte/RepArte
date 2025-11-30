<!DOCTYPE html>
<html lang="pt-br">
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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cadastro.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/mensagens.css">
    <title>RepArte - Verificar Código</title>
    <style>
        .codigo-container {
            background: #f0f0f0;
            border: 2px dashed #667eea;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }

        .codigo-display {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #667eea;
            margin: 10px 0;
        }

        .codigo-input {
            font-size: 24px;
            letter-spacing: 8px;
            text-align: center;
            padding: 15px;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        .info-box {
            background: #e8f0fe;
            border-left: 4px solid #1a73e8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <header>
        <div class="interface">
            <div class="logo">
                <a href="#">
                    <img src="images/logo.png" alt="Logo">
                </a>
            </div>
        </div>
    </header>

    <div class="main-login">
        <div class="right-login">
            <div class="card-login">
                <div class="info-box">
                    <p style="margin: 0;"><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">
                        📧 Código válido por 1 hora | 5 tentativas
                    </p>
                </div>

                <!-- Formulário -->
                <form id="verificarForm" method="post" action="php/verificar_codigo_senha.php">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                    <div class="textfield">
                        <label for="codigo">Código:</label>
                        <input type="text" id="codigo" name="codigo" class="codigo-input" placeholder="000000"
                            maxlength="6" pattern="[0-9]{6}" required autocomplete="off">
                    </div>

                    <!-- Área de mensagens com espaço reservado -->
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

                    <button type="submit" class="btn-registrarse">Verificar Código</button>
                    <button type="button" class="btn-entrar" onclick="solicitarNovoCodigo()">Reenviar Código</button>
                </form>

                <p style="text-align: center; margin-top: 15px;">
                    <a href="login1.php" style="color: #667eea; text-decoration: none;">← Voltar ao login</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Formatar input para aceitar apenas números
        document.getElementById('codigo').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Função para reenviar código (chamada pelo botão)
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
                
                // Limpar mensagem após 3 segundos
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