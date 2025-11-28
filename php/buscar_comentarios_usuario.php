<?php
// Arquivo para buscar comentários de um usuário específico
require_once __DIR__ . '/config.php';
global $con;

// Função que busca comentários de um usuário
function buscarComentariosUsuario($id_usuario)
{
    global $con;

    if (!$con || $con->connect_error) {
        return [];
    }

    // Query para buscar comentários do usuário com dados da postagem e obra
    $sql = "SELECT 
                c.id,
                c.texto,
                c.data,
                p.id as postagem_id,
                p.titulo as titulo_post,
                o.titulo as obra_titulo,
                o.tipo as obra_tipo
            FROM comentarios c
            INNER JOIN postagens p ON c.id_conteudo = p.id
            LEFT JOIN obras o ON p.id_obra = o.id
            WHERE c.id_usuario = ? AND c.tipo_conteudo = 'postagem'
            ORDER BY c.data DESC";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    $comentarios = [];
    while ($row = $result->fetch_assoc()) {
        $comentarios[] = $row;
    }

    return $comentarios;
}
?>