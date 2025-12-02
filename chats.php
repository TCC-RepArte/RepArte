<?php
session_start();

require_once 'php/config.php';
require_once 'php/perfil_dados.php';
include 'vlibras_include.php';

if (!isset($_SESSION['id'])) {
    header("Location: login1.php");
    exit();
}

$perfil = buscaUsuario();
$meu_id = $_SESSION['id'];
$chat_id = $_GET['id_user'] ?? null;

global $con;

// Buscar usuários com quem já conversei ou todos os usuários (limitado)
$contatos = [];
$sql_contatos = "SELECT DISTINCT l.id, l.usuario, p.caminho, p.nomexi 
                 FROM login l 
                 LEFT JOIN perfil p ON l.id = p.id 
                 WHERE l.id != ? LIMIT 20"; // Simplificado para mostrar lista de usuarios
$stmt = $con->prepare($sql_contatos);
$stmt->bind_param("s", $meu_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $contatos[] = $row;
}

// Se selecionou um chat, buscar mensagens
$mensagens = [];
$chat_user = null;
if ($chat_id) {
    // Buscar dados do usuário do chat
    $stmt = $con->prepare("SELECT l.id, l.usuario, p.caminho, p.nomexi FROM login l LEFT JOIN perfil p ON l.id = p.id WHERE l.id = ?");
    $stmt->bind_param("s", $chat_id);
    $stmt->execute();
    $chat_user = $stmt->get_result()->fetch_assoc();

    // Buscar mensagens
    $sql_msgs = "SELECT * FROM mensagens 
                 WHERE (id_remetente = ? AND id_destinatario = ?) 
                    OR (id_remetente = ? AND id_destinatario = ?) 
                 ORDER BY data_envio ASC";
    $stmt = $con->prepare($sql_msgs);
    $stmt->bind_param("ssss", $meu_id, $chat_id, $chat_id, $meu_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $mensagens[] = $row;
    }

    // Enviar mensagem
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'])) {
        $msg = $_POST['mensagem'];
        if (!empty($msg)) {
            $stmt = $con->prepare("INSERT INTO mensagens (id_remetente, id_destinatario, mensagem) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $meu_id, $chat_id, $msg);
            $stmt->execute();
            header("Location: chats.php?id_user=" . $chat_id);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/telainicial.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Reparte - Chat</title>
    <style>
        .chat-container {
            display: flex;
            height: calc(100vh - 150px);
            margin-top: 20px;
            gap: 20px;
            padding: 0 20px;
        }

        .chat-sidebar {
            width: 300px;
            background: #0f0e0f;
            border-radius: 16px;
            padding: 20px;
            overflow-y: auto;
        }

        .chat-main {
            flex: 1;
            background: #0f0e0f;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s;
            color: white;
            text-decoration: none;
        }

        .contact-item:hover,
        .contact-item.active {
            background: #2a0e4c;
        }

        .contact-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #333;
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }

        .messages-area {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
            color: white;
            word-wrap: break-word;
        }

        .message.sent {
            align-self: flex-end;
            background: #ff6600;
            border-bottom-right-radius: 2px;
        }

        .message.received {
            align-self: flex-start;
            background: #333;
            border-bottom-left-radius: 2px;
        }

        .chat-input-area {
            padding: 20px;
            border-top: 1px solid #333;
            background: #1a1a1a;
        }

        .chat-form {
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            padding: 12px;
            border-radius: 20px;
            border: none;
            background: #333;
            color: white;
            outline: none;
        }

        .btn-send {
            background: #ff6600;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-chat {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #666;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <a href="telainicial.php"><img src="images/logo.png" alt="Logo"></a>
        </div>
        <div class="header-controls">
            <a href="telainicial.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </header>

    <div class="chat-container">
        <aside class="chat-sidebar">
            <h3 style="color: white; margin-bottom: 20px;">Contatos</h3>
            <?php foreach ($contatos as $contato): ?>
                <a href="chats.php?id_user=<?= $contato['id'] ?>"
                    class="contact-item <?= ($chat_id == $contato['id']) ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($contato['caminho'] ?? 'images/default.png') ?>" class="contact-img"
                        alt="Foto">
                    <span><?= htmlspecialchars($contato['nomexi'] ?? $contato['usuario']) ?></span>
                </a>
            <?php endforeach; ?>
        </aside>

        <main class="chat-main">
            <?php if ($chat_id && $chat_user): ?>
                <div class="chat-header">
                    <img src="<?= htmlspecialchars($chat_user['caminho'] ?? 'images/default.png') ?>" class="contact-img"
                        alt="Foto">
                    <h3><?= htmlspecialchars($chat_user['nomexi'] ?? $chat_user['usuario']) ?></h3>
                </div>
                <div class="messages-area" id="msgArea">
                    <?php foreach ($mensagens as $msg): ?>
                        <div class="message <?= ($msg['id_remetente'] == $meu_id) ? 'sent' : 'received' ?>">
                            <?= htmlspecialchars($msg['mensagem']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="chat-input-area">
                    <form class="chat-form" method="POST">
                        <input type="text" name="mensagem" class="chat-input" placeholder="Digite sua mensagem..." required
                            autocomplete="off">
                        <button type="submit" class="btn-send"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
                <script>
                    // Scroll to bottom
                    var msgArea = document.getElementById('msgArea');
                    msgArea.scrollTop = msgArea.scrollHeight;
                </script>
            <?php else: ?>
                <div class="empty-chat">
                    <p>Selecione um contato para conversar</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>