<!DOCTYPE html>
<html lang="pt-BR">
<?php
require '../back-end/php/perfil_dados.php';
require '../back-end/php/telainicial_post.php';

// Tranformando o $row em $perfil, qual vai puxar valores de colunas
$perfil = buscaUsuario();
$posts = postagensFeitas();

?>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../front-end/telainicial.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Reparte</title>
</head>

<body>
  <header>
    <div class="logo">
      <a href="#"><img src="../imagens/logo.png" alt="Logo do site"></a>
    </div>

    <div class="search-box">
      <span class="search-icon">🔍</span>
      <input type="text" class="search-text" placeholder="Procure uma obra...">
    </div>

    <div class="header-controls">
      <a href="../back-end/php/logout.php" class="btn-logout">
        <i class="fas fa-sign-out-alt"></i> Sair
      </a>
    </div>
  </header>

  <main class="main-container">
    <section class="left">
      <aside class="sidebar-left">
        <a class="user-red">
          <div class="user-principal">
            <img src="<?= $perfil['caminho'] ?>" alt="Seu Perfil">
            <strong><?php echo $perfil['nomexi']; ?></strong>
          </div>
        </a>
        <div class="user-list">
          <div class="user-item"><img src="../imagens/usuario2.png"> Usuário 1</div>
          <div class="user-item"><img src="../imagens/usuario1.png"> Usuário 2</div>
          <div class="user-item"><img src="../imagens/usuario7.png"> Usuário 3</div>
          <div class="user-item"><img src="../imagens/usuario8.png"> Usuário 4</div>
          <div class="user-item"><img src="../imagens/usuario9.png"> Usuário 5</div>
          <div class="user-item"><img src="../imagens/usuario10.png"> Usuário 6</div>
          <div class="user-item"><img src="../imagens/usuario11.png"> Usuário 7</div>
          <div class="user-item"><img src="../imagens/usuario12.png"> Usuário 8</div>
          <div class="user-item"><img src="../imagens/usuario13.jpg"> Usuário 9</div>
          <div class="user-item"><img src="../imagens/usuario14.jpg"> Usuário 10</div>
          <div class="user-item"><img src="../imagens/usuario15.png"> Usuário 11</div>
        </div>
      </aside>
    </section>

    <section class="feed">
      <div class="create-post">
        <form action="../back-end/php/criar_postagens.php" method="post">
          <div class="textarea-container">
            <textarea name="titulo_post" rows="1" class="t1 post" placeholder="Escreva o título..."></textarea>
            <textarea name="texto" class="t2 post" placeholder="Escreva sua análise..."></textarea>
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
                <img src="<?= $post['foto']?>" alt="Foto do Usuário V" class="post-user-photo" />
                <h3><?= $post['usuario'] ?></h3>
              </div>
              <div style="display: flex; align-items: center; gap: 10px;">
                <a href="postagem.php?id=<?= $post['id'] ?>" class="fullscreen-icon" title="Ver postagem completa">
                  <i class="fas fa-expand"></i>
                </a>
                <i class="post-set fa-solid fa-bars"></i>
              </div>
            </div>
            <div class="post-body">
              <img id="img-post" id-obra="<?= $post['id_obra'] ?>" tipo-obra="<?= $post['obra_tipo'] ?>" src="" alt="Imagem post">
              <div class="post-content">
                <p><?= $post['titulo'] ?></p>
                <div class="post-text-container" data-post-id="<?= $post['id'] ?>">
                  <p class="post-text-truncated"><?= $post['texto'] ?></p>
                  <button class="expand-button" onclick="expandirTexto('<?= $post['id'] ?>')" style="display: none;">
                    Ver mais...
                  </button>
                </div>
              </div>
            </div>
            <div class="post-buttons">
              <div class="comment-button">
                <i class="fas fa-comment-dots"></i>
              </div>
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
          <i class="fas fa-cog"></i>
        </div>
        <h5 id="h">hashtags</h5>
        <div class="hashtags">
          <a href="#" class="tag">#HistóriadoBrasil</a>
          <a href="#" class="tag">#1984</a>
          <a href="#" class="tag">#Literatura</a>
          <a href="#" class="tag">#LadyGaga</a>
          <a href="#" class="tag">#Neofascismo</a>
          <a href="#" class="tag">#Religião</a>
        </div>
        <h5 id="ob">Obras Populares:</h5>
        <div class="carousel-wrapper">
          <button class="carousel-btn left">&#10094;</button>
          <div class="carousel" id="carousel">
            <img src="../imagens/central.jpg" alt="Imagem 1">
            <img src="../imagens/vento.jpg" alt="Imagem 2">
            <img src="../imagens/tale.jpg" alt="Imagem 3">
          </div>
          <button class="carousel-btn right">&#10095;</button>
        </div>
      </aside>
    </section>
  </main>

  <script src="../back-end/js/telainicial.js"></script>
  <script src="../back-end/js/apis-obras.js"></script>


</body>

</html>