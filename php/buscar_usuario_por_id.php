<?php
// Arquivo para buscar dados de um usuário por ID
require_once __DIR__ . '/config.php';
global $con;

// Função que busca dados de um usuário por ID
function buscarUsuarioPorId($id_usuario)
{
    global $con;

    if (!$con || $con->connect_error) {
        return null;
    }

    // Query para buscar dados do usuário
    $stmt = $con->prepare("SELECT p.*, l.usuario, l.email,
                           CASE 
                               WHEN p.caminho IS NOT NULL AND p.caminho != '' THEN p.caminho
                               ELSE CONCAT('https://ui-avatars.com/api/?name=', REPLACE(COALESCE(p.nomexi, l.usuario, 'User'), ' ', '+'), '&background=ff6600&color=fff&size=200')
                           END as caminho
                           FROM login l
                           LEFT JOIN perfil p ON l.id = p.id
                           WHERE l.id = ?");

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        return $row;
    }

    return null;
}
?>