<?php
// Debug temporário
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'php/config.php';
require_once 'php/perfil_dados.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login1.php");
    exit();
}

$perfil = buscaUsuario();
$termo = trim($_GET['q'] ?? '');
$termo_like = "%$termo%";

// Inicializar arrays de resultados
$obras = [];
$postagens = [];
$usuarios = [];
$hashtags = [];

if (!empty($termo)) {
    global $con;

    // Verificar conexão com o banco
    if (!$con || $con->connect_error) {
        die("Erro de conexão: " . ($con->connect_error ?? 'Conexão não estabelecida'));
    }

    // 1. Buscar Obras
    $sql_obras = "SELECT * FROM obras WHERE titulo LIKE ? OR autor LIKE ? OR tipo LIKE ? LIMIT 20";
    $stmt = $con->prepare($sql_obras);
    if ($stmt) {
        $stmt->bind_param("sss", $termo_like, $termo_like, $termo_like);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $obras[] = $row;
            }
        }
        $stmt->close();
    }

    // 2. Buscar Postagens
    $sql_posts = "SELECT p.*, l.usuario, l.id as id_usuario, 
                   CASE 
                       WHEN perf.caminho IS NOT NULL AND perf.caminho != '' THEN perf.caminho
                       ELSE CONCAT('https://ui-avatars.com/api/?name=', REPLACE(l.usuario, ' ', '+'), '&background=ff6600&color=fff&size=50')
                   END as foto_usuario
                   FROM postagens p 
                   JOIN login l ON p.id_usuario = l.id 
                   LEFT JOIN perfil perf ON l.id = perf.id
                   WHERE p.titulo LIKE ? OR p.texto LIKE ? 
                   LIMIT 20";
    $stmt = $con->prepare($sql_posts);
    if ($stmt) {
        $stmt->bind_param("ss", $termo_like, $termo_like);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $postagens[] = $row;
            }
        }
        $stmt->close();
    }

    // 3. Buscar Usuários
    $sql_users = "SELECT l.id, l.usuario, p.nomexi, 
                   CASE 
                       WHEN p.caminho IS NOT NULL AND p.caminho != '' THEN p.caminho
                       ELSE CONCAT('https://ui-avatars.com/api/?name=', REPLACE(COALESCE(p.nomexi, l.usuario), ' ', '+'), '&background=ff6600&color=fff&size=50')
                   END as caminho
                   FROM login l 
                   LEFT JOIN perfil p ON l.id = p.id 
                   WHERE l.usuario LIKE ? OR p.nomexi LIKE ? 
                   LIMIT 20";
    $stmt = $con->prepare($sql_users);
    if ($stmt) {
        $stmt->bind_param("ss", $termo_like, $termo_like);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $usuarios[] = $row;
            }
        }
        $stmt->close();
    }

    // 4. Buscar Hashtags
    $tag_termo = $termo;
    if (strpos($tag_termo, '#') !== 0) {
        $tag_termo = '#' . $tag_termo;
    }
    
    $tag_like = "%$tag_termo%";
    $sql_hashtags = "SELECT p.*, l.usuario, l.id as id_usuario,
                      CASE 
                          WHEN perf.caminho IS NOT NULL AND perf.caminho != '' THEN perf.caminho
                          ELSE CONCAT('https://ui-avatars.com/api/?name=', REPLACE(l.usuario, ' ', '+'), '&background=ff6600&color=fff&size=50')
                      END as foto_usuario
                      FROM postagens p 
                      JOIN login l ON p.id_usuario = l.id 
                      LEFT JOIN perfil perf ON l.id = perf.id
                      WHERE p.texto LIKE ? 
                      LIMIT 20";
    $stmt = $con->prepare($sql_hashtags);
    if ($stmt) {
        $stmt->bind_param("s", $tag_like);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $hashtags[] = $row;
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/telainicial.css">
    <link rel="stylesheet" href="css/perfil_usuario.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Busca - <?= htmlspecialchars($termo) ?></title>
    <style>
        .busca-container {
            padding: 20px;
            color: white;
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 100px;
        }
        .busca-header {
            margin-bottom: 30px;
        }
        .tabs-busca {
            display: flex;
            gap: 20px;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
        }
        .tab-btn-busca {
            background: none;
            border: none;
            color: #888;
            font-size: 18px;
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        .tab-btn-busca.active {
            color: #ff6600;
            border-bottom: 2px solid #ff6600;
        }
        .tab-content-busca {
            display: none;
        }
        .tab-content-busca.active {
            display: block;
        }
        .result-item {
            background: #1a1a1a;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .result-item img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .obra-item img {
            width: 60px;
            height: 90px;
            border-radius: 5px;
        }
        .result-info h3 {
            margin: 0;
            font-size: 16px;
            color: #fff;
        }
        .result-info p {
            margin: 5px 0 0;
            color: #aaa;
            font-size: 14px;
        }
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="telainicial.php"><img src="images/logo.png" alt="Logo"></a>
        </div>
        <div class="search-box">
            <form action="busca.php" method="GET" style="display: flex; width: 100%; align-items: center;">
                <button type="submit" class="search-icon" style="background: none; border: none; cursor: pointer;">🔍</button>
                <input type="text" name="q" class="search-text" value="<?= htmlspecialchars($termo) ?>" placeholder="Procure uma obra, usuário ou hashtag...">
            </form>
        </div>
        <div class="header-controls">
            <a href="telainicial.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </header>

    <main class="busca-container">
        <div class="busca-header">
            <h1>Resultados para: "<?= htmlspecialchars($termo) ?>"</h1>
        </div>

        <div class="tabs-busca">
            <button class="tab-btn-busca active" onclick="openTab(event, 'obras')">Obras (<?= count($obras) ?>)</button>
            <button class="tab-btn-busca" onclick="openTab(event, 'postagens')">Postagens (<?= count($postagens) ?>)</button>
            <button class="tab-btn-busca" onclick="openTab(event, 'usuarios')">Usuários (<?= count($usuarios) ?>)</button>
            <button class="tab-btn-busca" onclick="openTab(event, 'hashtags')">Hashtags (<?= count($hashtags) ?>)</button>
        </div>

        <!-- Obras -->
        <div id="obras" class="tab-content-busca active">
            <?php if (empty($obras)): ?>
                <p>Nenhuma obra encontrada.</p>
            <?php else: ?>
                <?php foreach ($obras as $obra): ?>
                    <a href="obra.php?id=<?= $obra['id'] ?>">
                        <div class="result-item obra-item" data-obra-id="<?= htmlspecialchars($obra['id']) ?>" data-obra-tipo="<?= htmlspecialchars($obra['tipo']) ?>">
                            <img class="obra-imagem" src="" alt="<?= htmlspecialchars($obra['titulo']) ?>" style="background: #2a2a2a;">
                            <div class="result-info">
                                <h3><?= htmlspecialchars($obra['titulo']) ?></h3>
                                <p><?= htmlspecialchars($obra['autor'] ?? 'Autor desconhecido') ?> - <?= htmlspecialchars($obra['tipo']) ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Postagens -->
        <div id="postagens" class="tab-content-busca">
            <?php if (empty($postagens)): ?>
                <p>Nenhuma postagem encontrada.</p>
            <?php else: ?>
                <?php foreach ($postagens as $post): ?>
                    <a href="postagem.php?id=<?= $post['id'] ?>">
                        <div class="result-item">
                            <img src="<?= htmlspecialchars($post['foto_usuario']) ?>" alt="User">
                            <div class="result-info">
                                <h3><?= htmlspecialchars($post['titulo']) ?></h3>
                                <p>Por: <?= htmlspecialchars($post['usuario']) ?></p>
                                <p><?= substr(htmlspecialchars($post['texto']), 0, 100) ?>...</p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Usuários -->
        <div id="usuarios" class="tab-content-busca">
            <?php if (empty($usuarios)): ?>
                <p>Nenhum usuário encontrado.</p>
            <?php else: ?>
                <?php foreach ($usuarios as $user): ?>
                    <a href="usuario_perfil.php?id=<?= $user['id'] ?>">
                        <div class="result-item">
                            <img src="<?= htmlspecialchars($user['caminho']) ?>" alt="User">
                            <div class="result-info">
                                <h3><?= htmlspecialchars($user['nomexi'] ?? $user['usuario']) ?></h3>
                                <p>@<?= htmlspecialchars($user['usuario']) ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Hashtags -->
        <div id="hashtags" class="tab-content-busca">
            <?php if (empty($hashtags)): ?>
                <p>Nenhuma postagem com essa hashtag encontrada.</p>
            <?php else: ?>
                <?php foreach ($hashtags as $post): ?>
                    <a href="postagem.php?id=<?= $post['id'] ?>">
                        <div class="result-item">
                            <img src="<?= htmlspecialchars($post['foto_usuario']) ?>" alt="User">
                            <div class="result-info">
                                <h3><?= htmlspecialchars($post['titulo']) ?></h3>
                                <p>Por: <?= htmlspecialchars($post['usuario']) ?></p>
                                <p><?= substr(htmlspecialchars($post['texto']), 0, 100) ?>...</p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Scripts necessários -->
    <script src="js/apis-obras.js"></script>
    <script src="js/busca.js"></script>
</body>
</html>