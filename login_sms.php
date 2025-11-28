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
  <title>RepArte - Login SMS</title>
  <link rel="stylesheet" href="css/emailesqueceu.css">
  <link rel="stylesheet" href="css/mensagens.css">
  <style>
    .input-telefone {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 25px;
      background: #2b2146;
      color: #ccc;
      text-align: center;
      outline: none;
      margin: 15px 0;
      font-size: 16px;
    }
    
    .input-telefone::placeholder {
      color: #8a8a8a;
      font-style: italic;
    }
  </style>
</head>
<?php
session_start();

// Pegar mensagens de erro
$erros = $_SESSION['erros_sms'] ?? [];
unset($_SESSION['erros_sms']);
?>
<body>
  <header>
    <div class="interface">
           <div class="logo">
            <a href="login1.php">
                <img src="images/logo.png" alt="Logo do site">
            </a>
        </div>
    </div>
    </header>

  <main class="container">
    <h2>Login com SMS</h2>
    <p class="descricao">Insira seu número de telefone</p>

    <!-- Formulário para enviar SMS -->
    <form method="post" action="php/sms_enviar_codigo.php">
      <input type="tel" name="telefone" placeholder="(00) 00000-0000" class="input-telefone" required maxlength="15">

      <!-- Exibir mensagem de erro se existir -->
      <?php if (!empty($erros)): ?>
        <div class="mensagem-erro">
          <?php foreach($erros as $erro): ?>
            <p><?= htmlspecialchars($erro); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <p class="info">Enviaremos um código de 6 dígitos para seu celular.</p>

      <button type="submit" class="btn">Enviar código</button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
      <a href="login1.php" class="link-voltar">← Voltar para login</a>
    </p>
  </main>

  <script>
    // Máscara para telefone
    const inputTelefone = document.querySelector('input[name="telefone"]');
    inputTelefone.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 11) value = value.slice(0, 11);
      
      if (value.length > 6) {
        value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
      } else if (value.length > 2) {
        value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
      } else if (value.length > 0) {
        value = value.replace(/^(\d*)/, '($1');
      }
      
      e.target.value = value;
    });
  </script>
</body>
</html>

