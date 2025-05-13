<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form id="loginf" method="post" action="back-end/signup.php">
        <label>usuario:</label>
        <input name="usuario" id="usuario" type="text">
        <label for="">email:</label>
        <input name="email" id="email" type="text">
        <label for="">senha:</label>
        <input name="senha" id="senha" type="password">
        <button type="submit">Enviar</button>
    </form>

</body>
</html>

<script

<?php include '../back-end/signup.php'; ?>