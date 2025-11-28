<?php

require_once __DIR__ . '/config.php';

// Chamando o BD
global $con;

// Função que puxa dados do usuario logado
function buscaUsuario()
{
    global $con;

    // Verificar se a sessão existe
    if (!isset($_SESSION) || !isset($_SESSION['id'])) {
        return null;
    }

    // Verificar conexão com banco
    if (!$con || $con->connect_error) {
        return null;
    }

    // Utilizando da sessão do id para puxar outros dados do perfil
    $id_usuario = $_SESSION['id'];

    // Fazendo a seleção dos dados do perfil e login
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

    if (!$stmt->execute()) {
        return null;
    }

    $result = $stmt->get_result();

    // Tranformando o resultado em uma array, o row
    if ($result && $row = $result->fetch_assoc()) {
        return $row;
    } else {
        // Retorna null se não encontrar
        return null;
    }
}

?>