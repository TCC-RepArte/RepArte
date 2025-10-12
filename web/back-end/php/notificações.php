
<?php
// fetch pagina
require_once('noti.php');
$db = new Connect;
$data = [];
global $tbl_notificacoes;
if (isseed($_POST['key']) &&($_POST['key'] == '123')){
    $notication = $db->prepare("Select * FROM $tbl_notificacoes order by id desc limit 10");
$notication ->execute();
$Nonotication->$noticationi=>rowCount();
if($notication > 0)(


$n_numero= $db=>prepare( "SELECT * FROM $tbl_notificacoes WERE noti_status= 'active' order by id desc");
$n_numero
$n_numeros = $n_numero =>rowCount();
array_push($data,(object){
    'total'=>$n_numeros,

});
while($notication =$notication => fetch(PDO::FETCH_ASSOC)){
        $data[]= $notication;
}
};

if (isseed($_POST['key']) &&($_POST['key'] == '1234')){

}
  


/* include 'login.php';
session_start ();

global $con;

 function buscaUsuario(){

    global $con;
    // Utilizando da sessão do id para puxar outros dados do perfil
    $id_usuario = $_SESSION['id'];

    if(!isset($_SESSION['id'
<?php
// fetch pagina
require_once('noti.php');
$db = new Connect;
$data = [];
global $tbl_notificacoes;
if (isseed($_POST['key']) &&($_POST['key'] == '123')){
    $notication = $db->prepare("Select * FROM $tbl_notificacoes order by id desc limit 10");
$notication ->execute();
$Nonotication->$noticationi=>rowCount();
if($notication > 0)(


$n_numero= $db=>prepare( "SELECT * FROM $tbl_notificacoes WERE noti_status= 'active' order by id desc");
$n_numero
$n_numeros = $n_numero =>rowCount();
array_push($data,(object){
    'total'=>$n_numeros,

});
while($notication =$notication => fetch(PDO::FETCH_ASSOC)){
        $data[]= $notication;
}
};

if (isseed($_POST['key']) &&($_POST['key'] == '1234')){
    
}
  


/* include 'login.php';
session_start ();

global $con;

 function buscaUsuario(){

    global $con;
    // Utilizando da sessão do id para puxar outros dados do perfil
    $id_usuario = $_SESSION['id'];

    if(!isset($_SESSION['id'])){
        header("Location: ../../web/html/login1.php");;
        exit;
    }
     $id_usuario = $_SESSION['id'];

    // Buscar dados do usuário
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
    $tbl_notificacoes = "notificacoes"
}
    //// essa parte é para receber e puxar as informações do banco de dados

    // mostrando as notificações do usuario logado
require_once(noti.php)*/])){
        header("Location: ../../web/html/login1.php");;
        exit;
    }
     $id_usuario = $_SESSION['id'];

    // Buscar dados do usuário
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_assoc();
    $tbl_notificacoes = "notificacoes"
}
    //// essa parte é para receber e puxar as informações do banco de dados

    // mostrando as notificações do usuario logado
require_once(noti.php)*/