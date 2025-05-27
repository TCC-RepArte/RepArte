<!DOCTYPE html>
<html lang="pt-br">

<?php
session_start();

// Verificar se o usuário já está logado
if(isset($_SESSION['user_id'])) {
    header("Location: telainicial.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>

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
</head>
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

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const mensagemDiv = document.getElementById('mensagem');
            
            fetch('../back-end/php/login.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.sucesso) {
                    mensagemDiv.style.color = 'green';
                    mensagemDiv.textContent = data.mensagem;
                    // Redirecionar após login bem-sucedido
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.href = '../web/html/telainicial.php';
                    }
                } else {
                    mensagemDiv.style.color = 'red';
                    mensagemDiv.textContent = data.mensagem || 'Erro ao processar login';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mensagemDiv.style.color = 'red';
                mensagemDiv.textContent = 'Erro ao processar login: ' + error.message;
            });
        });
    </script>
</body>
</html>