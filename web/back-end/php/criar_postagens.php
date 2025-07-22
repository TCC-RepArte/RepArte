<?php


require_once 'config.php';
session_start();

global $con;

$idObra = $_POST['id'];

if(!empty($_SERVER['REQUEST_METHOD'] === 'POST')){

    function InserirDados(){

        global $con;
        global $idObra;
    
        // Informações da postagem
        $titulo = $_POST['titulo_post'];
        $texto = $_POST['texto'];
    
        // Definindo datas
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("d-m-Y");
        $hora = date("s:i:H");
        $datetime = date("Y-m-d H:i:s");
    
        // Pegando id do emissor e gerando id para postagem
        $id = $_SESSION['id'];
        $idPost = uniqid();
    
        // Inserindo informações na tabela 'postagens'
        $stmt = $con->prepare("INSERT into postagens (id_usuario, id_obra, id, titulo, texto, data_post) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $id, $idObra, $idPost, $titulo, $texto, $datetime);
        try{
            $stmt->execute();
            header('Location: ../../html/telainicial.php');
        } catch (Exception $e) {
            echo "Um erro ocorreu:". mysqli_stmt_error($stmt); 
        }
    
        $stmt->close();
    
        }

    if($idObra){

        // Verificando se a obra já está salva no BD
        $stmt = $con->prepare("SELECT * from obras WHERE id = ? ");
        $stmt-> bind_param("s", $idObra);
        $stmt->execute();
        $stmt->store_result();
        $id_bd = $stmt->num_rows;
        $stmt->close();
            
            if($id_bd == 1){
                InserirDados();
            } else{

                // Se não estiver, puxa informações para salvar
                $tituloObra = $_POST['titulo'];
                $descriObra = $_POST['descricao'];
                $anoObra = $_POST['ano'];
                $autorObra = $_POST['autor'];
                $tipoObra = $_POST['tipo'];
                var_dump($_POST);

                date_default_timezone_set('America/Sao_Paulo');
                $dataInsercao = date('Y-m-d H:i:s');

                // Inserindo informações
                $stmt = $con->prepare("INSERT into obras (id, titulo, autor, ano_lancamento, descricao, data_criacao, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $idObra, $tituloObra, $autorObra, $anoObra, $descriObra, $dataInsercao, $tipoObra);
                $stmt->execute();
                $stmt->close();

                InserirDados();
                
        }
    }

}
?>