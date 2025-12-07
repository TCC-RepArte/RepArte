document.addEventListener('DOMContentLoaded', function() {
    const searchType = document.getElementById('search-type');
    const searchCriteria = document.getElementById('search-criteria');
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('btn-search-id');
    const resultsContainer = document.getElementById('search-results');

    // Mapeamento de critérios por tipo
    const criteriaMap = {
        'postagem': [
            { value: 'texto', text: 'Texto / Título' },
            { value: 'usuario', text: 'Usuário (Autor)' },
            { value: 'hashtag', text: 'Hashtag' },
            { value: 'id', text: 'ID Exato' }
        ],
        'usuario': [
            { value: 'nome', text: 'Nome / @Usuário' },
            { value: 'email', text: 'Email' },
            { value: 'id', text: 'ID Exato' }
        ],
        'comentario': [
            { value: 'texto', text: 'Conteúdo' },
            { value: 'usuario', text: 'Usuário (Autor)' },
            { value: 'id', text: 'ID Exato' }
        ]
    };

    // Atualizar select de critérios quando o tipo muda
    if (searchType) {
        searchType.addEventListener('change', function() {
            const type = this.value;
            const options = criteriaMap[type] || [];
            
            searchCriteria.innerHTML = '';
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.text;
                searchCriteria.appendChild(option);
            });
        });

        // Disparar evento change inicialmente para popular o select
        searchType.dispatchEvent(new Event('change'));
    }

    // Função de busca
    if (searchBtn) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = searchType.value;
            const criteria = searchCriteria.value;
            const query = searchInput.value.trim();

            if (!query) {
                alert('Digite algo para buscar!');
                return;
            }

            resultsContainer.innerHTML = '<div class="loading">Buscando...</div>';

            fetch(`php/admin_search.php?type=${type}&criteria=${criteria}&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';
                    
                    if (data.error) {
                        resultsContainer.innerHTML = `<div class="error">Erro: ${data.error}</div>`;
                        return;
                    }

                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<div class="empty">Nenhum resultado encontrado.</div>';
                        return;
                    }

                    const list = document.createElement('div');
                    list.className = 'results-list';

                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'result-item';
                        div.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #333; background: rgba(255,255,255,0.05); margin-bottom: 5px; border-radius: 5px;';
                        
                        let content = '';
                        let title = '';
                        
                        if (type === 'postagem') {
                            title = item.titulo ? item.titulo : 'Sem título';
                            content = item.texto ? item.texto.substring(0, 80) + '...' : '';
                            div.innerHTML = `
                                <div class="result-info" style="flex: 1;">
                                    <strong style="color: #fff;">${title}</strong> <span style="color: #888;">(por @${item.usuario})</span><br>
                                    <small style="color: #ccc;">${content}</small><br>
                                    <span class="id-badge" style="font-family: monospace; background: #333; padding: 2px 5px; border-radius: 3px; font-size: 12px; color: #ff6600;">${item.id}</span>
                                </div>
                                <button class="btn-copy" onclick="copiarId('${item.id}', 'postagem')" style="background: #ff6600; border: none; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                                    <i class="fas fa-arrow-down"></i> Usar
                                </button>
                            `;
                        } else if (type === 'usuario') {
                            title = item.usuario;
                            content = item.nomexi ? item.nomexi : '';
                            const email = item.email ? ` | ${item.email}` : '';
                            div.innerHTML = `
                                <div class="result-info" style="flex: 1;">
                                    <strong style="color: #fff;">@${title}</strong> <span style="color: #888;">${content ? `(${content})` : ''}</span><br>
                                    <small style="color: #ccc;">${email}</small><br>
                                    <span class="id-badge" style="font-family: monospace; background: #333; padding: 2px 5px; border-radius: 3px; font-size: 12px; color: #ff6600;">${item.id}</span>
                                </div>
                                <button class="btn-copy" onclick="copiarId('${item.id}', 'usuario')" style="background: #ff6600; border: none; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                                    <i class="fas fa-arrow-down"></i> Usar
                                </button>
                            `;
                        } else if (type === 'comentario') {
                            content = item.texto ? item.texto.substring(0, 80) + '...' : '';
                            div.innerHTML = `
                                <div class="result-info" style="flex: 1;">
                                    <strong style="color: #fff;">Comentário de @${item.usuario}</strong><br>
                                    <small style="color: #ccc;">${content}</small><br>
                                    <span class="id-badge" style="font-family: monospace; background: #333; padding: 2px 5px; border-radius: 3px; font-size: 12px; color: #ff6600;">${item.id}</span>
                                </div>
                                <button class="btn-copy" onclick="copiarId('${item.id}', 'comentario')" style="background: #ff6600; border: none; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                                    <i class="fas fa-arrow-down"></i> Usar
                                </button>
                            `;
                        }

                        list.appendChild(div);
                    });

                    resultsContainer.appendChild(list);
                })
                .catch(err => {
                    console.error(err);
                    resultsContainer.innerHTML = '<div class="error">Erro ao buscar. Tente novamente.</div>';
                });
        });
    }
});

// Função global para copiar ID para o formulário de exclusão
window.copiarId = function(id, type) {
    const idInput = document.querySelector('input[name="id_item"]');
    const typeSelect = document.querySelector('select[name="tipo_item"]');
    
    if (idInput && typeSelect) {
        idInput.value = id;
        typeSelect.value = type;
        
        // Scroll até o formulário
        idInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Feedback visual
        idInput.style.transition = 'background-color 0.3s';
        idInput.style.backgroundColor = '#ff660055';
        setTimeout(() => {
            idInput.style.backgroundColor = '';
        }, 1000);
    }
};
