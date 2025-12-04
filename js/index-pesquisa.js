// Sistema de pesquisa e carrossel de obras para index.php
let obrasEncontradas = []; // Armazena as obras encontradas na pesquisa
let indiceAtual = 0; // Índice da obra atual no carrossel

// Aguarda o carregamento completo da página
document.addEventListener('DOMContentLoaded', function () {
    const inputPesquisa = document.querySelector('.search-text');
    const setaEsquerda = document.querySelector('.seta-esquerda');
    const setaDireita = document.querySelector('.seta-direita');
    const containerImagem = document.querySelector('.img-obra-container');

    // Evento de pesquisa - dispara quando o usuário digita
    let timeoutPesquisa;
    inputPesquisa.addEventListener('input', function () {
        clearTimeout(timeoutPesquisa);
        const termo = this.value.trim();

        // Só pesquisa se tiver pelo menos 3 caracteres
        if (termo.length >= 3) {
            timeoutPesquisa = setTimeout(() => {
                pesquisarObras(termo);
            }, 500); // Aguarda 500ms após parar de digitar
        }
    });

    // Navegação do carrossel - seta esquerda
    setaEsquerda.addEventListener('click', function () {
        if (obrasEncontradas.length > 0) {
            indiceAtual = (indiceAtual - 1 + obrasEncontradas.length) % obrasEncontradas.length;
            exibirObra(indiceAtual);
        }
    });

    // Navegação do carrossel - seta direita
    setaDireita.addEventListener('click', function () {
        if (obrasEncontradas.length > 0) {
            indiceAtual = (indiceAtual + 1) % obrasEncontradas.length;
            exibirObra(indiceAtual);
        }
    });

    // Redireciona para login ao clicar na imagem
    containerImagem.addEventListener('click', function () {
        window.location.href = 'login1.php';
    });
});

// Função para pesquisar obras usando a API
async function pesquisarObras(termo) {
    const containerImagem = document.querySelector('.img-obra-container');

    // Mostra loading durante a busca
    mostrarLoading(containerImagem);

    try {
        // Busca obras em todas as categorias
        const tipos = ['filme', 'serie', 'livro', 'arte', 'musica'];
        const resultado = await buscarObras(termo, tipos);

        if (resultado.resultados && resultado.resultados.length > 0) {
            // Limita a 5 obras
            obrasEncontradas = resultado.resultados.slice(0, 5);
            indiceAtual = 0;
            exibirObra(0);
        } else {
            // Nenhuma obra encontrada
            exibirMensagemNenhumaObra();
        }
    } catch (erro) {
        console.error('Erro ao pesquisar obras:', erro);
        exibirMensagemErro();
    }
}

// Função para exibir uma obra no carrossel
async function exibirObra(indice) {
    const obra = obrasEncontradas[indice];
    const containerImagem = document.querySelector('.img-obra-container');
    const imagem = containerImagem.querySelector('.img-obra');

    // Remove loading da busca de obras
    removerLoading(containerImagem);

    // Atualiza a imagem da obra
    imagem.src = obra.imagem;
    imagem.alt = obra.titulo;

    // Remove tags antigas e mostra loading de hashtags
    const tagsAntigas = containerImagem.querySelectorAll('.tag-flutuante');
    tagsAntigas.forEach(tag => tag.remove());

    // Mostra loading enquanto busca hashtags
    mostrarLoadingHashtags(containerImagem);

    // Busca as hashtags mais comentadas desta obra
    // Usa apiId se existir, senão usa id
    const idParaBuscar = obra.apiId || obra.id;

    try {
        const response = await fetch(`php/buscar_hashtags_obra.php?id_obra=${encodeURIComponent(idParaBuscar)}`);
        const dados = await response.json();

        console.log('Resposta da busca de hashtags:', dados); // Debug

        // Remove loading de hashtags
        const loadingTag = containerImagem.querySelector('.tag-loading');
        if (loadingTag) loadingTag.remove();

        if (dados.sucesso && dados.hashtags && dados.hashtags.length > 0) {
            // Exibe as 3 hashtags mais comentadas
            dados.hashtags.forEach((hashtag, i) => {
                const tag = document.createElement('span');
                tag.className = `tag-flutuante tag${i + 1}`;
                tag.textContent = hashtag.nome; // Já vem com # do banco
                containerImagem.appendChild(tag);
            });
        } else {
            // Nenhuma hashtag encontrada - convida o usuário a comentar
            const tagConvite = document.createElement('span');
            tagConvite.className = 'tag-flutuante tag3 tag-convite';
            tagConvite.innerHTML = '💬 Seja o primeiro a comentar!';
            containerImagem.appendChild(tagConvite);
        }
    } catch (erro) {
        console.error('Erro ao buscar hashtags:', erro);

        // Remove loading de hashtags
        const loadingTag = containerImagem.querySelector('.tag-loading');
        if (loadingTag) loadingTag.remove();

        // Em caso de erro, mostra convite para comentar
        const tagConvite = document.createElement('span');
        tagConvite.className = 'tag-flutuante tag3 tag-convite';
        tagConvite.innerHTML = '💬 Seja o primeiro a comentar!';
        containerImagem.appendChild(tagConvite);
    }
}

// Exibe mensagem quando nenhuma obra é encontrada
function exibirMensagemNenhumaObra() {
    const containerImagem = document.querySelector('.img-obra-container');
    const imagem = containerImagem.querySelector('.img-obra');

    imagem.src = 'images/vantagens.png';
    imagem.alt = 'Nenhuma obra encontrada';

    // Remove tags antigas
    const tagsAntigas = containerImagem.querySelectorAll('.tag-flutuante');
    tagsAntigas.forEach(tag => tag.remove());

    // Adiciona mensagem
    const tagMensagem = document.createElement('span');
    tagMensagem.className = 'tag-flutuante tag-mensagem';
    tagMensagem.textContent = '🔍 Nenhuma obra encontrada';
    containerImagem.appendChild(tagMensagem);
}

// Exibe mensagem de erro
function exibirMensagemErro() {
    const containerImagem = document.querySelector('.img-obra-container');
    const imagem = containerImagem.querySelector('.img-obra');

    imagem.src = 'images/vantagens.png';
    imagem.alt = 'Erro na pesquisa';

    // Remove tags antigas
    const tagsAntigas = containerImagem.querySelectorAll('.tag-flutuante');
    tagsAntigas.forEach(tag => tag.remove());

    // Adiciona mensagem de erro
    const tagErro = document.createElement('span');
    tagErro.className = 'tag-flutuante tag-erro';
    tagErro.textContent = '⚠️ Erro ao buscar obras';
    containerImagem.appendChild(tagErro);
}

function mostrarLoading(container) {
    const imagem = container.querySelector('.img-obra');

    // Mantém a imagem atual mas adiciona overlay de loading
    const tagsAntigas = container.querySelectorAll('.tag-flutuante, .loading-overlay');
    tagsAntigas.forEach(tag => tag.remove());

    // Cria overlay de loading
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner"></div>
        <p class="loading-text">Buscando obras...</p>
    `;
    container.appendChild(loadingOverlay);
}

// Mostra loading durante busca de hashtags
function mostrarLoadingHashtags(container) {
    const tagsAntigas = container.querySelectorAll('.tag-flutuante');
    tagsAntigas.forEach(tag => tag.remove());

    const loadingTag = document.createElement('span');
    loadingTag.className = 'tag-flutuante tag3 tag-loading';
    loadingTag.innerHTML = '⏳ Carregando...';
    container.appendChild(loadingTag);
}

// Remove o loading
function removerLoading(container) {
    const loadingOverlay = container.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
    }