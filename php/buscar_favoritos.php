<?php
session_start();
require_once 'config.php';

// Se for perfil de outro usuário, recebe o ID dele, senão usa da sessão
$id_usuario_alvo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : $_SESSION['id'];

// Query para buscar os posts favoritados por esse usuário
// Fazemos JOIN com a tabela postagens e login (para pegar dados do autor do post)
$sql = "SELECT p.*, l.usuario, l.caminho as foto_usuario 
        FROM favoritos f
        JOIN postagens p ON f.id_post = p.id
        JOIN login l ON p.id_usuario = l.id
        WHERE f.id_usuario = ?
        ORDER BY f.data_favorito DESC";

$stmt = $con->prepare($sql);
$stmt->bind_param("s", $id_usuario_alvo);
$stmt->execute();
$result = $stmt->get_result();

$favoritos = [];
while ($row = $result->fetch_assoc()) {
    $favoritos[] = $row;
}

// Retorna HTML ou JSON. Se você já tem uma estrutura de abas que carrega HTML, 
// o ideal é retornar o HTML dos cards de post.
// Vou assumir que você quer retornar o HTML pronto para injetar na div.

foreach ($favoritos as $post) {
    // Aqui você repete a estrutura HTML do seu card de post (igual da tela inicial)
    // Substituindo as variáveis pelos dados de $post
    include 'template_post.php'; // Sugestão: crie um arquivo template para não repetir código
}

if (empty($favoritos)) {
    echo "<p style='color:white; text-align:center; padding:20px;'>Nenhum favorito encontrado.</p>";
}
?>