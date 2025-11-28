<?php
// Arquivo para buscar postagens de um usuário específico
require_once __DIR__ . '/config.php';
global $con;

// Função que busca postagens de um usuário
function buscarPostagensUsuario($id_usuario)
{
    global $con;

    if (!$con || $con->connect_error) {
        return [];
    }

    // Query para buscar postagens do usuário com dados da obra
    $sql = "SELECT 
                p.id,
                p.titulo as titulo_post,
                p.texto,
                p.data_post,
                o.titulo as obra_titulo,
                o.tipo as obra_tipo,
                o.id as obra_id
            FROM postagens p
            INNER JOIN obras o ON p.id_obra = o.id
            WHERE p.id_usuario = ?
            ORDER BY p.data_post DESC";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    $postagens = [];
    while ($row = $result->fetch_assoc()) {
        $postagens[] = $row;
    }

    return $postagens;
}
?>