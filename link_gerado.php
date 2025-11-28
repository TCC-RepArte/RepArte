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
  <title>RepArte - Link de Recuperação</title>
  <link rel="stylesheet" href="css/emailesqueceu.css">
  <link rel="stylesheet" href="css/mensagens.css">
  <style>
    .link-box {
      background: linear-gradient(135deg, #2b2146, #3d2a5f);
      padding: 20px;
      border-radius: 15px;
      margin: 20px 0;
      word-break: break-all;
      border: 2px solid #ff6600;
      box-shadow: 0 4px 15px rgba(255, 102, 0, 0.2);
    }
    
    .link-box strong {
      color: #ff6600;
    }
    
    .link-box #linkRecuperacao {
      display: inline-block;
      color: #ccc;
      padding: 10px;
      background: rgba(0, 0, 0, 0.2);
      border-radius: 8px;
      margin-top: 10px;
      font-size: 14px;
    }
    
    .copiar-btn {
      margin-top: 15px;
      padding: 12px 20px;
      background: #ff6600;
      color: white;
      border: none;
      border-radius: 25px;
      cursor: pointer;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s;
      box-shadow: 0 2px 8px rgba(255, 102, 0, 0.3);
    }
    
    .copiar-btn:hover {
      background: #e55a00;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 102, 0, 0.4);
    }
  </style>
</head>
<body>
<?php
session_start();

// Verificar se existe token na sessão
if(!isset($_SESSION['token_recuperacao'])) {
    header('Location: emailesqueceu.html');
    exit();
}

$token = $_SESSION['token_recuperacao'];
$email = $_SESSION['email_usuario'];

// Gerar URL completa do link de recuperação
$link = "http://localhost/RepArte/web/html/novasenha.php?token=" . $token;

// Limpar sessão após usar
unset($_SESSION['token_recuperacao']);
?>
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
    <h2>Link de Recuperação</h2>
    <p class="descricao">Como você está em ambiente local, copie o link abaixo e cole no navegador:</p>

    <div class="link-box">
      <strong>Para:</strong> <?= htmlspecialchars($email); ?><br><br>
      <strong>Link:</strong><br>
      <span id="linkRecuperacao"><?= htmlspecialchars($link); ?></span>
    </div>

    <button class="copiar-btn" onclick="copiarLink()">Copiar Link</button>

    <p class="info" style="margin-top: 20px;">Este link expira em 1 hora.</p>

    <a href="<?= htmlspecialchars($link); ?>">
      <button class="btn">Ir para redefinir senha</button>
    </a>
  </main>

  <script>
    function copiarLink() {
      const link = document.getElementById('linkRecuperacao').textContent;
      navigator.clipboard.writeText(link).then(() => {
        alert('Link copiado!');
      });
    }
  </script>
</body>
</html>

