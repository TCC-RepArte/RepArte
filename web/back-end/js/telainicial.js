// Funções principais da tela inicial

// Carrega imagens das obras
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

// Sistema de curtidas e descurtidas
const voteButtons = document.querySelectorAll('.vote-buttons');

// Objeto para armazenar o estado das reações de cada post
const estadosPosts = {};

// Carrega o estado atual das reações do usuário
async function carregarEstadoReacoes() {
  for (const voteContainer of voteButtons) {
    const postId = voteContainer.dataset.id;
    try {
      // Carregar estado da reação do usuário
      const reacaoResponse = await fetch('../back-end/php/buscar_reacao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: postId })
      });
      
      if (reacaoResponse.ok) {
        const reacaoData = await reacaoResponse.json();
        if (reacaoData.success && reacaoData.reacao) {
          estadosPosts[postId] = reacaoData.reacao.tipo;
          atualizarBotoesPost(voteContainer, reacaoData.reacao.tipo);
        } else {
          estadosPosts[postId] = null;
        }
      }
      
      // Carregar contadores de reações
      const contadorResponse = await fetch('../back-end/php/contar_reacoes.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: postId })
      });
      
      if (contadorResponse.ok) {
        const contadorData = await contadorResponse.json();
        if (contadorData.success) {
          atualizarContadores(voteContainer, contadorData.likes, contadorData.dislikes);
        }
      }
    } catch (error) {
      console.log('Erro ao carregar dados da reação:', error);
      estadosPosts[postId] = null;
    }
  }
}

voteButtons.forEach(voteContainer => {
  const postId = voteContainer.dataset.id;
  const likeBtn = voteContainer.querySelector('.like-btn');
  const dislikeBtn = voteContainer.querySelector('.dislike-btn');
  
  // Inicializar estado do post (será atualizado pelo carregarEstadoReacoes)
  estadosPosts[postId] = null;
  
  // Event listener para curtir
  likeBtn.addEventListener('click', () => {
    const estadoAtual = estadosPosts[postId];
    
    if (estadoAtual === 'like') {
      // Se já está curtido, remove a curtida
      estadosPosts[postId] = null;
    } else {
      // Se não está curtido ou está descurtido, curte
      estadosPosts[postId] = 'like';
    }
    
    atualizarBotoesPost(voteContainer, estadosPosts[postId]);
    enviarReacao(postId, estadosPosts[postId]);
  });
  
  // Event listener para descurtir
  dislikeBtn.addEventListener('click', () => {
    const estadoAtual = estadosPosts[postId];
    
    if (estadoAtual === 'dislike') {
      // Se já está descurtido, remove a descurtida
      estadosPosts[postId] = null;
    } else {
      // Se não está descurtido ou está curtido, descurte
      estadosPosts[postId] = 'dislike';
    }
    
    atualizarBotoesPost(voteContainer, estadosPosts[postId]);
    enviarReacao(postId, estadosPosts[postId]);
                    });
                });

// Função para atualizar a aparência dos botões baseado no estado
function atualizarBotoesPost(voteContainer, estado) {
  const likeBtn = voteContainer.querySelector('.like-btn');
  const dislikeBtn = voteContainer.querySelector('.dislike-btn');
  const likeText = voteContainer.querySelector('.like-text');
  const dislikeText = voteContainer.querySelector('.dislike-text');
  
  // Remove classes ativas dos dois botões
  likeBtn.classList.remove('ativo');
  dislikeBtn.classList.remove('ativo');
  
  // Aplicar estilos baseado no estado
  if (estado === 'like') {
    likeBtn.classList.add('ativo');
    likeText.textContent = 'Curtido';
    dislikeText.textContent = 'Descurtir';
  } else if (estado === 'dislike') {
    dislikeBtn.classList.add('ativo');
    dislikeText.textContent = 'Descurtido';
    likeText.textContent = 'Curtir';
            } else {
    // Estado null - ambos desativados
    likeText.textContent = 'Curtir';
    dislikeText.textContent = 'Descurtir';
  }
}

// Função para atualizar os contadores de reação
function atualizarContadores(voteContainer, likes, dislikes) {
  const likeCount = voteContainer.querySelector('.like-count');
  const dislikeCount = voteContainer.querySelector('.dislike-count');
  
  if (likeCount) likeCount.textContent = likes;
  if (dislikeCount) dislikeCount.textContent = dislikes;
}

// Função para enviar reação para o servidor
async function enviarReacao(postId, estado) {
  let like = false, dislike = false;
  
  if (estado === 'like') like = true;
  else if (estado === 'dislike') dislike = true;
  
  try {
    const response = await fetch('../back-end/php/reagir.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: postId, like: like, dislike: dislike })
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      console.error('Erro na requisição:', response.status);
      alert('Erro ao processar reação. Tente novamente.');
    } else {
      if (data.success) {
        // Atualizar contadores após sucesso
        await atualizarContadoresPost(postId);
      } else {
        console.error('Erro do servidor:', data.error);
        alert('Erro: ' + data.error);
      }
    }
  } catch (error) {
    console.error('Erro detalhado ao enviar reação:', error);
    console.error('Tipo do erro:', error.constructor.name);
    console.error('Mensagem:', error.message);
    
    if (error.name === 'TypeError' && error.message.includes('fetch')) {
      alert('Erro de conexão: Não foi possível conectar ao servidor. Verifique se o WAMP está rodando.');
    } else if (error.name === 'SyntaxError') {
      alert('Erro: Resposta inválida do servidor. Verifique os logs.');
        } else {
      alert('Erro: ' + error.message);
    }
  }
}

// Função para atualizar contadores de um post específico
async function atualizarContadoresPost(postId) {
  try {
    const response = await fetch('../back-end/php/contar_reacoes.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: postId })
    });
    
    if (response.ok) {
      const data = await response.json();
      if (data.success) {
        const voteContainer = document.querySelector(`[data-id="${postId}"]`);
        if (voteContainer) {
          atualizarContadores(voteContainer, data.likes, data.dislikes);
        }
      }
    }
  } catch (error) {
    console.log('Erro ao atualizar contadores:', error);
  }
}


// Função para verificar se o texto precisa ser truncado
function verificarTruncamentoTexto() {
  console.log('=== INICIANDO VERIFICAÇÃO DE TRUNCAMENTO ===');
  
  const textContainers = document.querySelectorAll('.post-text-container');
  console.log('Encontrados', textContainers.length, 'containers de texto');
  
  if (textContainers.length === 0) {
    console.log('❌ Nenhum container encontrado! Verificando se a página carregou...');
    console.log('Elementos .post encontrados:', document.querySelectorAll('.post').length);
    return;
  }
  
  textContainers.forEach((container, index) => {
    console.log(`\n--- Container ${index} ---`);
    const textElement = container.querySelector('.post-text-truncated');
    const expandButton = container.querySelector('.expand-button');
    
    console.log('Elemento de texto encontrado:', !!textElement);
    console.log('Botão expandir encontrado:', !!expandButton);
    
    if (textElement && expandButton) {
      const textoOriginal = textElement.textContent.trim();
      
      // Define um limite de caracteres para truncamento
      const limiteCaracteres = 80;
      
      console.log(`Texto original: "${textoOriginal}"`);
      console.log(`Tamanho: ${textoOriginal.length} caracteres`);
      console.log(`Limite: ${limiteCaracteres} caracteres`);
      
      if (textoOriginal.length > limiteCaracteres) {
        // Trunca o texto
        const textoTruncado = textoOriginal.substring(0, limiteCaracteres) + '...';
        textElement.textContent = textoTruncado;
        
        // Armazena o texto original no botão
        expandButton.setAttribute('data-texto-original', textoOriginal);
        expandButton.classList.add('show');
        
        console.log(`✅ TRUNCADO: ${textoOriginal.length} -> ${textoTruncado.length} caracteres`);
        console.log(`Texto truncado: "${textoTruncado}"`);
      } else {
        console.log(`ℹ️ Não precisa truncar (${textoOriginal.length} <= ${limiteCaracteres})`);
      }
    } else {
      console.log(`❌ Elementos não encontrados no container ${index}`);
      if (!textElement) console.log('  - Elemento .post-text-truncated não encontrado');
      if (!expandButton) console.log('  - Elemento .expand-button não encontrado');
    }
  });
  
  console.log('=== FIM DA VERIFICAÇÃO ===\n');
}

// Função para expandir texto da postagem
function expandirTexto(postId) {
  const container = document.querySelector(`[data-post-id="${postId}"]`);
  if (container) {
    const textElement = container.querySelector('.post-text-truncated');
    const expandButton = container.querySelector('.expand-button');
    
    if (textElement && expandButton) {
      const textoOriginal = expandButton.getAttribute('data-texto-original');
      if (textoOriginal) {
        textElement.textContent = textoOriginal;
        expandButton.textContent = 'Ver menos...';
        expandButton.onclick = () => contrairTexto(postId);
        console.log(`Texto expandido para post ${postId}`);
      }
    }
  }
}

// Função para contrair texto da postagem
function contrairTexto(postId) {
  const container = document.querySelector(`[data-post-id="${postId}"]`);
  if (container) {
    const textElement = container.querySelector('.post-text-truncated');
    const expandButton = container.querySelector('.expand-button');
    
    if (textElement && expandButton) {
      const textoOriginal = expandButton.getAttribute('data-texto-original');
      if (textoOriginal) {
        const limiteCaracteres = 80;
        const textoTruncado = textoOriginal.substring(0, limiteCaracteres) + '...';
        textElement.textContent = textoTruncado;
        expandButton.textContent = 'Ver mais...';
        expandButton.onclick = () => expandirTexto(postId);
        console.log(`Texto contraído para post ${postId}`);
      }
    }
  }
}


// Inicialização quando o DOM carrega
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM carregado - telainicial.js');
  
  // Carregar imagens e estado das reações
  carregarImagens();
  carregarEstadoReacoes();
  
  // Executar truncamento imediatamente
  verificarTruncamentoTexto();
  
  // Verificar truncamento de texto após carregar as imagens
  setTimeout(() => {
    verificarTruncamentoTexto();
  }, 500);
  
  // Tentar novamente após mais tempo
  setTimeout(() => {
    verificarTruncamentoTexto();
  }, 1500);
  
  // Tentar uma terceira vez
  setTimeout(() => {
    verificarTruncamentoTexto();
  }, 3000);
  
  // Adicionar função global para teste
  window.testarTruncamento = verificarTruncamentoTexto;
  
  // Função para forçar truncamento (teste)
  window.forcarTruncamento = function() {
    console.log('=== FORÇANDO TRUNCAMENTO ===');
    const textContainers = document.querySelectorAll('.post-text-container');
    textContainers.forEach((container, index) => {
      const textElement = container.querySelector('.post-text-truncated');
      const expandButton = container.querySelector('.expand-button');
      
      if (textElement && expandButton) {
        const textoOriginal = textElement.textContent.trim();
        const limiteCaracteres = 50; // Limite muito baixo para forçar
        
        if (textoOriginal.length > limiteCaracteres) {
          const textoTruncado = textoOriginal.substring(0, limiteCaracteres) + '...';
          textElement.textContent = textoTruncado;
          expandButton.setAttribute('data-texto-original', textoOriginal);
          expandButton.classList.add('show');
          console.log(`FORÇADO: Container ${index} truncado para ${limiteCaracteres} caracteres`);
        }
      }
    });
  };

  // Configurar clique no perfil
  const perfilRed = document.querySelector('.user-red');
  if (perfilRed) {
    perfilRed.addEventListener("click", function () {
      window.location.href = "../back-end/php/perfil_dados.php"
    });
  }

  // Referenciando os inputs para envio do id das obras
  const idInput = document.querySelector('.id_obra');
  const tituloInput = document.querySelector('.tit_obra');
  const autorInput = document.querySelector('.autor_obra');
  const descricaoInput = document.querySelector('.descricao_obra');
  const anoInput = document.querySelector('.ano_obra');
  const tipoInput = document.querySelector('.tipo_obra');
  const imagemInput = document.querySelector('.img_obra');

  // Configurar hover para botões de configuração dos posts
  const botoesPosts = document.querySelectorAll('.post-set');
  botoesPosts.forEach(botao => {
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

  // Criar novo painel de busca de obras
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

  // Evento de clique no overlay
  overlay.addEventListener('click', function () {
    painelBuscaObras.style.display = 'none';
    overlay.style.display = 'none';
  });

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

    // Exibir indicador de carregamento
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

    try {
      // Buscar obras usando a função do apis-obras.js
      const resultado = await buscarObras(termoBusca, tiposSelecionados);

      console.log("Resultado da busca:", resultado);
      
      if (resultado.mensagem) {
        resultadosBuscaEl.innerHTML = `
          <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
            <p style="color:#aaa;margin:0;text-align:center;">${resultado.mensagem}</p>
          </div>
        `;
        return;
      }

      if (!resultado.resultados || resultado.resultados.length === 0) {
        resultadosBuscaEl.innerHTML = `
          <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
            <p style="color:#aaa;margin:0;text-align:center;">Nenhuma obra encontrada com esse termo.</p>
          </div>
        `;
        return;
      }

      // Renderizar resultados
      renderizarResultados(resultado.resultados);
    } catch (error) {
      console.error('Erro na busca:', error);
      resultadosBuscaEl.innerHTML = `
        <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
          <p style="color:#aaa;margin:0;text-align:center;">Erro ao buscar obras. Tente novamente.</p>
        </div>
      `;
    }
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
      <div class="resultados-container" style="height:100%;display:flex;flex-direction:column;overflow:hidden;width:100%;">
        <!-- Conteúdo da página atual -->
        <div id="pagina-conteudo" style="flex:1;overflow-y:auto;overflow-x:hidden;margin-bottom:10px;width:100%;"></div>
        
        <!-- Controles de paginação -->
        <div class="paginacao" style="display:flex;align-items:center;justify-content:center;gap:5px;padding-top:5px;border-top:1px solid #333;flex-shrink:0;">
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
  }

  // Função para mostrar uma página de resultados
  function mostrarPagina(resultados, pagina, itensPorPagina) {
    const inicio = (pagina - 1) * itensPorPagina;
    const fim = Math.min(inicio + itensPorPagina, resultados.length);
    const resultadosPagina = resultados.slice(inicio, fim);

    const conteudoEl = document.getElementById('pagina-conteudo');

    let html = '<ul style="list-style:none;padding:0;margin:0;height:100%;display:flex;flex-direction:column;overflow-x:hidden;width:100%;">';

    resultadosPagina.forEach((obra, index) => {
      const icone = getIconeTipo(obra.tipo, obra.subtipo);

      // Limitar título a 20 caracteres para evitar quebra de layout
      let tituloEncurtado = obra.titulo;
      if (tituloEncurtado.length > 20) {
        tituloEncurtado = tituloEncurtado.substring(0, 20) + '...';
      }

      const subtitulo = obra.tipo === 'livro' || obra.tipo === 'arte' ?
        obra.autor :
        obra.ano;

      // Limitar subtítulo para evitar quebra de layout
      let subtituloEncurtado = subtitulo;
      if (subtituloEncurtado && subtituloEncurtado.length > 15) {
        subtituloEncurtado = subtituloEncurtado.substring(0, 15) + '...';
      }

      // Adicionar etiqueta para documentários
      const etiquetaDoc = obra.subtipo === 'documentário' ?
        '<span style="background:#E91E63;color:white;font-size:9px;padding:1px 3px;border-radius:3px;margin-left:3px;">DOC</span>' :
        '';

      // Adicionar etiqueta para séries
      const etiquetaSerie = obra.tipo === 'serie' && obra.subtipo !== 'documentário' ?
        '<span style="background:#9C27B0;color:white;font-size:9px;padding:1px 3px;border-radius:3px;margin-left:3px;">SÉRIE</span>' :
        '';

                    html += `
        <li style="display:flex;align-items:center;padding:6px;border-radius:6px;margin-bottom:5px;background:#2a2a2a;transition:background 0.2s;cursor:pointer;width:100%;max-width:100%;box-sizing:border-box;flex:1;" 
            class="resultado-item" data-id="${obra.apiId}" data-tipo="${obra.tipo}" title="${obra.titulo}">
          <img src="${obra.imagem}" referrerpolicy="no-referrer" alt="${obra.titulo}" 
               style="width:40px;max-height:60px;object-fit:contain;background:#1a1a1a;border-radius:3px;margin-right:10px;flex-shrink:0;">
          <div style="flex:1;min-width:0;overflow:hidden;max-width:calc(100% - 60px);">
            <h4 style="margin:0 0 2px 0;font-size:13px;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;">
              ${icone}${tituloEncurtado}${etiquetaDoc}${etiquetaSerie}
            </h4>
            <p style="margin:0;font-size:11px;color:#aaa;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;">${subtituloEncurtado} ${obra.ano ? `• ${obra.ano}` : ''}</p>
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
            <img src="${obra.imagem}" referrerpolicy="no-referrer" alt="${obra.titulo}" style="width:150px;max-height:220px;object-fit:contain;background:#1a1a1a;border-radius:3px;display:block;">
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
      const spanResposta = campo.querySelector('span:not(.rotulo)');
      if (spanResposta) {
        spanResposta.style.textAlign = 'right';
      }
    });

    // Adicionar hover ao botão
    const selecionarBtn = document.querySelector('.selecionar-para-analise');
    if (selecionarBtn) {
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
        selecionarObraCompleta(obra);
      });
    }

    // Adicionar evento ao botão voltar 
    document.querySelector('.voltar-btn').addEventListener('click', voltarParaResultados);
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
        const resultados = window.ultimosResultadosBusca;

        if (resultados) {
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
          mostrarPagina(resultados, pagina, 3);
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

    // Reconectar eventos de navegação
    const prevBtn = document.getElementById('prev-pagina');
    const nextBtn = document.getElementById('next-pagina');

    // Remover listeners antigos se existirem
    const newPrevBtn = prevBtn.cloneNode(true);
    const newNextBtn = nextBtn.cloneNode(true);
    prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
    nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);

    // Adicionar novos listeners
    newPrevBtn.addEventListener('click', function () {
      if (paginaAtual > 1) {
        const novaPagina = paginaAtual - 1;
        mostrarPagina(window.ultimosResultadosBusca, novaPagina, 3);
        atualizarBotoesPagina(novaPagina, totalPaginas);
      }
    });

    newNextBtn.addEventListener('click', function () {
      if (paginaAtual < totalPaginas) {
        const novaPagina = paginaAtual + 1;
        mostrarPagina(window.ultimosResultadosBusca, novaPagina, 3);
        atualizarBotoesPagina(novaPagina, totalPaginas);
            }
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
        html += '<span style="margin:0 5px;color:#666;">...</span>';
      } else if (paginaAtual >= totalPaginas - 3) {
        // Se estamos nas últimas páginas
        html += '<span style="margin:0 5px;color:#666;">...</span>';
        for (let i = totalPaginas - 4; i < totalPaginas; i++) {
          html += getBotaoPagina(i, paginaAtual);
        }
      } else {
        // No meio
        html += '<span style="margin:0 5px;color:#666;">...</span>';
        for (let i = paginaAtual - 1; i <= paginaAtual + 1; i++) {
          html += getBotaoPagina(i, paginaAtual);
        }
        html += '<span style="margin:0 5px;color:#666;">...</span>';
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

  // Função para selecionar obra completa
  function selecionarObraCompleta(obra) {
    // Preencher os campos hidden
    if (idInput) idInput.value = obra.apiId || obra.id;
    if (tituloInput) tituloInput.value = obra.titulo;
    if (autorInput) autorInput.value = obra.autor || '';
    if (tipoInput) tipoInput.value = obra.tipo;
    if (anoInput) anoInput.value = obra.ano || '';
    if (imagemInput) imagemInput.value = obra.imagem || '';
    if (descricaoInput) descricaoInput.value = obra.descricao || '';

    // Fechar o painel
    painelBuscaObras.style.display = 'none';
    overlay.style.display = 'none';

    alert(`Obra "${obra.titulo}" selecionada para análise!`);
  }

  // Função global para selecionar obra (compatibilidade)
  window.selecionarObra = function(apiId, tipo, titulo, autor, ano, imagem) {
    selecionarObraCompleta({
      apiId: apiId,
      tipo: tipo,
      titulo: titulo,
      autor: autor,
      ano: ano,
      imagem: imagem
    });
  };

  // Adicionar eventos de busca
  buscarBtn.addEventListener('click', realizarBusca);
  
  buscaObraInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      realizarBusca();
    }
  });

  // Busca em tempo real (opcional)
  buscaObraInput.addEventListener('input', function () {
    const termoBusca = this.value.trim();
    if (termoBusca.length >= 3) {
      setTimeout(realizarBusca, 500); // Debounce simples
    } else if (termoBusca.length === 0) {
      resultadosBuscaEl.innerHTML = `
        <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
          <p style="color:#aaa;margin:0;text-align:center;">Digite um termo para buscar.</p>
        </div>
      `;
    }
  });
});

// Função auxiliar para criar ID de posts
async function criarIDPost(tamanho) {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  let resultado = "";

  for (let i = 0; i < tamanho; i++) {
    resultado += chars.charAt(Math.floor(Math.random() * chars.length));
    if (resultado.length == tamanho - 3) resultado += "-";
  }

  const id = resultado;
  console.log("ID gerado:", id);

  try {
    const url = '../back-end/php/reagir.php';
    console.log("Tentando acessar:", url);

    const response = await fetch(url, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ id })
    });

    console.log("Status da resposta:", response.status);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    // Verificar o tipo de conteúdo retornado
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      console.error("Resposta não é JSON:", await response.text());
      throw new Error('Resposta do servidor não é JSON válido');
    }

    const data = await response.json();
    console.log("Resposta do servidor:", data);

    if (data.duplicate) {
      console.warn("ID duplicado. Tentando novamente...");
      return await criarID(tamanho); // Recursivo até gerar único
    }

    return data;

  } catch (error) {
    console.error("Erro ao gerar ID:", error);
    return { success: false, message: "Erro ao comunicar com o servidor" };
  }
}
