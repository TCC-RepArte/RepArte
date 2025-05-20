<!DOCTYPE html>
<html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../front-end/cadastro.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
  <title>Sign up</title>
</head>
<body>
  <header>
    <div class="interface">
      <div class="logo">
        <a href="#">
          <img src="../imagens/logo.png">
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
        <h1>Sign up</h1>
        <form method="post" action="../back-end/php/signup.php">
          <div class="textfield">
            <label for="nome">Nome Completo: </label>
            <input type="text" name="usuario" placeholder="Insira seu nome">
          </div>
 
          <div class="textfield">
            <label for="e-mail">Endereço de E-mail: </label>
            <input type="text" name="email" placeholder="Zamboni@gmail.com">
          </div>
 
          <!-- Campos lado a lado -->
          <div class="textfield-row">
            <div class="textfield">
              <label for="senha">Senha:</label>
              <input type="password" name="senha" placeholder="Senha">
            </div>
            <div class="textfield">
              <label for="confsenha">Confirme sua Senha:</label>
              <input type="password" name="confsenha" placeholder="Confirme a senha">
            </div>
          </div>
 
          <button class="btn-registrarse" type="submit">Registrar-se</button>
          <button class="btn-entrar">Entrar</button>
        </form>
 
        <div class="errologin">
          <?php if (!empty($erros)): ?>
          <div class="errolog">
            <?php foreach($erros as $erro): ?>
            <p><?= htmlspecialchars($erro); ?></p>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
 
  <script src="../back-end/js/signup.js"></script>
</body>
</html>