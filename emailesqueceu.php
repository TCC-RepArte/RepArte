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
  <title>RepArte - Redefinir Senha</title>
  <link rel="stylesheet" href="css/emailesqueceu.css">
  <link rel="stylesheet" href="css/mensagens.css">
</head>
<?php
session_start();

// Pegar mensagens de erro específicas de recuperação
$erros = $_SESSION['erros_recuperacao'] ?? [];
unset($_SESSION['erros_recuperacao']);
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
    <h2>Redefinir senha</h2>
    <p class="descricao">Insira o seu e-mail ou nome de usuário</p>

    <!-- Formulário para enviar dados ao backend -->
    <form method="post" action="php/enviar_link_recuperacao.php">
      <input type="text" name="usuario_email" placeholder="Insira seu nome de usuário ou e-mail" class="input-usuario" required>

      <!-- Exibir mensagem de erro se existir -->
      <?php if (!empty($erros)): ?>
        <div class="mensagem-erro">
          <?php foreach($erros as $erro): ?>
            <p><?= htmlspecialchars($erro); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <p class="info">Enviaremos um código para redefinir a sua senha.</p>

      <button type="submit" class="btn">Enviar código</button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
      <a href="login1.php" class="link-voltar">← Voltar para login</a>
    </p>
  </main>
</body>
</html>
