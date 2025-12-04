<?php
session_start();
require 'php/config.php';
require 'php/perfil_dados.php';
require 'php/vlibras_config.php';

// Pega a hashtag da URL
$tag_nome = isset($_GET['tag']) ? $_GET['tag'] : '';
$tag_nome = str_replace('#', '', $tag_nome);

if (empty($tag_nome)) {
    header("Location: telainicial.php");
    exit;
}

// Filtros
$desde = isset($_GET['desde']) ? $_GET['desde'] : 'sempre';
$mais = isset($_GET['mais']) ? $_GET['mais'] : 'recentes';

// Filtro de Data
$data_filtro = "";
if ($desde == '1dia')
    $data_filtro = "AND p.data_post >= NOW() - INTERVAL 1 DAY";
elseif ($desde == '1semana')
    $data_filtro = "AND p.data_post >= NOW() - INTERVAL 1 WEEK";
elseif ($desde == '1mes')
    $data_filtro = "AND p.data_post >= NOW() - INTERVAL 1 MONTH";
elseif ($desde == '3meses')
    $data_filtro = "AND p.data_post >= NOW() - INTERVAL 3 MONTH";

// Ordenação
$ordem = "p.data_post DESC";
if ($mais == 'curtidos') {
    $ordem = "(SELECT COUNT(*) FROM reacoes r WHERE r.id_conteudo = p.id AND r.tipo = 'like') DESC";
} elseif ($mais == 'comentados') {
    $ordem = "(SELECT COUNT(*) FROM comentarios c WHERE c.id_conteudo = p.id) DESC";
}

// Busca Posts
$sql = "SELECT p.*, 
               l.usuario, 
               pf.foto, 
               pf.caminho,
               o.titulo as obra_titulo,
               o.tipo as obra_tipo
        FROM postagens p
        JOIN post_hashtags ph ON p.id = ph.post_id
        JOIN hashtags h ON ph.hashtag_id = h.id
        JOIN login l ON p.id_usuario = l.id
        LEFT JOIN perfil pf ON p.id_usuario = pf.id
        LEFT JOIN obras o ON p.id_obra = o.id
        WHERE h.nome = ? $data_filtro
        ORDER BY $ordem";

$stmt = $con->prepare($sql);
$tag_param = "#" . $tag_nome;
$stmt->bind_param("s", $tag_param);
$stmt->execute();
$result = $stmt->get_result();
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

// Busca Obras Relacionadas
$sql_obras = "SELECT o.id, o.titulo, o.tipo, COUNT(p.id) as total
              FROM obras o
              JOIN postagens p ON o.id = p.id_obra
              JOIN post_hashtags ph ON p.id = ph.post_id
              JOIN hashtags h ON ph.hashtag_id = h.id
              WHERE h.nome = ?
              GROUP BY o.id
              ORDER BY total DESC
              LIMIT 6"; // Aumentei para 6 para fechar o grid
$stmt_obras = $con->prepare($sql_obras);
$stmt_obras->bind_param("s", $tag_param);
$stmt_obras->execute();
$res_obras = $stmt_obras->get_result();
$obras_hashtag = [];
while ($row = $res_obras->fetch_assoc()) {
    $obras_hashtag[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>#<?= htmlspecialchars($tag_nome) ?> - RepArte</title>
    <link rel="stylesheet" href="css/telainicial.css">
    <link rel="stylesheet" href="css/hashtag.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <header>
        <div class="logo">
            <a href="telainicial.php"><img src="images/logo.png" alt="Logo do site"></a>
        </div>

        <div class="search-box">
            <form action="busca.php" method="GET" style="display: flex; width: 100%; align-items: center;">
                <button type="submit" class="search-icon"
                    style="background: none; border: none; cursor: pointer;">🔍</button>
                <input type="text" name="q" class="search-text" placeholder="Procure uma obra, usuário ou hashtag..."
                    value="#<?= htmlspecialchars($tag_nome) ?>">
            </form>
        </div>

        <div class="header-actions">
            <i class="fas fa-bell"></i>
            <a href="configuracoes.php" style="color: inherit; text-decoration: none;"><i class="fas fa-cog"></i></a>
            <a href="chats.php" class="btn-chat" style="color: white" title="Chat">
                <i class="fas fa-comments"></i>
            </a>
        </div>
    </header>

    <div class="hashtag-banner">
        <div class="hashtag-title">
            <span class="hash-symbol">#</span>
            <h1><?= htmlspecialchars($tag_nome) ?></h1>
        </div>
    </div>
    <main class="hashtag-container">
        <!-- Feed -->
        <section class="feed-hashtag">
            <?php if (empty($posts)): ?>
                <div class="no-posts">
                    <i class="fas fa-ghost" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                    <p>Nenhum post encontrado com a tag <strong>#<?= htmlspecialchars($tag_nome) ?></strong>.</p>
                    <p style="font-size: 12px; margin-top: 10px;">Tente ajustar os filtros ou seja o primeiro a postar!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <div class="post-header">
                            <div class="post-user">
                                <a href="usuario_perfil.php?id=<?= $post['id_usuario'] ?>"
                                    style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px;">
                                    <img src="<?= !empty($post['caminho']) ? $post['caminho'] : $post['foto'] ?>"
                                        alt="Foto do Usuário" class="post-user-photo" />
                                    <h3><?= $post['usuario'] ?></h3>
                                </a>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <a href="postagem.php?id=<?= $post['id'] ?>" class="fullscreen-icon"
                                    title="Ver postagem completa">
                                    <i class="fas fa-expand"></i>
                                </a>
                                <!-- Container para o menu de opções do post -->
                                <div class="post-set-container">
                                    <i class="post-set fa-solid fa-bars" style="color: rgb(255, 102, 0);" title="Opções"></i>

                                    <!-- Menu Dropdown (Balãozinho) -->
                                    <div class="post-options-dropdown">
                                        <!-- Copiar Link -->
                                        <a href="#" onclick="copiarLink(event, '<?= $post['id'] ?>')">
                                            <i class="fas fa-link"></i> Copiar Link
                                        </a>

                                        <a href="#" class="btn-favorito" data-id="<?= $post['id'] ?>"
                                            onclick="toggleFavorito(event, '<?= $post['id'] ?>')">

                                            <i class="far fa-star" id="fav-icon-<?= $post['id'] ?>"></i>
                                            <span id="fav-text-<?= $post['id'] ?>">Favoritar</span>
                                        </a>

                                        <!-- Adicionar à Lista (Lógica futura) -->
                                        <a href="#">
                                            <i class="fas fa-list"></i> Salvar
                                        </a>

                                        <!-- Deletar: Só aparece se o usuário for o dono do post -->
                                        <?php if (isset($_SESSION['id']) && $_SESSION['id'] == $post['id_usuario']): ?>
                                            <a href="php/deletar_post.php?id=<?= $post['id'] ?>"
                                                onclick="return confirm('Tem certeza que deseja deletar este post?')">
                                                <i class="fas fa-trash" style="color: red;"></i> Deletar
                                            </a>
                                        <?php endif; ?>

                                        <!-- Denunciar -->
                                        <a href="#" onclick="abrirDenuncia(event, '<?= $post['id'] ?>', 'postagem')">
                                            <i class="fas fa-flag"></i> Denunciar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="post-body">
                            <a href="obra.php?id=<?= $post['id_obra'] ?>" class="obra-link">
                                <img id="img-post" id-obra="<?= $post['id_obra'] ?>" tipo-obra="<?= $post['obra_tipo'] ?>"
                                    src="" alt="Imagem post">
                            </a>
                            <div class="post-content">
                                <a href="obra.php?id=<?= $post['id_obra'] ?>" class="obra-title-link">
                                    <p><?= $post['titulo'] ?></p>
                                </a>
                                <div class="post-text-container" data-post-id="<?= $post['id'] ?>">
                                    <div class="post-text-truncated"><?= nl2br(htmlspecialchars($post['texto'])) ?></div>
                                    <button class="expand-button" onclick="expandirTexto('<?= $post['id'] ?>')"
                                        style="display: none;">
                                        Ver mais...
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="post-buttons">
                            <a href="postagem.php?id=<?= $post['id'] ?>" class="comment-button" title="Ver comentários">
                                <i class="fas fa-comment-dots"></i>
                            </a>
                            <div class="vote-buttons" data-id="<?= $post['id'] ?>">
                                <button class="like-btn">
                                    <i class="fas fa-arrow-up"></i>
                                    <span class="like-text">Curtir</span>
                                </button>
                                <span class="like-count">0</span>
                                <button class="dislike-btn">
                                    <i class="fas fa-arrow-down"></i>
                                    <span class="dislike-text">Descurtir</span>
                                </button>
                                <span class="dislike-count">0</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Sidebar -->
        <aside class="sidebar-hashtag">
            <!-- Filtros -->
            <div class="sidebar-box filter-box">
                <div class="filter-header">
                    <span>Filtros</span>
                    <i class="fas fa-filter" style="color: #ff6600;"></i>
                </div>
                <form action="hashtag.php" method="GET" id="filterForm">
                    <input type="hidden" name="tag" value="<?= htmlspecialchars($tag_nome) ?>">

                    <div class="filter-options">
                        <div class="filter-group">
                            <label>Período</label>
                            <select name="desde" onchange="this.form.submit()">
                                <option value="sempre" <?= $desde == 'sempre' ? 'selected' : '' ?>>Todo o tempo</option>
                                <option value="1dia" <?= $desde == '1dia' ? 'selected' : '' ?>>Últimas 24h</option>
                                <option value="1semana" <?= $desde == '1semana' ? 'selected' : '' ?>>Última semana</option>
                                <option value="1mes" <?= $desde == '1mes' ? 'selected' : '' ?>>Último mês</option>
                                <option value="3meses" <?= $desde == '3meses' ? 'selected' : '' ?>>Últimos 3 meses</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Ordenar por</label>
                            <select name="mais" onchange="this.form.submit()">
                                <option value="recentes" <?= $mais == 'recentes' ? 'selected' : '' ?>>Mais Recentes
                                </option>
                                <option value="curtidos" <?= $mais == 'curtidos' ? 'selected' : '' ?>>Mais Curtidos
                                </option>
                                <option value="comentados" <?= $mais == 'comentados' ? 'selected' : '' ?>>Mais Comentados
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Obras -->
            <div class="sidebar-box obras-box">
                <h3>Obras em destaque</h3>
                <div class="mini-carousel">
                    <?php if (!empty($obras_hashtag)): ?>
                        <?php foreach ($obras_hashtag as $obra): ?>
                            <a href="obra.php?id=<?= $obra['id'] ?>" title="<?= $obra['titulo'] ?>">
                                <img class="carousel-img" src="images/placeholder_obra.jpg" data-id="<?= $obra['id'] ?>"
                                    data-tipo="<?= $obra['tipo'] ?>">
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="font-size: 13px; color: #666; text-align: center; grid-column: 1/-1;">Nenhuma obra marcada
                            nesta hashtag.</p>
                    <?php endif; ?>
                </div>
            </div>
        </aside>
    </main>

    <script src="js/apis-obras.js"></script>
    <script src="js/telainicial.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof carregarImagensCarousel === 'function') {
                carregarImagensCarousel();
            }
        });
    </script>
    <?php renderizarVLibras(); ?>
</body>

</html>