// Funções principais da tela inicial
// Este arquivo gerencia a funcionalidade da página principal do RepArte
// Inclui sistema de curtidas, busca de obras e interface do usuário

// Sistema de Loading para Imagens
// Cria um placeholder animado apenas se a imagem demorar mais de 10 segundos
function criarLoadingPlaceholder(tipo = 'post-image') {
  const loadingDiv = document.createElement('div');
  loadingDiv.className = `image-loading ${tipo}`;
  loadingDiv.innerHTML = `
    <div class="loading-spinner"></div>
  `;
  return loadingDiv;
}

// Função para determinar o tipo de imagem baseado no tipo de obra
function determinarTipoImagem(tipoObra) {
  switch (tipoObra) {
    case 'musica':
      return 'square-size'; // 1:1 para músicas
    case 'filme':
    case 'serie':
      return 'poster-size'; // 2:3 para filmes/séries
    case 'livro':
      return 'cover-size'; // 3:4 para livros
    case 'arte':
      return 'square-size'; // 1:1 para arte
    default:
      return 'post-image'; // Padrão
  }
}

// Função para carregar imagem com loading (apenas após 10 segundos)
async function carregarImagemComLoading(imgElement, obraId, tipoObra) {
  let loadingDiv = null;
  let loadingTimeout = null;
  let imageLoaded = false;

  // Configura timeout para mostrar loading após 10 segundos
  loadingTimeout = setTimeout(() => {
    if (!imageLoaded) {
      loadingDiv = criarLoadingPlaceholder();
      imgElement.parentNode.style.position = 'relative';
      imgElement.parentNode.appendChild(loadingDiv);
    }
  }, 1000); // 10 segundos

  try {
    const obra = await obterDetalhesObra({ apiId: obraId, tipo: tipoObra });

    // Quando a imagem carregar, remove o loading e cancela timeout
    imgElement.onload = () => {
      imageLoaded = true;
      if (loadingTimeout) {
        clearTimeout(loadingTimeout);
      }
      if (loadingDiv) {
        loadingDiv.remove();
      }
    };

    imgElement.onerror = () => {
      imageLoaded = true;
      if (loadingTimeout) {
        clearTimeout(loadingTimeout);
      }
      if (loadingDiv) {
        loadingDiv.remove();
      }
    };

    imgElement.src = obra.imagem;
    imgElement.alt = obra.titulo;

  } catch (error) {
    console.error('Erro ao carregar imagem:', error);
    imageLoaded = true;
    if (loadingTimeout) {
      clearTimeout(loadingTimeout);
    }
    if (loadingDiv) {
      loadingDiv.remove();
    }
  }
}

// Carrega imagens das obras - busca informações das APIs externas
// para exibir as capas/imagens das obras nos posts
async function carregarImagens() {
  const imgPosts = document.querySelectorAll('#img-post');

  const promessas = Array.from(imgPosts).map(async (img_post) => {
    const obraPost = img_post.getAttribute('id-obra');
    const tipoObraPost = img_post.getAttribute('tipo-obra');

    // Usa o sistema de loading
    await carregarImagemComLoading(img_post, obraPost, tipoObraPost);
  });

  await Promise.all(promessas);
}

// Sistema de curtidas e descurtidas
// Gerencia as reações dos usuários aos posts (like/dislike)
const voteButtons = document.querySelectorAll('.vote-buttons');

// Objeto para armazenar o estado das reações de cada post
// Evita que o usuário clique múltiplas vezes no mesmo botão
const estadosPosts = {};

// Carrega o estado atual das reações do usuário
// Busca no banco de dados quais posts o usuário já curtiu/descurtiu
async function carregarEstadoReacoes() {
  for (const voteContainer of voteButtons) {
    const postId = voteContainer.dataset.id;
    try {
      // Carregar estado da reação do usuário
      const reacaoResponse = await fetch('php/buscar_reacao.php', {
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
      const contadorResponse = await fetch('php/contar_reacoes.php', {
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
    const response = await fetch('php/reagir.php', {
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
    console.error('Erro ao enviar reação:', error);
    console.error('Tipo do erro:', error.constructor.name);
    console.error('Mensagem:', error.message);

    if (error.name === 'TypeError' && error.message.includes('fetch')) {
      console.log('Erro de conexão: Não foi possível conectar ao servidor.');
    } else if (error.name === 'SyntaxError') {
      console.log('Erro: Resposta inválida do servidor.');
    } else {
      console.log('Erro: ' + error.message);
    }
  }
}

// Função para atualizar contadores de um post específico
async function atualizarContadoresPost(postId) {
  try {
    const response = await fetch('php/contar_reacoes.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: postId })
    });

    if (response.ok) {
      const data = await response.json();
      if (data.success) {
        // Procura especificamente pela div de botões de voto com este ID
        const voteContainer = document.querySelector(`.vote-buttons[data-id="${postId}"]`);

        if (voteContainer) {
          atualizarContadores(voteContainer, data.likes, data.dislikes);
        } else {
          console.warn(`Container de votos não encontrado para o post ${postId}`);
        }
      }
    }
  } catch (error) {
    console.log('Erro ao atualizar contadores:', error);
  }
}


// Função para verificar se o texto precisa ser truncado
// Limita o tamanho dos textos dos posts para manter o layout organizado
// Se o texto for muito longo, mostra apenas uma parte com botão "Ver mais..."
function verificarTruncamentoTexto() {

  console.log('=== INICIO DA VERIFICAÇÃO ===\n');

  const textContainers = document.querySelectorAll('.post-text-container');
  console.log('Encontrados', textContainers.length, 'containers de texto');

  if (textContainers.length === 0) {
    console.log('Nenhum container encontrado!');
    return;
  }

  textContainers.forEach((container, index) => {
    console.log(`Container ${index}`);
    const textElement = container.querySelector('.post-text-truncated');
    const expandButton = container.querySelector('.expand-button');

    if (textElement && expandButton) {
      // Define um limite de caracteres para truncamento
      const limiteCaracteres = 200;

      // Verifica se o texto já foi expandido pelo usuário
      const limiteAtual = parseInt(textElement.getAttribute('data-limite-atual') || '0');
      const foiExpandido = limiteAtual > limiteCaracteres;

      // Se foi expandido pelo usuário, não re-truncar
      if (foiExpandido) {
        console.log(`Texto já foi expandido pelo usuário, mantendo estado atual`);
        return;
      }

      // Obter texto original - se não existe, usar o atual
      let textoOriginalHTML = textElement.getAttribute('data-texto-original');
      if (!textoOriginalHTML) {
        textoOriginalHTML = textElement.innerHTML;
        textElement.setAttribute('data-texto-original', textoOriginalHTML);
      }

      const textoOriginalTexto = textElement.textContent.trim();

      console.log(`Texto original: "${textoOriginalTexto}"`);
      console.log(`Tamanho: ${textoOriginalTexto.length} caracteres`);
      console.log(`Limite: ${limiteCaracteres} caracteres`);
      console.log(`Limite atual: ${limiteAtual}, Foi expandido: ${foiExpandido}`);

      // Verifica se precisa truncar por caracteres OU por largura
      const precisaTruncar = textoOriginalTexto.length > limiteCaracteres ||
        verificarEstouroLargura(textElement);

      if (precisaTruncar) {

        // Criar um elemento temporário para trabalhar com o HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = textoOriginalHTML;

        // Obter o texto puro para truncamento
        const textoPuro = tempDiv.textContent || tempDiv.innerText || '';
        const textoTruncado = textoPuro.substring(0, limiteCaracteres) + '...';

        // Se o HTML original tinha <br>, manter a estrutura
        if (textoOriginalHTML.includes('<br')) {
          // Criar HTML truncado mantendo algumas quebras de linha
          const linhas = textoOriginalHTML.split('<br');
          let textoTruncadoHTML = '';
          let contadorCaracteres = 0;

          for (let i = 0; i < linhas.length && contadorCaracteres < limiteCaracteres; i++) {
            const linhaTexto = linhas[i].replace(/<[^>]*>/g, ''); // Remove outras tags
            if (contadorCaracteres + linhaTexto.length <= limiteCaracteres) {
              textoTruncadoHTML += linhas[i] + (i < linhas.length - 1 ? '<br' : '');
              contadorCaracteres += linhaTexto.length;
            } else {
              const resto = limiteCaracteres - contadorCaracteres;
              textoTruncadoHTML += linhaTexto.substring(0, resto) + '...';
              break;
            }
          }

          textElement.innerHTML = textoTruncadoHTML;
        } else {
          // Texto simples, truncar normalmente
          textElement.innerHTML = textoTruncado.replace(/\n/g, '<br>');
        }

        // Armazena o texto original no elemento
        textElement.setAttribute('data-texto-original', textoOriginalHTML);
        textElement.setAttribute('data-limite-atual', limiteCaracteres);
        expandButton.setAttribute('data-texto-original', textoOriginalHTML);

        // Força a visibilidade do botão
        expandButton.style.display = 'inline-block';
        expandButton.style.visibility = 'visible';
        expandButton.style.opacity = '1';
        expandButton.classList.add('show');

        // Configura o onclick do botão
        expandButton.onclick = () => expandirTexto(container.dataset.postId);

        console.log(`TRUNCADO: ${textoOriginalTexto.length} -> ${textElement.innerHTML.length} caracteres`);
        console.log(`Texto truncado: "${textElement.innerHTML}"`);
        console.log(`Botão expandir: display=${expandButton.style.display}, class=${expandButton.className}`);
      } else {
        // Se não precisa truncar, mas o botão não está visível, mostrar
        if (textoOriginalTexto.length > limiteCaracteres || verificarEstouroLargura(textElement)) {
          expandButton.style.display = 'inline-block';
          expandButton.style.visibility = 'visible';
          expandButton.style.opacity = '1';
          expandButton.classList.add('show');
          expandButton.onclick = () => expandirTexto(container.dataset.postId);

          // Armazena o texto original
          textElement.setAttribute('data-texto-original', textoOriginalHTML);
          textElement.setAttribute('data-limite-atual', limiteCaracteres);
          expandButton.setAttribute('data-texto-original', textoOriginalHTML);

          console.log(` Botão mostrado para texto que precisa truncar`);
        } else {
          // Não esconder o botão se ele já está visível e tem onclick
          if (expandButton.onclick && expandButton.classList.contains('show')) {
            console.log(`Mantendo botão visível (já configurado)`);
          } else {
            expandButton.style.display = 'none';
            expandButton.classList.remove('show');
            console.log(`Não precisa truncar (${textoOriginalTexto.length} <= ${limiteCaracteres})`);
          }
        }
      }
    } else {
      console.log(`Elementos não encontrados no container ${index}`);
      if (!textElement) console.log('  - Elemento .post-text-truncated não encontrado');
      if (!expandButton) console.log('  - Elemento .expand-button não encontrado');
    }
  });

  console.log('=== FIM DA VERIFICAÇÃO ===\n');
}

// Função para verificar se o texto está estourando em largura
function verificarEstouroLargura(elemento) {
  // Cria uma cópia temporária para medir
  const tempElement = elemento.cloneNode(true);
  tempElement.style.position = 'absolute';
  tempElement.style.visibility = 'hidden';
  tempElement.style.whiteSpace = 'nowrap';
  tempElement.style.width = 'auto';

  document.body.appendChild(tempElement);

  const larguraTexto = tempElement.offsetWidth;
  const larguraContainer = elemento.parentElement.offsetWidth;

  document.body.removeChild(tempElement);

  const estourou = larguraTexto > larguraContainer;

  if (estourou) {
    console.log(`Texto estourou em largura: ${larguraTexto}px > ${larguraContainer}px`);
  }

  return estourou;
}

// Função para expandir texto da postagem (progressivo)
// Quando o usuário clica em "Ver mais...", mostra mais texto do post
// Funciona de forma progressiva - mostra 100 caracteres a mais por vez
function expandirTexto(postId) {
  console.log('=== EXPANDINDO TEXTO ===');
  console.log('Post ID:', postId);

  const container = document.querySelector(`[data-post-id="${postId}"]`);
  if (!container) {
    console.error('Container não encontrado para post', postId);
    return;
  }

  const textElement = container.querySelector('.post-text-truncated');
  const expandButton = container.querySelector('.expand-button');

  if (!textElement || !expandButton) {
    console.error('Elementos não encontrados para post', postId);
    return;
  }

  const textoOriginalHTML = textElement.getAttribute('data-texto-original') || expandButton.getAttribute('data-texto-original');
  const limiteAtual = parseInt(textElement.getAttribute('data-limite-atual') || '200');

  console.log('Texto original HTML:', !!textoOriginalHTML);
  console.log('Limite atual:', limiteAtual);

  if (!textoOriginalHTML) {
    console.error('Texto original não encontrado para post', postId);
    return;
  }

  // Obter texto puro do HTML original
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = textoOriginalHTML;
  const textoPuroOriginal = tempDiv.textContent || tempDiv.innerText || '';
  const novoLimite = limiteAtual + 100; // Aumenta 100 caracteres por vez

  console.log('Texto puro original:', textoPuroOriginal.substring(0, 100) + '...');
  console.log('Tamanho original:', textoPuroOriginal.length);
  console.log('Novo limite:', novoLimite);

  // Se o novo limite excede o texto original, mostra tudo
  if (novoLimite >= textoPuroOriginal.length) {
    textElement.innerHTML = textoOriginalHTML;
    expandButton.textContent = 'Ver menos...';
    expandButton.onclick = () => contrairTexto(postId);
    textElement.removeAttribute('data-limite-atual');
    console.log(`Texto expandido completamente para post ${postId}`);
  } else {
    // Expande progressivamente usando o texto original completo
    const textoTruncado = textoPuroOriginal.substring(0, novoLimite) + '...';

    textElement.innerHTML = textoTruncado.replace(/\n/g, '<br>');
    textElement.setAttribute('data-limite-atual', novoLimite);
    expandButton.textContent = 'Ver mais...';
    console.log(`Texto expandido progressivamente para post ${postId} (${novoLimite} chars)`);
  }
}

// Função para contrair texto da postagem
function contrairTexto(postId) {
  console.log('Contraindo texto para post:', postId);
  const container = document.querySelector(`[data-post-id="${postId}"]`);
  if (container) {
    const textElement = container.querySelector('.post-text-truncated');
    const expandButton = container.querySelector('.expand-button');

    if (textElement && expandButton) {
      const textoOriginalHTML = textElement.getAttribute('data-texto-original') || expandButton.getAttribute('data-texto-original');
      if (textoOriginalHTML) {
        const limiteCaracteres = 200;

        // Trunca o HTML original mantendo as tags <br>
        let textoTruncadoHTML = textoOriginalHTML;

        // Se o HTML tem <br>, precisa truncar de forma inteligente
        if (textoOriginalHTML.includes('<br>')) {
          // Remove as tags <br> temporariamente para contar caracteres
          const textoLimpo = textoOriginalHTML.replace(/<br\s*\/?>/gi, '\n');
          const textoTruncadoLimpo = textoLimpo.substring(0, limiteCaracteres) + '...';

          // Reconverte quebras de linha para <br>
          textoTruncadoHTML = textoTruncadoLimpo.replace(/\n/g, '<br>');
        } else {
          // Se não tem <br>, trunca normalmente
          const textoOriginalTexto = textElement.textContent.trim();
          const textoTruncadoTexto = textoOriginalTexto.substring(0, limiteCaracteres) + '...';
          textoTruncadoHTML = textoTruncadoTexto.replace(/\n/g, '<br>');
        }

        textElement.innerHTML = textoTruncadoHTML;
        textElement.setAttribute('data-limite-atual', limiteCaracteres);
        expandButton.textContent = 'Ver mais...';
        expandButton.onclick = () => expandirTexto(postId);
        console.log(`Texto contraído para post ${postId}`);
      } else {
        console.error('Texto original não encontrado para post', postId);
      }
    } else {
      console.error('Elementos não encontrados para post', postId);
    }
  } else {
    console.error('Container não encontrado para post', postId);
  }
}

// Variáveis globais para armazenar referências
window.selecionarObraCompleta = function (obra) {
  console.log('=== SELECIONANDO OBRA ===');
  console.log('Obra recebida:', obra);

  // Buscar os campos hidden diretamente
  const idInput = document.querySelector('.id_obra');
  const tituloInput = document.querySelector('.tit_obra');
  const autorInput = document.querySelector('.autor_obra');
  const descricaoInput = document.querySelector('.descricao_obra');
  const anoInput = document.querySelector('.ano_obra');
  const tipoInput = document.querySelector('.tipo_obra');
  const imagemInput = document.querySelector('.img_obra');

  console.log('Campos encontrados:', {
    idInput: !!idInput,
    tituloInput: !!tituloInput,
    autorInput: !!autorInput,
    descricaoInput: !!descricaoInput,
    anoInput: !!anoInput,
    tipoInput: !!tipoInput,
    imagemInput: !!imagemInput
  });

  // Preencher os campos hidden com os dados da obra
  if (idInput) idInput.value = obra.apiId || '';
  if (tituloInput) tituloInput.value = obra.titulo || '';
  if (autorInput) autorInput.value = obra.autor || '';
  if (descricaoInput) descricaoInput.value = obra.descricao || '';
  if (anoInput) anoInput.value = obra.ano || '';
  if (tipoInput) tipoInput.value = obra.tipo || '';
  if (imagemInput) imagemInput.value = obra.imagem || '';

  console.log('Valores preenchidos nos campos hidden');

  // Atualizar o preview visual
  const previewBtn = document.getElementById('obra-preview-btn');
  console.log('Preview button encontrado:', !!previewBtn);

  if (previewBtn) {
    const previewImg = previewBtn.querySelector('img');
    const iconePlus = previewBtn.querySelector('.fa-plus');

    console.log('Elementos do preview:', {
      img: !!previewImg,
      iconePlus: !!iconePlus
    });

    if (previewImg) {
      // Adicionar classe para indicar que tem obra
      previewBtn.classList.add('com-obra');

      // Atualizar imagem
      previewImg.src = obra.imagem;
      previewImg.alt = obra.titulo;
      previewImg.style.display = 'block';

      console.log('Imagem atualizada:', previewImg.src);

      // Esconder o ícone de +
      if (iconePlus) {
        iconePlus.style.display = 'none';
      }
    }
  }

  // Fechar o painel - buscar elementos diretamente
  const painelBuscaObras = document.getElementById('painel-busca-obras');
  const overlay = document.querySelector('.overlay');

  console.log('Elementos para fechar:', {
    painel: !!painelBuscaObras,
    overlay: !!overlay
  });

  if (painelBuscaObras) painelBuscaObras.style.display = 'none';
  if (overlay) overlay.style.display = 'none';

  console.log('Obra selecionada com sucesso!');
};

// Função para remover a obra selecionada
window.removerObraSelecionada = function (event) {
  if (event) event.stopPropagation();

  console.log('=== REMOVENDO OBRA ===');

  // Buscar e limpar os campos hidden diretamente
  const idInput = document.querySelector('.id_obra');
  const tituloInput = document.querySelector('.tit_obra');
  const autorInput = document.querySelector('.autor_obra');
  const descricaoInput = document.querySelector('.descricao_obra');
  const anoInput = document.querySelector('.ano_obra');
  const tipoInput = document.querySelector('.tipo_obra');
  const imagemInput = document.querySelector('.img_obra');

  if (idInput) idInput.value = '';
  if (tituloInput) tituloInput.value = '';
  if (autorInput) autorInput.value = '';
  if (descricaoInput) descricaoInput.value = '';
  if (anoInput) anoInput.value = '';
  if (tipoInput) tipoInput.value = '';
  if (imagemInput) imagemInput.value = '';

  console.log('Campos hidden limpos');

  // Resetar o preview visual
  const previewBtn = document.getElementById('obra-preview-btn');

  if (previewBtn) {
    const previewImg = previewBtn.querySelector('img');
    const iconePlus = previewBtn.querySelector('.fa-plus');

    // Remover classe
    previewBtn.classList.remove('com-obra');

    if (previewImg) {
      // Esconder imagem
      previewImg.src = '';
      previewImg.alt = '';
      previewImg.style.display = 'none';
    }

    // Mostrar o ícone de + novamente
    if (iconePlus) {
      iconePlus.style.display = 'block';
    }

    console.log('Preview resetado');
  }

  console.log('Obra removida com sucesso!');
};

// Adicionar evento de clique no botão de remover
const removerObraBtn = document.querySelector('.remover-obra-btn');
if (removerObraBtn) {
  removerObraBtn.addEventListener('click', function (e) {
    e.stopPropagation(); // Evitar que abra o painel
    removerObraSelecionada();
  });
}


// Inicialização quando o DOM carrega
// Executa todas as funções necessárias quando a página termina de carregar
document.addEventListener('DOMContentLoaded', function () {
  console.log('DOM carregado - telainicial.js');

  // Mobile Menu Logic
  const menuToggle = document.getElementById('menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  const closeMenu = document.getElementById('close-menu');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', function () {
      mobileMenu.classList.add('active');
      document.body.style.overflow = 'hidden'; // Prevent scrolling
    });
  }

  if (closeMenu && mobileMenu) {
    closeMenu.addEventListener('click', function () {
      mobileMenu.classList.remove('active');
      document.body.style.overflow = ''; // Restore scrolling
    });
  }

  // Carregar imagens e estado das reações
  carregarImagens();
  carregarEstadoReacoes();
  carregarFavoritosIniciais();
  carregarImagensCarousel();

  // Executar truncamento imediatamente
  verificarTruncamentoTexto();

  // Verificar truncamento de texto após carregar as imagens
  setTimeout(() => {
    console.log('Executando truncamento após 500ms...');
    verificarTruncamentoTexto();
  }, 500);

  // Tentar novamente após mais tempo
  setTimeout(() => {
    console.log('Executando truncamento após 1500ms...');
    verificarTruncamentoTexto();
  }, 1500);

  // Tentar uma terceira vez
  setTimeout(() => {
    console.log('Executando truncamento após 3000ms...');
    verificarTruncamentoTexto();
  }, 3000);

  // Função para debug dos botões
  function debugBotoes() {
    console.log('=== DEBUG DOS BOTÕES ===');
    const containers = document.querySelectorAll('.post-text-container');

    containers.forEach((container, index) => {
      const textElement = container.querySelector('.post-text-truncated');
      const expandButton = container.querySelector('.expand-button');

      console.log(`\n--- Container ${index} ---`);
      console.log('Elemento texto:', !!textElement);
      console.log('Botão:', !!expandButton);

      if (textElement && expandButton) {
        const textoOriginal = textElement.getAttribute('data-texto-original');
        const limiteAtual = textElement.getAttribute('data-limite-atual');
        const textoAtual = textElement.textContent.trim();

        console.log('Texto original armazenado:', !!textoOriginal);
        console.log('Limite atual:', limiteAtual);
        console.log('Texto atual:', textoAtual.substring(0, 50) + '...');
        console.log('Tamanho atual:', textoAtual.length);

        console.log('Botão display:', expandButton.style.display);
        console.log('Botão visibility:', expandButton.style.visibility);
        console.log('Botão opacity:', expandButton.style.opacity);
        console.log('Botão classes:', expandButton.className);
        console.log('Botão onclick:', !!expandButton.onclick);

        // Testar clique
        console.log('Testando clique...');
        if (expandButton.onclick) {
          expandButton.onclick();
          console.log('Clique executado');
        } else {
          console.log('Sem onclick definido');
        }
      }
    });

    console.log('=== FIM DEBUG ===');
  }

  // Adicionar função global para teste
  window.testarTruncamento = verificarTruncamentoTexto;
  window.debugBotoes = debugBotoes;

  // Função para forçar truncamento (teste)
  window.forcarTruncamento = function () {
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
          expandButton.style.display = 'inline-block';
          expandButton.style.visibility = 'visible';
          expandButton.style.opacity = '1';
          expandButton.onclick = () => expandirTexto(container.dataset.postId);
          console.log(`FORÇADO: Container ${index} truncado para ${limiteCaracteres} caracteres`);
        }
      }
    });
  };

  // Função para mostrar todos os botões expandir
  window.mostrarTodosBotoes = function () {
    console.log('=== MOSTRANDO TODOS OS BOTÕES ===');
    const expandButtons = document.querySelectorAll('.expand-button');
    expandButtons.forEach((btn, index) => {
      btn.style.display = 'inline-block';
      btn.style.visibility = 'visible';
      btn.style.opacity = '1';
      btn.classList.add('show');
      console.log(`Botão ${index}: display=${btn.style.display}, class=${btn.className}`);
    });
  };

  // Configurar clique no perfil
  const perfilRed = document.querySelector('.user-red');
  if (perfilRed) {
    perfilRed.addEventListener("click", function () {
      window.location.href = "perfil.php"
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
          <div class="contador-tipos">5/5 tipos selecionados</div>
          <div class="controles-filtro">
            <button class="btn-filtro-todos" type="button">Todos</button>
            <button class="btn-filtro-nenhum" type="button">Nenhum</button>
          </div>
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
  const btnTodos = painelBuscaObras.querySelector('.btn-filtro-todos');
  const btnNenhum = painelBuscaObras.querySelector('.btn-filtro-nenhum');

  // Configurar eventos dos checkboxes
  opcoesLabels.forEach(label => {
    label.addEventListener('click', function (e) {
      // Se clicou diretamente no checkbox, não fazer nada (comportamento padrão)
      if (e.target.type === 'checkbox') {
        return;
      }

      // Se clicou no label, alternar o checkbox
      const checkbox = label.querySelector('input[type="checkbox"]');
      if (checkbox) {
        checkbox.checked = !checkbox.checked;

        // Adicionar/remover classe visual
        if (checkbox.checked) {
          label.classList.add('checked');
        } else {
          label.classList.remove('checked');
        }

        // Disparar evento de mudança para outros listeners
        checkbox.dispatchEvent(new Event('change'));
      }
    });

    // Adicionar classe visual inicial baseada no estado do checkbox
    const checkbox = label.querySelector('input[type="checkbox"]');
    if (checkbox && checkbox.checked) {
      label.classList.add('checked');
    }
  });

  // Eventos de mudança nos checkboxes
  checkboxesTipos.forEach(checkbox => {
    checkbox.addEventListener('change', function () {
      const label = checkbox.closest('.opcao-filtro');
      if (checkbox.checked) {
        label.classList.add('checked');
      } else {
        label.classList.remove('checked');
      }

      // Usar debounce para atualizar busca
      debounceCheckboxes();
    });
  });

  // Eventos dos botões de controle
  btnTodos.addEventListener('click', function () {
    checkboxesTipos.forEach(checkbox => {
      checkbox.checked = true;
      const label = checkbox.closest('.opcao-filtro');
      label.classList.add('checked');
    });

    // Usar debounce para atualizar busca
    debounceCheckboxes();
  });

  btnNenhum.addEventListener('click', function () {
    checkboxesTipos.forEach(checkbox => {
      checkbox.checked = false;
      const label = checkbox.closest('.opcao-filtro');
      label.classList.remove('checked');
    });

    // Usar debounce para atualizar busca
    debounceCheckboxes();
  });

  // Adicionar evento de clique no botão fechar
  fecharBtn.addEventListener('click', function () {
    painelBuscaObras.style.display = 'none';
    overlay.style.display = 'none';
  });

  // Obter botão de buscar obra na página principal
  const buscarObraBtn = document.getElementById('obra-preview-btn');

  // Adicionar evento de clique no botão de remover (dentro do DOMContentLoaded)
  setTimeout(() => {
    const removerObraBtn = document.querySelector('.remover-obra-btn');
    if (removerObraBtn) {
      removerObraBtn.addEventListener('click', function (e) {
        e.stopPropagation(); // Evitar que abra o painel
        removerObraSelecionada(e);
      });
      console.log('Evento do botão remover obra configurado!');
    } else {
      console.warn('Botão remover obra não encontrado!');
    }
  }, 100);


  // Configurar mensagem inicial centralizada
  resultadosBuscaEl.innerHTML = `
    <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
      <p style="color:#aaa;margin:0;text-align:center;">Digite um termo para buscar.</p>
    </div>
  `;

  // Função para obter tipos selecionados
  function getTiposSelecionados() {
    // Garantir que temos os checkboxes atualizados
    const checkboxes = painelBuscaObras.querySelectorAll('input[type="checkbox"]');
    console.log(`Encontrados ${checkboxes.length} checkboxes`);

    const selecionados = Array.from(checkboxes)
      .filter(checkbox => checkbox.checked)
      .map(checkbox => checkbox.value);

    console.log(`Tipos selecionados:`, selecionados);
    return selecionados;
  }

  // Função para atualizar contador de tipos selecionados
  function atualizarContadorTipos() {
    const tiposSelecionados = getTiposSelecionados();
    const contadorEl = painelBuscaObras.querySelector('.contador-tipos');
    if (contadorEl) {
      contadorEl.textContent = `${tiposSelecionados.length}/5 tipos selecionados`;
    }
  }

  // Função para debounce dos checkboxes
  function debounceCheckboxes() {
    clearTimeout(timeoutCheckboxes);
    timeoutCheckboxes = setTimeout(() => {
      console.log('Checkboxes alterados - atualizando busca...');
      atualizarContadorTipos();

      // Se há um termo de busca, realizar nova busca
      const termoBusca = buscaObraInput.value.trim();
      if (termoBusca.length >= 2) {
        ultimoTermoBusca = ''; // Reset para forçar nova busca
        realizarBusca();
      }
    }, 300); // 300ms de debounce
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

  // Controle de busca para evitar múltiplas execuções
  let buscaEmAndamento = false;
  let ultimoTermoBusca = '';

  // Controle de debounce para checkboxes
  let timeoutCheckboxes;

  // Função para realizar busca
  async function realizarBusca() {
    const termoBusca = buscaObraInput.value.trim();

    // Aguardar um pouco para garantir que os checkboxes foram atualizados
    await new Promise(resolve => setTimeout(resolve, 50));

    const tiposSelecionados = getTiposSelecionados();

    // Evitar buscas duplicadas
    if (buscaEmAndamento || termoBusca === ultimoTermoBusca) {
      console.log("Busca já em andamento ou termo igual ao anterior, ignorando...");
      return;
    }

    buscaEmAndamento = true;
    ultimoTermoBusca = termoBusca;

    console.log("Realizando busca para:", termoBusca);
    console.log("Tipos incluídos:", tiposSelecionados);

    if (termoBusca === '') {
      resultadosBuscaEl.innerHTML = `
        <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
          <p style="color:#aaa;margin:0;text-align:center;">Digite um termo para buscar.</p>
        </div>
      `;
      buscaEmAndamento = false;
      return;
    }

    if (tiposSelecionados.length === 0) {
      resultadosBuscaEl.innerHTML = `
        <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
          <p style="color:#aaa;margin:0;text-align:center;">Selecione pelo menos um tipo de obra.</p>
        </div>
      `;
      buscaEmAndamento = false;
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
    } finally {
      // Liberar controle de busca
      buscaEmAndamento = false;
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

    // Verificar se há problemas com a API de arte
    const problemasArte = resultados.filter(r => r.id === 'arte_indisponivel' || r.id === 'arte_erro');
    const temProblemasArte = problemasArte.length > 0;

    // Configuração da paginação
    const itensPorPagina = 3;
    const totalPaginas = Math.ceil(resultados.length / itensPorPagina);
    const paginaAtual = 1;

    // Criar layout para resultados paginados
    const html = `
      <div class="resultados-container" style="height:100%;display:flex;flex-direction:column;overflow:hidden;width:100%;">
        ${temProblemasArte ? `
          <div style="background:#ff4444;color:white;padding:8px;margin-bottom:10px;border-radius:4px;font-size:12px;text-align:center;">
            ⚠️ API de Arte temporariamente indisponível
          </div>
        ` : ''}
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
    console.log('Botão selecionar encontrado:', !!selecionarBtn);

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

      selecionarBtn.addEventListener('click', function (event) {
        event.stopPropagation();
        event.preventDefault();

        console.log('Obra a ser selecionada:', obra);

        console.log('=== SELECIONANDO OBRA ===');

        // Preencher campos hidden
        const idInput = document.querySelector('.id_obra');
        const tituloInput = document.querySelector('.tit_obra');
        const autorInput = document.querySelector('.autor_obra');
        const descricaoInput = document.querySelector('.descricao_obra');
        const anoInput = document.querySelector('.ano_obra');
        const tipoInput = document.querySelector('.tipo_obra');
        const imagemInput = document.querySelector('.img_obra');

        if (idInput) idInput.value = obra.apiId || '';
        if (tituloInput) tituloInput.value = obra.titulo || '';
        if (autorInput) autorInput.value = obra.autor || '';
        if (descricaoInput) descricaoInput.value = obra.descricao || '';
        if (anoInput) anoInput.value = obra.ano || '';
        if (tipoInput) tipoInput.value = obra.tipo || '';
        if (imagemInput) imagemInput.value = obra.imagem || '';

        // Atualizar preview
        const previewBtn = document.getElementById('obra-preview-btn');
        if (previewBtn) {
          const previewImg = previewBtn.querySelector('img');
          const iconePlus = previewBtn.querySelector('.fa-plus');

          if (previewImg) {
            previewBtn.classList.add('com-obra');
            previewImg.src = obra.imagem;
            previewImg.alt = obra.titulo;
            previewImg.style.display = 'block';

            if (iconePlus) iconePlus.style.display = 'none';

            console.log('IMAGEM ATUALIZADA:', obra.imagem);
          }
        }

        // Fechar painel
        const painelBuscaObras = document.getElementById('painel-busca-obras');
        const overlay = document.querySelector('.overlay');
        if (painelBuscaObras) painelBuscaObras.style.display = 'none';
        if (overlay) overlay.style.display = 'none';

        console.log('OBRA SELECIONADA COM SUCESSO!');
      });

      console.log('Evento de clique adicionado ao botão selecionar');
    } else {
      console.error('Botão selecionar NÃO encontrado!');
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
  window.selecionarObra = function (apiId, tipo, titulo, autor, ano, imagem) {
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

  // Busca automática com debounce
  let timeoutBusca;
  buscaObraInput.addEventListener('input', function () {
    clearTimeout(timeoutBusca);

    // Se o campo estiver vazio, limpar resultados
    if (buscaObraInput.value.trim() === '') {
      ultimoTermoBusca = ''; // Reset do último termo
      resultadosBuscaEl.innerHTML = `
        <div style="width:100%;height:100%;display:flex;justify-content:center;align-items:center;">
          <p style="color:#aaa;margin:0;text-align:center;">Digite um termo para buscar.</p>
        </div>
      `;
      return;
    }

    // Aguardar 800ms após parar de digitar antes de buscar
    timeoutBusca = setTimeout(() => {
      const termoAtual = buscaObraInput.value.trim();
      if (termoAtual.length >= 2 && termoAtual !== ultimoTermoBusca) {
        realizarBusca();
      }
    }, 800);
  });

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
    const url = 'php/reagir.php';
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

// Função para copiar o link da postagem
function copiarLink(event, idPost) {
  event.preventDefault(); // Evita que a página suba pro topo

  // Define a base da URL 
  const baseUrl = "https://reparte.free.nf/";

  const link = `${baseUrl}postagem.php?id=${idPost}`;

  // Copia para a área de transferência
  navigator.clipboard.writeText(link).then(() => {
    alert("Link copiado: " + link);
  }).catch(err => {
    console.error('Erro ao copiar: ', err);
  });
}

// Variáveis globais para controle da denúncia
let itemDenunciaAtual = null;
let tipoDenunciaAtual = null;

// Função para abrir o painel de denúncia
function abrirDenuncia(event, idItem, tipo) {
  event.preventDefault();

  itemDenunciaAtual = idItem;
  tipoDenunciaAtual = tipo;

  let painel = document.getElementById('painel-denuncia');
  let overlay = document.querySelector('.overlay-denuncia');

  if (!painel) {
    criarPainelDenuncia();
    painel = document.getElementById('painel-denuncia');
    overlay = document.querySelector('.overlay-denuncia');
  }

  document.getElementById('motivo-denuncia').value = '';

  painel.style.display = 'block';
  overlay.style.display = 'block';
}

// Criar painel
function criarPainelDenuncia() {
  const overlay = document.createElement('div');
  overlay.className = 'overlay-denuncia';
  overlay.style.display = 'none';
  overlay.style.zIndex = "9998";
  document.body.appendChild(overlay);

  const painel = document.createElement('div');
  painel.id = 'painel-denuncia';
  painel.className = 'painel-busca-obras';
  painel.style.display = 'none';
  painel.style.height = 'auto';
  painel.style.maxHeight = '90vh';
  painel.style.zIndex = "9999";

  painel.innerHTML = `
        <div class="painel-cabecalho" style="background-color: #ff6600;">
            <h3 style="color: white;"><i class="fas fa-exclamation-triangle"></i> Denunciar Conteúdo</h3>
            <button class="fechar-painel" onclick="fecharDenuncia()" style="color: white;">×</button>
        </div>

        <div class="painel-conteudo" style="padding: 20px;">
            <p style="color: #ddd; margin-bottom: 15px;">Descreva o motivo da sua denúncia. Isso nos ajuda a manter a comunidade segura.</p>
            
            <div class="form-group">
                <textarea id="motivo-denuncia" rows="5" placeholder="Descreva o motivo da denúncia aqui..."
                    style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #444; background: #1a1a1a; color: white; resize: vertical;"></textarea>
            </div>
            
            <div class="botoes-acao" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button onclick="fecharDenuncia()" style="padding: 8px 15px; background: #444; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
                <button onclick="enviarDenuncia()" style="padding: 8px 15px; background: #ff6600; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Enviar Denúncia</button>
            </div>
        </div>
    `;

  document.body.appendChild(painel);

  overlay.addEventListener('click', fecharDenuncia);
}

// Fechar painel
function fecharDenuncia() {
  const painel = document.getElementById('painel-denuncia');
  const overlay = document.querySelector('.overlay-denuncia');

  if (painel) painel.style.display = 'none';
  if (overlay) overlay.style.display = 'none';

  itemDenunciaAtual = null;
  tipoDenunciaAtual = null;
}

// Enviar denúncia
async function enviarDenuncia() {
  const motivo = document.getElementById('motivo-denuncia').value.trim();

  if (!motivo) {
    Swal.fire({
      icon: 'warning',
      title: 'Ops...',
      text: 'Você precisa escrever um motivo.',
      confirmButtonColor: '#ff6600',
    });
    return;
  }

  if (!itemDenunciaAtual || !tipoDenunciaAtual) {
    Swal.fire({
      icon: 'error',
      title: 'Erro',
      text: 'Não foi possível identificar o item denunciado.',
      confirmButtonColor: '#ff6600',
    });
    return;
  }

  try {
    // ID simples e funcional
    const idDenuncia = 'DEN-' + Date.now();

    const response = await fetch('php/salvar_denuncia.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id_denuncia: idDenuncia,
        id_item: itemDenunciaAtual,
        tipo: tipoDenunciaAtual,
        motivo: motivo
      })
    });

    const data = await response.json();

    if (data.success) {

      // FECHA O PAINEL **ANTES** DO SWEET ALERT
      fecharDenuncia();

      setTimeout(() => {
        Swal.fire({
          icon: 'success',
          title: 'Denúncia enviada!',
          text: 'Obrigado por nos ajudar a manter a comunidade segura.',
          confirmButtonColor: '#ff6600'
        });
      }, 200);

    } else {
      Swal.fire({
        icon: 'error',
        title: 'Erro',
        text: data.error || 'Erro desconhecido.',
        confirmButtonColor: '#ff6600',
      });
    }

  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Erro de conexão',
      text: 'Não foi possível enviar sua denúncia.',
      confirmButtonColor: '#ff6600',
    });
  }
}



// Função para favoritar/desfavoritar
async function toggleFavorito(event, postId) {
  event.preventDefault(); // Impede que o link pule para o topo da página

  // Pega os elementos da tela que vão ser mudados (icone e texto)
  const icon = document.getElementById(`fav-icon-${postId}`);
  const text = document.getElementById(`fav-text-${postId}`);

  try {
    // Manda o pedido para o PHP
    const response = await fetch('php/favoritar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_post: postId })
    });

    const data = await response.json();

    if (data.success) {
      // Se o PHP disse que deu certo, atualiza a tela
      if (data.status === 'favoritado') {
        // Mudou para favoritado: estrela cheia
        icon.classList.remove('far');
        icon.classList.add('fas');
        text.textContent = 'Favoritado';
      } else {
        // Mudou para removido: estrela vazia
        icon.classList.remove('fas');
        icon.classList.add('far');
        text.textContent = 'Favoritar';
      }
    } else {
      console.error('Erro:', data.message);
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
}

// Função para verificar se um post específico está favoritado
async function atualizarEstadoFavorito(postId) {
  try {
    const response = await fetch('php/verificar_favorito.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_post: postId })
    });

    if (response.ok) {
      const data = await response.json();
      if (data.success) {
        // Busca os elementos pelo ID único que colocamos no HTML
        const icon = document.getElementById(`fav-icon-${postId}`);
        const text = document.getElementById(`fav-text-${postId}`);

        if (icon && text) {
          if (data.favorito) {
            // Está favoritado: Estrela cheia
            icon.classList.remove('far');
            icon.classList.add('fas');
            text.textContent = 'Favoritado';
          } else {
            // Não está favoritado: Estrela vazia
            icon.classList.remove('fas');
            icon.classList.add('far');
            text.textContent = 'Favoritar';
          }
        }
      }
    }
  } catch (error) {
    console.log('Erro ao verificar favorito:', error);
  }
}

// Função que roda ao carregar a página para verificar todos os posts
function carregarFavoritosIniciais() {
  // Pega todos os botões que tenham a classe 'btn-favorito'
  const botoes = document.querySelectorAll('.btn-favorito');

  botoes.forEach(btn => {
    const id = btn.getAttribute('data-id');
    if (id) {
      atualizarEstadoFavorito(id);
    }
  });
}

// Carrossel de Obras Populares
let scrollAmount = 0;

function moverCarrossel(direcao) {
  const carousel = document.getElementById('carousel');
  const images = carousel.querySelectorAll('img');

  if (images.length === 0) return;

  // Tamanho de uma imagem + gap
  const step = 110;

  // Largura total do conteúdo
  const maxScroll = carousel.scrollWidth - carousel.clientWidth;

  if (direcao === 1) { // Direita
    scrollAmount += step;
    // Se chegou no fim, volta pro começo (Loop)
    if (scrollAmount > maxScroll) {
      scrollAmount = 0;
    }
  } else { // Esquerda
    scrollAmount -= step;
    // Se passou do começo, vai pro fim (Loop)
    if (scrollAmount < 0) {
      scrollAmount = maxScroll;
    }
  }

  carousel.scrollTo({
    top: 0,
    left: scrollAmount,
    behavior: 'smooth'
  });
}

// Auto-play (passa sozinho a cada 5 segundos)
setInterval(() => {
  moverCarrossel(1);
}, 5000);

// Função para carregar as imagens do carrossel (Obras Populares)
// Função para carregar as imagens do carrossel (Obras Populares) e Mobile
async function carregarImagensCarousel() {
  // Seleciona imagens do carrossel desktop e do grid mobile
  const imgsCarousel = document.querySelectorAll('.carousel-img');
  const imgsMobile = document.querySelectorAll('.obra-mobile-item img');

  // Junta todas as imagens em um único array
  const todasImagens = [...imgsCarousel, ...imgsMobile];

  const promessas = todasImagens.map(async (img) => {
    const id = img.getAttribute('data-id');
    const tipo = img.getAttribute('data-tipo');

    if (id && tipo) {
      // Reutiliza a função de loading que já existe
      await carregarImagemComLoading(img, id, tipo);
    }
  });

  await Promise.all(promessas);
}

// Função para atualizar o preview da obra selecionada
function atualizarPreviewObra(obra) {
  const previewBtn = document.getElementById('obra-preview-btn');

  if (!obra) {
    // Resetar para o estado inicial (quadrado cinza com +)
    previewBtn.classList.remove('has-obra');
    previewBtn.innerHTML = '<i class="fas fa-plus"></i>';
    return;
  }

  // Adicionar classe e mostrar imagem da obra
  previewBtn.classList.add('has-obra');
  previewBtn.innerHTML = `
    <img src="${obra.img}" alt="${obra.titulo}">
    <div class="remove-obra-btn" onclick="removerObraSelecionada(event)">
      <i class="fas fa-times"></i>
    </div>
  `;
}

// Função para remover a obra selecionada
function removerObraSelecionada(event) {
  event.stopPropagation();

  // Limpar os campos hidden
  document.querySelector('.id_obra').value = '';
  document.querySelector('.tit_obra').value = '';
  document.querySelector('.ano_obra').value = '';
  document.querySelector('.tipo_obra').value = '';
  document.querySelector('.autor_obra').value = '';
  document.querySelector('.descricao_obra').value = '';
  document.querySelector('.img_obra').value = '';

  // Resetar o preview
  atualizarPreviewObra(null);
}
