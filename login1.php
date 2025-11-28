<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login1.css">
    <link rel="stylesheet" href="css/mensagens.css">
    <title>RepArte - Login</title>
</head>
<?php

session_start();

// Limpeza automática
@include_once 'php/limpar_dados_temporarios.php';

// Verificar se o usuário já está logado
if (isset($_SESSION['id'])) {
    header("Location: telainicial.php");
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
                <h1>Login</h1>
                <form id="loginForm" method="post" action="php/login.php">
                    <div class="textfield" id="login-email">
                        <label for="usuario"></label>
                        <input type="text" id="usuario" name="usuario" placeholder="Nome de Usuário ou e-mail" required>
                    </div>
                    <div class="textfield">
                        <label for="senha"></label>
                        <input type="password" name="senha" placeholder="Senha" required>
                    </div>

                    <!-- Área de mensagens com espaço reservado -->
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

                    <div class="checkbox">
                        <input type="checkbox" name="lembrar">
                        <label for="checkbox">Lembre-se de mim</label>
                    </div>
                    <div class="esqueceu-senha">
                        <p><a href="emailesqueceu.php">Esqueceu a senha?</a></p>
                    </div>

                    <button type="submit" class="btn-entrar">Entrar</button>
                    <a href="cadastro.php"><button type="button" class="btn-registrarse">Registrar-se</button></a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>