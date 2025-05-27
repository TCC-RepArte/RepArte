<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../front-end/telainicial.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">  
  <title>Reparte</title>
  <style>
    /* Estilo do botão de logout */
    .btn-logout {
      display: inline-block;
      background-color: #ff3b3b;
      color: white;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      margin-left: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
    }
    
    .btn-logout:hover {
      background-color: #d32f2f;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    
    /* Ajustar o header para incluir o botão */
    header {
      display: flex !important;
      justify-content: space-between !important;
      align-items: center !important;
      position: relative !important;
    }
    
    .header-controls {
      display: flex;
      align-items: center;
      margin-right: 20px;
    }
  </style>
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
      <div class="user-principal">
        <img src="../imagens/usuario3.png" alt="Seu Perfil">
        <strong>Seu Nome</strong>
      </div>
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
        <textarea placeholder="Escreva sua análise..."></textarea>
        <div class="post-actions">
          <button>Anexar</button>
          <button>Enviar</button>
        </div>
      </div>

      <div class="post">
        <div class="post-header">
          <img src="../imagens/usuario1.png" alt="Foto do Usuário V" class="post-user-photo" />
          <h3>Usuário V</h3>
        </div>
        <div class="post-body">
          <img src="../imagens/blackmirror.jpg" alt="Imagem post">
          <p>A série Black Mirror, criada por Charlie Brooker, é uma das obras contemporâneas mais incisivas na
            crítica social, especialmente no que diz respeito à tecnologia, à sociedade de consumo e ao comportamento
            humano diante da evolução digital. Cada episódio funciona como uma espécie de parábola moderna, usando
            narrativas autônomas e distópicas para explorar temas complexos e provocativos.<br></p>
        </div>
        <div class="post-buttons">
  <div class="comment-button">
    <i class="fas fa-comment-dots"></i>
  </div>
  <div class="vote-buttons">
    <i class="fas fa-arrow-up"></i>
    <i class="fas fa-arrow-down"></i>
  </div>
</div>
      </div>

      <div class="post">
        <div class="post-header">
          <img src="../imagens/usuario2.png" alt="Foto do Usuário V" class="post-user-photo" />
          <h3>Usuário W</h3>
        </div>
        <div class="post-body">
          <img src="../imagens/pecadores.jpg" alt="Imagem post">
          <p>Sinners é uma proposta inovadora que mistura drama criminal e familiar com o realismo mágico do
            gótico sulista,evocando a América profunda de Flannery O'Connor. Apesar disso, o diretor Ryan
            Coogler mantém elementos de sua abordagem na Marvel, como o espetáculo visual, influências dos
            super-heróis e um estilo maniqueísta. Michael B. Jordan interpreta dois irmãos gêmeos que retornam
            ao sul para abrir um bar de jive e blues. O filme destaca-se ao explorar profundamente a ligação
            entre música e cultura afro-americana, aproximando-se até de um musical ao fundir blues, tribalismo
            africano e hip hop. O ponto forte da obra está na sua rica ambientação sulista.<br></p>
        </div>
        <div class="post-buttons">
  <div class="comment-button">
    <i class="fas fa-comment-dots"></i>
  </div>
  <div class="vote-buttons">
    <i class="fas fa-arrow-up"></i>
    <i class="fas fa-arrow-down"></i>
  </div>
</div>
      </div>

      <div class="post">
        <div class="post-header">
          <img src="../imagens/usuario4.png" alt="Foto do Usuário V" class="post-user-photo" />
          <h3>Usuário X</h3>
        </div>
        <div class="post-body">
          <img src="../imagens/listrado.jpg" alt="Imagem post">
          <p>O filme mostra perfeitamente a perspectiva de uma criança em meio aos confusos conflitos
             dos adultos na alemanha durante a 2º guerra mundial. Dos sutis gestos de preconceito,
              aos ataques de fúrias sem sentido contra os judeus, tudo observado pelo ponto de vista
              de um menino inocente e desbravador de 8 anos.<br></p>
        </div>
        <div class="post-buttons">
  <div class="comment-button">
    <i class="fas fa-comment-dots"></i>
  </div>
  <div class="vote-buttons">
    <i class="fas fa-arrow-up"></i>
    <i class="fas fa-arrow-down"></i>
  </div>
</div>
      </div>

      <div class="post">
        <div class="post-header">
          <img src="../imagens/usuario5.png" alt="Foto do Usuário V" class="post-user-photo" />
          <h3>Usuário Y</h3>
        </div>
        <div class="post-body">
          <img src="../imagens/vantagens.png" alt="Imagem post">
          <p>A obra As Vantagens de Ser Invisível (The Perks of Being a Wallflower), escrita por Stephen
             Chbosky, é um romance de formação (ou coming-of-age) que aborda com sensibilidade temas como
             adolescência, trauma, identidade, amizade e saúde mental. Através da narrativa em forma de 
             cartas escritas pelo protagonista, Charlie, a história oferece uma perspectiva íntima e 
             tocante sobre o processo de amadurecimento em um mundo cheio de incertezas emocionais.
             O título "As Vantagens de Ser Invisível" é profundamente simbólico: fala sobre a experiência
             de observar o mundo ao redor sem ser notado, mas também sobre a solidão, a repressão de
             sentimentos e o medo de se expressar.<br></p>
        </div>
        <div class="post-buttons">
  <div class="comment-button">
    <i class="fas fa-comment-dots"></i>
  </div>
  <div class="vote-buttons">
    <i class="fas fa-arrow-up"></i>
    <i class="fas fa-arrow-down"></i>
  </div>
</div>
      </div>

      <div class="post">
        <div class="post-header">
          <img src="../imagens/usuario6.png" alt="Foto do Usuário V" class="post-user-photo" />
          <h3>Usuário Z</h3>
        </div>
        <div class="post-body">
          <img src="../imagens/adolescence.jpg" alt="Imagem post">
          <p>A nova minissérie britânica Adolescência (Adolescence), tem causado grande impacto tanto pela
             sua narrativa intensa quanto pela abordagem de temas sociais urgentes. Com apenas quatro episódios,
             a série rapidamente se tornou um fenômeno global, alcançando o topo das audiências em mais de 70 
             países e recebendo aclamação quase unânime da crítica especializada. A trama gira em torno de Jamie
             Miller, um garoto de 13 anos acusado de assassinar uma colega de escola. A partir desse evento chocante,
            a série mergulha nas reações da família, da polícia e de uma psicóloga forense, enquanto tentam compreender
             o que realmente aconteceu.<br></p>
        </div>
       <div class="post-buttons">
  <div class="comment-button">
    <i class="fas fa-comment-dots"></i>
  </div>
  <div class="vote-buttons">
    <i class="fas fa-arrow-up"></i>
    <i class="fas fa-arrow-down"></i>
  </div>
</div>
      </div>

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

      </div>
    </aside>
    </section>
  </main>
</body>
</html>
