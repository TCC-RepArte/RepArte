<!DOCTYPE html>
<html lang="pt-br">
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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cadastro.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/mensagens.css">
    <title>RepArte - Verificação de Código</title>
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

        .loading {
            display: none;
            text-align: center;
            margin: 10px 0;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
                        📧 Verifique sua caixa de entrada e spam
                    </p>
                </div>

                <!-- Formulário de verificação -->
                <form id="verificarForm">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                    <div class="textfield">
                        <label for="codigo">Código de Verificação:</label>
                        <input type="text" id="codigo" name="codigo" class="codigo-input" placeholder="000000"
                            maxlength="6" pattern="[0-9]{6}" required autocomplete="off">
                    </div>

                    <!-- Área de mensagens com espaço reservado -->
                    <div class="message-container">
                        <div id="mensagem"></div>

                        <!-- Loading spinner -->
                        <div class="loading" id="loading">
                            <div class="spinner"></div>
                            <p>Verificando...</p>
                        </div>
                    </div>

                    <button type="submit" class="btn-registrarse">Verificar Código</button>
                    <button type="button" class="btn-entrar" onclick="solicitarNovoCodigo()">Reenviar Código</button>
                </form>

                <p style="text-align: center; margin-top: 15px;">
                    <a href="cadastro.php" style="color: #667eea; text-decoration: none;">← Voltar ao cadastro</a>
                </p>
            </div>
        </div>
    </div>

    <script>
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
            const loadingDiv = document.getElementById('loading');

            // Limpar mensagens e mostrar loading
            mensagemDiv.innerHTML = '';
            loadingDiv.style.display = 'block';

            try {
                const response = await fetch('php/verificar_codigo_cadastro.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                loadingDiv.style.display = 'none';

                if (data.success) {
                    mensagemDiv.innerHTML = '<div class="mensagem-sucesso">' + data.message + '</div>';

                    // Aguardar 1 segundo e redirecionar para concluir cadastro
                    setTimeout(() => {
                        window.location.href = 'php/signup.php';
                    }, 1000);
                } else {
                    mensagemDiv.innerHTML = '<div class="mensagem-erro">' + data.message + '</div>';

                    // Limpar campo se bloqueado ou expirado
                    if (data.bloqueado || data.expirado) {
                        document.getElementById('codigo').value = '';
                    }
                }
            } catch (error) {
                loadingDiv.style.display = 'none';
                console.error('Erro:', error);
                mensagemDiv.innerHTML = '<div class="mensagem-erro">Erro ao verificar código. Tente novamente.</div>';
            }
        });

        // Formatar input para aceitar apenas números
        document.getElementById('codigo').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>