<!DOCTYPE html>
<html lang="pt-br">

<?php
session_start();

// Verificar se o usuário já está logado
if (isset($_SESSION['id'])) {
  header("Location: telainicial.php");
  exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$erros = $_SESSION['erros'] ?? [];
// Limpar a sessão de erros após exibi-los
if (isset($_SESSION['erros'])) {
  unset($_SESSION['erros']);
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/cadastro.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/mensagens.css">
  <title>RepArte - Cadastro</title>
</head>

<body>
  <header>
    <div class="interface">
      <div class="logo">
        <a href="#">
          <img src="images/logo.png">
        </a>
      </div>
    </div>
  </header>

  <div class="main-login">
    <div class="right-login">
      <div class="card-login">
        <h1>Sign up</h1>

        <form id="signupForm" method="post" action="php/signup.php">
          <div class="textfield">
            <label for="nome">Nome de Usuario: </label>
            <input type="text" name="usuario" placeholder="" required>
          </div>

          <div class="textfield">
            <label for="e-mail">Endereço de E-mail: </label>
            <input type="email" name="email" placeholder="1234@gmail.com" required>
          </div>

          <div class="textfield-row">
            <div class="textfield">
              <label for="senha">Senha:</label>
              <input type="password" name="senha" placeholder="********" required>
            </div>
            <div class="textfield">
              <label for="confsenha">Confirme sua Senha:</label>
              <input type="password" name="confsenha" placeholder="********" required>
            </div>
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

            <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
              <div class="mensagem-sucesso">
                <?= htmlspecialchars($_SESSION['mensagem_sucesso']); ?>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
              </div>
            <?php endif; ?>

            <div id="mensagem-js"></div>
          </div>

          <button class="btn-registrarse" type="submit">Registrar-se</button>
          <a href="login1.php"><button type="button" class="btn-entrar">Entrar</button></a>
        </form>
      </div>
    </div>
  </div>

  <script src="js/criarID.js"></script>
  <script>
    // Adicionar tratamento de erros no lado do cliente
    const formSignup = document.getElementById('signupForm');
    const originalSubmit = formSignup.onsubmit; // Guardar o evento de submit original

    formSignup.addEventListener('submit', function (e) {
      e.preventDefault(); // Sempre prevenir o envio padrão

      const senha = document.querySelector('input[name="senha"]').value;
      const confSenha = document.querySelector('input[name="confsenha"]').value;
      const email = document.querySelector('input[name="email"]').value;
      const usuario = document.querySelector('input[name="usuario"]').value;
      const mensagemDiv = document.getElementById('mensagem-js');
      let erroEncontrado = false;

      // Limpar mensagens anteriores
      mensagemDiv.innerHTML = '';

      // Validar nome de usuário (apenas letras e underscore, sem espaços ou números)
      const regexUsuario = /^[a-zA-ZÀ-ÿ_]+$/;
      if (!regexUsuario.test(usuario)) {
        mensagemDiv.innerHTML = '<div class="mensagem-erro">O nome de usuário deve conter apenas letras e underscore (sem espaços ou números)</div>';
        erroEncontrado = true;
      }

      // Verificar se as senhas coincidem
      else if (senha !== confSenha) {
        mensagemDiv.innerHTML = '<div class="mensagem-erro">As senhas não coincidem</div>';
        erroEncontrado = true;
      }

      // Verificar complexidade da senha
      else if (senha.length < 6) {
        mensagemDiv.innerHTML = '<div class="mensagem-erro">A senha deve ter pelo menos 6 caracteres</div>';
        erroEncontrado = true;
      }

      // Se não houver erros, gerar ID e salvar dados na sessão
      if (!erroEncontrado) {
        (async function () {
          try {
            // Gera o ID e aguarda
            const id = await criarID(10, 'php/verificar_id.php');

            if (id && id.success) {
              // Salvar dados na sessão via PHP
              const response = await fetch('php/salvar_dados_cadastro.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `usuario=${encodeURIComponent(usuario)}&email=${encodeURIComponent(email)}&senha=${encodeURIComponent(senha)}&id=${encodeURIComponent(id.id)}`
              });

              const data = await response.json();

              if (data.success) {
                // Redirecionar para página de verificação
                window.location.href = 'verificar_codigo.php';
              } else {
                mensagemDiv.innerHTML = '<div class="mensagem-erro">' + data.message + '</div>';
              }
            } else {
              mensagemDiv.innerHTML = '<div class="mensagem-erro">Erro ao gerar ID. Tente novamente.</div>';
            }
          } catch (error) {
            console.error("Erro ao processar formulário:", error);
            mensagemDiv.innerHTML = '<div class="mensagem-erro">Ocorreu um erro ao processar o formulário. Tente novamente.</div>';
          }
        })();
      }

      return false;
    });
  </script>
</body>

</html>