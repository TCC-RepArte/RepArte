<?php
require_once 'config.php';
global $con;


function postagensFeitas(){
    global $con;
    if($con){

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
        $resultado = $stmt-> get_result();
        $posts = [];

        while ($row = $resultado->fetch_assoc()) {
            $posts[] = $row;
        }

    }else if($con->connect_error){
        $erros[] = 'Erro na conexão!';
        return;
    }

    return $posts;
    
}

?>