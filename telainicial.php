<!DOCTYPE html>
<html lang="pt-BR">
<?php
session_start();
require 'php/perfil_dados.php';
require 'php/telainicial_post.php';

// Tranformando o $row em $perfil, qual vai puxar valores de colunas
$perfil = buscaUsuario();
$posts = postagensFeitas();

?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/telainicial.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Reparte</title>
</head>

<body>
  <header>
    <div class="logo">
      <a href="#"><img src="images/logo.png" alt="Logo do site"></a>
    </div>

    <div class="search-box">
      <form action="busca.php" method="GET" style="display: flex; width: 100%; align-items: center;">
        <button type="submit" class="search-icon" style="background: none; border: none; cursor: pointer;">🔍</button>
        <input type="text" name="q" class="search-text" placeholder="Procure uma obra, usuário ou hashtag...">
      </form>
    </div>

    <div class="header-actions" style="display: flex; gap: 15px; margin-right: 20px;">
      <a href="chats.php" class="btn-chat" title="Chat">
        <i class="fas fa-comments" style="color: white; font-size: 1.5rem;"></i>
      </a>
    </div>

  </header>

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
          <!-- Usuários estáticos por enquanto, idealmente viriam do BD -->
          <div class="user-item"><img src="images/usuario2.png"> Usuário 1</div>
          <div class="user-item"><img src="images/usuario1.png"> Usuário 2</div>
          <div class="user-item"><img src="images/usuario7.png"> Usuário 3</div>
          <div class="user-item"><img src="images/usuario8.png"> Usuário 4</div>
          <div class="user-item"><img src="images/usuario9.png"> Usuário 5</div>
        </div>
      </aside>
    </section>

    <section class="feed">
      <div class="create-post">
        <form action="php/criar_postagens.php" method="post">
          <div class="textarea-container">
            <textarea name="titulo_post" rows="1" class="t1 post" placeholder="Escreva o título..."></textarea>
            <textarea name="texto" class="t2 post" placeholder="Escreva sua análise... #hashtag"></textarea>
            <input type="hidden" class="id_obra" name="id">
            <input type="hidden" class="tit_obra" name="titulo">
            <input type="hidden" class="ano_obra" name="ano">
            <input type="hidden" class="tipo_obra" name="tipo">
            <input type="hidden" class="autor_obra" name="autor">
            <input type="hidden" class="descricao_obra" name="descricao">
            <input type="hidden" class="img_obra" name="img">
          </div>
          <div class="buttons-row">
            <button id="buscar-obra-btn" class="btn-buscar-obra">
              <i class="fas fa-search"></i>Buscar Obra
            </button>
            <div class="post-actions">
              <button type="submit">Anexar</button>
              <button>Enviar</button>
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
                <i class="post-set fa-solid fa-bars"></i>
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
        <div class="top-icons">
          <i class="fas fa-bell"></i>
          <a href="configuracoes.php" style="color: inherit; text-decoration: none;"><i class="fas fa-cog"></i></a>
        </div>
        <h5 id="h">hashtags</h5>
        <div class="hashtags">
          <a href="busca.php?q=%23HistóriadoBrasil" class="tag">#HistóriadoBrasil</a>
          <a href="busca.php?q=%231984" class="tag">#1984</a>
          <a href="busca.php?q=%23Literatura" class="tag">#Literatura</a>
          <a href="busca.php?q=%23LadyGaga" class="tag">#LadyGaga</a>
          <a href="busca.php?q=%23Neofascismo" class="tag">#Neofascismo</a>
          <a href="busca.php?q=%23Religião" class="tag">#Religião</a>
        </div>
        <h5 id="ob">Obras Populares:</h5>
        <div class="carousel-wrapper">
          <button class="carousel-btn left">&#10094;</button>
          <div class="carousel" id="carousel">
            <img src="images/central.jpg" alt="Imagem 1">
            <img src="images/vento.jpg" alt="Imagem 2">
            <img src="images/tale.jpg" alt="Imagem 3">
          </div>
          <button class="carousel-btn right">&#10095;</button>
        </div>
      </aside>
    </section>
  </main>

  <script src="js/telainicial.js"></script>
  <script src="js/apis-obras.js"></script>


</body>

</html>