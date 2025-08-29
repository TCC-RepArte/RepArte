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
            <button id="buscar-obra-btn" class="btn-buscar-obra">
              <i class="fas fa-search"></i>Buscar Obra
            </button>
          </div>
          <div class="post-actions">
            <button type="submit">Anexar</button>
            <button>Enviar</button>
          </div>
        </form>
      </div>
      <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>

                            <!-- POSTAGENS -->

          <div class="postback"> <!-- bloco cinza que engloba tudo -->
  <div class="post-header">
    <div class="post-user">
      <img src="<?= $post['foto']?>" alt="Foto do Usuário V" class="post-user-photo" />
      <h3><?= $post['usuario'] ?></h3>
    </div>
    <i class="post-set fa-solid fa-bars"></i>
  </div>
</div>
<div class="post-container">
  <div class="post-body">
    <img id="img-post" id-obra="<?= $post['id_obra'] ?>" tipo-obra="<?= $post['obra_tipo'] ?>" src="" alt="Imagem post">

    <div class="post-texto">
      <p class="titulo"><?= $post['titulo'] ?></p>
      <p class="texto"><?= $post['texto'] ?></p>
    </div>
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
  <script>

    carregarImagens();

    perfil_red = document.querySelector('.user-red');
    perfil_red.addEventListener("click", function () {

      window.location.href = "../back-end/php/perfil_dados.php"

    });

    // Referenciando o input para envio do id das obras
    let idInput = document.querySelector('.id_obra');
    let tituloInput = document.querySelector('.tit_obra');
    let autorInput = document.querySelector('.autor_obra');
    let descricaoInput = document.querySelector('.descricao_obra');
    let anoInput = document.querySelector('.ano_obra');
    let tipoInput = document.querySelector('.tipo_obra');
    let imagemInput = document.querySelector('.img_obra');

    // Script para o painel de busca de obras
    document.addEventListener('DOMContentLoaded', function () {
      console.log("Inicializando script do painel de busca");

      const botaoPost = document.querySelectorAll('.post-set').forEach(botao =>{
        botao.onmouseover = function(){
        botao.style.color = '#ff6600';
      }

      botao.onmouseout = function(){
        botao.style.color = '#272727';
      }

      });
      
      // Remover qualquer painel existente para evitar duplicatas
      const painelExistente = document.getElementById('painel-busca-obras');
      if (painelExistente) {
        painelExistente.remove();
      }

      // Criar novo painel
      const painelBuscaObras = document.createElement('div');
      painelBuscaObras.id = 'painel-busca-obras';
      painelBuscaObras.className = 'painel-busca-obras';
      painelBuscaObras.style.backgroundColor = '#130922';
      painelBuscaObras.innerHTML = `
        <div class="painel-cabecalho">
          <h3>Buscar Obra para Análise</h3>
          <button class="fechar-painel">×</button>
        </div>
        <div class="painel-conteudo">
          <div class="painel-layout">
            <div class="filtros-tipos">
              <h4>Tipos de Obra</h4>
              <div class="opcoes-filtro">
                <label class="opcao-filtro">
                  <input type="checkbox" value="filme" checked> Filmes
                </label>
                <label class="opcao-filtro">
                  <input type="checkbox" value="serie" checked> Séries
                </label>
                <label class="opcao-filtro">
                  <input type="checkbox" value="livro" checked> Livros
                </label>
                <label class="opcao-filtro">
                  <input type="checkbox" value="arte" checked> Pinturas/Arte
                </label>
                <label class="opcao-filtro">
                  <input type="checkbox" value="musica" checked> Músicas
                </label>
              </div>
            </div>
            <div class="conteudo-principal">
              <div class="campo-busca">
                <input type="text" id="busca-obra-input" placeholder="Digite o nome da obra...">
                <button class="btn-buscar-painel">Buscar</button>
              </div>
              <div id="resultados-busca" class="resultados-busca">
              </div>
            </div>
          </div>
        </div>
      `;

      // Adicionar painel ao body
      document.body.appendChild(painelBuscaObras);

      // Criar overlay
      const overlay = document.createElement('div');
      overlay.className = 'overlay';
      document.body.appendChild(overlay);

      // Obter referências para elementos do painel
      const buscaObraInput = painelBuscaObras.querySelector('#busca-obra-input');
      const resultadosBuscaEl = painelBuscaObras.querySelector('#resultados-busca');
      const checkboxesTipos = painelBuscaObras.querySelectorAll('.opcao-filtro input[type="checkbox"]');
      const opcoesLabels = painelBuscaObras.querySelectorAll('.opcao-filtro');
      const buscarBtn = painelBuscaObras.querySelector('.btn-buscar-painel');
      const fecharBtn = painelBuscaObras.querySelector('.fechar-painel');

      // Adicionar evento de clique no botão fechar
      fecharBtn.addEventListener('click', function () {
        painelBuscaObras.style.display = 'none';
        overlay.style.display = 'none';
      });

      // Obter botão de buscar obra na página principal
      const buscarObraBtn = document.getElementById('buscar-obra-btn');

      // Configurar mensagem inicial centralizada
      resultadosBuscaEl.innerHTML = `
        <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
          <p style="color:#aaa;margin:0;text-align:center;">Digite um termo para buscar.</p>
        </div>
      `;

      // Função para obter tipos selecionados
      function getTiposSelecionados() {
        return Array.from(checkboxesTipos)
          .filter(checkbox => checkbox.checked)
          .map(checkbox => checkbox.value);
      }

      // Função para exibir ícones para diferentes tipos de obras
      function getIconeTipo(tipo, subtipo) {
        if (tipo === 'filme') {
          return subtipo === 'documentário' ?
            '<i class="fas fa-video" style="color:#ff6600;margin-right:5px;"></i>' :
            '<i class="fas fa-film" style="color:#ff6600;margin-right:5px;"></i>';
        } else if (tipo === 'serie') {
          return subtipo === 'documentário' ?
            '<i class="fas fa-tv" style="color:#E91E63;margin-right:5px;"></i>' :
            '<i class="fas fa-tv" style="color:#9C27B0;margin-right:5px;"></i>';
        } else if (tipo === 'livro') {
          return '<i class="fas fa-book" style="color:#4CAF50;margin-right:5px;"></i>';
        } else if (tipo === 'arte') {
          return '<i class="fas fa-paint-brush" style="color:#2196F3;margin-right:5px;"></i>';
        } else if (tipo === 'musica') {
          return '<i class="fas fa-music" style="color:#FFC107;margin-right:5px;"></i>';
        } else {
          return '';
        }
      }

      // Função para exibir detalhes da obra
      function exibirDetalhesObra(obra) {

        // Determinar campos adicionais baseado no tipo
        let camposAdicionais = '';

        switch (obra.tipo) {
          case 'filme':
            camposAdicionais = `
              <div class="campo-info">
                <span class="rotulo">Gênero:</span>
                <span>${obra.genero || 'Não informado'}</span>
              </div>
            `;
            break;
          case 'serie':
            camposAdicionais = `
              <div class="campo-info">
                <span class="rotulo">Criador:</span>
                <span>${obra.criador || 'Não informado'}</span>
              </div>
              <div class="campo-info">
                <span class="rotulo">Temp.:</span>
                <span>${obra.temporadas || 'N/A'}</span>
              </div>
            `;
            break;
          case 'livro':
            camposAdicionais = `
              <div class="campo-info">
                <span class="rotulo">Autor:</span>
                <span>${obra.autor || 'Não informado'}</span>
              </div>
            `;
            break;
          case 'arte':
            camposAdicionais = `
              <div class="campo-info">
                <span class="rotulo">Artista:</span>
                <span>${obra.autor || 'Não informado'}</span>
              </div>
            `;
            break;
          case 'musica':
            // Incluir player de amostra se disponível
            const audioPlayer = obra.amostra ? `
              <div style="margin-top:10px;width:100%;">
                <p style="font-size:12px;margin:0 0 5px 0;color:#aaa;">Amostra da música:</p>
                <audio controls style="width:100%;height:35px;border-radius:5px;">
                  <source src="${obra.amostra}" type="audio/mpeg">
                  Seu navegador não suporta áudio HTML5.
                </audio>
              </div>
            ` : '';

            camposAdicionais = `
              <div class="campo-info">
                <span class="rotulo">Artista:</span>
                <span>${obra.autor || 'Não informado'}</span>
              </div>
              <div class="campo-info">
                <span class="rotulo">Álbum:</span>
                <span>${obra.album || 'Não informado'}</span>
              </div>
              <div class="campo-info">
                <span class="rotulo">Duração:</span>
                <span>${obra.duracao || 'N/A'}</span>
              </div>
              <div class="campo-info">
                <span class="rotulo">Popular.:</span>
                <span>${obra.popularidade || 'N/A'}</span>
              </div>
              ${audioPlayer}
            `;
            break;
          default:
            camposAdicionais = '';
        }


        const icone = getIconeTipo(obra.tipo, obra.subtipo);

        // Não quebrar o título nos detalhes da obra
        const tituloCompleto = obra.titulo;

        // Adicionar etiquetas para documentários e séries
        const etiquetaDoc = obra.subtipo === 'documentário' ?
          '<span style="background:#E91E63;color:white;font-size:10px;padding:1px 3px;border-radius:3px;margin-left:4px;">DOC</span>' :
          '';

        const etiquetaSerie = obra.tipo === 'serie' && obra.subtipo !== 'documentário' ?
          '<span style="background:#9C27B0;color:white;font-size:10px;padding:1px 3px;border-radius:3px;margin-left:4px;">SÉRIE</span>' :
          '';

        // Limitar descrição a um número máximo de caracteres
        let descricaoEncurtada = obra.descricao || 'Descrição não disponível';
        if (descricaoEncurtada.length > 300) {
          descricaoEncurtada = descricaoEncurtada.substring(0, 300) + '...';
        }

        // Determinar o estilo de fonte baseado no tipo da obra
        let estiloFonte = obra.tipo === 'livro' ? 'normal' : 'inherit';

        // Determinar cores baseadas no tipo da obra para elementos visuais
        let corPrimaria, corSecundaria;
        switch (obra.tipo) {
          case 'filme':
            corPrimaria = '#ff6600';
            corSecundaria = '#cc5200';
            break;
          case 'serie':
            corPrimaria = '#9C27B0';
            corSecundaria = '#7B1FA2';
            break;
          case 'livro':
            corPrimaria = '#4CAF50';
            corSecundaria = '#388E3C';
            break;
          case 'arte':
            corPrimaria = '#2196F3';
            corSecundaria = '#1976D2';
            break;
          case 'musica':
            corPrimaria = '#FFC107';
            corSecundaria = '#FFA000';
            break;
          default:
            corPrimaria = '#ff6600';
            corSecundaria = '#cc5200';
        }

        const detalhesHTML = `
          <div class="obra-detalhes-container" style="text-align:center;height:100%;display:flex;flex-direction:column;">
            <div class="detalhes-header" style="margin-bottom:8px;text-align:left;">
              <button class="voltar-btn" style="background:transparent;border:none;color:#ff6600;cursor:pointer;padding:2px 0;font-weight:bold;font-size:12px;">&larr; Voltar à busca</button>
            </div>
            
            <div class="obra-content" style="flex:1;display:flex;flex-direction:column;align-items:center;overflow-y:auto;padding:0 15px;">
              <div class="obra-poster-container" style="position:relative;margin-bottom:15px;padding:5px;border-radius:6px;background:linear-gradient(135deg, ${corPrimaria}, ${corSecundaria});box-shadow:0 4px 15px rgba(0,0,0,0.3);">
                <img src="${obra.imagem}" referrerpolicy="no-referrer" alt="${obra.titulo}" style="width:150px;border-radius:3px;display:block;">
              </div>
              
              <div class="obra-info-completa" style="width:100%;max-width:400px;">
                <h3 style="margin:0 0 10px 0;font-size:18px;color:#fff;line-height:1.3;position:relative;padding-bottom:8px;">
                  ${icone}${tituloCompleto}${etiquetaDoc}${etiquetaSerie}
                  <span style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:40px;height:2px;background:${corPrimaria};"></span>
                </h3>
                
                <div class="info-basica" style="margin-bottom:12px;font-size:14px;background:#130922;padding:10px;border-radius:8px;">
                  ${camposAdicionais}
                  <div class="campo-info">
                    <span class="rotulo">Ano:</span>
                    <span>${obra.ano || 'N/A'}</span>
                  </div>
                </div>
                
                <p style="margin:0 0 15px 0;font-size:14px;line-height:1.4;color:#ddd;text-align:left;">
                  ${descricaoEncurtada}
                </p>
                
                <button class="selecionar-para-analise" 
                        style="background:${corPrimaria};color:white;border:none;padding:8px 16px;border-radius:5px;cursor:pointer;font-weight:bold;font-size:14px;transition:all 0.2s ease;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
                  Selecionar para Análise
                </button>
              </div>
            </div>
          </div>
        `;

        resultadosBuscaEl.innerHTML = detalhesHTML;

        // Aplicar estilo de fonte para descrição baseado no tipo da obra
        if (obra.tipo === 'livro') {
          const descricaoParagrafo = resultadosBuscaEl.querySelector('.obra-info-completa p');
          if (descricaoParagrafo) {
            descricaoParagrafo.style.fontWeight = 'normal';
          }
        }

        // Adicionar hover ao botão
        const selecionarBtn = document.querySelector('.selecionar-para-analise');
        if (selecionarBtn) {
          // Armazenar os dados da obra diretamente no botão como uma propriedade
          selecionarBtn.obraData = obra;

          selecionarBtn.addEventListener('mouseover', function () {
            this.style.background = corSecundaria;
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
          });

          selecionarBtn.addEventListener('mouseout', function () {
            this.style.background = corPrimaria;
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
          });

          selecionarBtn.addEventListener('click', function () {

            // Enviando dados da obra pelos input's
            let dadosObras = [
              idInput.value = selecionarBtn.obraData.id,
              tituloInput.value = selecionarBtn.obraData.titulo,
              autorInput.value = selecionarBtn.obraData.autor,
              tipoInput.value = selecionarBtn.obraData.tipo,
              anoInput.value = selecionarBtn.obraData.ano,
              descricaoInput.value = selecionarBtn.obraData.descricao,
              imagemInput.value = selecionarBtn.obraData.imagem
            ];
            console.log("Id da obra selecionada:", dadosObras);

          })
        }

        // Estilizar campos de informação
        const camposInfo = resultadosBuscaEl.querySelectorAll('.campo-info');
        camposInfo.forEach(campo => {
          campo.style.display = 'flex';
          campo.style.justifyContent = 'space-between';
          campo.style.gap = '8px';
          campo.style.marginBottom = '5px';
          campo.style.fontSize = '14px';
        });

        const rotulos = resultadosBuscaEl.querySelectorAll('.rotulo');
        rotulos.forEach(rotulo => {
          rotulo.style.fontWeight = 'bold';
          rotulo.style.color = '#aaa';
        });

        camposInfo.forEach(campo => {
          // Pegar o segundo span (que não tem a classe "rotulo")
          const spanResposta = campo.querySelector('span:not(.rotulo)');
          if (spanResposta) {
            spanResposta.style.textAlign = 'right';
          }
        });

        // Adicionar evento ao botão voltar 
        document.querySelector('.voltar-btn').addEventListener('click', voltarParaResultados);

        // Adicionar evento ao botão selecionar
        document.querySelector('.selecionar-para-analise').addEventListener('click', function () {
          // Usar os dados armazenados diretamente como propriedade
          const obraData = this.obraData;

          // Fechar o painel
          painelBuscaObras.style.display = 'none';
          overlay.style.display = 'none';

          // Adicionar informações da obra à textarea
          const textarea = document.querySelector('.create-post textarea');
          if (textarea) {
            let tipoObra = '';
            switch (obraData.tipo) {
              case 'filme':
                tipoObra = obraData.subtipo || 'filme';
                break;
              case 'serie':
                tipoObra = obraData.subtipo || 'série';
                break;
              case 'livro':
                tipoObra = 'livro';
                break;
              case 'arte':
                tipoObra = 'obra de arte';
                break;
              case 'musica':
                tipoObra = 'música';
                break;
            }

            let autorTexto = '';
            if (obraData.autor && (obraData.tipo === 'livro' || obraData.tipo === 'arte' || obraData.tipo === 'musica')) {
              autorTexto = ` de ${obraData.autor}`;
            } else if (obraData.criador && obraData.tipo === 'serie') {
              autorTexto = ` de ${obraData.criador}`;
            }

          }
        });
      }

      // Função centralizada para voltar aos resultados da busca
      function voltarParaResultados() {
        console.log("Voltando para resultados da busca");

        // Restauração direta do HTML armazenado
        if (window.resultadosHtmlCache) {
          resultadosBuscaEl.innerHTML = window.resultadosHtmlCache;
          reconectarEventosBusca();
          return true;
        }

        // Se não tiver o cache HTML, tentar renderizar novamente
        if (window.ultimosResultadosBusca && window.ultimosResultadosBusca.length > 0) {
          console.log("Renderizando novamente os resultados");
          renderizarResultados(window.ultimosResultadosBusca);
          return true;
        }

        // Se tudo falhar, mostrar mensagem
        console.log("Não foi possível recuperar os resultados");
        resultadosBuscaEl.innerHTML = `
          <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
            <p style="color:#aaa;margin:0;text-align:center;">Digite um termo para buscar.</p>
          </div>
        `;
        return false;
      }

      // Função para reconectar eventos após restaurar o HTML
      function reconectarEventosBusca() {
        // Reconectar eventos de paginação
        document.querySelectorAll('.numero-pagina').forEach(btn => {
          btn.addEventListener('click', function () {
            const pagina = parseInt(this.getAttribute('data-pagina'));
            const paginaAtual = document.querySelector('.numero-pagina.ativo');
            // Usar os resultados salvos globalmente
            const resultados = window.ultimosResultadosBusca;

            if (resultados && paginaAtual) {
              // Fixado em 3 itens por página
              mostrarPagina(resultados, pagina, 3);
              atualizarBotoesPagina(pagina, Math.ceil(resultados.length / 3));
            }
          });
        });

        // Reconectar eventos de navegação
        const prevBtn = document.getElementById('prev-pagina');
        if (prevBtn) {
          prevBtn.addEventListener('click', function () {
            const paginaAtualEl = document.querySelector('.numero-pagina.ativo');
            if (paginaAtualEl) {
              const paginaAtual = parseInt(paginaAtualEl.getAttribute('data-pagina'));
              const resultados = window.ultimosResultadosBusca;

              if (resultados && paginaAtual > 1) {
                const novaPagina = paginaAtual - 1;
                mostrarPagina(resultados, novaPagina, 3);
                atualizarBotoesPagina(novaPagina, Math.ceil(resultados.length / 3));
              }
            }
          });
        }

        const nextBtn = document.getElementById('next-pagina');
        if (nextBtn) {
          nextBtn.addEventListener('click', function () {
            const paginaAtualEl = document.querySelector('.numero-pagina.ativo');
            if (paginaAtualEl) {
              const paginaAtual = parseInt(paginaAtualEl.getAttribute('data-pagina'));
              const resultados = window.ultimosResultadosBusca;

              if (resultados) {
                const totalPaginas = Math.ceil(resultados.length / 3);
                if (paginaAtual < totalPaginas) {
                  const novaPagina = paginaAtual + 1;
                  mostrarPagina(resultados, novaPagina, 3);
                  atualizarBotoesPagina(novaPagina, totalPaginas);
                }
              }
            }
          });
        }

        // Reconectar eventos de clique nos itens de resultado
        document.querySelectorAll('.resultado-item').forEach(item => {
          item.addEventListener('click', async () => {
            const obraId = item.getAttribute('data-id');
            const obraTipo = item.getAttribute('data-tipo');

            console.log('Item clicado! ID:', obraId, 'Tipo:', obraTipo);

            // Armazenar o HTML atual antes de mostrar a mensagem de carregamento
            window.resultadosHtmlCache = resultadosBuscaEl.innerHTML;

            try {
              // Exibir indicador de carregamento com ícone girando
              resultadosBuscaEl.innerHTML = `
                <div style="width:100%;height:100%;display:flex;flex-direction:column;justify-content:center;align-items:center;">
                  <div class="loading-spinner" style="width:30px;height:30px;border:3px solid rgba(255,102,0,0.2);border-radius:50%;border-top-color:#ff6600;animation:spin 1s ease-in-out infinite;margin-bottom:10px;"></div>
                  <p style="color:#aaa;margin:5px 0 0 0;text-align:center;">Carregando detalhes...</p>
                  <style>
                    @keyframes spin {
                      to { transform: rotate(360deg); }
                    }
                  </style>
                </div>
              `;

              console.log('Chamando obterDetalhesObra para ID:', obraId, 'Tipo:', obraTipo);

              const obra = await obterDetalhesObra({
                apiId: obraId,
                tipo: obraTipo
              });

              console.log('Detalhes recebidos:', obra);
              exibirDetalhesObra(obra);
            } catch (err) {
              console.error('Erro ao obter detalhes:', err);
              // Limpar resultados se o campo ficar vazio
              resultadosBuscaEl.innerHTML = `
                <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
                  <p style="color:#aaa;margin:0;text-align:center;">Erro ao carregar detalhes. Tente novamente.</p>
                </div>
              `;
            }
          });

          // Adicionar hover efeito
          item.addEventListener('mouseover', () => {
            item.style.background = '#333';
          });

          item.addEventListener('mouseout', () => {
            item.style.background = '#2a2a2a';
          });
        });
      }

      // Função para mostrar uma página de resultados
      function mostrarPagina(resultados, pagina, itensPorPagina) {
        const inicio = (pagina - 1) * itensPorPagina;
        const fim = Math.min(inicio + itensPorPagina, resultados.length);
        const resultadosPagina = resultados.slice(inicio, fim);

        console.log('Mostrando página', pagina, 'com', resultadosPagina.length, 'itens');
        console.log('Itens da página:', resultadosPagina);

        const conteudoEl = document.getElementById('pagina-conteudo');

        let html = '<ul style="list-style:none;padding:0;margin:0;height:100%;display:flex;flex-direction:column;">';

        resultadosPagina.forEach((obra, index) => {
          const icone = getIconeTipo(obra.tipo, obra.subtipo);

          // Limitar título a 30 caracteres para evitar quebra de layout
          let tituloEncurtado = obra.titulo;
          if (tituloEncurtado.length > 30) {
            tituloEncurtado = tituloEncurtado.substring(0, 30) + '...';
          }

          const subtitulo = obra.tipo === 'livro' || obra.tipo === 'arte' ?
            obra.autor :
            obra.ano;

          // Limitar subtítulo para evitar quebra de layout
          let subtituloEncurtado = subtitulo;
          if (subtituloEncurtado && subtituloEncurtado.length > 25) {
            subtituloEncurtado = subtituloEncurtado.substring(0, 25) + '...';
          }

          // Adicionar etiqueta para documentários
          const etiquetaDoc = obra.subtipo === 'documentário' ?
            '<span style="background:#E91E63;color:white;font-size:9px;padding:1px 3px;border-radius:3px;margin-left:3px;">DOC</span>' :
            '';

          // Adicionar etiqueta para séries
          const etiquetaSerie = obra.tipo === 'serie' && obra.subtipo !== 'documentário' ?
            '<span style="background:#9C27B0;color:white;font-size:9px;padding:1px 3px;border-radius:3px;margin-left:3px;">SÉRIE</span>' :
            '';

          console.log(`Item ${index}: ID=${obra.apiId}, Tipo=${obra.tipo}, Título=${obra.titulo}`);

          html += `
            <li style="display:flex;align-items:center;padding:6px;border-radius:6px;margin-bottom:5px;background:#2a2a2a;transition:background 0.2s;cursor:pointer;max-width:100%;flex:1;" 
                class="resultado-item" data-id="${obra.apiId}" data-tipo="${obra.tipo}" title="${obra.titulo}">
              <img src="${obra.imagem}" referrerpolicy="no-referrer" alt="${obra.titulo}" 
                   style="width:40px;height:60px;object-fit:cover;border-radius:3px;margin-right:10px;flex-shrink:0;">
              <div style="flex:1;min-width:0;overflow:hidden;">
                <h4 style="margin:0 0 2px 0;font-size:13px;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  ${icone}${tituloEncurtado}${etiquetaDoc}${etiquetaSerie}
                </h4>
                <p style="margin:0;font-size:11px;color:#aaa;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${subtituloEncurtado} ${obra.ano ? `• ${obra.ano}` : ''}</p>
              </div>
            </li>
          `;
        });

        // Se tiver menos de 3 itens, adicionar espaços vazios para manter a altura consistente
        const itemsFaltantes = 3 - resultadosPagina.length;
        for (let i = 0; i < itemsFaltantes; i++) {
          html += `<li style="height:72px;margin-bottom:5px;flex:1;"></li>`;
        }

        html += '</ul>';
        conteudoEl.innerHTML = html;

        console.log('HTML gerado:', html);

        // Verificar se os elementos foram criados corretamente
        setTimeout(() => {
          const itensGerados = document.querySelectorAll('.resultado-item');
          console.log('Itens gerados:', itensGerados.length);
          itensGerados.forEach((item, i) => {
            console.log(`Item gerado ${i}:`, item.getAttribute('data-id'), item.getAttribute('data-tipo'));
          });
        }, 0);

        // Ajustar para que os itens preencham todo o espaço vertical disponível
        const itens = conteudoEl.querySelectorAll('li');
        if (itens.length > 0) {
          // Distribuir o espaço igualmente entre os itens
          const alturaTotal = conteudoEl.clientHeight;
          const alturaItem = Math.floor(alturaTotal / 3); // 3 itens por página

          itens.forEach(item => {
            item.style.height = `${alturaItem}px`;
          });
        }

        // Reconectar os eventos de clique
        document.querySelectorAll('.resultado-item').forEach(item => {
          item.addEventListener('click', async () => {
            const obraId = item.getAttribute('data-id');
            const obraTipo = item.getAttribute('data-tipo');

            console.log('Item clicado! ID:', obraId, 'Tipo:', obraTipo);

            // Armazenar o HTML atual antes de mostrar a mensagem de carregamento
            window.resultadosHtmlCache = resultadosBuscaEl.innerHTML;

            try {
              // Exibir indicador de carregamento com ícone girando
              resultadosBuscaEl.innerHTML = `
                <div style="width:100%;height:100%;display:flex;flex-direction:column;justify-content:center;align-items:center;">
                  <div class="loading-spinner" style="width:30px;height:30px;border:3px solid rgba(255,102,0,0.2);border-radius:50%;border-top-color:#ff6600;animation:spin 1s ease-in-out infinite;margin-bottom:10px;"></div>
                  <p style="color:#aaa;margin:5px 0 0 0;text-align:center;">Carregando detalhes...</p>
                  <style>
                    @keyframes spin {
                      to { transform: rotate(360deg); }
                    }
                  </style>
                </div>
              `;

              console.log('Chamando obterDetalhesObra para ID:', obraId, 'Tipo:', obraTipo);

              const obra = await obterDetalhesObra({
                apiId: obraId,
                tipo: obraTipo
              });

              console.log('Detalhes recebidos:', obra);
              exibirDetalhesObra(obra);
            } catch (err) {
              console.error('Erro ao obter detalhes:', err);
              // Limpar resultados se o campo ficar vazio
              resultadosBuscaEl.innerHTML = `
                <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
                  <p style="color:#aaa;margin:0;text-align:center;">Erro ao carregar detalhes. Tente novamente.</p>
                </div>
              `;
            }
          });

          // Adicionar hover efeito
          item.addEventListener('mouseover', () => {
            item.style.background = '#333';
          });

          item.addEventListener('mouseout', () => {
            item.style.background = '#2a2a2a';
          });
        });
      }

      // Função para gerar botões de página
      function gerarBotoesPagina(totalPaginas, paginaAtual) {
        let html = '';

        // Se houver muitas páginas, mostrar numeração limitada com elipses
        if (totalPaginas > 7) {
          // Sempre mostrar primeira página
          html += getBotaoPagina(1, paginaAtual);

          // Lógica para páginas intermediárias
          if (paginaAtual <= 4) {
            // Se estamos nas primeiras páginas
            for (let i = 2; i <= 5; i++) {
              html += getBotaoPagina(i, paginaAtual);
            }
            html += '<span style="margin:0 5px;">...</span>';
          } else if (paginaAtual >= totalPaginas - 3) {
            // Se estamos nas últimas páginas
            html += '<span style="margin:0 5px;">...</span>';
            for (let i = totalPaginas - 4; i < totalPaginas; i++) {
              html += getBotaoPagina(i, paginaAtual);
            }
          } else {
            // No meio
            html += '<span style="margin:0 5px;">...</span>';
            for (let i = paginaAtual - 1; i <= paginaAtual + 1; i++) {
              html += getBotaoPagina(i, paginaAtual);
            }
            html += '<span style="margin:0 5px;">...</span>';
          }

          // Sempre mostrar última página
          html += getBotaoPagina(totalPaginas, paginaAtual);
        } else {
          // Se houver poucas páginas, mostrar todas
          for (let i = 1; i <= totalPaginas; i++) {
            html += getBotaoPagina(i, paginaAtual);
          }
        }

        return html;
      }

      // Função auxiliar para gerar HTML do botão de página
      function getBotaoPagina(numeroPagina, paginaAtual) {
        const ativo = numeroPagina === paginaAtual;
        return `
          <button class="numero-pagina ${ativo ? 'ativo' : ''}" data-pagina="${numeroPagina}" 
                  style="min-width:22px;height:22px;padding:0 4px;border-radius:11px;border:none;
                  background:${ativo ? '#ff6600' : '#333'};
                  color:white;font-weight:${ativo ? 'bold' : 'normal'};cursor:pointer;
                  display:flex;align-items:center;justify-content:center;font-size:11px;">
            ${numeroPagina}
          </button>
        `;
      }

      // Função para atualizar botões de página
      function atualizarBotoesPagina(paginaAtual, totalPaginas) {
        // Atualizar estado dos botões de navegação
        document.getElementById('prev-pagina').disabled = paginaAtual === 1;
        document.getElementById('next-pagina').disabled = paginaAtual === totalPaginas;

        // Atualizar os botões numerados
        document.getElementById('numeros-pagina').innerHTML = gerarBotoesPagina(totalPaginas, paginaAtual);

        // Reconectar eventos aos novos botões
        document.querySelectorAll('.numero-pagina').forEach(btn => {
          btn.addEventListener('click', function () {
            const pagina = parseInt(this.getAttribute('data-pagina'));
            const resultados = window.ultimosResultadosBusca;

            if (resultados) {
              mostrarPagina(resultados, pagina, 3); // Fixado em 3 itens por página
              atualizarBotoesPagina(pagina, Math.ceil(resultados.length / 3));
            }
          });

          // Adicionar hover
          btn.addEventListener('mouseover', function () {
            if (!this.classList.contains('ativo')) {
              this.style.background = '#444';
            }
          });

          btn.addEventListener('mouseout', function () {
            if (!this.classList.contains('ativo')) {
              this.style.background = '#333';
            }
          });
        });
      }

      // Função para renderizar resultados
      function renderizarResultados(resultados) {
        console.log('Renderizando resultados:', resultados);

        // Armazenar resultados globalmente para paginação
        window.ultimosResultadosBusca = resultados;

        if (!resultados || resultados.length === 0) {
          resultadosBuscaEl.innerHTML = `
            <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
              <p style="color:#aaa;margin:0;text-align:center;">Nenhuma obra encontrada com esse termo.</p>
            </div>
          `;
          return;
        }

        // Configuração da paginação
        const itensPorPagina = 3;
        const totalPaginas = Math.ceil(resultados.length / itensPorPagina);
        const paginaAtual = 1;

        // Criar layout para resultados paginados
        const html = `
          <div class="resultados-container" style="height:100%;display:flex;flex-direction:column;">
            <!-- Conteúdo da página atual -->
            <div id="pagina-conteudo" style="flex:1;overflow-y:auto;margin-bottom:10px;"></div>
            
            <!-- Controles de paginação -->
            <div class="paginacao" style="display:flex;align-items:center;justify-content:center;gap:5px;padding-top:5px;border-top:1px solid #333;">
              <button id="prev-pagina" style="background:#333;color:white;border:none;width:22px;height:22px;border-radius:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:10px;" disabled>&lt;</button>
              <div id="numeros-pagina" style="display:flex;gap:3px;"></div>
              <button id="next-pagina" style="background:#333;color:white;border:none;width:22px;height:22px;border-radius:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:10px;">&gt;</button>
            </div>
          </div>
        `;

        resultadosBuscaEl.innerHTML = html;

        // Mostrar primeira página
        mostrarPagina(resultados, paginaAtual, itensPorPagina);

        // Gerar botões de paginação
        atualizarBotoesPagina(paginaAtual, totalPaginas);

        // Adicionar listeners para os botões de paginação
        document.getElementById('prev-pagina').addEventListener('click', function () {
          const paginaAtualEl = document.querySelector('.numero-pagina.ativo');
          const paginaAtual = parseInt(paginaAtualEl.getAttribute('data-pagina'));
          if (paginaAtual > 1) {
            const novaPagina = paginaAtual - 1;
            mostrarPagina(resultados, novaPagina, itensPorPagina);
            atualizarBotoesPagina(novaPagina, totalPaginas);
          }
        });

        document.getElementById('next-pagina').addEventListener('click', function () {
          const paginaAtualEl = document.querySelector('.numero-pagina.ativo');
          const paginaAtual = parseInt(paginaAtualEl.getAttribute('data-pagina'));
          if (paginaAtual < totalPaginas) {
            const novaPagina = paginaAtual + 1;
            mostrarPagina(resultados, novaPagina, itensPorPagina);
            atualizarBotoesPagina(novaPagina, totalPaginas);
          }
        });

        // Adicionar listeners para os números de página
        document.querySelectorAll('.numero-pagina').forEach(btn => {
          btn.addEventListener('click', function () {
            const pagina = parseInt(this.getAttribute('data-pagina'));
            mostrarPagina(resultados, pagina, itensPorPagina);
            atualizarBotoesPagina(pagina, totalPaginas);
          });

          // Adicionar hover
          btn.addEventListener('mouseover', function () {
            if (!this.classList.contains('ativo')) {
              this.style.background = '#444';
            }
          });

          btn.addEventListener('mouseout', function () {
            if (!this.classList.contains('ativo')) {
              this.style.background = '#333';
            }
          });
        });
      }

      // Função para realizar busca
      async function realizarBusca() {
        const termoBusca = buscaObraInput.value.trim();
        const tiposSelecionados = getTiposSelecionados();

        console.log("Realizando busca para:", termoBusca);
        console.log("Tipos incluídos:", tiposSelecionados);

        if (termoBusca === '') {
          resultadosBuscaEl.innerHTML = `
            <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
              <p style="color:#aaa;margin:0;text-align:center;">Digite um termo para buscar.</p>
            </div>
          `;
          return;
        }

        if (tiposSelecionados.length === 0) {
          resultadosBuscaEl.innerHTML = `
            <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
              <p style="color:#aaa;margin:0;text-align:center;">Selecione pelo menos um tipo de obra.</p>
            </div>
          `;
          return;
        }

        // Exibir indicador de carregamento com ícone girando
        resultadosBuscaEl.innerHTML = `
          <div style="width:100%;height:100%;display:flex;flex-direction:column;justify-content:center;align-items:center;">
            <div class="loading-spinner" style="width:30px;height:30px;border:3px solid rgba(255,102,0,0.2);border-radius:50%;border-top-color:#ff6600;animation:spin 1s ease-in-out infinite;margin-bottom:10px;"></div>
            <p style="color:#aaa;margin:5px 0 0 0;text-align:center;">Buscando obras...</p>
            <style>
              @keyframes spin {
                to { transform: rotate(360deg); }
              }
            </style>
          </div>
        `;

        // Buscar obras
        const resultado = await buscarObras(termoBusca, tiposSelecionados);

        console.log("Resultado da busca:", resultado);
        if (resultado.resultados) {
          console.log("Total de obras encontradas:", resultado.resultados.length);
          console.log("Tipos encontrados:", resultado.resultados.map(r => r.tipo));
        }

        if (resultado.mensagem) {
          resultadosBuscaEl.innerHTML = `
            <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
              <p style="color:#aaa;margin:0;text-align:center;">${resultado.mensagem}</p>
            </div>
          `;
          return;
        }

        // Renderizar resultados
        renderizarResultados(resultado.resultados);
      }

      // Função para gerar um debounce (atrasar execução de função)
      function debounce(func, delay) {
        let timeout;
        return function () {
          const context = this;
          const args = arguments;
          clearTimeout(timeout);
          timeout = setTimeout(() => func.apply(context, args), delay);
        };
      }

      // Busca com debounce para o campo de texto
      const realizarBuscaDebounced = debounce(realizarBusca, 500);

      // Adicionar evento de input ao campo de busca para busca em tempo real
      buscaObraInput.addEventListener('input', function () {
        const termoBusca = this.value.trim();
        if (termoBusca.length >= 3) {
          realizarBuscaDebounced();
        } else if (termoBusca.length === 0) {
          // Limpar resultados se o campo ficar vazio
          resultadosBuscaEl.innerHTML = `
            <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
              <p style="color:#aaa;margin:0;text-align:center;">Digite um termo para buscar.</p>
            </div>
          `;
        }
      });

      // Evento de clique no botão de buscar obra na página principal
      if (buscarObraBtn) {
        buscarObraBtn.addEventListener('click', function (e) {
          e.preventDefault();
          console.log("Botão de buscar obra clicado");
          abrirPainel();
        });
      } else {
        console.error("Botão de buscar obra não encontrado!");
      }

      // Função para abrir o painel
      function abrirPainel() {
        console.log("Abrindo painel");
        if (painelBuscaObras) {
          painelBuscaObras.style.display = 'block';
          overlay.style.display = 'block';
          if (buscaObraInput) {
            buscaObraInput.focus();
          }
        } else {
          console.error("Painel de busca não encontrado!");
        }
      }

      // Evento de clique no botão de buscar no painel
      buscarBtn.addEventListener('click', realizarBusca);

      // Permitir busca ao pressionar Enter
      buscaObraInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          realizarBusca();
        }
      });

      // Evento de clique no overlay
      overlay.addEventListener('click', function () {
        painelBuscaObras.style.display = 'none';
        overlay.style.display = 'none';
      });

      // Adicionar evento às checkboxes para atualização automática via AJAX
      checkboxesTipos.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
          // Verifica se há pelo menos um tipo selecionado
          const tiposSelecionados = getTiposSelecionados();
          console.log("Tipos selecionados:", tiposSelecionados);

          if (tiposSelecionados.length === 0) {
            // Se nenhum tipo estiver selecionado, reseleciona este
            this.checked = true;
            return;
          }

          // Atualizar estilo visual dos filtros
          opcoesLabels.forEach(label => {
            const input = label.querySelector('input');
            if (input.checked) {
              label.style.background = '#2a0e4c';
              label.style.fontWeight = 'bold';
            } else {
              label.style.background = 'transparent';
              label.style.fontWeight = 'normal';
            }
          });

          // Se há termo de busca, atualiza os resultados automaticamente
          const termoBusca = buscaObraInput.value.trim();
          if (termoBusca !== '') {
            realizarBusca();
          }
        });
      });

      console.log("Script do painel de busca inicializado com sucesso!");
    });

    async function carregarImagens() {
      const imgPosts = document.querySelectorAll('#img-post');

      const promessas = Array.from(imgPosts).map(async (img_post) => {
      const obraPost = img_post.getAttribute('id-obra');
      const tipoObraPost = img_post.getAttribute('tipo-obra');

      try {
        const obra = await obterDetalhesObra({ apiId: obraPost, tipo: tipoObraPost });

        img_post.setAttribute('src', obra.imagem);
        img_post.setAttribute('alt', obra.titulo);

      } catch (err) {
        console.error('Erro ao carregar imagem da obra:', err);
      }
    });

    await Promise.all(promessas);
  }

  

  </script>
</body>

</html>