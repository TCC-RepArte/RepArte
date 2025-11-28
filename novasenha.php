<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
  <title>RepArte - Nova Senha</title>
  <link rel="stylesheet" href="css/novasenha.css">
  <link rel="stylesheet" href="css/mensagens.css">
</head>
<?php
session_start();
require 'php/validar_token.php';

// Verificar se o token foi fornecido na URL
if(!isset($_GET['token'])) {
    $_SESSION['erros_recuperacao'] = ['Link inválido'];
    header('Location: emailesqueceu.php');
    exit();
}

$token = $_GET['token'];

// Validar o token
$validacao = validarToken($token);

if(!$validacao['valido']) {
    $_SESSION['erros_recuperacao'] = [$validacao['erro']];
    header('Location: emailesqueceu.php');
    exit();
}

// Pegar mensagens de erro específicas de nova senha
$erros = $_SESSION['erros_nova_senha'] ?? [];
unset($_SESSION['erros_nova_senha']);
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

  <main class="container">
    <h2>Atualize a sua senha</h2>
    
    <!-- Formulário para atualizar senha -->
    <form method="post" action="php/atualizar_senha.php">
      <!-- Campo oculto com o token -->
      <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
      
      <p class="descricao">Insira a nova senha:</p>
   
      <div class="textfield">
        <label for="senha"></label>
        <input type="password" name="senha" placeholder="" required>
      </div>

      <p class="descricao2">Confirme a nova senha:</p>
   
      <div class="textfield2">
        <label for="confirmar_senha"></label>
        <input type="password" name="confirmar_senha" placeholder="" required>
      </div>

      <!-- Exibir mensagem de erro se existir -->
      <?php if (!empty($erros)): ?>
        <div class="mensagem-erro">
          <?php foreach($erros as $erro): ?>
            <p><?= htmlspecialchars($erro); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <button type="submit" class="btn">Atualizar senha</button>
    </form>
  </main>
</body>
</html>
