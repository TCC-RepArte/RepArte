<!DOCTYPE html>
<html lang="pt-br">

<?php
session_start();

// Verificar se o usuário já está logado
if(isset($_SESSION['id'])) {
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
        <link rel="stylesheet" href="../front-end/cadastro.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
        <title>Sign up</title>
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
        
        <!-- Exibir mensagens de erro -->
        <?php if (!empty($erros)): ?>
            <div class="mensagem-erro">
                <?php foreach($erros as $erro): ?>
                    <p><?= htmlspecialchars($erro); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Exibir mensagem de sucesso se existir -->
        <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
            <div class="mensagem-sucesso">
                <?= htmlspecialchars($_SESSION['mensagem_sucesso']); ?>
                <?php unset($_SESSION['mensagem_sucesso']); ?>
            </div>
        <?php endif; ?>
        
        <form id="signupForm" method="post" action="../back-end/php/signup.php">
          <div class="textfield">
            <label for="nome">Nome Completo: </label>
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
 
          <button class="btn-registrarse" type="submit">Registrar-se</button>
          <a href="login1.php"><button type="button" class="btn-entrar">Entrar</button></a>
        </form>
        
        <div id="mensagem-js" style="margin-top: 10px; text-align: center;"></div>
      </div>
    </div>
  </div>
 
  <script src="../back-end/js/criarID.js"></script>
  <script>
    // Adicionar tratamento de erros no lado do cliente
    const formSignup = document.getElementById('signupForm');
    const originalSubmit = formSignup.onsubmit; // Guardar o evento de submit original
    
    formSignup.addEventListener('submit', function(e) {
        e.preventDefault(); // Sempre prevenir o envio padrão
        
        const senha = document.querySelector('input[name="senha"]').value;
        const confSenha = document.querySelector('input[name="confsenha"]').value;
        const mensagemDiv = document.getElementById('mensagem-js');
        let erroEncontrado = false;
        
        // Limpar mensagens anteriores
        mensagemDiv.innerHTML = '';
        
        // Verificar se as senhas coincidem
        if (senha !== confSenha) {
            mensagemDiv.innerHTML = '<div class="mensagem-erro">As senhas não coincidem</div>';
            erroEncontrado = true;
        }
        
        // Verificar complexidade da senha
        else if (senha.length < 6) {
            mensagemDiv.innerHTML = '<div class="mensagem-erro">A senha deve ter pelo menos 6 caracteres</div>';
            erroEncontrado = true;
        }
        
        // Se não houver erros, continuar com o processo de submissão do ID original (criarID.js)
        if (!erroEncontrado) {
            // Usar o processo de geração de ID do criarID.js
            (async function() {
                try {
                    // Gera o ID e aguarda
                    const id = await criarID(10, '../back-end/php/signup.php');
                    
                    if (id && id.success) {
                        // Cria e adiciona input hidden ao form
                        const hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "id";
                        hiddenInput.value = id.id;
                        formSignup.appendChild(hiddenInput);

                        // Agora envia o formulário com o ID incluso
                        formSignup.submit();
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