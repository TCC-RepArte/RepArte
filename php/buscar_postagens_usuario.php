<?php
// Arquivo para buscar postagens de um usuário específico
require_once __DIR__ . '/config.php';
global $con;

// Função que busca postagens de um usuário com filtros
function buscarPostagensUsuario($id_usuario, $desde = 'sempre', $mais = 'recentes')
{
    global $con;

    if (!$con || $con->connect_error) {
        return [];
    }

    // Filtro de Data
    $data_filtro = "";
    if ($desde == '1dia')
        $data_filtro = "AND p.data_post >= NOW() - INTERVAL 1 DAY";
    elseif ($desde == '1semana')
        $data_filtro = "AND p.data_post >= NOW() - INTERVAL 1 WEEK";
    elseif ($desde == '1mes')
        $data_filtro = "AND p.data_post >= NOW() - INTERVAL 1 MONTH";
    elseif ($desde == '3meses')
        $data_filtro = "AND p.data_post >= NOW() - INTERVAL 3 MONTH";

    // Ordenação
    $ordem = "p.data_post DESC";
    if ($mais == 'curtidos') {
        $ordem = "(SELECT COUNT(*) FROM reacoes r WHERE r.id_conteudo = p.id AND r.tipo = 'like') DESC";
    } elseif ($mais == 'comentados') {
        $ordem = "(SELECT COUNT(*) FROM comentarios c WHERE c.id_conteudo = p.id) DESC";
    }

    // Query para buscar postagens do usuário com dados da obra
    $sql = "SELECT 
                p.id,
                p.id_usuario,
                p.titulo as titulo_post,
                p.texto,
                p.data_post,
                o.titulo as obra_titulo,
                o.tipo as obra_tipo,
                o.id as id_obra
            FROM postagens p
            INNER JOIN obras o ON p.id_obra = o.id
            WHERE p.id_usuario = ? $data_filtro
            ORDER BY $ordem";

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