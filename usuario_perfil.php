<?php

session_start();
require 'php/config.php';
include 'vlibras_include.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
  header("Location: login1.php");
  exit();
}

// Verificar se o ID do usuário foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
  header("Location: telainicial.php");
  exit();
}

$id_usuario = $_GET['id'];

// Se for o próprio perfil, redireciona para meu_perfil.php
if ($id_usuario == $_SESSION['id']) {
  header("Location: meu_perfil.php");
  exit();
}

require_once 'php/buscar_usuario_por_id.php';
require_once 'php/buscar_postagens_usuario.php';
require_once 'php/buscar_comentarios_usuario.php';

// Buscar dados do usuário
$perfil = buscarUsuarioPorId($id_usuario);

if (!$perfil) {
  header("Location: telainicial.php");
  exit();
}

$postagens = buscarPostagensUsuario($id_usuario) ?? [];
$comentarios = buscarComentariosUsuario($id_usuario) ?? [];

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
  <link rel="stylesheet" href="css/pagperfilusuario.css">
  <title>Perfil de <?= htmlspecialchars($perfil['nomexi'] ?? $perfil['usuario']) ?> - RepArte</title>
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
              style="background-image: url('<?= htmlspecialchars($perfil['caminho']) ?>'); background-size: cover; background-position: center; cursor: default;">
              <?php if (empty($perfil['caminho']) || !file_exists($perfil['caminho'])): ?>
                <?= strtoupper(substr($perfil['usuario'], 0, 2)) ?>
              <?php endif; ?>
            </div>
          </div>

          <div class="info">
            <div class="name-row">
              <span class="display-name"><?= htmlspecialchars($perfil['nomexi'] ?? $perfil['usuario']) ?></span>

              <div class="actions">
                <button class="btn ghost" onclick="window.history.back()">Voltar</button>
                <button class="btn accent" onclick="window.location.href='chats.php'">
                  <i class="bi bi-chat-dots"></i>
                </button>
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

      <main>
        <div class="tab-panels" id="tab-content">
          <!-- Conteúdo de Atividade (padrão) -->
          <h2 style="grid-column: 1 / -1; color: var(--text); font-size: 20px; margin: 20px 0 10px;">Postagens de
            <?= htmlspecialchars($perfil['nomexi'] ?? $perfil['usuario']) ?>
          </h2>

          <?php if (!empty($postagens)): ?>
            <?php foreach ($postagens as $post): ?>
              <div class="post-card">
                <div class="title">
                  <a href="postagem.php?id=<?= $post['id'] ?>" style="color: var(--text); text-decoration: none;">
                    <?= htmlspecialchars($post['titulo_post']) ?>
                  </a>
                </div>
                <div class="meta">
                  <?= htmlspecialchars($post['obra_titulo']) ?> • <?= date('d/m/Y', strtotime($post['data_post'])) ?>
                </div>
                <div class="meta" style="margin-top: 8px; font-size: 12px;">
                  <?= htmlspecialchars(substr($post['texto'], 0, 100)) ?>     <?= strlen($post['texto']) > 100 ? '...' : '' ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty">Este usuário ainda não fez nenhuma postagem...</div>
          <?php endif; ?>
        </div>
      </main>

    </section>

  </div>

  <script src="js/perfil.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {

      // Desabilitar clique no avatar (não é o perfil do usuário logado)
      const avatarWrap = document.querySelector(".avatar-wrap");
      if (avatarWrap) {
        avatarWrap.style.cursor = "default";
        avatarWrap.querySelector(".avatar").style.cursor = "default";
      }

      // Remover texto "Alterar foto"
      if (avatarWrap) {
        const style = document.createElement('style');
        style.textContent = '.avatar-wrap::after { display: none; }';
        document.head.appendChild(style);
      }

      // Sistema de abas
      const tabs = document.querySelectorAll(".tab");
      const panel = document.getElementById("tab-content");

      tabs.forEach((tab) => {
        tab.addEventListener("click", () => {

          // Remove classe 'active' de todas
          tabs.forEach(t => t.classList.remove("active"));

          // Ativa a aba clicada
          tab.classList.add("active");

          // Pega o tipo de aba
          const tabType = tab.getAttribute('data-tab');

          // Conteúdo por aba
          if (tabType === 'atividade') {
            // Já está carregado no PHP
            location.reload();
          }
          else if (tabType === 'favoritos') {
            panel.innerHTML = `
                    <h2 style="grid-column: 1 / -1; color: var(--text); font-size: 20px; margin: 20px 0 10px;">Favoritos</h2>
                    <p class="empty">Nenhum favorito disponível 👀</p>
                `;
          }
          else if (tabType === 'sobre') {
            panel.innerHTML = `
                    <h2 style="grid-column: 1 / -1; color: var(--text); font-size: 20px; margin: 20px 0 10px;">Sobre</h2>
                    <div style="grid-column: 1 / -1; padding: 20px; background: rgba(255,255,255,0.02); border-radius: 10px;">
                        <p style="color: var(--muted); margin: 0 0 10px;">
                            <strong style="color: var(--text);">Usuário:</strong> @<?= htmlspecialchars($perfil['usuario']) ?>
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