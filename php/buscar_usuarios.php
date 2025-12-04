<?php
// Arquivo para buscar outros usuários para a sidebar
// NÃO incluir config.php nem session_start() aqui, pois já foram chamados antes

// Função que busca usuários para exibir na sidebar, excluindo o usuário logado
function buscarOutrosUsuarios($limite = 15)
{
    global $con;

    if (!isset($_SESSION['id'])) {
        return [];
    }

    $id_usuario_logado = $_SESSION['id'];

    // Query para buscar outros usuários com perfil
    $stmt = $con->prepare("SELECT 
        l.id,
        l.usuario,
        p.nomexi,
        CASE 
            WHEN p.caminho IS NOT NULL AND p.caminho != '' THEN p.caminho
            ELSE CONCAT('https://ui-avatars.com/api/?name=', REPLACE(COALESCE(p.nomexi, l.usuario, 'User'), ' ', '+'), '&background=ff6600&color=fff&size=200')
        END as caminho
    FROM login l
    LEFT JOIN perfil p ON l.id = p.id
    WHERE l.id != ?
    ORDER BY p.data_perf DESC
    LIMIT ?");

    $stmt->bind_param("si", $id_usuario_logado, $limite);
    $stmt->execute();
    $result = $stmt->get_result();

    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }

    return $usuarios;
}
?>