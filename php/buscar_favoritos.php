<?php
session_start();
require_once 'config.php';

// Se for perfil de outro usuário, recebe o ID dele, senão usa da sessão
$id_usuario_alvo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : $_SESSION['id'];

// Query para buscar os posts favoritados por esse usuário
// JOIN com a tabela postagens e login (para pegar dados do autor do post)
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

foreach ($favoritos as $post) {
    include 'template_post.php';
}

if (empty($favoritos)) {
    echo "<p style='color:white; text-align:center; padding:20px;'>Nenhum favorito encontrado.</p>";
}
?>