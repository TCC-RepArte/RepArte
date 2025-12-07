document.querySelector('.tab[data-tab="favoritos"]').addEventListener('click', function() {
    // Limpa o conteúdo atual
    const container = document.querySelector('.posts-container'); 
    container.innerHTML = '<div class="loading">Carregando...</div>';
    
    // Busca os favoritos
    fetch('php/buscar_favoritos.php?id_usuario=' + idUsuarioAtual) 
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        });
});