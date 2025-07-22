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
    <link rel="stylesheet" href="../front-end/login1.css"> 
    <title>Reparte</title>
    <style>
        .mensagem-erro {
            background-color: #ffecec;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .mensagem-sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<?php

session_start();

// Verificar se o usuário já está logado
if(isset($_SESSION['id'])) {
    header("Location: telainicial.php");
    exit();
}

$erros = $_SESSION['erros'] ?? [];
$val = $_SESSION['val'] ?? [];

unset($_SESSION['erros'], $_SESSION['val']);

if (isset($_SESSION['erros']) || isset($_SESSION['val'])) {
    unset($_SESSION['erros']);
    unset($_SESSION['val']);
}

?>
<body>
    <header>
    <div class="interface">
           <div class="logo">
            <a href="#">
                <img src="../imagens/logo.png" alt="Logo do site">
            </a>
        </div>
    </div>
    </header>
    <div class="main-login">
        <div class="left-login">
            <div class="btns-logins">
            <h1>SEJA BEM-VINDO!<br></h1>
             <a href="#" class="btn-email">
                <button type="button">Entre com o e-mail</button>
            </a>
            <a href="#" class="btn-gmail">
                <button type="button">Entre com o gmail</button>
            </a>
              <a href="#" class="btn-numerodetel">
                <button type="button">Entre com o Nº de telefone</button>
            </a>
            </div>
        </div>

        <div class="right-login">
            <div class="card-login">
                <h1>Login</h1>
                
                <form id="loginForm" method="post" action="../back-end/php/login.php">
                    <div class="textfield">
                        <label for="usuario"></label>
                        <input type="text" name="usuario" placeholder="Nome de Usuário ou e-mail" required>
                    </div>
                    <div class="textfield">
                        <label for="senha"></label>
                        <input type="password" name="senha" placeholder="Senha" required>
                    </div>
                    <!-- Exibir mensagem de erro se existir -->
                    <?php if (!empty($erros)): ?>
                        <div class="mensagem-erro">
                            <?php foreach($erros as $erro): ?>
                                <p><?= htmlspecialchars($erro); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <!-- Exibir mensagem de validacao -->
                    <?php if (!empty($val)): ?>
                        <div class="mensagem-sucesso">
                            <?php foreach($val as $validacao): ?>
                                <p><?= htmlspecialchars($validacao); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="checkbox">
                        <input type="checkbox" name="lembrar">
                        <label for="checkbox">Lembre-se de mim</label>
                    </div>
                    <button type="submit" class="btn-entrar">Entrar</button>
                    <a href="cadastro.php"><button type="button" class="btn-registrarse">Registrar-se</button></a>
                </form>
                <div id="mensagem" style="margin-top: 10px; text-align: center;"></div>
            </div>
        </div>
    </div>
</body>
</html>