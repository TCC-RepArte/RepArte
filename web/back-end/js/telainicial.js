// Aguardar o carregamento completo do DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado');
    
    // Referência ao botão de buscar obra
    const buscarObraBtn = document.getElementById('buscar-obra-btn');
    console.log('Botão buscar obra:', buscarObraBtn);
    
    // Referência ao painel de busca
    const painelBuscaObras = document.getElementById('painel-busca-obras');
    console.log('Painel busca obras:', painelBuscaObras);
    
    // Função para abrir o painel de busca se ele existir
    function abrirPainel() {
        console.log('Abrindo painel');
        const painel = document.getElementById('painel-busca-obras');
        const overlay = document.querySelector('.overlay');
        
        if (painel) {
            painel.style.display = 'block';
            if (overlay) overlay.style.display = 'block';
            
            const input = painel.querySelector('#busca-obra-input');
            if (input) input.focus();
        } else {
            console.log('Painel de busca ainda não foi criado');
        }
    }
    
    // Função para fechar o painel de busca
    function fecharPainel() {
        console.log('Fechando painel');
        const painel = document.getElementById('painel-busca-obras');
        const overlay = document.querySelector('.overlay');
        
        if (painel) {
            painel.style.display = 'none';
            if (overlay) overlay.style.display = 'none';
            
            const input = painel.querySelector('#busca-obra-input');
            if (input) input.value = '';
            const resultadosBusca = painel.querySelector('.resultados-busca');
            if (resultadosBusca) resultadosBusca.innerHTML = '';
        } else {
            console.log('Painel de busca ainda não foi criado');
        }
    }
    
    // Função para realizar a busca de obras
    function buscarObra() {
        const termoBusca = document.getElementById('busca-obra-input').value.trim();
        
        if (termoBusca === '') {
            const resultadosBusca = document.querySelector('.resultados-busca');
            if (resultadosBusca) resultadosBusca.innerHTML = '<p>Digite um termo para buscar.</p>';
            return;
        }
        
        setTimeout(() => {
            // Exemplo de resultados
            const obras = [
                { titulo: 'Black Mirror', tipo: 'Série', ano: '2011-2023', imagem: '../imagens/blackmirror.jpg' },
                { titulo: 'As Vantagens de Ser Invisível', tipo: 'Livro/Filme', ano: '2012', imagem: '../imagens/vantagens.png' },
                { titulo: 'O Menino do Pijama Listrado', tipo: 'Livro/Filme', ano: '2008', imagem: '../imagens/listrado.jpg' }
            ];
            
            if (obras.length > 0) {
                let html = '<ul class="lista-obras">';
                obras.forEach(obra => {
                    html += `
                        <li class="item-obra" data-titulo="${obra.titulo}">
                            <img src="${obra.imagem}" alt="${obra.titulo}">
                            <div class="obra-info">
                                <h4>${obra.titulo}</h4>
                                <p>${obra.tipo} • ${obra.ano}</p>
                            </div>
                            <button class="selecionar-obra">Selecionar</button>
                        </li>
                    `;
                });
                html += '</ul>';
                if (resultadosBusca) resultadosBusca.innerHTML = html;
                
                // Adicionar eventos aos botões de selecionar
                document.querySelectorAll('.selecionar-obra').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const obraTitulo = this.parentElement.getAttribute('data-titulo');
                        const obraImg = this.parentElement.querySelector('img').src;
                        
                        // Aqui você pode adicionar a lógica para inserir a obra selecionada na análise
                        alert(`Obra "${obraTitulo}" selecionada para análise!`);
                        
                        // Fechar o painel após selecionar
                        fecharPainel();
                    });
                });
            } else {
                if (resultadosBusca) resultadosBusca.innerHTML = '<p>Nenhuma obra encontrada com esse termo.</p>';
            }
        }, 500);
    }
    
    // Registrar evento de clique no botão se ele existir
    if (buscarObraBtn) {
        buscarObraBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Botão de buscar obra clicado');
            abrirPainel();
        });
    }
    
    // Event listeners
    const fecharPainelBtn = document.querySelector('.fechar-painel');
    if (fecharPainelBtn) {
        fecharPainelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fecharPainel();
        });
    }
    
    const overlay = document.querySelector('.overlay');
    if (overlay) {
        overlay.addEventListener('click', fecharPainel);
    }
    
    const buscarBtn = document.querySelector('.btn-buscar');
    if (buscarBtn) {
        buscarBtn.addEventListener('click', function(e) {
            e.preventDefault();
            buscarObra();
        });
    }
    
    // Permitir busca ao pressionar Enter
    const buscaInput = document.getElementById('busca-obra-input');
    if (buscaInput) {
        buscaInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarObra();
            }
        });
    }
    
    // Garantir que o botão de buscar obra funcione
    console.log('Event listeners configurados');
}); 