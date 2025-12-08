<!DOCTYPE html>
<html lang="pt-BR">
<?php
session_start();
require 'php/perfil_dados.php';
require 'php/telainicial_post.php';
require 'php/vlibras_config.php';
require 'php/buscar_usuarios.php';

// Tranformando o $row em $perfil, qual vai puxar valores de colunas
$perfil = buscaUsuario();
$posts = postagensFeitas();
$outros_usuarios = buscarOutrosUsuarios();

$hashtags_populares = buscarHashtagsPopulares();
$obras_populares = buscarObrasPopulares();

if (isset($_SESSION['id'])) {
  $sql_fav = "SELECT id_post FROM favoritos WHERE id_usuario = ?";
  $stmt_fav = $con->prepare($sql_fav);
  $stmt_fav->bind_param("s", $_SESSION['id']);
  $stmt_fav->execute();
  $res_fav = $stmt_fav->get_result();

  // Guardo cada ID encontrado na minha lista
  while ($row_fav = $res_fav->fetch_assoc()) {
    $meus_favoritos[] = $row_fav['id_post'];
  }
}

?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="css/telainicial.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Reparte</title>
</head>

<body>
  <header>
    <button class="menu-toggle" id="menu-toggle">
      <i class="fas fa-bars"></i>
    </button>

    <div class="logo">
      <a href="#"><img src="images/logo.png" alt="Logo do site"></a>
    </div>

    <div class="search-box">
      <form action="busca.php" method="GET" style="display: flex; width: 100%; align-items: center;">
        <button type="submit" class="search-icon" style="background: none; border: none; cursor: pointer;">🔍</button>
        <input type="text" name="q" class="search-text" placeholder="Procure uma obra, usuário ou hashtag...">
      </form>
    </div>

    <div class="header-actions">
      <div class="notif-container">
        <i class="fas fa-bell" id="notif-icon"></i>
        <span class="notif-badge" id="notif-badge" style="display: none;">0</span>

        <!-- Dropdown de notificações -->
        <div class="notif-dropdown" id="notif-dropdown">
          <div class="notif-header">
            <h4>Notificações</h4>
            <button class="marcar-todas-lidas" onclick="marcarTodasLidas()">Marcar todas como lidas</button>
          </div>
          <div class="notif-list" id="notif-list">
            <div class="notif-empty">Carregando...</div>
          </div>
          <a href="notificacoes.php" class="notif-footer">Ver preferências</a>
        </div>
      </div>
      <a href="configuracoes.php" style="color: inherit; text-decoration: none;"><i class="fas fa-cog"></i></a>
      <?php if (isset($_SESSION['id']) && $_SESSION['id'] === 'rFRCxqU-Yze'): ?>
        <a href="admin.php" style="color: inherit; text-decoration: none;" title="Painel Admin">
          <i class="fas fa-shield-alt"></i>
        </a>
      <?php endif; ?>
    </div>

  </header>

  <!-- Mobile Menu Overlay -->
  <div class="mobile-menu" id="mobile-menu">
    <div class="mobile-menu-header">
      <span class="mobile-menu-title">Menu</span>
      <button class="close-menu" id="close-menu"><i class="fas fa-times"></i></button>
    </div>
    <div class="mobile-menu-content">
      <!-- Content injected via JS or duplicates of sidebars -->
      <div class="mobile-section">
        <h3><i class="fas fa-user-circle"></i> Meu Perfil</h3>
        <a href="meu_perfil.php" class="mobile-link">
          <div class="user-principal-mobile">
            <img src="<?= $perfil['caminho'] ?>" alt="Seu Perfil">
            <strong><?php echo $perfil['nomexi']; ?></strong>
          </div>
        </a>
      </div>

      <div class="mobile-section">
        <h3><i class="fas fa-users"></i> Outros Usuários</h3>
        <div class="user-list-mobile">
          <?php if (!empty($outros_usuarios)): ?>
            <?php foreach ($outros_usuarios as $usuario): ?>
              <a href="usuario_perfil.php?id=<?= $usuario['id'] ?>" class="user-item">
                <img src="<?= $usuario['caminho'] ?>"
                  alt="<?= htmlspecialchars($usuario['nomexi'] ?? $usuario['usuario']) ?>">
                <span><?= htmlspecialchars($usuario['nomexi'] ?? $usuario['usuario']) ?></span>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="empty-msg">Nenhum outro usuário.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="mobile-section">
        <h3><i class="fas fa-hashtag"></i> Hashtags</h3>
        <div class="hashtags-mobile">
          <?php if (!empty($hashtags_populares)): ?>
            <?php foreach ($hashtags_populares as $tag): ?>
              <?php $nome_tag = str_replace('#', '', $tag['nome']); ?>
              <a href="hashtag.php?tag=<?= $nome_tag ?>" class="tag">#<?= $nome_tag ?></a>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="empty-msg">Nenhuma hashtag.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="mobile-section">
        <h3><i class="fas fa-palette"></i> Obras Populares</h3>
        <div class="obras-mobile-grid">
          <?php if (!empty($obras_populares)): ?>
            <?php foreach ($obras_populares as $obra): ?>
              <a href="obra.php?id=<?= $obra['id'] ?>" class="obra-mobile-item">
                <img src="images/placeholder_obra.jpg" data-id="<?= $obra['id'] ?>" data-tipo="<?= $obra['tipo'] ?>"
                  alt="<?= $obra['titulo'] ?>">
                <span><?= mb_strimwidth($obra['titulo'], 0, 15, '...') ?></span>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>


  <main class="main-container">
    <section class="left">
      <aside class="sidebar-left">
        <a href="meu_perfil.php" class="user-red" style="text-decoration: none; color: inherit;">
          <div class="user-principal">
            <img src="<?= $perfil['caminho'] ?>" alt="Seu Perfil">
            <strong><?php echo $perfil['nomexi']; ?></strong>
          </div>
        </a>
        <div class="user-list">
          <?php if (!empty($outros_usuarios)): ?>
            <?php foreach ($outros_usuarios as $usuario): ?>
              <a href="usuario_perfil.php?id=<?= $usuario['id'] ?>" class="user-item"
                style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px;">
                <img src="<?= $usuario['caminho'] ?>"
                  alt="<?= htmlspecialchars($usuario['nomexi'] ?? $usuario['usuario']) ?>">
                <span class="user-name"><?= htmlspecialchars($usuario['nomexi'] ?? $usuario['usuario']) ?></span>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="color: #666; font-size: 12px; text-align: center; padding: 20px 0;">Nenhum outro usuário cadastrado
              ainda.</p>
          <?php endif; ?>
        </div>
      </aside>
    </section>

    <section class="feed">
      <div class="create-post">
        <form action="php/criar_postagens.php" method="post">
          <div class="textarea-container">
            <textarea name="titulo_post" rows="1" class="t1 post" placeholder="Título"></textarea>
            <textarea name="texto" class="t2 post" placeholder="Escreva a sua análise..."></textarea>

            <!-- Botão de seleção de obra (quadrado cinza com +) -->
            <div id="obra-preview-btn" class="obra-preview-button">
              <i class="fas fa-plus"></i>
              <img src="" alt="Obra selecionada" style="display: none;">
              <button type="button" class="remover-obra-btn" title="Remover obra">×</button>
            </div>

            <!-- Campos hidden para armazenar dados da obra -->
            <input type="hidden" class="id_obra" name="id">
            <input type="hidden" class="tit_obra" name="titulo">
            <input type="hidden" class="ano_obra" name="ano">
            <input type="hidden" class="tipo_obra" name="tipo">
            <input type="hidden" class="autor_obra" name="autor">
            <input type="hidden" class="descricao_obra" name="descricao">
            <input type="hidden" class="img_obra" name="img">
          </div>
          <div class="buttons-row">
            <div class="post-actions">
              <button type="submit">Enviar</button>
            </div>
          </div>
        </form>
      </div>

      <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
          <div class="post">
            <div class="post-header">
              <div class="post-user">
                <a href="usuario_perfil.php?id=<?= $post['id_usuario'] ?>"
                  style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px;">
                  <img src="<?= $post['foto'] ?>" alt="Foto do Usuário V" class="post-user-photo" />
                  <h3><?= $post['usuario'] ?></h3>
                </a>
              </div>
              <div style="display: flex; align-items: center; gap: 10px;">
                <a href="postagem.php?id=<?= $post['id'] ?>" class="fullscreen-icon" title="Ver postagem completa">
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
                <img id="img-post" id-obra="<?= $post['id_obra'] ?>" tipo-obra="<?= $post['obra_tipo'] ?>" src=""
                  alt="Imagem post">
              </a>
              <div class="post-content">
                <a href="obra.php?id=<?= $post['id_obra'] ?>" class="obra-title-link">
                  <p><?= $post['titulo'] ?></p>
                </a>
                <div class="post-text-container" data-post-id="<?= $post['id'] ?>">
                  <div class="post-text-truncated"><?= nl2br(htmlspecialchars($post['texto'])) ?></div>
                  <button class="expand-button" onclick="expandirTexto('<?= $post['id'] ?>')" style="display: none;">
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
    <section class="right">
      <aside class="sidebar-right">
        <h5 id="h">Hashtags:</h5>
        <div class="hashtags" style="display: flex; flex-wrap: wrap; gap: 8px;">
          <?php if (!empty($hashtags_populares)): ?>
            <?php foreach ($hashtags_populares as $tag): ?>
              <!-- Remove o # se já vier do banco com ele -->
              <?php $nome_tag = str_replace('#', '', $tag['nome']); ?>
              <a href="hashtag.php?tag=<?= $nome_tag ?>" class="tag">#<?= $nome_tag ?></a>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="color: #666; font-size: 12px;">Nenhuma hashtag ainda.</p>
          <?php endif; ?>
        </div>

        <h5 id="ob">Obras Populares:</h5>
        <?php if (!empty($obras_populares)): ?>
          <div class="carousel-wrapper">
            <button class="carousel-btn left" onclick="moverCarrossel(-1)">&#10094;</button>

            <div class="carousel" id="carousel">
              <?php foreach ($obras_populares as $obra): ?>
                <a href="obra.php?id=<?= $obra['id'] ?>" title="<?= $obra['titulo'] ?>"
                  style="position: relative; display: block;">
                  <img class="carousel-img" src="images/placeholder_obra.jpg" data-id="<?= $obra['id'] ?>"
                    data-tipo="<?= $obra['tipo'] ?>" alt="<?= $obra['titulo'] ?>"
                    style="min-width: 80px; height: 120px; object-fit: cover;">
                </a>
              <?php endforeach; ?>
            </div>

            <button class="carousel-btn right" onclick="moverCarrossel(1)">&#10095;</button>
          </div>
        <?php else: ?>
          <p style="color: #666; font-size: 12px; text-align: center; margin-top: 10px;">Nenhuma obra popular
            ainda.</p>
        <?php endif; ?>
      </aside>
    </section>
  </main>

  <script src="js/criarID.js"></script>
  <script src="js/telainicial.js"></script>
  <script src="js/apis-obras.js"></script>
  <script src="js/notificacoes.js"></script>

  <?php renderizarVLibras(); ?>

  <?php if (isset($_GET['post_enviado'])): ?>
<script>
Swal.fire({
    title: 'Postagem enviada!',
    text: 'Sua análise foi publicada com sucesso.',
    icon: 'success',
    confirmButtonText: 'OK'
});
</script>
<?php endif; ?>


</body>

</html>