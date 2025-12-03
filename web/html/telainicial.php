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

        <!-- Ícone de configurações -->
        <a href="../html/configuracoes.html" class="btn-config">
            <i class="fas fa-cog"></i>
        </a>

        <!-- Foto do usuário logado (CORRIGIDO) -->
        <a href="../front-end/pagperfilusuario.php" class="btn-perfil"> 
            <img class="foto-perfil-header" 
                 src="<?= $perfil['foto'] ?>" 
                 alt="Foto de perfil">
        </a>
    </div>
</header>

  <main class="main-container">

      <section class="left">
  <aside class="sidebar-left">

      <h3 class="titulo-lista">Quem seguir</h3>

      <div class="lista-usuarios"></div>

      <!-- TEMPLATE OCULTO (não mexe) -->
      <template id="usuario-template">
          <div class="usuario-item">
              <img class="usuario-foto" src="../imagens/padrao_usuario.png" alt="Foto de perfil">
              <div class="usuario-info">
                  <p class="usuario-nome">Nome de usuário</p>
              </div>
              <button class="usuario-seguir-btn">Seguir</button>
          </div>
      </template>

  </aside>
</section>

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

                <!-- MENU DE OPÇÕES -->
                <div class="post-menu">
                  <button class="menu-toggle">
                    <i class="fa-solid fa-bars"></i>
                  </button>

                  <div class="menu-options oculto">
                    <button class="menu-item" data-action="denunciar" data-id="<?= $post['id'] ?>">
                      Denunciar postagem
                    </button>
                    <hr>
                    <button class="menu-item" data-action="favoritar" data-id="<?= $post['id'] ?>">
                      Favoritar
                    </button>
                    <hr>
                    <button class="menu-item" data-action="copiar" data-id="<?= $post['id'] ?>">
                      Copiar link
                    </button>
                  </div>
                </div>
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
    
        <h3 id="h">hashtags</h3>
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
          <div class="carousel" id="carousel">
            <img src="../imagens/central.jpg" alt="Imagem 1">
            <img src="../imagens/vento.jpg" alt="Imagem 2">
            <img src="../imagens/tale.jpg" alt="Imagem 3">
          </div>
        </div>
  </main>

  
<script>
// Aqui vem lista que vai do PHP.
const usuariosSugeridos = window.usuariosSugeridos || [];

// Elementos
const lista = document.querySelector(".lista-usuarios");
const template = document.querySelector("#usuario-template");

lista.innerHTML = "";

// Se não houver usuários aparece 3 placeholders
const quantidade = usuariosSugeridos.length > 0 ? usuariosSugeridos.length : 3;

for (let i = 0; i < quantidade; i++) {
    const clone = template.content.cloneNode(true);

    const usuario = usuariosSugeridos[i];

    clone.querySelector(".usuario-foto").src = usuario?.foto || "../imagens/padrao_usuario.png";
    clone.querySelector(".usuario-nome").textContent = usuario?.nome || "Nome de usuário";

    lista.appendChild(clone);
}
</script>

<script>
 document.addEventListener("click", function(event) {
  const toggle = event.target.closest(".menu-toggle");
  const postMenu = event.target.closest(".post-menu");

  // Fechar todos os menus
  document.querySelectorAll(".menu-options").forEach(m => m.classList.add("oculto"));

  // Se clicou no botão de menu, abrir só o dele
  if (toggle && postMenu) {
    postMenu.querySelector(".menu-options").classList.toggle("oculto");
  }
});
</script>

  <script src="../back-end/js/telainicial.js"></script>
  <script src="../back-end/js/apis-obras.js"></script>

</body>
</html>
