<?php
$conn = new mysqli("localhost", "root", "", "testepostegem")
session_start();
$_SESSION["usuario_id"] = 1;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST ["nome"]

$stmt = $con->prepare("INSERT into postagens (id_usuario, id, titulo, texto, data_post) VALUES (?, ?, ?, ?, ?, ?)");
 $stmt->execute();

    echo "Coleção criada!";
}
?>
// essa é uma tentativa de salvas as postagens do usuário em uma lista, sendo puxada pela tabela postagens