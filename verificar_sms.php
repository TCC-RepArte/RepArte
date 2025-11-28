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
  <title>RepArte - Verificar Código</title>
  <link rel="stylesheet" href="css/emailesqueceu.css">
  <link rel="stylesheet" href="css/mensagens.css">
  <style>
    .input-codigo {
      width: 100%;
      padding: 18px;
      border: none;
      border-radius: 25px;
      background: #2b2146;
      color: #fff;
      text-align: center;
      letter-spacing: 10px;
      margin: 15px 0;
      font-weight: bold;
      font-size: 24px;
      outline: none;
    }
    
    .input-codigo::placeholder {
      color: #8a8a8a;
      letter-spacing: normal;
    }
    
    .codigo-box {
      background: linear-gradient(135deg, #2b2146, #3d2a5f);
      padding: 20px;
      border-radius: 15px;
      margin: 20px 0;
      border: 2px solid #ff6600;
      text-align: center;
      box-shadow: 0 4px 15px rgba(255, 102, 0, 0.2);
    }
    
    .codigo-display {
      font-size: 36px;
      font-weight: bold;
      color: #ff6600;
      letter-spacing: 12px;
      margin: 15px 0;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .codigo-box p {
      margin: 8px 0;
    }
  </style>
</head>
<?php
session_start();

// Verificar se veio da página anterior
if(!isset($_SESSION['telefone_sms'])) {
    header('Location: login_sms.php');
    exit();
}

$telefone = $_SESSION['telefone_sms'];
$codigo_enviado = $_SESSION['codigo_enviado'] ?? null;

// Pegar mensagens
$erros = $_SESSION['erros_verificacao'] ?? [];
$sucesso = $_SESSION['val_sms'] ?? [];
unset($_SESSION['erros_verificacao']);
unset($_SESSION['val_sms']);
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
    <h2>Verificar Código SMS</h2>
    <p class="descricao">Digite o código enviado para: <strong><?= htmlspecialchars($telefone); ?></strong></p>

    <?php if($codigo_enviado): ?>
    <!-- Mostrar código (apenas em ambiente local) -->
    <div class="codigo-box">
      <p style="margin: 0; color: #666; font-size: 14px;">Como você está em ambiente local, aqui está o código:</p>
      <div class="codigo-display"><?= htmlspecialchars($codigo_enviado); ?></div>
      <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;">Válido por 5 minutos</p>
    </div>
    <?php endif; ?>

    <!-- Formulário para verificar código -->
    <form method="post" action="php/sms_verificar_codigo.php">
      <input type="text" name="codigo" placeholder="000000" class="input-codigo" required maxlength="6" pattern="[0-9]{6}">

      <!-- Exibir mensagem de sucesso -->
      <?php if (!empty($sucesso)): ?>
        <div class="mensagem-sucesso">
          <?php foreach($sucesso as $msg): ?>
            <p><?= htmlspecialchars($msg); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Exibir mensagem de erro -->
      <?php if (!empty($erros)): ?>
        <div class="mensagem-erro">
          <?php foreach($erros as $erro): ?>
            <p><?= htmlspecialchars($erro); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <p class="info">Digite o código de 6 dígitos recebido por SMS.</p>

      <button type="submit" class="btn">Verificar e entrar</button>
    </form>

    <p style="text-align: center; margin-top: 20px;">
      <a href="login_sms.php" class="link-voltar">← Enviar novo código</a>
    </p>
  </main>

  <script>
    // Aceitar apenas números
    const inputCodigo = document.querySelector('input[name="codigo"]');
    inputCodigo.addEventListener('input', function(e) {
      e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6);
    });
  </script>
</body>
</html>

