document.querySelector('.tab[data-tab="favoritos"]').addEventListener('click', function() {
    // Limpa o conteúdo atual
    const container = document.querySelector('.posts-container'); // ou onde você exibe os posts
    container.innerHTML = '<div class="loading">Carregando...</div>';
    
    // Busca os favoritos
    fetch('php/buscar_favoritos.php?id_usuario=' + idUsuarioAtual) // Defina idUsuarioAtual no PHP
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        });
});