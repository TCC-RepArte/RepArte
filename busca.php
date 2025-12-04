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
        die("Erro de conexão: " . ($con->connect_error ?? 'Conexção não estabelecida'));
    }

    // 1. Buscar Obras
    $sql_obras = "SELECT * FROM obras WHERE titulo LIKE ? OR autor LIKE ? OR tipo LIKE ? LIMIT 20";

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
        body {
            overflow: hidden;
            /* Remove rolagem global conforme solicitado */
            height: 100vh;
            background: linear-gradient(to right, #000000, #0d0419, #180730, #1a0637);
        }

        .busca-container {
            padding: 0 20px;
            color: white;
            max-width: 100%;
            margin: 80px auto 0;
            /* Reduzido de 100px para 80px */
            height: calc(100vh - 90px);
            /* Ajustado para ocupar o resto da tela */
            display: flex;
            flex-direction: column;
        }

        .busca-header {
            margin-bottom: 10px;
            /* Reduzido de 15px */
            flex-shrink: 0;
        }

        .busca-header h1 {
            font-size: 22px;
            /* Levemente menor */
            margin: 0;
        }

        .tabs-busca {
            display: flex;
            gap: 20px;
            border-bottom: 1px solid #333;
            margin-bottom: 10px;
            /* Reduzido de 15px */
            flex-shrink: 0;
        }

        .tab-btn-busca {
            background: none;
            border: none;
            color: #888;
            font-size: 16px;
            padding: 8px 15px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }

        .tab-btn-busca.active {
            color: #ff6600;
            border-bottom: 2px solid #ff6600;
        }

        .tab-content-busca {
            display: none;
            flex: 1;
            overflow: hidden;
            /* Conteúdo interno gerencia o layout */
            position: relative;
        }

        .tab-content-busca.active {
            display: block;
        }

        /* Layout Masonry para Obras */
        #obras-container-dinamico {
            column-count: 6;
            column-gap: 15px;
            column-fill: auto;
            /* Preenche colunas sequencialmente */
            height: calc(100% - 60px);
            /* Altura fixa menos espaço da paginação */
            width: 100%;
        }

        @media (max-width: 1400px) {
            #obras-container-dinamico {
                column-count: 5;
            }
        }

        @media (max-width: 1100px) {
            #obras-container-dinamico {
                column-count: 4;
            }
        }

        @media (max-width: 800px) {
            #obras-container-dinamico {
                column-count: 3;
            }
        }

        @media (max-width: 500px) {
            #obras-container-dinamico {
                column-count: 2;
            }
        }

        .obra-card-wrapper {
            break-inside: avoid;
            /* Evita quebra do card entre colunas */
            margin-bottom: 15px;
            display: inline-block;
            /* Necessário para break-inside funcionar bem */
            width: 100%;
        }

        /* Estilos gerais de lista (para outras abas) */
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

        /* Paginação fixa na parte inferior */
        #paginacao-container-obras {
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to top, #1a0637 90%, transparent);
            /* Fundo mais sólido para garantir visibilidade */
            z-index: 10;
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
                <button type="submit" class="search-icon"
                    style="background: none; border: none; cursor: pointer;">🔍</button>
                <input type="text" name="q" class="search-text" value="<?= htmlspecialchars($termo) ?>"
                    placeholder="Procure uma obra, usuário ou hashtag...">
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
            <button class="tab-btn-busca active" onclick="openTab(event, 'obras')">Obras</button>
            <button class="tab-btn-busca" onclick="openTab(event, 'postagens')">Postagens
                (<?= count($postagens) ?>)</button>
            <button class="tab-btn-busca" onclick="openTab(event, 'usuarios')">Usuários
                (<?= count($usuarios) ?>)</button>
            <button class="tab-btn-busca" onclick="openTab(event, 'hashtags')">Hashtags
                (<?= count($hashtags) ?>)</button>
        </div>

        <!-- Obras -->
        <div id="obras" class="tab-content-busca active">
            <div class="layout-busca-obras" style="display: flex; gap: 20px; height: 100%;">
                <!-- Sidebar de Filtros (Estilo do Painel) -->
                <div class="filtros-sidebar"
                    style="width: 200px; flex-shrink: 0; background: #130922; padding: 15px; border-radius: 10px; height: fit-content; border: 1px solid #333;">
                    <h4 style="color: white; margin-top: 0; margin-bottom: 15px; font-size: 16px;">Tipos de Obra</h4>

                    <div class="controles-filtro" style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <button onclick="marcarTodosFiltros(true)"
                            style="background: #333; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px;">Todos</button>
                        <button onclick="marcarTodosFiltros(false)"
                            style="background: #333; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px;">Nenhum</button>
                    </div>

                    <div class="opcoes-filtro-busca">
                        <label class="opcao-filtro"
                            style="display: flex; align-items: center; gap: 8px; color: #ccc; margin-bottom: 12px; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" value="filme" checked onchange="filtrarObras()"> Filmes
                        </label>
                        <label class="opcao-filtro"
                            style="display: flex; align-items: center; gap: 8px; color: #ccc; margin-bottom: 12px; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" value="serie" checked onchange="filtrarObras()"> Séries
                        </label>
                        <label class="opcao-filtro"
                            style="display: flex; align-items: center; gap: 8px; color: #ccc; margin-bottom: 12px; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" value="livro" checked onchange="filtrarObras()"> Livros
                        </label>
                        <label class="opcao-filtro"
                            style="display: flex; align-items: center; gap: 8px; color: #ccc; margin-bottom: 12px; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" value="arte" checked onchange="filtrarObras()"> Pinturas/Arte
                        </label>
                        <label class="opcao-filtro"
                            style="display: flex; align-items: center; gap: 8px; color: #ccc; margin-bottom: 12px; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" value="musica" checked onchange="filtrarObras()"> Músicas
                        </label>
                    </div>
                </div>

                <!-- Container de Resultados -->
                <div style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
                    <!-- Container dinâmico preenchido via JavaScript -->
                    <div id="obras-container-dinamico">
                        <p style="color: #888; text-align: center; padding: 20px;">Carregando obras...</p>
                    </div>
                    <!-- Container de paginação adicionado aqui -->
                    <div id="paginacao-container-obras"></div>
                </div>
            </div>
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