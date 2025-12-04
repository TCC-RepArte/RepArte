<?php
error_reporting(0);
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode([]);
    exit;
}

global $con;

if (!$con || $con->connect_error) {
    echo json_encode(['error' => 'Erro de conexão com o banco']);
    exit;
}

try {
    // Primeiro, vamos descobrir a estrutura da tabela favoritos
    // Buscar colunas da tabela favoritos
    $check_sql = "SHOW COLUMNS FROM favoritos";
    $check_result = $con->query($check_sql);

    $columns = [];
    while ($row = $check_result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    // Determinar o nome correto das colunas
    $id_post_column = in_array('id_postagem', $columns) ? 'id_postagem' :
        (in_array('id_post', $columns) ? 'id_post' :
            (in_array('postagem_id', $columns) ? 'postagem_id' : 'id_conteudo'));

    // Query adaptada
    $sql = "SELECT 
                p.id as id_post,
                p.titulo as titulo_post,
                p.id_obra,
                o.titulo as obra_titulo,
                o.tipo as obra_tipo,
                l.usuario,
                CASE 
                    WHEN perf.caminho IS NOT NULL AND perf.caminho != '' THEN perf.caminho
                    ELSE CONCAT('https://ui-avatars.com/api/?name=', REPLACE(l.usuario, ' ', '+'), '&background=ff6600&color=fff&size=50')
                END as foto_usuario
            FROM favoritos f
            JOIN postagens p ON f.$id_post_column = p.id
            JOIN obras o ON p.id_obra = o.id
            JOIN login l ON p.id_usuario = l.id
            LEFT JOIN perfil perf ON l.id = perf.id
            WHERE f.id_usuario = ?
            ORDER BY f.data_favorito DESC";

    $stmt = $con->prepare($sql);

    if (!$stmt) {
        // Se ainda der erro, retornar as colunas disponíveis para debug
        echo json_encode(['error' => 'Colunas disponíveis: ' . implode(', ', $columns)]);
        exit;
    }

    $stmt->bind_param("s", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $favoritos = [];
    while ($row = $result->fetch_assoc()) {
        $favoritos[] = $row;
    }

    echo json_encode($favoritos);

} catch (Exception $e) {
    echo json_encode(['error' => 'Erro: ' . $e->getMessage()]);
}
?>