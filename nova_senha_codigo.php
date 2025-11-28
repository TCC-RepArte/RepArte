<!DOCTYPE html>
<html lang="pt-BR">
<?php
session_start();

// Verificar se código foi validado
if (!isset($_SESSION['codigo_senha_validado']) || !isset($_SESSION['email_nova_senha'])) {
    header('Location: emailesqueceu.php');
    exit();
}

$erros = $_SESSION['erros_nova_senha'] ?? [];
unset($_SESSION['erros_nova_senha']);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400..800&display=swap" rel="stylesheet">
    <title>RepArte - Nova Senha</title>
    <link rel="stylesheet" href="css/novasenha.css">
    <link rel="stylesheet" href="css/mensagens.css">
</head>

<body>
    <header>
        <div class="interface">
            <div class="logo">
                <a href="#"><img src="images/logo.png" alt="Logo do site"></a>
            </div>
        </div>
    </header>

    <main class="container">
        <h2>Defina sua Nova Senha</h2>

        <form method="post" action="php/atualizar_senha_codigo.php">
            <p class="descricao">Insira a nova senha:</p>

            <div class="textfield">
                <label for="senha"></label>
                <input type="password" name="senha" placeholder="Nova senha" required minlength="6">
            </div>

            <p class="descricao2">Confirme a nova senha:</p>

            <div class="textfield2">
                <label for="confirmar_senha"></label>
                <input type="password" name="confirmar_senha" placeholder="Confirme a senha" required minlength="6">
            </div>

            <!-- Exibir mensagens de erro -->
            <?php if (!empty($erros)): ?>
                <div class="mensagem-erro">
                    <?php foreach ($erros as $erro): ?>
                        <p><?= htmlspecialchars($erro); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn">Atualizar Senha</button>
        </form>

        <p style="text-align: center; margin-top: 15px;">
            <a href="login1.php" style="color: #667eea; text-decoration: none;">← Voltar ao login</a>
        </p>
    </main>
</body>

</html>