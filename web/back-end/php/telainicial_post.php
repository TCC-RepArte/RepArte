<?php
require_once 'config.php';
global $con;


function postagensFeitas(){
    global $con;
    if($con){

        $sql = "SELECT 
                p.*,
                pf.nomexi AS usuario,
                l.usuario AS arroba, 
                pf.caminho AS foto ,
                o.tipo as obra_tipo
            FROM postagens AS p
            INNER JOIN perfil AS pf ON p.id_usuario = pf.id
            INNER JOIN obras AS o ON p.id_obra = o.id
            INNER JOIN login AS l ON p.id_usuario = l.id
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