<?php
include 'login.php';
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
}
    //// essa parte é para receber e puxar as informações do banco de dados 





    // mostrando as notificações do usuario logado


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Notificações </title>
     <a href="notificações.css"> Notificações </a>
    </head>
    <body>
        <button id="ativarnoti" > Notificação </button>
         <?php foreach ($notificacoes as $n): ?>
      <p>📨 <?php echo htmlspecialchars($n['mensagem']); ?> (<?php echo $n['tipo']; ?>)</p>
    <?php endforeach; ?>
    </div>

            <script> 
            const idUsuario = 1; 
            const socket = new WebSocket ('ws://localhost:8080/notificacoes');
            socket.onopen = function () {
                console.log ('Está conectado ao WS');
                socket.send('Usuario:' + idUsuario) // informa qual é o usuário

            };
            socket_onmessage = function(event) {
            const mensagem = event. data;
            mostrarNoti(mensagem);
            if (notification. permission === )
            };
    </body>
    </html>