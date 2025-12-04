// Script para busca de obras via APIs externas com Layout Masonry Dinâmico
console.log('=== BUSCA.JS CARREGADO (MASONRY DINÂMICO) ===');

// Estado global da paginação
let estadoPaginacao = {
    obras: [],
    paginaAtual: 1,
    itensPorPagina: 0, // Será calculado dinamicamente
    indicesPaginas: [0] // Armazena o índice inicial de cada página: [0, 15, 32, ...]
};

// Armazena todas as obras carregadas para permitir filtragem local
window.todasObrasCarregadas = [];

// Retorna o ícone correto baseado no tipo de obra
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

// Função para alternar abas
function openTab(evt, tabName) {
    const tabcontent = document.getElementsByClassName("tab-content-busca");
    for (let i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        tabcontent[i].classList.remove("active");
    }

    const tablinks = document.getElementsByClassName("tab-btn-busca");
    for (let i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }

    const targetTab = document.getElementById(tabName);
    if (targetTab) {
        targetTab.style.display = "block";
        targetTab.classList.add("active");

        // Se for a aba de obras, recalcular layout
        if (tabName === 'obras' && estadoPaginacao.obras.length > 0) {
            // Pequeno delay para garantir que o container está visível
            setTimeout(() => {
                renderizarPaginaDinamica(estadoPaginacao.paginaAtual);
            }, 50);
        }
    }

    if (evt && evt.currentTarget) {
        evt.currentTarget.classList.add("active");
    }
}

// Executa busca automaticamente (Local + API)
async function realizarBuscaAutomatica() {
    const urlParams = new URLSearchParams(window.location.search);
    const termo = urlParams.get('q');

    if (!termo || termo.trim() === '') return;

    const tipos = ['filme', 'serie', 'livro', 'arte', 'musica'];
    const container = document.getElementById('obras-container-dinamico');

    if (!container) return;

    // Mostra o loading enquanto carrega
    container.innerHTML = `
        <div style="text-align:center;padding:60px;">
            <div style="width:40px;height:40px;border:4px solid rgba(255,102,0,0.2);border-radius:50%;border-top-color:#ff6600;animation:spin 1s ease-in-out infinite;margin:0 auto 15px;"></div>
            <p style="color:#aaa;">Buscando obras...</p>
            <style>@keyframes spin { to { transform: rotate(360deg); }}</style>
        </div>
    `;

    try {
        // 1. Processar obras locais (que vieram do PHP)
        let obrasLocais = [];
        if (window.obrasLocais && Array.isArray(window.obrasLocais)) {
            obrasLocais = window.obrasLocais.map(obra => ({
                id: obra.id,
                tipo: obra.tipo || 'arte',
                subtipo: obra.subtipo || '',
                titulo: obra.titulo,
                ano: obra.ano || 'N/A',
                imagem: obra.img || obra.imagem || 'images/placeholder_obra.jpg',
                autor: obra.autor || 'Desconhecido',
                apiId: obra.id,
                origem: 'local'
            }));
        }

        // 2. Buscar na API (Externo)
        const resultado = await buscarObras(termo, tipos);

        // 3. Juntar tudo (Locais + API) na mesma lista
        const todasObras = [...obrasLocais, ...(resultado.resultados || [])];

        // Salvar na variável global para filtragem
        window.todasObrasCarregadas = todasObras;

        // Se não achou nada em lugar nenhum
        if (todasObras.length === 0) {
            container.innerHTML = '<p style="text-align:center;color:#aaa;padding:40px;">Nenhuma obra encontrada.</p>';
            return;
        }

        // Aplicar filtros iniciais (todos marcados por padrão)
        filtrarObras();

    } catch (error) {
        console.error('Erro na busca:', error);
        container.innerHTML = '<p style="text-align:center;color:#aaa;padding:40px;">Erro ao buscar obras.</p>';
    }
}

// Função para filtrar as obras com base nos checkboxes
function filtrarObras() {
    const checkboxes = document.querySelectorAll('.opcao-filtro input[type="checkbox"]');
    const tiposSelecionados = Array.from(checkboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    // Filtrar a lista completa
    const obrasFiltradas = window.todasObrasCarregadas.filter(obra => {
        // Se o tipo da obra estiver na lista de selecionados, retorna true
        // Normaliza o tipo para minúsculo para garantir
        const tipoObra = (obra.tipo || '').toLowerCase();
        return tiposSelecionados.includes(tipoObra);
    });

    // Atualizar estado da paginação
    estadoPaginacao.obras = obrasFiltradas;
    estadoPaginacao.paginaAtual = 1;
    estadoPaginacao.indicesPaginas = [0];

    // Renderizar
    const container = document.getElementById('obras-container-dinamico');
    if (obrasFiltradas.length === 0) {
        container.innerHTML = '<p style="text-align:center;color:#aaa;padding:40px;">Nenhuma obra encontrada com os filtros selecionados.</p>';
        document.getElementById('paginacao-container-obras').innerHTML = '';
    } else {
        renderizarPaginaDinamica(1);
    }
}

// Função para marcar/desmarcar todos os filtros
function marcarTodosFiltros(marcar) {
    const checkboxes = document.querySelectorAll('.opcao-filtro input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = marcar);
    filtrarObras();
}

// Renderiza itens preenchendo o container até não caber mais
function renderizarPaginaDinamica(pagina) {
    const container = document.getElementById('obras-container-dinamico');
    if (!container) return;

    // Limpar container
    container.innerHTML = '';

    if (pagina > estadoPaginacao.indicesPaginas.length) {
        pagina = estadoPaginacao.indicesPaginas.length;
    }

    const indiceInicial = estadoPaginacao.indicesPaginas[pagina - 1];
    let indiceAtual = indiceInicial;

    const containerRect = container.getBoundingClientRect();
    const containerRight = containerRect.right;

    while (indiceAtual < estadoPaginacao.obras.length) {
        const obra = estadoPaginacao.obras[indiceAtual];
        const card = criarCardObra(obra);

        container.appendChild(card);

        const cardRect = card.getBoundingClientRect();

        if (cardRect.left > containerRight - 10) {
            container.removeChild(card);
            break;
        }

        indiceAtual++;
    }

    if (pagina === estadoPaginacao.indicesPaginas.length && indiceAtual < estadoPaginacao.obras.length) {
        estadoPaginacao.indicesPaginas.push(indiceAtual);
    }

    estadoPaginacao.paginaAtual = pagina;
    renderizarControlesPaginacao();
}

function criarCardObra(obra) {
    const div = document.createElement('div');
    div.className = 'obra-card-wrapper';

    const icone = getIconeTipo(obra.tipo, obra.subtipo);
    let titulo = obra.titulo;
    if (titulo.length > 30) titulo = titulo.substring(0, 30) + '...';

    const subtitulo = obra.tipo === 'livro' || obra.tipo === 'arte' ? obra.autor : obra.ano;
    let subtituloTrunc = subtitulo || '';
    if (subtituloTrunc.length > 25) subtituloTrunc = subtituloTrunc.substring(0, 25) + '...';

    const obraUrl = `obra.php?id=${obra.apiId || obra.id}&tipo=${obra.tipo}`;

    div.innerHTML = `
        <a href="${obraUrl}" style="text-decoration:none;color:inherit;display:block;">
            <div class="obra-card" style="position:relative;background:#1a1a1a;border-radius:10px;overflow:hidden;cursor:pointer;transition:all 0.3s ease;">
                <div style="position:absolute;top:8px;left:8px;z-index:2;background:rgba(0,0,0,0.75);padding:6px 8px;border-radius:6px;">
                    ${icone}
                </div>
                
                <img src="${obra.imagem}" 
                     referrerpolicy="no-referrer"
                     alt="${obra.titulo}"
                     style="width:100%;height:auto;display:block;object-fit:cover;background:#2a2a2a;">
                
                <div class="obra-overlay" style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 70%, transparent 100%);padding:15px 10px 10px;transform:translateY(100%);transition:transform 0.3s ease;">
                    <h4 style="color:#fff;font-size:13px;margin:0;font-weight:bold;">${titulo}</h4>
                    <p style="color:#aaa;font-size:11px;margin:3px 0 0;">${subtituloTrunc}</p>
                </div>
            </div>
        </a>
    `;

    const card = div.querySelector('.obra-card');
    const overlay = div.querySelector('.obra-overlay');

    card.addEventListener('mouseenter', () => {
        overlay.style.transform = 'translateY(0)';
        card.style.boxShadow = '0 8px 24px rgba(255, 102, 0, 0.4)';
        card.style.transform = 'translateY(-4px)';
    });

    card.addEventListener('mouseleave', () => {
        overlay.style.transform = 'translateY(100%)';
        card.style.boxShadow = 'none';
        card.style.transform = 'translateY(0)';
    });

    return div;
}

function renderizarControlesPaginacao() {
    let container = document.getElementById('paginacao-container-obras');

    if (!container) {
        // Se o container de paginação foi removido (ex: ao filtrar e não ter resultados), recriar
        // Mas precisamos saber onde colocar. Ele deve estar dentro da div #obras, após o container dinâmico
        // Como a estrutura mudou para ter sidebar, o container de paginação está dentro da div flex-column
        const parent = document.getElementById('obras-container-dinamico').parentElement;
        container = document.createElement('div');
        container.id = 'paginacao-container-obras';
        parent.appendChild(container);
    }

    const paginaAtual = estadoPaginacao.paginaAtual;
    const totalObras = estadoPaginacao.obras.length;
    const temMaisObras = estadoPaginacao.indicesPaginas[paginaAtual] < totalObras ||
        (estadoPaginacao.indicesPaginas.length > paginaAtual);

    let html = '<div style="display:flex;align-items:center;gap:10px;">';

    html += `
        <button onclick="navegarPagina(${paginaAtual - 1})" 
                ${paginaAtual === 1 ? 'disabled' : ''}
                style="padding:8px 15px;border-radius:20px;border:none;background:#333;color:white;cursor:pointer;font-weight:bold;${paginaAtual === 1 ? 'opacity:0.3;cursor:not-allowed;' : ''}">
            <i class="fas fa-chevron-left"></i> Anterior
        </button>
    `;

    if (totalObras > 0) {
        html += `<span style="color:#888;font-size:14px;">Página ${paginaAtual} (${totalObras} obras)</span>`;
    } else {
        html += `<span style="color:#888;font-size:14px;">Nenhuma obra</span>`;
    }

    html += `
        <button onclick="navegarPagina(${paginaAtual + 1})" 
                ${!temMaisObras ? 'disabled' : ''}
                style="padding:8px 15px;border-radius:20px;border:none;background:#ff6600;color:white;cursor:pointer;font-weight:bold;${!temMaisObras ? 'opacity:0.3;cursor:not-allowed;background:#333;' : ''}">
            Próxima <i class="fas fa-chevron-right"></i>
        </button>
    `;

    html += '</div>';
    container.innerHTML = html;
}

function navegarPagina(novaPagina) {
    if (novaPagina < 1) return;

    if (novaPagina > estadoPaginacao.indicesPaginas.length) {
        const ultimoIndice = estadoPaginacao.indicesPaginas[estadoPaginacao.indicesPaginas.length - 1];
        if (ultimoIndice >= estadoPaginacao.obras.length) return;
    }

    renderizarPaginaDinamica(novaPagina);
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof buscarObras === 'undefined') {
        console.error('ERRO: apis-obras.js não foi carregado!');
        return;
    }

    realizarBuscaAutomatica();

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            estadoPaginacao.indicesPaginas = [0];
            renderizarPaginaDinamica(1);
        }, 300);
    });
});

window.navegarPagina = navegarPagina;
window.openTab = openTab;
window.filtrarObras = filtrarObras;
window.marcarTodosFiltros = marcarTodosFiltros;

console.log('=== BUSCA.JS INICIALIZADO ===');
