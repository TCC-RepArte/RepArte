<?php

include 'config.php';
session_start();

// Chamando o BD
global $con;

// Função que puxa dados do usuario logado
 function buscaUsuario(){

    global $con;
    // Utilizando da sessão do id para puxar outros dados do perfil
    $id_usuario = $_SESSION['id'];

    if(!isset($_SESSION['id'])){
        header("Location: ../../web/html/login1.php");;
        exit;
    }

// Retorna notificações não lidas como JSON
if (isset($_GET['acao']) && $_GET['acao'] === 'buscar') {
    $notiresult = $con->query("SELECT * FROM notificacoes WHERE lida = 0 ORDER BY id DESC");
    $novasmensagens = $notiresult->num_rows; // conta quantas novas mensagens existem
}
   $notificacoes = [];

    while ($row = $notiresult->fetch_assoc()) {
        $notificacoes[] = $row;
    }
    $mensagem = $novamensagem
    if($mensagem =={
        echo"Há uma nova notificação"
    }
    else {
        echo"Não há novas notificações "
    })

    header('Content-Type: application/json');
    echo json_encode($notificacoes);
    exit;

?>
<!-- JS simples para atualizar a cada 5 segundos -->
<script>
function atualizarNotificacoes() {
    fetch(window.location.href) // pega a mesma página
        .then(res => res.text())
        .then(html => {
            // Pega a div #notificacoes do HTML retornado
            let tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            let novas = tempDiv.querySelector('#notificacoes').innerHTML;
            document.getElementById('notificacoes').innerHTML = novas;
        });
}

// Atualiza a cada 5 segundos
setInterval(atualizarNotificacoes, 5000);
</script>