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
                <div class="textfield">
                    <label for="usuario"></label>
                    <input type="text" name="usuario" placeholder="Nome de Usuário ou e-mail">
                </div>
                <div class="textfield">
                    <label for="senha"></label>
                    <input type="password" name="senha" placeholder="Senha">
                </div>
                <div class="checkbox">
                    <input type="checkbox">
                    <label for="checkbox">Lembre-se de mim</label>
                </div>
                <button class="btn-entrar">Entrar</button>
                <a href="cadastro.php"><button class="btn-registrarse">Registrar-se</button></a>
            </div>
        </div>

    </div>
</body>
</html>