<?php
require_once 'config.php';
global $con;


function postagensFeitas()
{
    global $con;
    if ($con) {

        $sql = "SELECT 
                p.*,
                p.id_usuario,
                COALESCE(pf.nomexi, l.usuario) AS usuario,
                l.usuario AS arroba,
                CASE 
                    WHEN pf.caminho IS NOT NULL AND pf.caminho != '' THEN pf.caminho
                    ELSE CONCAT('https://ui-avatars.com/api/?name=', REPLACE(COALESCE(pf.nomexi, l.usuario, 'User'), ' ', '+'), '&background=ff6600&color=fff&size=50')
                END AS foto,
                o.tipo as obra_tipo
            FROM postagens AS p
            INNER JOIN login AS l ON p.id_usuario = l.id
            LEFT JOIN perfil AS pf ON p.id_usuario = pf.id
            INNER JOIN obras AS o ON p.id_obra = o.id
            ORDER BY p.data_post DESC";

        $stmt = $con->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $posts = [];

        while ($row = $resultado->fetch_assoc()) {
            $posts[] = $row;
        }

    } else if ($con->connect_error) {
        $erros[] = 'Erro na conexão!';
        return;
    }

    return $posts;

}

// Função para buscar as hashtags mais usadas ou recentes
function buscarHashtagsPopulares()
{
    global $con;
    // Busca as 15 hashtags mais usadas (contando quantas vezes aparecem na tabela post_hashtags)
    // Se quiser por ordem de criação, mude ORDER BY para h.id DESC
    $sql = "SELECT h.nome, COUNT(ph.hashtag_id) as uso 
            FROM hashtags h 
            LEFT JOIN post_hashtags ph ON h.id = ph.hashtag_id 
            GROUP BY h.id 
            ORDER BY uso DESC, h.id DESC 
            LIMIT 15";

    $result = $con->query($sql);
    $hashtags = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $hashtags[] = $row;
        }
    }
    return $hashtags;
}

// Função para buscar as 10 obras mais comentadas (populares)
function buscarObrasPopulares()
{
    global $con;
    // Conta quantos posts existem para cada obra
    $sql = "SELECT o.id, o.titulo, o.tipo, COUNT(p.id) as total_posts
            FROM obras o
            JOIN postagens p ON o.id = p.id_obra
            GROUP BY o.id
            ORDER BY total_posts DESC
            LIMIT 10";

    $result = $con->query($sql);
    $obras = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Se não tiver imagem no banco, usa uma placeholder
            if (empty($row['imagem'])) {
                $row['imagem'] = 'images/placeholder_obra.jpg';
            }
            $obras[] = $row;
        }
    }
    return $obras;
}

?>