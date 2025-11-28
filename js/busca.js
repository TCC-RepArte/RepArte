// Script para busca de obras - FUNCIONAL
// Integrado com apis-obras.js seguindo padrão do telainicial.js

console.log('=== BUSCA.JS CARREGADO ===');

// Função para carregar imagens das obras nos resultados de busca
async function carregarImagensObras() {
    console.log('Iniciando carregamento de imagens das obras...');

    // Verificar se apis-obras.js foi carregado
    if (typeof obterDetalhesObra === 'undefined') {
        console.error('ERRO CRÍTICO: apis-obras.js não foi carregado!');
        console.error('A função obterDetalhesObra não está disponível');
        return;
    }

    console.log('✓ apis-obras.js carregado com sucesso');

    const obrasItems = document.querySelectorAll('.obra-item');
    console.log(`Encontrados ${obrasItems.length} itens de obras para carregar`);

    if (obrasItems.length === 0) {
        console.log('Nenhuma obra encontrada para carregar imagens');
        return;
    }

    // Processar todas as obras em paralelo (como no telainicial.js)
    const promessas = Array.from(obrasItems).map(async (item, index) => {
        const obraId = item.getAttribute('data-obra-id');
        const obraTipo = item.getAttribute('data-obra-tipo');
        const imgElement = item.querySelector('.obra-imagem');

        console.log(`[${index + 1}/${obrasItems.length}] Carregando obra: ID=${obraId}, Tipo=${obraTipo}`);

        if (!imgElement) {
            console.error(`Elemento de imagem não encontrado para obra ${obraId}`);
            return;
        }

        try {
            // Buscar detalhes da obra usando a função do apis-obras.js
            const obra = await obterDetalhesObra({ apiId: obraId, tipo: obraTipo });

            console.log(`✓ Obra carregada:`, {
                titulo: obra.titulo,
                tipo: obra.tipo,
                imagem: obra.imagem ? obra.imagem.substring(0, 50) + '...' : 'sem imagem'
            });

            // Configurar eventos de carregamento
            imgElement.onload = () => {
                console.log(`✓ Imagem renderizada: ${obra.titulo}`);
                imgElement.style.background = 'transparent';
            };

            imgElement.onerror = () => {
                console.error(`✗ Erro ao carregar imagem: ${obra.titulo}`);
                imgElement.style.background = '#2a2a2a';
                imgElement.alt = 'Imagem não disponível';
            };

            // Atualizar imagem
            imgElement.src = obra.imagem;
            imgElement.alt = obra.titulo;

        } catch (error) {
            console.error(`✗ Erro ao carregar obra ${obraId}:`, error.message || error);
            // Manter fundo cinza em caso de erro
            imgElement.style.background = '#2a2a2a';
            imgElement.alt = 'Erro ao carregar';
        }
    });

    // Aguardar todas as promessas
    try {
        await Promise.all(promessas);
        console.log('=== CARREGAMENTO DE IMAGENS CONCLUÍDO ===');
    } catch (error) {
        console.error('Erro ao aguardar carregamento de todas as imagens:', error);
    }
}

// Função para alternar abas
function openTab(evt, tabName) {
    console.log(`Alternando para aba: ${tabName}`);

    var i, tabcontent, tablinks;

    // Esconder todos os conteúdos
    tabcontent = document.getElementsByClassName("tab-content-busca");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        tabcontent[i].classList.remove("active");
    }

    // Remover classe active de todos os botões
    tablinks = document.getElementsByClassName("tab-btn-busca");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Mostrar conteúdo atual e ativar botão
    const targetTab = document.getElementById(tabName);
    if (targetTab) {
        targetTab.style.display = "block";
        targetTab.classList.add("active");
    }

    if (evt && evt.currentTarget) {
        evt.currentTarget.className += " active";
    }

    console.log(`✓ Aba ${tabName} ativada`);
}

// Inicializar quando o DOM carregar
document.addEventListener('DOMContentLoaded', function () {
    console.log('=== DOM CARREGADO - INICIALIZANDO BUSCA ===');

    // Verificar se apis-obras.js foi carregado
    if (typeof obterDetalhesObra === 'undefined') {
        console.error('ERRO: apis-obras.js não foi carregado!');
        console.error('Certifique-se de que o script está sendo incluído antes de busca.js');
        console.error('Ordem correta: <script src="js/apis-obras.js"></script> <script src="js/busca.js"></script>');
        return;
    }

    console.log('✓ apis-obras.js disponível');
    console.log('✓ Iniciando carregamento de imagens das obras...');

    // Carregar imagens das obras
    carregarImagensObras();
});

// Exportar função para uso global
window.carregarImagensObras = carregarImagensObras;
window.openTab = openTab;

console.log('=== BUSCA.JS INICIALIZADO ===');
