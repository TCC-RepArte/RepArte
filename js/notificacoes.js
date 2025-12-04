// Sistema de notificações
let notifDropdownAberto = false;

// Carregar notificações ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    carregarNotificacoes();
    atualizarBadge();
    
    // Auto-refresh a cada 30 segundos
    setInterval(() => {
        atualizarBadge();
        if (notifDropdownAberto) {
            carregarNotificacoes();
        }
    }, 30000);
    
    // Toggle do dropdown ao clicar no sino
    const notifIcon = document.getElementById('notif-icon');
    const notifDropdown = document.getElementById('notif-dropdown');
    
    if (notifIcon && notifDropdown) {
        notifIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdownAberto = !notifDropdownAberto;
            notifDropdown.style.display = notifDropdownAberto ? 'block' : 'none';
            
            if (notifDropdownAberto) {
                carregarNotificacoes();
            }
        });
        
        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            if (!notifDropdown.contains(e.target) && e.target !== notifIcon) {
                notifDropdownAberto = false;
                notifDropdown.style.display = 'none';
            }
        });
    }
});

// Função para carregar notificações
function carregarNotificacoes() {
    fetch('php/notificacoes_api.php?acao=buscar')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarNotificacoes(data.notificacoes);
            }
        })
        .catch(error => console.error('Erro ao carregar notificações:', error));
}

// Função para renderizar notificações
function renderizarNotificacoes(notificacoes) {
    const notifList = document.getElementById('notif-list');
    
    if (!notifList) return;
    
    if (notificacoes.length === 0) {
        notifList.innerHTML = '<div class="notif-empty">Nenhuma notificação</div>';
        return;
    }
    
    notifList.innerHTML = '';
    
    notificacoes.forEach(notif => {
        const notifItem = document.createElement('div');
        notifItem.className = 'notif-item' + (notif.lida == 0 ? ' unread' : '');
        notifItem.onclick = () => marcarComoLida(notif.id, notif.id_conteudo, notif.tipo);
        
        // Calcular tempo relativo
        const tempoRelativo = calcularTempoRelativo(notif.data_criacao);
        
        notifItem.innerHTML = `
            <img src="${notif.foto_origem}" alt="${notif.nome_origem}">
            <div class="notif-content">
                <p>${notif.mensagem}</p>
                <span class="notif-time">${tempoRelativo}</span>
            </div>
        `;
        
        notifList.appendChild(notifItem);
    });
}

// Função para atualizar badge
function atualizarBadge() {
    fetch('php/notificacoes_api.php?acao=contar_nao_lidas')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    if (data.total > 0) {
                        badge.textContent = data.total > 99 ? '99+' : data.total;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => console.error('Erro ao atualizar badge:', error));
}

// Função para marcar como lida
function marcarComoLida(id, id_conteudo, tipo) {
    fetch('php/notificacoes_api.php?acao=marcar_lida', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            atualizarBadge();
            carregarNotificacoes();
            
            // Redirecionar para o conteúdo
            if (tipo === 'comentario' || tipo === 'reacao') {
                window.location.href = 'postagem.php?id=' + id_conteudo;
            }
        }
    })
    .catch(error => console.error('Erro ao marcar como lida:', error));
}

// Função para marcar todas como lidas
function marcarTodasLidas() {
    fetch('php/notificacoes_api.php?acao=marcar_todas_lidas', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            atualizarBadge();
            carregarNotificacoes();
        }
    })
    .catch(error => console.error('Erro ao marcar todas como lidas:', error));
}

// Função para calcular tempo relativo
function calcularTempoRelativo(dataStr) {
    const data = new Date(dataStr);
    const agora = new Date();
    const diff = Math.floor((agora - data) / 1000); // diferença em segundos
    
    if (diff < 60) return 'agora';
    if (diff < 3600) return Math.floor(diff / 60) + 'm';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h';
    if (diff < 604800) return Math.floor(diff / 86400) + 'd';
    return Math.floor(diff / 604800) + 'sem';
}