<?php
session_start();


// LIMPAR CACHE E FORÇAR DADOS ATUALIZADOS
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// FORÇAR RECARREGAMENTO DA SESSÃO
session_regenerate_id(true);

require 'php/config.php';
include 'vlibras_include.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
  header("Location: login1.php");
  exit();
}

require_once 'php/perfil_dados.php';
require_once 'php/buscar_postagens_usuario.php';
require_once 'php/buscar_comentarios_usuario.php';

// Buscar dados do usuário logado
$perfil = buscaUsuario();

// Verificar se o perfil foi encontrado
if (!$perfil) {
  header("Location: perfil.php");
  exit();
}

// Filtros
$desde = isset($_GET['desde']) ? $_GET['desde'] : 'sempre';
$mais = isset($_GET['mais']) ? $_GET['mais'] : 'recentes';

// Buscar postagens e comentários com filtros aplicados
$postagens = buscarPostagensUsuario($_SESSION['id'], $desde, $mais) ?? [];
$comentarios = buscarComentariosUsuario($_SESSION['id']) ?? [];

// Buscar postagens e comentários com filtros aplicados
$postagens = buscarPostagensUsuario($_SESSION['id'], $desde, $mais) ?? [];
$comentarios = buscarComentariosUsuario($_SESSION['id']) ?? [];

// DEBUG
echo "<script>console.log('ID DA SESSÃO: " . $_SESSION['id'] . "');</script>";
echo "<script>console.log('TOTAL DE POSTAGENS: " . count($postagens) . "');</script>";
if (!empty($postagens)) {
    echo "<script>console.log('ID DO PRIMEIRO POST: " . $postagens[0]['id_usuario'] . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="css/pagperfilusuario.css">
  <link rel="stylesheet" href="css/perfil_posts.css">
  <title>Meu Perfil - RepArte</title>
</head>

<body>

  <div class="container">

    <div class="topbar">
      <div class="logo">
        <a href="telainicial.php" style="text-decoration: none;">
          <span class="brand">Rep<span class="accent">Arte</span></span>
        </a>
      </div>
    </div>

    <section id="profile-template">

      <header class="profile-header">

        <div class="cover"></div>

        <div class="profile-main">

          <div class="avatar-wrap">
            <div class="avatar"
              style="background-image: url('<?= htmlspecialchars($perfil['caminho']) ?>'); background-size: cover; background-position: center;">
              <?php if (empty($perfil['caminho']) || !file_exists($perfil['caminho'])): ?>
                <?= strtoupper(substr($perfil['usuario'], 0, 2)) ?>
              <?php endif; ?>
            </div>
          </div>

          <div class="info">
            <div class="name-row">
              <span class="display-name"><?= htmlspecialchars($perfil['nomexi'] ?? $perfil['usuario']) ?></span>

              <div class="actions">
                <button class="btn ghost" onclick="window.location.href='perfil.php'">Editar Perfil</button>
              </div>
            </div>

            <div class="counters">
              <div class="count">
                <span class="num"><?= count($postagens) ?></span> postagens
              </div>
              <div class="count">
                <span class="num"><?= count($comentarios) ?></span> comentários
              </div>
            </div>

            <?php if (!empty($perfil['descri'])): ?>
              <p class="bio">
                <?= nl2br(htmlspecialchars($perfil['descri'])) ?>
              </p>
            <?php else: ?>
              <p class="bio" style="color: #666;">
                Sem descrição ainda...
              </p>
            <?php endif; ?>
          </div>

        </div>

        <div class="tabs">
          <div class="tab-list">
            <div class="tab active" data-tab="atividade">Atividade</div>
            <div class="tab" data-tab="favoritos">Favoritos</div>
            <div class="tab" data-tab="sobre">Sobre</div>
          </div>
        </div>

      </header>

      <main class="hashtag-container">
        <!-- Feed -->
        <section class="feed-hashtag">
          <?php if (empty($postagens)): ?>
            <div class="no-posts">
              <i class="fas fa-ghost" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
              <p>Você ainda não fez nenhuma postagem.</p>
              <p style="font-size: 12px; margin-top: 10px;">Seja o primeiro a compartilhar suas ideias!</p>
            </div>
          <?php else: ?>
            <?php foreach ($postagens as $post): ?>
              <div class="post">
                <div class="post-header">
                  <div class="post-user">
                    <a href="usuario_perfil.php?id=<?= $post['id_usuario'] ?>"
                      style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px;">
                      <img src="<?= !empty($perfil['caminho']) ? $perfil['caminho'] : $perfil['foto'] ?>"
                        alt="Foto do Usuário" class="post-user-photo" />
                      <h3><?= $perfil['usuario'] ?></h3>
                    </a>
                  </div>
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <a href="postagem.php?id=<?= $post['id'] ?>" class="fullscreen-icon" title="Ver postagem completa">
                      <i class="fas fa-expand"></i>
                    </a>
                    <!-- Container para o menu de opções do post -->
                    <div class="post-set-container">
                      <i class="post-set fa-solid fa-bars" style="color: rgb(255, 102, 0);" title="Opções"></i>

                      <!-- Menu Dropdown -->
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
                        <a href="php/deletar_post.php?id=<?= $post['id'] ?>"
                          onclick="return confirm('Tem certeza que deseja deletar este post?')">
                          <i class="fas fa-trash" style="color: red;"></i> Deletar
                        </a>

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
                      <p><?= $post['obra_titulo'] ?></p>
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

        <!-- Sidebar -->
        <aside class="sidebar-hashtag">
          <!-- Filtros -->
          <div class="sidebar-box filter-box">
            <div class="filter-header">
              <span>Filtros</span>
              <i class="fas fa-filter" style="color: #ff6600;"></i>
            </div>
            <form action="meu_perfil.php" method="GET" id="filterForm">
              <div class="filter-options">
                <div class="filter-group">
                  <label>Período</label>
                  <select name="desde" onchange="this.form.submit()">
                    <option value="sempre" <?= (isset($_GET['desde']) && $_GET['desde'] == 'sempre') ? 'selected' : '' ?>>
                      Todo o tempo</option>
                    <option value="1dia" <?= (isset($_GET['desde']) && $_GET['desde'] == '1dia') ? 'selected' : '' ?>>
                      Últimas 24h</option>
                    <option value="1semana" <?= (isset($_GET['desde']) && $_GET['desde'] == '1semana') ? 'selected' : '' ?>>Última semana</option>
                    <option value="1mes" <?= (isset($_GET['desde']) && $_GET['desde'] == '1mes') ? 'selected' : '' ?>>
                      Último mês</option>
                    <option value="3meses" <?= (isset($_GET['desde']) && $_GET['desde'] == '3meses') ? 'selected' : '' ?>>
                      Últimos 3 meses</option>
                  </select>
                </div>

                <div class="filter-group">
                  <label>Ordenar por</label>
                  <select name="mais" onchange="this.form.submit()">
                    <option value="recentes" <?= (isset($_GET['mais']) && $_GET['mais'] == 'recentes') ? 'selected' : '' ?>>Mais Recentes</option>
                    <option value="curtidos" <?= (isset($_GET['mais']) && $_GET['mais'] == 'curtidos') ? 'selected' : '' ?>>Mais Curtidos</option>
                    <option value="comentados" <?= (isset($_GET['mais']) && $_GET['mais'] == 'comentados') ? 'selected' : '' ?>>Mais Comentados</option>
                  </select>
                </div>
              </div>
            </form>
          </div>

          <!-- Obras Populares -->
          <div class="sidebar-box obras-box">
            <h3>Obras Populares</h3>
            <div class="mini-carousel">
              <?php
              // Buscar obras mais postadas pelo usuário
              $sql_obras = "SELECT o.id, o.titulo, o.tipo, COUNT(p.id) as total
                            FROM obras o
                            JOIN postagens p ON o.id = p.id_obra
                            WHERE p.id_usuario = ?
                            GROUP BY o.id
                            ORDER BY total DESC
                            LIMIT 6";
              $stmt_obras = $con->prepare($sql_obras);
              $stmt_obras->bind_param("s", $_SESSION['id']);
              $stmt_obras->execute();
              $res_obras = $stmt_obras->get_result();
              $obras_usuario = [];
              while ($row = $res_obras->fetch_assoc()) {
                $obras_usuario[] = $row;
              }
              ?>
              <?php if (!empty($obras_usuario)): ?>
                <?php foreach ($obras_usuario as $obra): ?>
                  <a href="obra.php?id=<?= $obra['id'] ?>" title="<?= htmlspecialchars($obra['titulo']) ?>">
                    <img class="carousel-img" src="images/placeholder_obra.jpg" data-id="<?= $obra['id'] ?>"
                      data-tipo="<?= $obra['tipo'] ?>">
                  </a>
                <?php endforeach; ?>
              <?php else: ?>
                <p style="font-size: 13px; color: #666; text-align: center; grid-column: 1/-1;">Nenhuma obra postada
                  ainda.</p>
              <?php endif; ?>
            </div>
          </div>
        </aside>
        </aside>
      </main>

    </section>

  </div>

  <script src="js/apis-obras.js"></script>
  <script src="js/telainicial.js"></script>
  <script src="js/perfil.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {

      // Sistema de abas
      const tabs = document.querySelectorAll(".tab");
      const panel = document.querySelector(".feed-hashtag");

      console.log('Tabs encontradas:', tabs.length);
      console.log('Panel encontrado:', panel);

      tabs.forEach((tab) => {
        tab.addEventListener("click", () => {

          // Remove classe 'active' de todas
          tabs.forEach(t => t.classList.remove("active"));

          // Ativa a aba clicada
          tab.classList.add("active");

          // Pega o tipo de aba
          const tabType = tab.getAttribute('data-tab');
          console.log('Aba clicada:', tabType);

          // Controle da Sidebar
          const sidebar = document.querySelector(".sidebar-hashtag");
          const mainContainer = document.querySelector(".hashtag-container");

          if (tabType === 'favoritos' || tabType === 'sobre') {
            if (sidebar) sidebar.style.display = 'none';
            if (mainContainer) mainContainer.style.gridTemplateColumns = '1fr';
          } else {
            if (sidebar) sidebar.style.display = 'flex';
            if (mainContainer) mainContainer.style.gridTemplateColumns = '1fr 300px';
          }

          // Conteúdo por aba
          if (tabType === 'atividade') {
            // Já está carregado no PHP
            location.reload();
          }
          else if (tabType === 'favoritos') {
            console.log('Iniciando carregamento de favoritos...');

            // Mostrar loading
            panel.innerHTML = `
    <h2 style="grid-column: 1 / -1; color: var(--text); font-size: 20px; margin: 20px 0 10px;">Favoritos</h2>
    <p class="empty">Carregando...</p>
  `;

            // Buscar postagens favoritas via AJAX com timestamp para evitar cache
            const timestamp = new Date().getTime();

            // Buscar postagens favoritas via AJAX
            fetch(`php/buscar_favoritos_perfil.php?t=${timestamp}`)
              .then(response => {
                console.log('Resposta recebida, status:', response.status);
                if (!response.ok) {
                  throw new Error('Erro na resposta do servidor: ' + response.status);
                }
                return response.json();
              })
              .then(favoritos => {
                console.log('Favoritos recebidos:', favoritos);
                console.log('Tipo de favoritos:', typeof favoritos);
                console.log('É array?', Array.isArray(favoritos));
                console.log('Conteúdo completo:', JSON.stringify(favoritos));

                if (!favoritos || favoritos.length === 0) {
                  panel.innerHTML = `
                <h2 style="grid-column: 1 / -1; color: var(--text); font-size: 20px; margin: 20px 0 10px;">Favoritos</h2>
                <p class="empty">Nenhum favorito ainda 👀</p>
              `;
                } else {
                  let favoritosHTML = `
                <div style="grid-column: 1 / -1;">
                  <h2 style="color: var(--text); font-size: 20px; margin: 20px 0 10px;">Meus Favoritos (${favoritos.length})</h2>
                  <div class="favoritos-masonry">
              `;

                  // Função auxiliar para ícones (Cópia exata do busca.js para fidelidade visual)
                  function getIconeTipo(tipo, subtipo) {
                    if (tipo === 'filme') {
                      return subtipo === 'documentário' ?
                        '<i class="fas fa-video" style="color:#ff6600;"></i>' :
                        '<i class="fas fa-film" style="color:#ff6600;"></i>';
                    } else if (tipo === 'serie') {
                      return subtipo === 'documentário' ?
                        '<i class="fas fa-tv" style="color:#E91E63;"></i>' :
                        '<i class="fas fa-tv" style="color:#9C27B0;"></i>';
                    } else if (tipo === 'livro') {
                      return '<i class="fas fa-book" style="color:#4CAF50;"></i>';
                    } else if (tipo === 'arte') {
                      return '<i class="fas fa-paint-brush" style="color:#2196F3;"></i>';
                    } else if (tipo === 'musica') {
                      return '<i class="fas fa-music" style="color:#FFC107;"></i>';
                    }
                    return '';
                  }

                  favoritos.forEach(fav => {
                    const tituloTrunc = fav.titulo_post && fav.titulo_post.length > 30 ?
                      fav.titulo_post.substring(0, 30) + '...' :
                      fav.titulo_post || 'Sem título';

                    // Gera o ícone usando a função padronizada
                    const iconeObra = getIconeTipo(fav.obra_tipo, '');

                    favoritosHTML += `
                  <div class="favorito-card-wrapper">
                    <a href="postagem.php?id=${fav.id_post}" style="text-decoration:none;color:inherit;display:block;">
                      <div class="favorito-card">
                        
                        <!-- Fichinha do Tipo de Obra (Igual busca.php) -->
                        <div style="position:absolute; top:8px; left:8px; z-index:3; background:rgba(0,0,0,0.75); padding:6px 8px; border-radius:6px;">
                          ${iconeObra}
                        </div>

                        <div class="favorito-user-badge">
                          <img src="${fav.foto_usuario || 'images/default-avatar.png'}" 
                               alt="${fav.usuario || 'Usuário'}"
                               onerror="this.src='images/default-avatar.png'">
                        </div>
                        
                        <img class="favorito-obra-img" 
                             id-obra="${fav.id_obra}" 
                             tipo-obra="${fav.obra_tipo}"
                             src="images/placeholder_obra.jpg" 
                             alt="${fav.obra_titulo || 'Obra'}">
                        
                        <div class="favorito-overlay">
                          <h4>${tituloTrunc}</h4>
                          <p>${fav.obra_titulo || 'Obra desconhecida'}</p>
                        </div>
                      </div>
                    </a>
                  </div>
                `;
                  });

                  favoritosHTML += `
                  </div>
                </div>
              `;

                  panel.innerHTML = favoritosHTML;

                  // Carregar imagens das obras
                  // Aguardar um pouco e deixar o apis-obras.js carregar as imagens automaticamente
                  // O apis-obras.js já procura por elementos com atributos id-obra e tipo-obra
                  setTimeout(() => {
                    // Forçar o carregamento manual se necessário
                    document.querySelectorAll('.favorito-obra-img').forEach(img => {
                      const id = img.getAttribute('id-obra');
                      const tipo = img.getAttribute('tipo-obra');

                      if (id && tipo) {
                        // Chamar a API diretamente
                        fetch(`php/api-endpoints.php?action=${tipo === 'filme' ? 'movie' : tipo === 'serie' ? 'tv' : 'track'}_details&id=${id}`)
                          .then(r => r.json())
                          .then(data => {
                            let imgUrl = '';
                            if (tipo === 'musica') {
                              imgUrl = data.album?.images?.[0]?.url || '';
                            } else if (tipo === 'filme' || tipo === 'serie') {
                              imgUrl = data.poster_path ? `https://image.tmdb.org/t/p/w500${data.poster_path}` : '';
                            }
                            if (imgUrl) img.src = imgUrl;
                          })
                          .catch(err => console.error('Erro:', err));
                      }
                    });
                  }, 1000);
                }
              })
              .catch(error => {
                console.error('Erro ao carregar favoritos:', error);
                panel.innerHTML = `
              <h2 style="grid-column: 1 / -1; color: var(--text); font-size: 20px; margin: 20px 0 10px;">Favoritos</h2>
              <p class="empty" style="color: red;">Erro ao carregar favoritos: ${error.message}</p>
            `;
              });
          }
          else if (tabType === 'sobre') {
            panel.innerHTML = `
          <h2 style="grid-column: 1 / -1; color: var(--text); font-size: 20px; margin: 20px 0 10px;">Sobre</h2>
          <div style="grid-column: 1 / -1; padding: 20px; background: rgba(255,255,255,0.02); border-radius: 10px;">
              <p style="color: var(--muted); margin: 0 0 10px;">
                  <strong style="color: var(--text);">Usuário:</strong> @<?= htmlspecialchars($perfil['usuario']) ?>
              </p>
              <p style="color: var(--muted); margin: 0 0 10px;">
                  <strong style="color: var(--text);">Membro desde:</strong> <?= date('d/m/Y', strtotime($perfil['data_perf'] ?? 'now')) ?>
              </p>
              <p style="color: var(--muted); margin: 0;">
                  <strong style="color: var(--text);">Total de atividades:</strong> <?= count($postagens) + count($comentarios) ?>
              </p>
          </div>
        `;
          }

        });
      });

    });
  </script>

</body>

</html>